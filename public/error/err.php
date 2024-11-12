<?php

chdir("../");

require_once('../config.php');

?>

<!DOCTYPE html>
<html lang="<?= $kpf_config["seo"]["lang_short"] ?>">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?> | error</title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
</head>

<body>

    <?php require_once '../inc/header.php' ?>

    <div class="errorInterface">
        <p><?= htmlspecialchars($_GET['e']) ?></p>
    </div>

    <?php require_once '../inc/script.php' ?>
</body>

</html>