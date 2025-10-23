<?php
include 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'create') {
    $transaction_id = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("INSERT INTO invoices (appointment_id, amount, status, transaction_id) VALUES (?, ?, 'unpaid', ?)");
    $stmt->execute([$input['appointment_id'], $input['amount'], $transaction_id]);
    echo json_encode(['success' => true, 'transaction_id' => $transaction_id]);
} elseif ($action === 'read') {
    $stmt = $pdo->prepare("SELECT i.*, a.date, p.name AS pet_name FROM invoices i JOIN appointments a ON i.appointment_id = a.id JOIN pets p ON a.pet_id = p.id");
    $stmt->execute();
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} elseif ($action === 'mark_paid') {
    $stmt = $pdo->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?");
    $stmt->execute([$input['invoice_id']]);
    echo json_encode(['success' => true]);
}
?>