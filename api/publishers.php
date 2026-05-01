<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // GET — lista case editrici o una sola
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM publishers WHERE id = ?");
            $stmt->execute([$id]);
            $publisher = $stmt->fetch();

            if (!$publisher) errorResponse('Casa editrice non trovata', 404);
            successResponse($publisher);
        } else {
            $stmt = $pdo->query("SELECT * FROM publishers ORDER BY name ASC");
            successResponse($stmt->fetchAll());
        }
        break;

    // POST — crea casa editrice
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("INSERT INTO publishers (name, website) VALUES (?, ?)");
        $stmt->execute([
            trim($body['name']),
            $body['website'] ?? null
        ]);

        $new = $pdo->prepare("SELECT * FROM publishers WHERE id = ?");
        $new->execute([$pdo->lastInsertId()]);
        successResponse($new->fetch(), 'Casa editrice creata', 201);
        break;

    // PUT — modifica casa editrice
    case 'PUT':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("UPDATE publishers SET name = ?, website = ? WHERE id = ?");
        $stmt->execute([
            trim($body['name']),
            $body['website'] ?? null,
            $id
        ]);

        if ($stmt->rowCount() === 0) errorResponse('Casa editrice non trovata', 404);
        successResponse(null, 'Casa editrice aggiornata');
        break;

    // DELETE — elimina casa editrice
    case 'DELETE':
        if (!$id) errorResponse('ID obbligatorio', 400);

        try {
            $stmt = $pdo->prepare("DELETE FROM publishers WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) errorResponse('Casa editrice non trovata', 404);
            successResponse(null, 'Casa editrice eliminata');
        } catch (PDOException $e) {
            errorResponse('Impossibile eliminare: la casa editrice ha libri associati', 409);
        }
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}