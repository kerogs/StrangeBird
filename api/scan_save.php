<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/core.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$scanId = filter_var($input['scan_id'] ?? 0, FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if (!$scanId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID scan invalide']);
    exit;
}

// Check if the scan exists
$stmt = $pdo->prepare('SELECT id FROM scan WHERE id = ?');
$stmt->execute([$scanId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Scan non trouvé']);
    exit;
}

// Check if the user has already saved the scan
$stmt = $pdo->prepare('SELECT id FROM scan_save WHERE id_scan = ? AND id_user = ?');
$stmt->execute([$scanId, $userId]);
$existingSave = $stmt->fetch();

if ($existingSave) {
    // remove save
    $stmt = $pdo->prepare('DELETE FROM scan_save WHERE id_scan = ? AND id_user = ?');
    $stmt->execute([$scanId, $userId]);

    echo json_encode([
        'success' => true,
        'saved' => false,
        'message' => 'Sauvegarde retirée'
    ]);
} else {
    // add save
    $stmt = $pdo->prepare('INSERT INTO scan_save (id_scan, id_user, datetime) VALUES (?, ?, ?)');
    $stmt->execute([$scanId, $userId, time()]);

    echo json_encode([
        'success' => true,
        'saved' => true,
        'message' => 'Scan sauvegardé'
    ]);
}
