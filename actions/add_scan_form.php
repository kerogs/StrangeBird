<?php

require_once __DIR__ . '/../includes/core.php';

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

    // --- background optional ---
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
        'background' => $uploadedPaths['background'],
        'datetime' => time(),
        'user_id' => $_SESSION['user_id']
    ]);

    // get last insert id
    $lastId = $pdo->lastInsertId('id');

    // isert all tags in tag table but ignore if already exists
    $tags = explode(',', $tag);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        $tag = strtolower($tag);
        
        if (!empty($tag)) {
            $stmt = $pdo->prepare('INSERT INTO tags (name) VALUES (:tag) ON CONFLICT(name) DO NOTHING');
            $stmt->execute(['tag' => $tag]);
        }
    }


    // header('Location: /add/scan?ntf=scan_added');
    header('Location: /scan/' . $lastId);
    exit;
}
