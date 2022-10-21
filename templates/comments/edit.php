<?php
/**
 * @var \MyProject\Models\Comments\Comment $comment
 */
include __DIR__ . '/../header.php';
?>
    <h1>Редактирование комментария</h1>
<?php if (!empty($error)): ?>
    <div style="color: red"><?= $error ?></div>
<?php endif ?>
    <div style="background-color: darkgray"><?= $user->getNickname() . '  ' . $comment->getCreatedAt() ?></div>
    <br>
    <form action="/articles/comments/<?= $comment->getId() ?>/edit" method="post">
        <label for="text">Текст комментария</label><br>
        <textarea name="text" id="text" cols="125" rows="10"><?= $_POST['text'] ?? $comment->getText() ?></textarea><br>
        <br>
        <input type="submit" value="Обновить">
    </form>

<?php include __DIR__ . '/../footer.php';