<?php

require_once __DIR__ . '/../includes/core.php';

// allow error display for debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('sqlite:' . __DIR__ . '/../backend/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user is logged in
if ($auth->isLoggedIn() === false) {
    // give 403 error
    http_response_code(403);
    require __DIR__ . '/../403.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $tag = strtolower($tag);

    if (!$name || !$description || !$tag) {
        die('Please fill all required fields.');
    }

    // Dossier upload
    $uploadDir = __DIR__ . '/../uploads/scans/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    $uploadedPaths = [];

    // --- cover required ---
    if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== 0) {
        die("Cover image is required.");
    }

    $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        die("Invalid file type for cover");
    }
    $newName = uniqid('cover_') . '.' . $ext;
    move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $newName);
    $uploadedPaths['cover'] = '/uploads/scans/' . $newName;

    // --- background optionnel ---
    $uploadedPaths['background'] = null;
    if (isset($_FILES['background']) && $_FILES['background']['error'] === 0) {
        $extB = strtolower(pathinfo($_FILES['background']['name'], PATHINFO_EXTENSION));
        if (!in_array($extB, ['jpg', 'jpeg', 'png', 'webp'])) {
            die("Invalid file type for background");
        }
        $newNameB = uniqid('background_') . '.' . $extB;
        move_uploaded_file($_FILES['background']['tmp_name'], $uploadDir . $newNameB);
        $uploadedPaths['background'] = '/uploads/scans/' . $newNameB;
    }

    $stmt = $pdo->prepare('INSERT INTO scan 
        (name, description, tag, cover, background, view, like, dislike, datetime, addedby_user_id)
        VALUES (:name, :description, :tag, :cover, :background, 0, 0, 0, :datetime, :user_id)
    ');

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'tag' => $tag,
        'cover' => $uploadedPaths['cover'],
        'background' => $uploadedPaths['background'], // null si pas d’upload
        'datetime' => time(),
        'user_id' => $_SESSION['user_id']
    ]);

    header('Location: /add/scan?ntf=scan_added');
    exit;
}
