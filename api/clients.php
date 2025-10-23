<?php
include 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$user_id = $input['user_id'] ?? ''; // From token validation (simplified)
$role = $input['role'] ?? 'user'; // From token

if ($action === 'create_client') {
    $stmt = $pdo->prepare("INSERT INTO clients (user_id, name, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $input['name'], $input['phone'], $input['address']]);
    $client_id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'client_id' => $client_id]);
} elseif ($action === 'create_pet') {
    $stmt = $pdo->prepare("INSERT INTO pets (client_id, name, species, breed, age, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$input['client_id'], $input['name'], $input['species'], $input['breed'], $input['age'], $input['notes']]);
    echo json_encode(['success' => true]);
} elseif ($action === 'read') {
    $query = ($role === 'admin') ? "SELECT * FROM clients" : "SELECT * FROM clients WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute($role === 'admin' ? [] : [$user_id]);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($clients as &$client) {
        $stmt = $pdo->prepare("SELECT * FROM pets WHERE client_id = ?");
        $stmt->execute([$client['id']]);
        $client['pets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode(['success' => true, 'data' => $clients]);
} // Add update/delete similarly
?>