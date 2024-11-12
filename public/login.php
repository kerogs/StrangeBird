<?php

require_once('../config.php');

?>

<!DOCTYPE html>
<html lang="<?= $kpf_config["seo"]["lang_short"] ?>">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?> | login</title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
</head>

<body>

    <?php require_once '../inc/header.php' ?>

    <div class="loginInterface">

        <?php if ($_GET['a'] == "login") : ?>
            <form action="action/login.php" method="post">
                <h1>Login</h1>
                <input type="text" name="username" placeholder="your nameid" required id="">
                <input type="password" name="password" placeholder="your password" minlength="4" id="" required>
                <button type="submit">Login</button>
            </form>
        <?php else: ?>
            <form action="action/register.php" method="post">
                <h1>Register</h1>
                <input type="password" name="password" placeholder="write your password" minlength="4" id="" required>
                <button type="submit">Register</button>
            </form>
        <?php endif; ?>

    </div>

    <?php require_once '../inc/script.php' ?>
</body>

</html>