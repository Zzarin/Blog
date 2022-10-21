<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;

class AdminController extends AbstractController
{
    public function isAdmin()
    {
        return $this->user->isAdmin() ? ($this->user->getNickname() === 'admin') : false;
    }

    public function main(): void
    {
        if (!$this->isAdmin()) {
            throw new ForbiddenException('Недостаточно прав для входа в админ панель');
        }

        $articlesCount = count(Article::findAll());
        $commentsCount = count(Comment::findAll());
        $usersCount = count(User::findAll());

        $this->view->renderHTML('adminPanel/main.php',
            ['articlesCount' => $articlesCount, 'commentsCount' => $commentsCount, 'usersCount' => $usersCount]);
    }

    public function articles(): void
    {
        $articles = array_reverse(Article::findAll());

        $this->view->renderHTML('adminPanel/articles.php',
            ['articles' => $articles]
        );
    }

    public function edit($articleId): void
    {
        $article = Article::getById($articleId);

        if (!empty($_POST)) {
            try {
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('adminPanel/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }

            header('Location: /adminPanel/articles' . '#' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHTML('adminPanel/edit.php', ['article' => $article]);
    }

    public function comments(): void
    {
        $comments = array_reverse(Comment::findAll());

        $this->view->renderHTML('adminPanel/articles.php',
            ['comments' => $comments]
        );
    }

    public function editComment($commentId)
    {
        $comment = Comment::getById($commentId);

        if (!empty($_POST)) {
            try {
                $comment->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('adminPanel/edit.php', ['error' => $e->getMessage(), 'comment' => $comment]);
                return;
            }

            header('Location: /adminPanel/comments' . '#' . $comment->getId(), true, 302);
            exit();
        }

        $this->view->renderHTML('adminPanel/edit.php', ['comment' => $comment]);
    }

    public function users(): void
    {
        $users = User::findAll();

        //сортировка объектов по задаваемому полю: имен, дате регистрации или роли.
        if (!empty($_POST['SortByName'])) {
            usort($users, function ($userOne, $userTwo) {
                return strcasecmp($userOne->getNickname(), $userTwo->getNickname());
            });
        } elseif (!empty($_POST['SortByDate'])) {
            usort($users, function ($userOne, $userTwo) {
                return strtotime($userTwo->getCreatedAt()) <=> strtotime($userOne->getCreatedAt());
            });
        } elseif (!empty($_POST['SortByRole'])) {
            usort($users, function ($userOne, $userTwo) {
                return strcasecmp(!$userOne->isAdmin(), !$userTwo->isAdmin());
            });
        }

        $this->view->renderHTML('adminPanel/articles.php',
            ['users' => $users]
        );

    }
}