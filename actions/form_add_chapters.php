<?php
require_once __DIR__ . '/../includes/core.php';
header('Content-Type: application/json');

if (!$auth->isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$createOnly = $input['createOnly'] ?? false;

if ($createOnly) {
    $stmt = $pdo->prepare("INSERT INTO chapters (number, name, id_scan, description, view, like, dislike, datetime, everyImagesLink)
        VALUES (:number, :name, :id_scan, '', 0, 0, 0, :datetime, '')");
    $stmt->execute([
        'number' => $input['number'],
        'name' => $input['name'],
        'id_scan' => $input['id_scan'],
        'datetime' => time()
    ]);
    echo json_encode(['chapterId' => $pdo->lastInsertId()]);
    exit;
}

$chapterId = $_POST['chapter_id'];
$idScan = $_POST['id_scan'];
if (!isset($_FILES['images'])) {
    echo json_encode(['success' => false]);
    exit;
}

$uploadDir = __DIR__ . "/../uploads/chapters/$idScan/$chapterId/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

$files = $_FILES['images'];
$storedNames = [];

for ($i = 0; $i < count($files['name']); $i++) {
    if ($files['error'][$i] !== 0) continue;
    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

    $newName = basename($files['name'][$i]);
    move_uploaded_file($files['tmp_name'][$i], $uploadDir . $newName);
    $storedNames[] = $newName;
}

if (!empty($storedNames)) {
    $stmt = $pdo->prepare("UPDATE chapters SET everyImagesLink = 
        CASE WHEN everyImagesLink = '' THEN :files ELSE everyImagesLink || '#~>' || :files END
        WHERE id = :id");
    $stmt->execute([
        'files' => implode('#~>', $storedNames),
        'id' => $chapterId
    ]);
}

echo json_encode(['success' => true, 'files' => $storedNames]);
exit;
