<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'Unknown'
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>