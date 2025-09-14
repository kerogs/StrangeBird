<?php

// ! Uncomment if you want to allow error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);


// ! if no database.sqlite, redirect to setup.php
if (!file_exists(__DIR__ . '/../backend/database.sqlite')) {
    header('Location: /setup.php');
    exit;
}


// auth
require __DIR__ . '/../backend/Auth.php';

// helpers
require __DIR__ . '/helpers/errors.php';
require __DIR__ . '/helpers/specialTags.php';

// login to the database
$pdo = new PDO('sqlite:' . __DIR__ . '/../backend/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);

// ? Uncomment/comment if you want to force login
// if (!$auth->isLoggedIn()) {
//     header('Location: /login');
//     exit;
// }