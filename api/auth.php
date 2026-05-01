<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // POST — login
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['username'])) errorResponse('Username obbligatorio', 400);
        if (empty($body['password'])) errorResponse('Password obbligatoria', 400);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([trim($body['username'])]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($body['password'], $user['password'])) {
            errorResponse('Credenziali non valide', 401);
        }

        // Avvia sessione
        session_start();
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        successResponse([
            'username' => $user['username']
        ], 'Login effettuato');
        break;

    // DELETE — logout
    case 'DELETE':
        session_start();
        session_destroy();
        successResponse(null, 'Logout effettuato');
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}