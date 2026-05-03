<?php
require_once '../includes/auth_check.php';
$activePage = 'catalogo';
$pageTitle  = 'Modifica Libro';

require_once '../includes/db.php';

// Leggi ID dalla query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: catalogo.php'); exit; }

// Recupera il libro
$stmt = $pdo->prepare("
    SELECT b.*, a.name AS author_name, c.name AS category_name, p.name AS publisher_name
    FROM books b
    JOIN authors    a ON b.author_id   = a.id
    JOIN categories c ON b.category_id = c.id
    LEFT JOIN publishers p ON b.publisher_id = p.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) { header('Location: catalogo.php'); exit; }

// dropdown per il form
$authors    = $pdo->query("SELECT id, name FROM authors ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$publishers = $pdo->query("SELECT id, name FROM publishers ORDER BY name")->fetchAll();

$bookMode = 'edit';
include '../includes/layoutBook.php';
?>
