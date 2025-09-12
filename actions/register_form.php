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
        die('Veuillez remplir tous les champs.');
    }

    // Vérifie si le pseudo est déjà pris
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        die('Nom d’utilisateur déjà utilisé.');
    }

    // Hash du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Timestamp actuel
    $timestamp = time();

    // Génération uuid
    $uuid = bin2hex(random_bytes(32));

    // Insertion dans la base
    $stmt = $pdo->prepare('
        INSERT INTO users (username, password, profile_picture, banner, datetime, uuid)
        VALUES (:username, :password, "", "", :datetime, :uuid)
    ');
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'datetime' => $timestamp,
        'uuid' => $uuid
    ]);

    // Récupération de l’ID du nouvel utilisateur
    $userId = $pdo->lastInsertId();

    // Stocke l'ID dans la session = utilisateur connecté
    $_SESSION['user_id'] = $userId;
    $_SESSION['uuid'] = $uuid;

    // Redirection vers une page d’accueil ou autre
    header('Location: /'); // change si besoin
    exit;
}

die('Méthode invalide');
