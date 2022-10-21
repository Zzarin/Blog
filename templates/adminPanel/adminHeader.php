<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Панель администратора' ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<table class="layout">
    <tr>
        <td colspan="2" class="header" style="text-align: center">Панель администратора</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right; background-color: cornflowerblue; font-size: 25px">
            <?php if (!empty($user)): ?>
                <strong><?= 'Привет ' . $user->getNickname()?></strong>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td width="300px" class="sidebar">
            <div class="sidebarHeader">Навигация:</div>
            <ul>
                <li style="padding-bottom: 10px">
                    <a href="/" >Перейти на главную страницу сайта</a>
                </li>
                <li style="padding-bottom: 10px">
                    <a href="/adminPanel/main" >Перейти на главную страницу админки</a>
                </li>
                <li style="padding-bottom: 10px">
                    <a href="/adminPanel/articles">Последние добавленные статьи</a>
                </li>
                <li style="padding-bottom: 10px">
                    <a href="/adminPanel/comments">Последние добавленные комментарии</a>
                </li>
                <li style="padding-bottom: 10px">
                    <a href="/adminPanel/users">Список пользователей</a>
                </li>
            </ul>

        </td>
        <td>