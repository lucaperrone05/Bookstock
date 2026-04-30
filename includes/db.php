<?php

// Configurazione database
define('DB_HOST', 'localhost');
define('DB_NAME', 'bookstock');
define('DB_USER', 'root');
define('DB_PASS', '2846');
define('DB_CHARSET', 'utf8mb4');

// Connessione PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lancia eccezioni sugli errori
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // risultati come array associativi
            PDO::ATTR_EMULATE_PREPARES   => false,                  // prepared statements reali per evitare SQL Injection
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Errore di connessione al database'
    ]);
    exit;
}