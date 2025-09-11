<?php

require __DIR__ . '/../backend/Auth.php';

// login to the database
$pdo = new PDO('sqlite:' . __DIR__ . '/../backend/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);

// ? Uncomment/comment if you want to force login
// if (!$auth->isLoggedIn()) {
//     header('Location: /login');
//     exit;
// }