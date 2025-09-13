<?php

session_start();

/**
 * Connexion PDO à la BDD SQLite
 */
$dbPath = __DIR__ . '/../backend/database.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

/**
 * Vérification si formulaire soumis
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation de base
    if ($username === '' || $password === '') {
        header('Location: /login?ntf=empty_fields');
        exit;
    }

    // Recherche de l'utilisateur
    $stmt = $pdo->prepare('SELECT id, username, password, uuid FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: /login?ntf=invalid_credentials');
        exit;
    }

    // Vérification du mot de passe
    if (!password_verify($password, $user['password'])) {
        header('Location: /login?ntf=invalid_credentials');
        exit;
    }

    // Connexion réussie - stockage dans la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['uuid'] = $user['uuid'];

    // Redirection vers la page d'accueil
    header('Location: /');
    exit;
}

// Si la méthode n'est pas POST, redirection vers la page de login
header('Location: /login');
exit;
