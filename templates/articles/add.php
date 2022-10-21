<?php include __DIR__ . '/../header.php'; ?>
<h1>Создание новой статьи</h1>
<?php if (!empty($error)): ?>
    <div style="color: red"><?=$error?></div>
<?php endif; ?>
<form action="/articles/add" method="post">
    <label for="name">Название статьи</label><br>
    <input type="text" name="name" id="name" value="<?= $_POST['name'] ?? '' ?>" size="50"><br>
    <br>
    <label for="text">Текст статьи</label><br>
    <textarea name="text" id="text" cols="120" rows="10"><?= $_POST['text'] ?? '' ?></textarea><br>
    <br>
    <input type="submit" value="Создать">
</form>

<?php include __DIR__ . '/../footer.php';