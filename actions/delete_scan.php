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
    $scanInfo = $pdo->prepare('SELECT * FROM "scan" WHERE id = :id');
    $scanInfo->bindValue(':id', $scanId);
    $scanInfo->execute();
    $scanInfo = $scanInfo->fetch(PDO::FETCH_ASSOC);

    if ($scanInfo['addedby_user_id'] !== $_SESSION['user_id']) {
        sendHttpError(401);
        exit;
    }

    foreach ($chapters as $chapterId) {
        $imagesDir = __DIR__ . "/../uploads/chapters/$scanId/$chapterId/";
        if (file_exists($imagesDir)) {
            array_map('unlink', glob("$imagesDir/*.*"));
            rmdir($imagesDir);
        }
    }

    $scanDir = __DIR__ . "/../uploads/chapters/$scanId/";
    if (file_exists($scanDir)) {
        rmdir($scanDir);
    }

    $coverFile = __DIR__ . "/.." . $scanInfo['cover'];
    if (file_exists($coverFile)) {
        unlink($coverFile);
    }

    if ($scanInfo['background'] !== null) {
        $backgroundFile = __DIR__ . "/.." . $scanInfo['background'];
        if (file_exists($backgroundFile)) {
            unlink($backgroundFile);
        }
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM scan_like WHERE id_scan = ?');
    $stmt->execute([$scanId]);

    $stmt = $pdo->prepare('DELETE FROM scan_save WHERE id_scan = ?');
    $stmt->execute([$scanId]);

    $stmt = $pdo->prepare('DELETE FROM chapters WHERE id_scan = ?');
    $stmt->execute([$scanId]);

    $stmt = $pdo->prepare('DELETE FROM scan WHERE id = ?');
    $stmt->execute([$scanId]);

    $pdo->commit();

    header('Location: /?ntf=scan_deleted');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    header('Location: /?ntf=server_error');
    exit;
}
