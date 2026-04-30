<?php

// Imposta gli header per le risposte JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Risposta di successo
function successResponse($data = null, $message = 'OK', $status = 200) {
    http_response_code($status);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ]);
    exit;
}

// Risposta di errore
function errorResponse($message = 'Errore generico', $status = 400) {
    http_response_code($status);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'data'    => null
    ]);
    exit;
}

// Gestione richieste OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}