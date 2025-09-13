<?php
require_once __DIR__ . '/../includes/core.php';

// Vérification authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise']);
    ob_end_flush();
    exit;
}

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    ob_end_flush();
    exit;
}

// Lire et parser le JSON
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    ob_end_flush();
    exit;
}

$scanId = filter_var($input['scan_id'] ?? 0, FILTER_VALIDATE_INT);
$opinion = in_array($input['type'] ?? '', ['like', 'dislike']) ? $input['type'] : null;
$userId = $_SESSION['user_id'];

if (!$scanId || !$opinion) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres invalides']);
    ob_end_flush();
    exit;
}

try {
    // Vérifier si le scan existe
    $stmt = $pdo->prepare('SELECT id FROM scan WHERE id = ?');
    $stmt->execute([$scanId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Scan non trouvé']);
        ob_end_flush();
        exit;
    }

    // Vérifier l'état actuel (en utilisant "opinion" au lieu de "type")
    $stmt = $pdo->prepare('SELECT opinion FROM scan_like WHERE id_scan = ? AND id_user = ?');
    $stmt->execute([$scanId, $userId]);
    $existingLike = $stmt->fetch();

    if ($existingLike) {
        if ($existingLike['opinion'] === $opinion) {
            // Retirer le like/dislike
            $stmt = $pdo->prepare('DELETE FROM scan_like WHERE id_scan = ? AND id_user = ?');
            $stmt->execute([$scanId, $userId]);

            echo json_encode([
                'success' => true,
                'action' => 'removed',
                'type' => $opinion,
                'message' => 'Réaction retirée'
            ]);
        } else {
            // Changer l'opinion (like -> dislike ou vice versa)
            $stmt = $pdo->prepare('UPDATE scan_like SET opinion = ? WHERE id_scan = ? AND id_user = ?');
            $stmt->execute([$opinion, $scanId, $userId]);

            echo json_encode([
                'success' => true,
                'action' => 'changed',
                'type' => $opinion,
                'message' => 'Réaction changée'
            ]);
        }
    } else {
        // Ajouter une nouvelle réaction (en utilisant "opinion")
        $stmt = $pdo->prepare('INSERT INTO scan_like (id_scan, id_user, opinion) VALUES (?, ?, ?)');
        $stmt->execute([$scanId, $userId, $opinion]);

        echo json_encode([
            'success' => true,
            'action' => 'added',
            'type' => $opinion,
            'message' => 'Réaction ajoutée'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
exit;
?>