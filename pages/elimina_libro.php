<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // ignoriamo l'errore per ora
    }
}

header('Location: catalogo.php?deleted=1');
exit;
