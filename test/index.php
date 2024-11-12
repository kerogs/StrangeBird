<?php

chdir("../");
require_once('config.php');
require_once('backend/core-labs.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labs</title>
</head>
<style>
    * {
        background-color: #F4F4F9;
        font-family: sans-serif;
    }

    h1,
    h2 {
        color: #a038ec;
    }

    h1.p span {
        color: #a038ec;
    }

    table {
        box-shadow: 0 0 10px rgb(0, 0, 0) !important;
    }

    tr * {
        color: #212529;
        background-color: #EDEDF3 !important;
    }

    tr.h td {
        border: 1px solid #a038ec !important;
        padding: 20px;
    }

    td {
        border: 1px solid #DADAE3 !important;
    }

    td.e {
        background-color: #C7C7D2 !important;
    }

    tr:hover * {
        background-color: #DADAE3 !important;
    }

    .logo {
        position: fixed;
        bottom: -80px;
        right: -80px;
        opacity: .2;
        width: 350px;
    }

    .logo img {
        width: 100%;
    }
</style>

<body>

    <?= phpinfo() ?>

    <div class="logo">
        <img src="src/img/logo.png" alt="">
    </div>

</body>
<script>
    const elements = document.querySelectorAll('h1.p');
    elements.forEach(element => {
        element.innerHTML += '<br><span>KerogsPHP Version <?= $kpf_config['other']['website_version'] ?></span>';
    });
</script>

</html>