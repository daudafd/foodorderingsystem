<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db = getenv('DB_NAME');

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => "Connection failed: " . $e->getMessage()]);
    die(); // Stop execution
}
?>