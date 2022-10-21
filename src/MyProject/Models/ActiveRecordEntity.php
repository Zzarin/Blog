<?php

namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity implements \JsonSerializable
{
    /** @var int */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __set(string $name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }

    /**
     * @return static[]
     */
    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    abstract protected static function getTableName(): string;

    /**
     * @param int $id
     * @return static|null
     */
    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
        //var_dump($mappedProperties);
    }

    private function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;

        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index;                 // :param1
            $columns2params[] = $column . '=' . $param; // column1 = :param1
            $params2values[$param] = $value;            //[:param1 => value1]
            $index++;
        }

        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) .
            ' WHERE id= ' . $this->id;
        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);


    }

    private function insert(array $mappedProperties): void
    {
        $filteredProperties = array_filter($mappedProperties);

        $columns = [];
        $paramsNames = [];
        $params2values = [];

        foreach ($filteredProperties as $columnName => $value) {

            $columns[] = '`' . $columnName . '`';    // ['columnName']
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;             //[:columnName];
            $params2values[$paramName] = $value;            //[:columnName => value]
        }

        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);

        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ') ' .
            'VALUES (' . $paramsNamesViaSemicolon . ');';

        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);

        $this->id = $db->getLastInsertId();

        $this->refresh();

    }

    /*Он берет версию объекта из базы, получает все его свойства. Затем бежит в цикле по этим свойствам и:
        1) делает их публичными;
        2) читает их имя;
        3) в текущем объекте (у которого вызвали refresh) свойству с таким же именем задаёт значение из свойства,
            взятого у объекта из базы ($objectFromDb).
     * */
    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);

        /*foreach позволяет пройти по тем свойствам объекта (как по элементам массива), которые
        являются видимыми и доступными. Если копнуть глубже, то с помощью интерфейса Traversable,
        который определяет является ли класс обходимым с использованием foreach.
         */
        foreach ($objectFromDb as $property => $value) {
            $this->$property = $value;
        }

        /* решение через получение свойств объекта (встр.функцию php
          $properties = get_object_vars($objectFromDb);

        foreach ($properties as $key=>$value) {
            $this->$key = $value;
        }*/
    }

    public function delete(): void
    {
        $db = Db::getInstance();
        $db->query(
            'DELETE FROM `' . static::getTableName() . '` WHERE id = :id',
            [':id' => $this->id]);

        $this->id = null;
    }

    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    public static function findOneByColumn(string $columnName, $value): ?self
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1;',
            [':value' => $value],
            static::class
        );
        if ($result === []) {
            return null;
        }
        return $result[0];
    }

    public function jsonSerialize()
    {
        return $this->mapPropertiesToDbFormat();
    }

    public static function getPagesCount(int $itemsPerPage): int
    {
        $db = Db::getInstance();
        $result = $db->query('SELECT COUNT(*) AS cnt FROM ' . static::getTableName() . ';');
        return ceil($result[0]->cnt / $itemsPerPage);
    }

    /**
     * @return static[]
     */
    public static function getPage(int $pageNum, int $itemsPerPage): array
    {
        $db = Db::getInstance();

        /*$result = $db->query('SELECT * FROM ' . static::getTableName() . ' ORDER BY id DESC LIMIT :howMany OFFSET :fromWhich;',
            [':howMany' => $itemsPerPage,
             ':fromWhich' => ($pageNum - 1) * $itemsPerPage],
            static::class
        );

        return $result;*/

        return $db->query(
            sprintf(
                'SELECT * FROM `%s` ORDER BY id DESC LIMIT %d OFFSET %d;',
                static::getTableName(),
                $itemsPerPage,
                ($pageNum - 1) * $itemsPerPage
            ),
            [],
            static::class
        );
    }
}