<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action']  ?? null;
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : null;

switch ($method) {

    // GET — movimenti o alert scorte
    case 'GET':

        if ($action === 'alerts') {
            $stmt = $pdo->query("
                SELECT
                    b.id,
                    b.title,
                    b.stock_qty,
                    b.stock_alert_qty,
                    a.name AS author_name
                FROM books b
                JOIN authors a ON b.author_id = a.id
                WHERE b.stock_qty <= b.stock_alert_qty
                ORDER BY b.stock_qty ASC
            ");
            successResponse($stmt->fetchAll(), 'Libri sotto soglia scorta');
        }

        if ($book_id) {
            $stmt = $pdo->prepare("
                SELECT sm.*, b.title AS book_title
                FROM stock_movements sm
                JOIN books b ON sm.book_id = b.id
                WHERE sm.book_id = ?
                ORDER BY sm.id DESC
            ");
            $stmt->execute([$book_id]);
            successResponse($stmt->fetchAll());
        }

        $stmt = $pdo->query("
            SELECT sm.*, b.title AS book_title
            FROM stock_movements sm
            JOIN books b ON sm.book_id = b.id
            ORDER BY sm.id DESC
            LIMIT 50
        ");
        successResponse($stmt->fetchAll());
        break;

    // POST — registra un movimento
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['book_id']))  errorResponse('Il campo book_id è obbligatorio', 400);
        if (empty($body['type']))     errorResponse('Il campo type è obbligatorio', 400);
        if (empty($body['quantity'])) errorResponse('Il campo quantity è obbligatorio', 400);

        if (!in_array($body['type'], ['carico', 'scarico'])) {
            errorResponse('Type deve essere: carico o scarico', 400);
        }

        $qty = (int)$body['quantity'];
        if ($qty <= 0) errorResponse('La quantity deve essere maggiore di 0', 400);

        $check = $pdo->prepare("SELECT id, stock_qty FROM books WHERE id = ?");
        $check->execute([(int)$body['book_id']]);
        $book = $check->fetch();
        if (!$book) errorResponse('Libro non trovato', 404);

        $delta    = $body['type'] === 'carico' ? +$qty : -$qty;
        $newStock = $book['stock_qty'] + $delta;

        if ($newStock < 0) {
            errorResponse('Stock insufficiente. Disponibili: ' . $book['stock_qty'], 400);
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO stock_movements (book_id, type, quantity, note)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                (int)$body['book_id'],
                $body['type'],
                $qty,
                $body['note'] ?? null
            ]);

            $update = $pdo->prepare("UPDATE books SET stock_qty = ? WHERE id = ?");
            $update->execute([$newStock, (int)$body['book_id']]);

            $pdo->commit();
            successResponse(['new_stock' => $newStock], 'Movimento registrato', 201);

        } catch (PDOException $e) {
            $pdo->rollBack();
            errorResponse('Errore durante la registrazione del movimento', 500);
        }
        break;

    default:
        errorResponse('Metodo non supportato', 405);
}