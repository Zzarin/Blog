<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\NotFoundException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UsersAuthService;
use MyProject\View\View;

class MainController extends AbstractController
{

    public function main()
    {
        $lastID = Article::getLastID();

        if ($lastID === null) {
            throw new NotFoundException();
        }

        $this->after($lastID + 1);
    }

    private function page(array $articles)
    {
        if ($articles === []) {
            throw new NotFoundException();
        }

        $firstID = $articles[0]->getId();
        $lastID = $articles[count($articles) - 1]->getId();


        $this->view->renderHTML('main/main.php', [
                'articles' => $articles,
                'previousPageLink' => Article::hasPreviousPage($firstID) ? '/before/' . $firstID : null,
                'nextPageLink' => Article::hasNextPage($lastID) ? '/after/' . $lastID : null
            ]);
    }

    public function before(int $id)
    {
        $this->page(Article::getPageBefore($id, 5));
    }

    public function after(int $id)
    {
        $this->page(Article::getPagesAfter($id, 5));
    }

    public function sayHello(string $name)
    {
        $this->view->renderHTML('main/hello.php', ['name' => $name, 'title' => 'Страница приветствия']);
    }

    public function sayBye(string $name)
    {
        $this->view->renderHTML('main/main.php', ['name' => $name]);
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}