<?php
// userapi.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // for testing
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate input
$requiredFields = ['googleId', 'email', 'name', 'role', 'date_of_birth', 'gender', 'password', 'mobile'];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing or empty field: $field"]);
        exit;
    }
}

// Optional: Validate date
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_of_birth'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date_of_birth format. Use YYYY-MM-DD.']);
    exit;
}

// DB credentials
$host = 'localhost';
$dbname = 'vinod';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert query
    $stmt = $pdo->prepare("
        INSERT INTO users (
            googleId, email, name, role, create_at, version, date_of_birth, gender, password, mobile
        ) VALUES (
            :googleId, :email, :name, :role, NOW(6), 0, :date_of_birth, :gender, :password, :mobile
        )
    ");

    $stmt->execute([
        ':googleId' => $data['googleId'],
        ':email' => $data['email'],
        ':name' => $data['name'],
        ':role' => $data['role'],
        ':date_of_birth' => $data['date_of_birth'],
        ':gender' => $data['gender'],
        ':password' => $data['password'],  // should be hashed in real apps!
        ':mobile' => $data['mobile']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'User inserted successfully',
        'user_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
