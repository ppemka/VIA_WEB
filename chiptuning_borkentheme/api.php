<?php
require_once 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'read':
            $stmt = $conn->query("SELECT * FROM chip ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'create':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO chip (brand, model, year, engine, ecu, original_hp, stage1_hp, stage2_hp, original_torque, stage1_torque, stage2_torque) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['brand'],
                $data['model'],
                $data['year'],
                $data['engine'],
                $data['ecu'],
                $data['original_hp'],
                $data['stage1_hp'],
                $data['stage2_hp'],
                $data['original_torque'],
                $data['stage1_torque'],
                $data['stage2_torque']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'update':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'];
            $stmt = $conn->prepare("UPDATE chip SET brand = ?, model = ?, year = ?, engine = ?, ecu = ?, original_hp = ?, stage1_hp = ?, stage2_hp = ?, original_torque = ?, stage1_torque = ?, stage2_torque = ? WHERE id = ?");
            $stmt->execute([
                $data['brand'],
                $data['model'],
                $data['year'],
                $data['engine'],
                $data['ecu'],
                $data['original_hp'],
                $data['stage1_hp'],
                $data['stage2_hp'],
                $data['original_torque'],
                $data['stage1_torque'],
                $data['stage2_torque'],
                $id
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'delete':
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM chip WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
