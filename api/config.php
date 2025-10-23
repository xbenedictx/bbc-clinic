<?php
// Load environment variables
$env = parse_ini_file(__DIR__ . '/.env');
$supabaseUrl = $env['SUPABASE_URL'];
$supabaseKey = $env['SUPABASE_KEY']; // Use service key for server-side access

try {
    // Extract host from URL for PostgreSQL connection
    $host = parse_url($supabaseUrl, PHP_URL_HOST);
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=postgres", "postgres", $supabaseKey);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    exit;
}
?>