<?php include __DIR__ . '/adminHeader.php'; ?>
<?php if (!empty($error)): ?>
    <div style="color: red"><?= $error ?></div>
<?php endif ?>
<?php if (isset($article)): ?>
    <form action="/adminPanel/<?= $article->getId()?>/edit" method="post">
        <label for="name">Название статьи</label><br>
        <input type="text" name="name" value="<?= $_POST['name'] ?? $article->getName() ?>" size="50"><br>
        <br>
        <label for="text">Текст статьи</label><br>
        <textarea name="text" id="text" cols="120" rows="10"><?= $_POST['text'] ?? $article->getText() ?></textarea><br>
        <br>
        <input type="submit" value="Обновить">
    </form>
<?php endif;?>

<?php if (isset($comment)): ?>
    <form action="/adminPanel/<?= $comment->getId()?>/editComment" method="post">
        <p><?= $comment->getAuthor()->getNickname() . '  ' . $comment->getCreatedAt() ?></p>
        <label for="text">Текст комментария</label><br>
        <textarea name="text" id="text" cols="120" rows="10"><?= $_POST['text'] ?? $comment->getText() ?></textarea><br>
        <br>
        <input type="submit" value="Обновить комментарий">
    </form>
<?php endif; ?>
<?php include __DIR__ . '/adminFooter.php'; ?>
