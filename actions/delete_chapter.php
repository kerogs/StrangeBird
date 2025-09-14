<?php
require_once __DIR__ . '/../includes/core.php';

if (!$auth->isLoggedIn()) {
        sendHttpError(401);
        exit;
}

$chapterId = $_POST['chapter_id'] ?? null;
$scanId = $_POST['id_scan'] ?? null;

if (!$chapterId || !$scanId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

try {
    // ? Security
    // get scan info
    $scanInfo = $pdo->prepare('SELECT * FROM "scan" WHERE id = :id');
    $scanInfo->bindValue(':id', $scanId);
    $scanInfo->execute();
    $scanInfo = $scanInfo->fetch(PDO::FETCH_ASSOC);

    // check if user_id is the author of the scan
    if ($scanInfo['addedby_user_id'] !== $_SESSION['user_id']) {
        sendHttpError(401);
        exit;
    }

    $imagesDir = __DIR__ . "/../uploads/chapters/$scanId/$chapterId/";
    if (file_exists($imagesDir)) {
        array_map('unlink', glob("$imagesDir/*.*"));
        rmdir($imagesDir);
    }

    $stmt = $pdo->prepare('DELETE FROM chapters WHERE id = ? AND id_scan = ?');
    $stmt->execute([$chapterId, $scanId]);

    // remove images
    $dir_chapter_path = __DIR__ . "/../uploads/chapters/$scanId/$chapterId/";

    if (file_exists($dir_chapter_path)) {
        array_map('unlink', glob("$dir_chapter_path/*.*"));
        rmdir($dir_chapter_path);
    }

    header('Location: /scan/' . $scanId . '?ntf=chapter_deleted');
    exit;
} catch (Exception $e) {
    http_response_code(500);
    header('Location: /add/chapters/' . $scanId . '&ntf=delete_failed');
    exit;
}
