<?php
include 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'create') {
    $stmt = $pdo->prepare("INSERT INTO health_records (pet_id, visit_date, diagnosis, prescription, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$input['pet_id'], $input['visit_date'], $input['diagnosis'], $input['prescription'], $input['notes']]);
    echo json_encode(['success' => true]);
} elseif ($action === 'read') {
    $stmt = $pdo->prepare("SELECT h.*, p.name AS pet_name FROM health_records h JOIN pets p ON h.pet_id = p.id");
    $stmt->execute();
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} // Add update/delete similarly
?>