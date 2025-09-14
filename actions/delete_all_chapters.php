<?php
require_once __DIR__ . '/../includes/core.php';

if (!$auth->isLoggedIn()) {
        sendHttpError(401);
        exit;
}

$scanId = $_POST['id_scan'] ?? null;

if (!$scanId) {
        sendHttpError(400);
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

    // get chapters
    $stmt = $pdo->prepare('SELECT id FROM chapters WHERE id_scan = ?');
    $stmt->execute([$scanId]);
    $chapters = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // delete all images
    foreach ($chapters as $chapterId) {
        $imagesDir = __DIR__ . "/../uploads/chapters/$scanId/$chapterId/";
        if (file_exists($imagesDir)) {
            array_map('unlink', glob("$imagesDir/*.*"));
            rmdir($imagesDir);
        }
    }

    // remove all chapters from the database
    $stmt = $pdo->prepare('DELETE FROM chapters WHERE id_scan = ?');
    $stmt->execute([$scanId]);

    header('Location: /scan/' . $scanId . '?ntf=all_chapters_deleted');
    exit;
} catch (Exception $e) {
    http_response_code(500);
    header('Location: /add/chapters/' . $scanId . '?ntf=delete_failed');
    exit;
}
