<?php include __DIR__ . '/../header.php'; ?>

    <h1>Проблема с активацией аккаунта
        <?php if (!empty($userName)): ?>
        <?=$userName?></h1>
    <?php endif; ?>
    <p style="font-size: 25px"><?=$errorMessage ?></p>
<?php include __DIR__ . '/../footer.php'; ?>