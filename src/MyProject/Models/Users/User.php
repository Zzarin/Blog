<?php

namespace MyProject\Models\Users;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{
    /** @var string */
    protected $nickname;

    /** @var string */
    protected $email;

    /** @var int */
    protected $isConfirmed;

    /** @var string */
    protected $role;

    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $avatarPath;

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    protected static function getTableName(): string
    {
        return 'users';
    }

    public function getActivationStatus(): int
    {
        if ($this->isConfirmed === 1) {
            return true;
        }
        return false;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    private function refreshAuthToken(): void
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }

    private function getRole(): string
    {
        return $this->role;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function getCreatedAt(): string
    {
        return date('H:i:s d.m.Y', strtotime($this->createdAt));
    }

    public function setToken($newToken): void
    {
        $this->authToken = $newToken;
    }

    public static function signUp(array $userData, array $userAvatar): User
    {
        if (empty($userData['nickname'])) {
            throw new InvalidArgumentException('Не передан nickname');
        }

        if (!preg_match('/^[a-zA-z0-9]+$/', $userData['nickname'])) {
            throw new InvalidArgumentException('Nickname может состоять только из символов латинского алфавита и цифр');
        }

        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email некорректен');
        }

        if (empty($userData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }

        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }

        if (static::findOneByColumn('nickname', $userData['nickname']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
        }

        if (static::findOneByColumn('email', $userData['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже существует');
        }

        if (!empty($userAvatar['avatar']['tmp_name'])) {
            $file = $userAvatar['avatar'];

            // собираем путь до нового файла - папка uploads в текущей директории
            // в качестве имени оставляем исходное файла имя во время загрузки в браузере
            $srcFileName = $file['name'];
            $newFilePath = __DIR__ . '/../../../../www/uploads/' . $srcFileName;

            $allowedExtensions = ['jpg', 'png', 'gif'];
            $extension = pathinfo($srcFileName, PATHINFO_EXTENSION);
            $definition = $_FILES['avatar']['tmp_name'] ? getimagesize($_FILES['avatar']['tmp_name']) : null;


            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new InvalidArgumentException('Файл не был загружен');
            } elseif ($file['error'] === UPLOAD_ERR_INI_SIZE) {
                throw new InvalidArgumentException('Размер файла превышает допустимый предел');
            } elseif (!in_array($extension, $allowedExtensions)) {
                throw new InvalidArgumentException('Загрузка файлов с таким расширением запрещена!');
            } elseif (file_exists($newFilePath)) {
                throw new InvalidArgumentException('Файл с таким именем уже существует');
            } elseif ($definition[0] > 512 && $definition[1] > 512) {
                throw new InvalidArgumentException('Превышено разрешение изображения');
            } elseif (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
                throw new InvalidArgumentException('Ошибка при загрузке файла');
            }
                $userAvatarPath = 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $srcFileName;

        } else {
            $userAvatarPath = 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/default/default.png';
        }

        $user = new User();
        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false;
        $user->role = 'user';
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->avatarPath = $userAvatarPath;
        $user->save();

        return $user;
    }

    public function activate(): void
    {
        $this->isConfirmed = true;
        $this->save();
    }

    public static function login(array $loginData): User
    {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (empty($loginData['password'])) {
            throw new InvalidArgumentException('Не передан пароль');
        }

        $user = User::findOneByColumn('email', $loginData['email']);

        if ($user === null) {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }

        if (!password_verify($loginData['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный пароль');
        }

        if (!$user->isConfirmed) {
            throw new InvalidArgumentException('Пользователь не подтверждён');
        }

        $user->refreshAuthToken();
        $user->save();

        return $user;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        if ($this->getRole() !== 'admin') {
            return false;
        }

        return true;
    }

}