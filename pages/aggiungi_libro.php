<?php
$activePage = 'catalogo';
$pageTitle  = 'Aggiungi Libro';

require_once '../includes/db.php';

// Carica dropdown per il form
$authors    = $pdo->query("SELECT id, name FROM authors ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$publishers = $pdo->query("SELECT id, name FROM publishers ORDER BY name")->fetchAll();

$book     = null;
$bookMode = 'add';
include '../includes/layoutBook.php';
?>
