<?php
include 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'create') {
    // Simple availability check: no overlapping appointments
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE date = ? AND time = ? AND status != 'cancelled'");
    $stmt->execute([$input['date'], $input['time']]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Time slot taken']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO appointments (pet_id, date, time, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$input['pet_id'], $input['date'], $input['time']]);
    echo json_encode(['success' => true]);
} elseif ($action === 'read') {
    $stmt = $pdo->prepare("SELECT a.*, p.name AS pet_name FROM appointments a JOIN pets p ON a.pet_id = p.id");
    $stmt->execute();
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} // Add update/delete similarly
?>