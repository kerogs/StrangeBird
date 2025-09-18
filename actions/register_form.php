<?php

session_start();

$dbPath = __DIR__ . '/../backend/database.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        die('Be sure to fill all required fields.');
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        die('Username already taken.');
    }

    // check if it's the first user
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $result = $stmt->fetch();
    $isFirstUser = ($result['count'] == 0);
    
    $adminStatus = $isFirstUser ? 1 : 0;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $timestamp = time();
    $uuid = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare('
        INSERT INTO users (username, password, profile_picture, banner, datetime, uuid, admin)
        VALUES (:username, :password, "", "", :datetime, :uuid, :admin)
    ');
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'datetime' => $timestamp,
        'uuid' => $uuid,
        'admin' => $adminStatus
    ]);

    $userId = $pdo->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['uuid'] = $uuid;
    
    // Si admin, stocker aussi dans la session
    if ($isFirstUser) {
        $_SESSION['admin'] = true;
    }

    header('Location: /');
    exit;
}

die('Invalid request.');