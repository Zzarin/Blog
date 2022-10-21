<?php

return [
    '~^$~' => [\MyProject\Controllers\MainController::class, 'main'],
    '~^hello/(.*)$~' => [\MyProject\Controllers\MainController::class, 'sayHello'],
    '~^bye/(.*)$~' => [\MyProject\Controllers\MainController::class, 'sayBye'],

    '~^(\d+)$~' => [\MyProject\Controllers\MainController::class, 'page'],

    '~^before/(\d+)$~' => [\MyProject\Controllers\MainController::class, 'before'],
    '~^after/(\d+)$~' => [\MyProject\Controllers\MainController::class, 'after'],

    '~^articles/(\d+)$~' => [\MyProject\Controllers\ArticlesController::class, 'view'],
    '~^articles/add$~' => [\MyProject\Controllers\ArticlesController::class, 'add'],
    '~^articles/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'edit'],
    '~^articles/(\d+)/delete$~' => [\MyProject\Controllers\ArticlesController::class, 'delete'],
    '~^articles/(\d+)/comments$~' => [\MyProject\Controllers\ArticlesController::class, 'comments'],
    '~^articles/comments/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'editComments'],

    '~^users/register$~' => [\MyProject\Controllers\UsersController::class, 'signUp'],
    '~^users/(\d+)/activate/(.+)$~' => [\MyProject\Controllers\UsersController::class, 'activate'],
    '~^users/login$~' => [\MyProject\Controllers\UsersController::class, 'login'],
    '~^users/logout$~' => [\MyProject\Controllers\UsersController::class, 'logout'],

    '~^adminPanel/main$~' => [\MyProject\Controllers\AdminController::class, 'main'],
    '~^adminPanel/(\d+)/edit$~' => [\MyProject\Controllers\AdminController::class, 'edit'],
    '~^adminPanel/articles$~' => [\MyProject\Controllers\AdminController::class, 'articles'],
    '~^adminPanel/comments$~' => [\MyProject\Controllers\AdminController::class, 'comments'],
    '~^adminPanel/(\d+)/editComment$~' => [\MyProject\Controllers\AdminController::class, 'editComment'],
    '~^adminPanel/users$~' => [\MyProject\Controllers\AdminController::class, 'users']
];