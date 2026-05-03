<?php
require_once '../includes/db.php';
require_once '../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Riceve un ID dal dropdown oppure un nome scritto a mano.
// Restituisce sempre un ID valido, inserendo il nome nel DB se non esiste ancora.

function resolveHybrid($pdo, $id, $newName, $table) {
    if (!empty($id)) return (int)$id;
    if (!empty($newName)) {
        $newName = trim($newName);
        $stmt = $pdo->prepare("SELECT id FROM $table WHERE name = ?");
        $stmt->execute([$newName]);
        $existing = $stmt->fetchColumn();
        if ($existing) return (int)$existing;
        
        $insert = $pdo->prepare("INSERT INTO $table (name) VALUES (?)");
        $insert->execute([$newName]);
        return (int)$pdo->lastInsertId();
    }
    return null;
}

switch ($method) {

    // GET — lista libri (con filtri) o uno solo
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT
                    b.*,
                    a.name AS author_name,
                    c.name AS category_name,
                    p.name AS publisher_name
                FROM books b
                JOIN authors    a ON b.author_id    = a.id
                JOIN categories c ON b.category_id  = c.id
                LEFT JOIN publishers p ON b.publisher_id = p.id
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            $book = $stmt->fetch();

            if (!$book) errorResponse('Libro non trovato', 404);
            successResponse($book);

        } else {
            $query = "
                SELECT
                    b.*,
                    a.name AS author_name,
                    c.name AS category_name,
                    p.name AS publisher_name
                FROM books b
                JOIN authors    a ON b.author_id   = a.id
                JOIN categories c ON b.category_id = c.id
                LEFT JOIN publishers p ON b.publisher_id = p.id
                WHERE 1=1
            ";
            $params = [];

            if (!empty($_GET['author_id'])) {
                $query   .= " AND b.author_id = ?";
                $params[] = (int)$_GET['author_id'];
            }

            if (!empty($_GET['category_id'])) {
                $query   .= " AND b.category_id = ?";
                $params[] = (int)$_GET['category_id'];
            }

            if (!empty($_GET['publisher_id'])) {
                $query   .= " AND b.publisher_id = ?";
                $params[] = (int)$_GET['publisher_id'];
            }

            if (!empty($_GET['publish_year'])) {
                $query   .= " AND b.publish_year = ?";
                $params[] = (int)$_GET['publish_year'];
            }

            if (isset($_GET['available']) && $_GET['available'] === 'true') {
                $query .= " AND b.stock_qty > 0";
            }

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

        if (empty($body['title'])) errorResponse('Il campo title è obbligatorio', 400);

        // Risoluzione ibrida
        $final_author_id   = resolveHybrid($pdo, $body['author_id'] ?? null, $body['new_author_name'] ?? null, 'authors');
        $final_category_id = resolveHybrid($pdo, $body['category_id'] ?? null, $body['new_category_name'] ?? null, 'categories');
        $final_publisher_id= resolveHybrid($pdo, $body['publisher_id'] ?? null, $body['new_publisher_name'] ?? null, 'publishers');

        if (!$final_author_id)   errorResponse('Specifica un autore o inseriscine uno nuovo', 400);
        if (!$final_category_id) errorResponse('Specifica una categoria o inseriscine una nuova', 400);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO books
                    (title, isbn, author_id, category_id, publisher_id, publish_year, description, price, stock_qty, stock_alert_qty)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                trim($body['title']),
                $body['isbn']            ?? null,
                $final_author_id,
                $final_category_id,
                $final_publisher_id,
                !empty($body['publish_year']) ? (int)$body['publish_year'] : null,
                $body['description']     ?? null,
                $body['price']           ?? 0.00,
                $body['stock_qty']       ?? 0,
                $body['stock_alert_qty'] ?? 5
            ]);

            $new = $pdo->prepare("SELECT * FROM books WHERE id = ?");
            $new->execute([$pdo->lastInsertId()]);
            successResponse($new->fetch(), 'Libro creato', 201);
        } catch (PDOException $e) {
            errorResponse('Errore nei dati inseriti: controlla che siano validi (es. anno pubblicazione, ISBN)', 400);
        }
        break;

    // PUT — modifica un libro esistente
    case 'PUT':
        if (!$id) errorResponse('ID obbligatorio', 400);

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['title'])) errorResponse('Il campo title è obbligatorio', 400);

        // Risoluzione ibrida
        $final_author_id   = resolveHybrid($pdo, $body['author_id'] ?? null, $body['new_author_name'] ?? null, 'authors');
        $final_category_id = resolveHybrid($pdo, $body['category_id'] ?? null, $body['new_category_name'] ?? null, 'categories');
        $final_publisher_id= resolveHybrid($pdo, $body['publisher_id'] ?? null, $body['new_publisher_name'] ?? null, 'publishers');

        if (!$final_author_id)   errorResponse('Specifica un autore o inseriscine uno nuovo', 400);
        if (!$final_category_id) errorResponse('Specifica una categoria o inseriscine una nuova', 400);

        // Controllo esistenza libro
        $check = $pdo->prepare("SELECT id FROM books WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            errorResponse('Libro non trovato', 404);
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE books SET
                    title           = ?,
                    isbn            = ?,
                    author_id       = ?,
                    category_id     = ?,
                    publisher_id    = ?,
                    publish_year    = ?,
                    description     = ?,
                    price           = ?,
                    stock_alert_qty = ?
                WHERE id = ?
            ");
            $stmt->execute([
                trim($body['title']),
                $body['isbn']            ?? null,
                $final_author_id,
                $final_category_id,
                $final_publisher_id,
                !empty($body['publish_year']) ? (int)$body['publish_year'] : null,
                $body['description']     ?? null,
                $body['price']           ?? 0.00,
                $body['stock_alert_qty'] ?? 5,
                $id
            ]);

            successResponse(null, 'Libro aggiornato');
        } catch (PDOException $e) {
            // Se l'errore riguarda i dati (es. anno non valido per il tipo YEAR)
            errorResponse('Errore nei dati inseriti: controlla che siano validi (es. anno pubblicazione, ISBN)', 400);
        }
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