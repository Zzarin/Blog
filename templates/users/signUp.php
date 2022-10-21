<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center">
        <h1>Регистрация</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red; padding: 5px; margin: 15px;"><?=$error?></div>
        <?php endif;?>
        <form action="/users/register" method="post" enctype="multipart/form-data">
            <label for="avatar">Avatar<input type="file" name="avatar" title="+"></label>
            <br>
            <br>
            <label>Nickname <input type="text" name="nickname" value="<?= $_POST['nickname']?? ''?>"></label>
            <br>
            <br>
            <label>Email <input type="text" name="email" value="<?= $_POST['email']?? ''?>"></label>
            <br>
            <br>
            <label>Password <input type="password" name="password" value="<?= $_POST['password']?? ''?>"></label>
            <br>
            <br>
            <input type="submit" value="Зарегистрироваться">
        </form>
    </div>
<?php include __DIR__ . '/../footer.php';
