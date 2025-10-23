<?php
include 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if ($action === 'login') {
    $stmt = $pdo->prepare("SELECT password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(16));
        echo json_encode(['success' => true, 'token' => $token, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        error_log('Login Error: Email or password mismatch for ' . $email, 0);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>