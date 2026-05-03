<?php
session_start();
// chiude la sessione e tutti i dati associati
session_unset();
session_destroy();

// Reindirizza l'utente alla pagina di login
header('Location: login.php');
exit;
?>
