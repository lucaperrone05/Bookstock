<?php
session_start();

// Se l'utente non è loggato, reindirizza alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
