<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // GET — lista autori o uno solo
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
            $stmt->execute([$id]);
            $author = $stmt->fetch();

            if (!$author) errorResponse('Autore non trovato', 404);
            successResponse($author);
        } else {
            $stmt = $pdo->query("SELECT * FROM authors ORDER BY name ASC");
            successResponse($stmt->fetchAll());
        }
        break;

    // POST — crea autore
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("INSERT INTO authors (name) VALUES (?)");
        $stmt->execute([trim($body['name'])]);

        $new = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
        $new->execute([$pdo->lastInsertId()]);
        successResponse($new->fetch(), 'Autore creato', 201);
        break;

    // PUT — modifica autore
    case 'PUT':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("UPDATE authors SET name = ? WHERE id = ?");
        $stmt->execute([trim($body['name']), $id]);

        if ($stmt->rowCount() === 0) errorResponse('Autore non trovato', 404);
        successResponse(null, 'Autore aggiornato');
        break;

    // DELETE — elimina autore
    case 'DELETE':
        if (!$id) errorResponse('ID obbligatorio', 400);

        try {
            $stmt = $pdo->prepare("DELETE FROM authors WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) errorResponse('Autore non trovato', 404);
            successResponse(null, 'Autore eliminato');
        } catch (PDOException $e) {
            errorResponse('Impossibile eliminare: l\'autore ha libri associati', 409);
        }
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}