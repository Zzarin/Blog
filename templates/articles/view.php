<?php include __DIR__ . '/../header.php'; ?>
    <h1><?= $article->getName() ?></h1>
    <table class="articleViewTable">
        <tr>
            <td colspan="2">
                <p style="font-size: 25px; text-align: justify"><?= $article->getParsedText() ?></p>
                <p>Автор: <?= $article->getAuthor()->getNickname() ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 65%">
                <?php if ($isEditable): ?>
                    <p>
                        <a href="/articles/<?= $article->getId() ?>/edit" style="display:inline-block; padding:0 2rem;
                        margin-bottom: 40px">Редактировать статью</a>
                        <a href="/articles/<?= $article->getId() ?>/delete">Удалить статью</a>
                    </p>
                <?php endif; ?>
            </td>
        </tr>

        <?php foreach ($comments as $comment): ?>
            <?php if ($comment->getAuthor() !== null): ?>
                <?php if ($article->getId() === $comment->getArticleId()): ?>
                    <tr id="<?= $comment->getId() ?>">
                        <td rowspan="3" width="80px">
                            <img src="<?= $comment->getAuthor()->getAvatarPath() ?>" alt="no avatar" width="80px">
                        </td>
                        <td style="background-color: darkgray">
                            <?= $comment->getAuthor()->getNickname() . '  ' . $comment->getCreatedAt() ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 40px; font-size: 25px">
                            <?= $comment->getText() ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 70%; padding-bottom: 30px">
                            <?php if ($user !== null): ?>
                                <?php if (($user->getId() === $comment->getUserId()) || ($user->isAdmin())): ?>
                                    <a href="/articles/comments/<?= $comment->getId() ?>/edit" style="color: teal">
                                        Редактировать комментарий</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <?php if ($user !== null): ?>
                <td colspan="2">
                    <form action="/articles/<?= $article->getId() ?>/comments" method="post">
                        <label for="text">Написать комментарий</label><br>
                        <textarea name="text" id="text" cols="125" rows="10"></textarea><br>
                        <input type="submit" value="Отправить">
                    </form>

                </td>
            <?php elseif (!empty($error)): ?>
        </tr>
        <tr>
            <td>
                <p><?= $error ?>,
                    <a href="/users/login">Войти</a></p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <hr>
<?php include __DIR__ . '/../footer.php'; ?>