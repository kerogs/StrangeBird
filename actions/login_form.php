<?php

session_start();


$dbPath = __DIR__ . '/../backend/database.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header('Location: /login?ntf=empty_fields');
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, username, password, uuid FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: /login?ntf=invalid_credentials');
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        header('Location: /login?ntf=invalid_credentials');
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['uuid'] = $user['uuid'];

    header('Location: /');
    exit;
}

header('Location: /login');
exit;
