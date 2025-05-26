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
$make = trim($data['make'] ?? '');
$model = trim($data['model'] ?? '');
$engine = trim($data['engine'] ?? '');
$year = trim($data['year'] ?? '');
$stage = trim($data['stage'] ?? '');

if (!$make || !$model || !$engine || !$year || !$stage) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO cars (make, model, engine, year, stage) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$make, $model, $engine, $year, $stage]);
    echo json_encode(['success' => 'Car selection saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>