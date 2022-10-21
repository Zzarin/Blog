<?php include __DIR__ . '/adminHeader.php'; ?>
<?php if (!empty($articles)): ?>
    <?php foreach ($articles as $article): ?>
        <div>
            <h2 id="<?= $article->getId() ?>"><?= $article->getName(); ?></h2>
            <p><a href="/adminPanel/<?= $article->getId() ?>/edit" title="редактировать статью">
                    <?= $article->getShortText() . '...'; ?></a></p>
            <p style="margin-left: 80%"><?= $article->getAuthor()->getNickname() . ' ' . $article->getCreatedAt(); ?> </p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <div>
            <h2 id="<?= $comment->getId() ?>">
                <p><?= $comment->getArticleName($comment->getArticleId()); ?> </p>
            </h2>
            <p style="font-size: 21px"><?= $comment->getAuthor()->getNickname() . ' ' . $comment->getCreatedAt(); ?> </p>
            <p style="font-size: 20px"><a href="/adminPanel/<?= $comment->getId() ?>/editComment"
                                          title="редактировать комментарий">
                    <?= $comment->getShortText() . '...'; ?></a></p>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($users)): ?>
    <div style="padding-bottom: 5px; clear:both; text-align:right;">
        <form action="/adminPanel/users" method="post" >
            <input type="hidden" name="SortByName" value="">
            <input type="submit" class="button" name="SortByName" value="По имени пользователя" style="font-size: 15px">
            <input type="hidden" name="SortByDate" value="">
            <input type="submit" class="button" name="SortByDate" value="По дате регистрации" style="font-size: 15px">
            <input type="hidden" name="SortByRole" value="">
            <input type="submit" class="button" name="SortByRole" value="По правам" style="font-size: 15px">
        </form>
    </div>
    <?php foreach ($users as $user): ?>
        <div>
            <table cellpadding="0%">
                <tr>
                    <td width="20%" height="20%" align="justify" style="margin: unset">
                        <img src="<?= $user->getAvatarPath(); ?>" alt="avatar" width="100%"></td>
                    <td style="width: 100%">
                        <h2 id="<?= $user->getId() ?>">
                            <p><?= $user->getNickname(); ?></p>
                        </h2>
                        <p>
                            <?= $user->getEmail(); ?>
                        </p>
                        <p>
                            <?php if (!$user->isAdmin()): ?>
                                Статус пользователя: Обычный пользователь
                            <?php else: ?>
                                Статус пользователя: Администратор
                            <?php endif; ?>
                        </p>
                        <p>
                            Пользователь зарегистрирован <?= $user->getCreatedAt(); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/adminFooter.php'; ?>
