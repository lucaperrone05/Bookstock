<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // GET — lista libri (con filtri) o uno solo
    case 'GET':
        if ($id) {
            // Singolo libro con autore e categoria (JOIN)
            $stmt = $pdo->prepare("
                SELECT
                    b.*,
                    a.name  AS author_name,
                    c.name  AS category_name
                FROM books b
                JOIN authors    a ON b.author_id    = a.id
                JOIN categories c ON b.category_id  = c.id
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            $book = $stmt->fetch();

            if (!$book) errorResponse('Libro non trovato', 404);
            successResponse($book);

        } else {
            // Lista con filtri opzionali
            $query  = "
                SELECT
                    b.*,
                    a.name AS author_name,
                    c.name AS category_name
                FROM books b
                JOIN authors    a ON b.author_id   = a.id
                JOIN categories c ON b.category_id = c.id
                WHERE 1=1
            ";
            $params = [];

            // Filtro per autore
            if (!empty($_GET['author_id'])) {
                $query   .= " AND b.author_id = ?";
                $params[] = (int)$_GET['author_id'];
            }

            // Filtro per categoria
            if (!empty($_GET['category_id'])) {
                $query   .= " AND b.category_id = ?";
                $params[] = (int)$_GET['category_id'];
            }

            // Filtro disponibilità (stock > 0)
            if (isset($_GET['available']) && $_GET['available'] === 'true') {
                $query .= " AND b.stock_qty > 0";
            }

            // Ricerca per titolo
            if (!empty($_GET['search'])) {
                $query   .= " AND b.title LIKE ?";
                $params[] = '%' . $_GET['search'] . '%';
            }

            $query .= " ORDER BY b.title ASC";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            successResponse($stmt->fetchAll());
        }
        break;

    // POST — crea un nuovo libro
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        // Validazione campi obbligatori
        if (empty($body['title']))       errorResponse('Il campo title è obbligatorio', 400);
        if (empty($body['author_id']))   errorResponse('Il campo author_id è obbligatorio', 400);
        if (empty($body['category_id'])) errorResponse('Il campo category_id è obbligatorio', 400);

        $stmt = $pdo->prepare("
            INSERT INTO books
                (title, isbn, author_id, category_id, description, cover_url, price, stock_qty, stock_alert_qty)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($body['title']),
            $body['isbn']            ?? null,
            (int)$body['author_id'],
            (int)$body['category_id'],
            $body['description']     ?? null,
            $body['cover_url']       ?? null,
            $body['price']           ?? 0.00,
            $body['stock_qty']       ?? 0,
            $body['stock_alert_qty'] ?? 5
        ]);

        $new = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $new->execute([$pdo->lastInsertId()]);
        successResponse($new->fetch(), 'Libro creato', 201);
        break;

    // PUT — modifica un libro esistente
    case 'PUT':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['title']))       errorResponse('Il campo title è obbligatorio', 400);
        if (empty($body['author_id']))   errorResponse('Il campo author_id è obbligatorio', 400);
        if (empty($body['category_id'])) errorResponse('Il campo category_id è obbligatorio', 400);

        $stmt = $pdo->prepare("
            UPDATE books SET
                title           = ?,
                isbn            = ?,
                author_id       = ?,
                category_id     = ?,
                description     = ?,
                cover_url       = ?,
                price           = ?,
                stock_alert_qty = ?
            WHERE id = ?
        ");
        $stmt->execute([
            trim($body['title']),
            $body['isbn']            ?? null,
            (int)$body['author_id'],
            (int)$body['category_id'],
            $body['description']     ?? null,
            $body['cover_url']       ?? null,
            $body['price']           ?? 0.00,
            $body['stock_alert_qty'] ?? 5,
            $id
        ]);

        if ($stmt->rowCount() === 0) errorResponse('Libro non trovato', 404);
        successResponse(null, 'Libro aggiornato');
        break;

    // DELETE — elimina un libro
    case 'DELETE':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) errorResponse('Libro non trovato', 404);
        successResponse(null, 'Libro eliminato');
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}