<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\View\View;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UsersAuthService;
use MyProject\Models\Comments\Comment;

class ArticlesController extends AbstractController
{

    public function view(int $articleId)
    {
        $article = Article::getById($articleId);

        /*$reflector = new \ReflectionObject($article);
        $properties = $reflector->getMethods();

        var_dump($properties);
        return;*/

        if ($article === null) {
            throw new NotFoundException();
        }

        $isEditable = ($this->user !== null) ? ($this->user->isAdmin()) : false;

        $comments = Comment::findAll();

        if ($comments === null) {
            throw new NotFoundException();
        }

        $this->view->renderHTML('articles/view.php', [
            'article' => $article,
            'isEditable' => $isEditable,
            'comments' => $comments,
        ]);

    }

    public function edit(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException('Недостаточно прав для редактирования статьи');
        }

        if (!empty($_POST)) {
            try {
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('articles/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }

            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHTML('articles/edit.php', ['article' => $article]);
    }

    public function add(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException('Недостаточно прав для добавления статьи');
        }

        if (!empty($_POST)) {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('articles/add.php', ['error' => $e->getMessage()]);
                return;
            }

            header('Location: /articles/' . $article->getId());
            exit();
        }

        $this->view->renderHTML('articles/add.php');

    }

    public function delete(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        $article->delete();
        $this->view->renderHTML('executed/requestIsExecuted.php', ['articleId' => $articleId]);


    }

    public function comments(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        $user = $this->user;

        if ($user === null) {
            throw new UnauthorizedException();
        }

        if (!empty($_POST['text'])) {
            try {
                $comment = Comment::createFromArray($_POST, $article, $user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('article/view.php', ['error' => $e->getMessage()]);
                return;
            }

            header('Location: /articles/' . $article->getId() . '#' . $comment->getId(), true, 302);
            exit();
        }

        $this->view($articleId);

    }

    public function editComments(int $commentId): void
    {

        $comment = Comment::getById($commentId);

        if ($comment === null) {
            throw new NotFoundException();
        }

        if ($this->user->getId() !== $comment->getUserId()) {
            if (!$this->user->isAdmin()) {
                throw new ForbiddenException('Только автор комментария или админ могут редактировать комментарий');
            }
        }

        if (!empty($_POST)) {
            try {
                $comment->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('comments/edit.php', ['error' => $e->getMessage(), 'comment' => $comment]);
                return;
            }

            header('Location: /articles/' . $comment->getArticleId() . '#' . $comment->getId(), true, 302);
            exit();
        }

        $this->view->renderHTML('comments/edit.php', ['comment' => $comment, 'user' => $this->user]);
    }
}