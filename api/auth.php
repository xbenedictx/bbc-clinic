<?php
include 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow frontend requests

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if ($action === 'register') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (id, email, password, role) VALUES (gen_random_uuid(), ?, ?, 'user')");
    try {
        $stmt->execute([$email, $hashedPassword]);
        echo json_encode(['success' => true, 'message' => 'User registered']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
} elseif ($action === 'login') {
    $stmt = $pdo->prepare("SELECT password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(16)); // Simple token (replace with JWT if needed)
        echo json_encode(['success' => true, 'token' => $token, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>