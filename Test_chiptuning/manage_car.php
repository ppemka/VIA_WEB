<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['id'])) {
    // Read single car
    try {
        $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $car = $stmt->fetch();
        if ($car) {
            echo json_encode(['success' => true, 'data' => $car]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Car not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    // Create or update car
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $make = trim($data['make'] ?? '');
    $model = trim($data['model'] ?? '');
    $engine = trim($data['engine'] ?? '');
    $year = trim($data['year'] ?? '');
    $stage = trim($data['stage'] ?? '');
    $horsepower = $data['horsepower'] ?? null;
    $torque = $data['torque'] ?? null;

    if (!$make || !$model || !$engine || !$year || !$stage) {
        http_response_code(400);
        echo json_encode(['error' => 'All required fields must be provided']);
        exit;
    }

    try {
        if ($id) {
            // Update existing car
            $stmt = $conn->prepare("UPDATE cars SET make = ?, model = ?, engine = ?, year = ?, stage = ?, horsepower = ?, torque = ? WHERE id = ?");
            $stmt->execute([$make, $model, $engine, $year, $stage, $horsepower, $torque, $id]);
            echo json_encode(['success' => 'Car updated successfully']);
        } else {
            // Create new car
            $stmt = $conn->prepare("INSERT INTO cars (make, model, engine, year, stage, horsepower, torque) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$make, $model, $engine, $year, $stage, $horsepower, $torque]);
            echo json_encode(['success' => 'Car added successfully']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    // Delete car
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Car ID is required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => 'Car deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Car not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>