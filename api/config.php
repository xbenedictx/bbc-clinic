<?php
// api/config.php
$supabaseUrl = getenv('SUPABASE_URL');
$supabaseKey = getenv('SUPABASE_ANON_KEY');
try {
    $pdo = new PDO("pgsql:host=" . parse_url($supabaseUrl)['host'] . ";dbname=postgres", "postgres", $supabaseKey);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>