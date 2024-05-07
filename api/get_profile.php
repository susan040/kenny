<?php
require_once('../database/db.php');

// Check if the token is provided
if (!isset($_GET['token'])) {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

$token = $_GET['token'];

// Retrieve the user profile based on the provided token
$stmt = $conn->prepare("SELECT users.id, users.name, users.email FROM users INNER JOIN api_tokens ON users.id = api_tokens.user_id WHERE api_tokens.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if a row is returned
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // User profile found
    $userProfile = [
        "id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email']
    ];

    // Return the user profile as JSON response
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json");
    echo json_encode($userProfile);
} else {
    // Invalid token or user not found
    header("HTTP/1.1 401 Unauthorized");
    exit();
}
