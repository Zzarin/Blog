<?php include __DIR__ . '/../header.php'; ?>
    <h1 style="background-color: red">У вас недостаточно прав доступа</h1>
    <?= $error; ?>
    <br>
    <a href="/users/login">Войти с правами администратора</a>
<?php include __DIR__ . '/../footer.php'; ?>