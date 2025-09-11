<?php
class Auth
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['uuid']);

        // Check if the session values correspond to a valid user in the database
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :id AND uuid = :uuid');
        $stmt->execute(['id' => $_SESSION['user_id'], 'uuid' => $_SESSION['uuid']]);
        return $stmt->fetchColumn() > 0; 
    }

    public function getUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id AND uuid = :uuid');
        $stmt->execute(['id' => $_SESSION['user_id'], 'uuid' => $_SESSION['uuid']]);
        $user = $stmt->fetch();

        if (!$user) {
            // L’ID n’existe plus en BDD → invalide la session
            $this->logout();
            return null;
        }

        // Valeurs par défaut si vides
        if (empty($user['profile_picture'])) {
            $user['profile_picture'] = '/assets/img/default/profile_picture.png';
        }
        if (empty($user['banner'])) {
            $user['banner'] = '/assets/img/default/profile_banner.png';
        }

        return $user;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }
}
