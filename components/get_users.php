<?php
// get_users.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// DB credentials
$host = 'localhost';
$dbname = 'vinod';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get all users
    $stmt = $pdo->query("SELECT id, googleId, email, name, role, create_at, version, date_of_birth, gender, mobile FROM users ORDER BY id DESC");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
}
