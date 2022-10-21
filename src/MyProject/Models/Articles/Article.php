<?php

namespace MyProject\Models\Articles;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;
use MyProject\Services\Db;

class Article extends ActiveRecordEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $text;

    /** @var int */
    protected $authorId;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return (int) $this->authorId;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $normalDate = date( 'H:i:s d.m.Y', strtotime($this->createdAt) );
    }

    /**
     * @return string
     */
    public function getShortText(): string
    {
        return mb_substr($this->getText(), 0, 100, 'UTF-8');
    }

    public function getParsedText(): string
    {
        $parser = new \Parsedown();
        return $parser->text($this->getText());
    }

    /**
     * @param string $newName
     * @return void
     */
    public function setName(string $newName): void
    {
        $this->name = $newName;
    }

    /**
     * @param string $newText
     * @return void
     */
    public function setText(string $newText): void
    {
        $this->text = $newText;
    }

    public function setId(int $newId): void
    {
        $this->id = $newId;
    }

    /**
     * @param User $author
     */
    public function setAuthor(object $author): void
    {
        $this->authorId = $author->getId();
    }

    public static function createFromArray(array $fields, User $author): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }

        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $article = new Article();

        $article->setAuthor($author);
        $article->setName($fields['name']);
        $article->setText($fields['text']);

        $article->save();

        return $article;
    }

    public function updateFromArray(array $fields): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано название статьи');
        }

        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $this->setName($fields['name']);
        $this->setText($fields['text']);

        $this->save();

        return $this;
    }

    /**
     * @return Article[]
     */
    public static function getPageBefore(int $id, int $limit): array
    {
        $db = Db::getInstance();
        $sql = sprintf('SELECT * FROM (SELECT * FROM ' . self::getTableName() . ' WHERE id > :id ORDER BY id ASC LIMIT %d) as articles ORDER by id DESC;', $limit);
        return $db->query($sql, ['id' => $id], self::class);
    }

    /**
     * @return Article[]
     */
    public static function getPagesAfter(int $id, int $limit): array
    {
        $db = Db::getInstance();
        $sql = sprintf('SELECT * FROM ' . self::getTableName() . ' WHERE id < :id ORDER by id DESC LIMIT %d;', $limit);
        return $db->query($sql, ['id' => $id], self::class);
    }

    public static function hasNextPage(int $pageLastId): bool
    {
        $db = Db::getInstance();
        $sql = 'SELECT id FROM ' . self::getTableName() . ' WHERE id < :id LIMIT 1;';
        $result = $db->query($sql, ['id' => $pageLastId]);
        return !empty($result);
    }

    public static function hasPreviousPage(int $pageFirstId): bool
    {
        $db = Db::getInstance();
        $sql = 'SELECT id FROM ' . self::getTableName() . ' WHERE id < :id LIMIT 1;';
        $result = $db->query($sql, ['id' => $pageFirstId]);
        return !empty($result);
    }

    public static function getLastID(): ?int
    {
        $db = Db::getInstance();
        $sql = 'SELECT id FROM ' . self::getTableName() . ' ORDER BY id DESC LIMIT 1;';
        $result = $db->query($sql);
        return !empty($result) ? $result[0]->id : null;
    }
}