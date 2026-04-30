<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // GET — lista tutte le categorie o una sola
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch();

            if (!$category) errorResponse('Categoria non trovata', 404);
            successResponse($category);
        } else {
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
            successResponse($stmt->fetchAll());
        }
        break;

    // POST — crea una nuova categoria
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([
            trim($body['name']),
            $body['description'] ?? null
        ]);

        $new = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $new->execute([$pdo->lastInsertId()]);
        successResponse($new->fetch(), 'Categoria creata', 201);
        break;

    // PUT — modifica una categoria esistente
    case 'PUT':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['name'])) errorResponse('Il campo name è obbligatorio', 400);

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([
            trim($body['name']),
            $body['description'] ?? null,
            $id
        ]);

        if ($stmt->rowCount() === 0) errorResponse('Categoria non trovata', 404);
        successResponse(null, 'Categoria aggiornata');
        break;

    // DELETE — elimina una categoria
    case 'DELETE':
        if (!$id) errorResponse('ID obbligatorio', 400);

        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) errorResponse('Categoria non trovata', 404);
            successResponse(null, 'Categoria eliminata');
        } catch (PDOException $e) {
            errorResponse('Impossibile eliminare: la categoria ha libri associati', 409);
        }
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}