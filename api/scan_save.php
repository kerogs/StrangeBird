<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/core.php';

// Vérification authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise']);
    exit;
}

// Traitement de la requête
$input = json_decode(file_get_contents('php://input'), true);
$scanId = filter_var($input['scan_id'] ?? 0, FILTER_VALIDATE_INT);
$userId = $_SESSION['user_id'];

if (!$scanId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID scan invalide']);
    exit;
}

// Vérifier si le scan existe
$stmt = $pdo->prepare('SELECT id FROM scan WHERE id = ?');
$stmt->execute([$scanId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Scan non trouvé']);
    exit;
}

// Vérifier si déjà sauvegardé
$stmt = $pdo->prepare('SELECT id FROM scan_save WHERE id_scan = ? AND id_user = ?');
$stmt->execute([$scanId, $userId]);
$existingSave = $stmt->fetch();

if ($existingSave) {
    // Retirer la sauvegarde
    $stmt = $pdo->prepare('DELETE FROM scan_save WHERE id_scan = ? AND id_user = ?');
    $stmt->execute([$scanId, $userId]);
    
    echo json_encode([
        'success' => true,
        'saved' => false,
        'message' => 'Sauvegarde retirée'
    ]);
} else {
    // Ajouter la sauvegarde
    $stmt = $pdo->prepare('INSERT INTO scan_save (id_scan, id_user) VALUES (?, ?)');
    $stmt->execute([$scanId, $userId]);
    
    echo json_encode([
        'success' => true,
        'saved' => true,
        'message' => 'Scan sauvegardé'
    ]);
}

?>