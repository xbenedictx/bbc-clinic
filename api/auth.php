<?php
include 'config.php';
$input = json_decode(file_get_contents('php://input'), true);
if ($input['action'] === 'register') {
    // Use Supabase Auth or manual
    $hash = password_hash($input['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$input['email'], $hash]);
    echo json_encode(['success' => true]);
} elseif ($input['action'] === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();
    if ($user && password_verify($input['password'], $user['password'])) {
        // Generate JWT or session token (use firebase/jwt for JWT)
        $token = bin2hex(random_bytes(32));  // Simple token
        echo json_encode(['success' => true, 'token' => $token, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
}
// Add admin-only checks using token validation in other endpoints
?>