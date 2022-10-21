<?php

namespace MyProject\Models\Comments;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Articles\Article;
use MyProject\Models\Users\User;

class Comment extends ActiveRecordEntity
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $articleId;

    /** @var string */
    protected $text;

    /** @var string */
    protected $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /** @return int */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /** @return int */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /** @return string */
    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): string
    {
        return $normalDate = date('H:i:s d.m.Y', strtotime($this->createdAt));
    }

    public static function getTableName(): string
    {
        return 'comments';
    }

    public function getAuthor(): ?User
    {
        return User::getById($this->userId);
    }

    public static function getArticleName($articleId): string
    {
        return Article::findOneByColumn('id', $articleId)->getName();
    }

    /**
     * @return string
     */
    public function getShortText(): string
    {
        return mb_substr($this->getText(), 0, 100, 'UTF-8');
    }

    /**
     * @param object $article
     * @return void
     */
    public function setUserId(object $user): void
    {
        $this->userId = $user->getId();
    }

    /**
     * @param object$article
     * @return void
     */
    public function setArticleId(object $article): void
    {
        $this->articleId = $article->getId();
    }

    public function setText($newText): void
    {
        $this->text = $newText;
    }

    public static function createFromArray(array $fields, Article $article, User $user): Comment
    {
        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст комментария');
        }

        $comment = new Comment();

        $comment->setUserId($user);
        $comment->setArticleId($article);
        $comment->setText($fields['text']);

        $comment->save();

        return $comment;
    }

    public function updateFromArray(array $fields): Comment
    {
        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $this->setText($fields['text']);

        $this->save();

        return $this;
    }
}