<?php
require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Simple validation
if (!$name || !$email || !$password) {
    echo json_encode(['error' => 'Name, email, and password are required']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
if ($stmt->execute([$name, $email, $hashedPassword])) {
    echo json_encode(['success' => true, 'message' => 'User registered']);
} else {
    echo json_encode(['error' => 'Registration failed or email already exists']);
}
?>
