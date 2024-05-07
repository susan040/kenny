<?php

// Include database connection
include('../database/db.php');
include('../global.php');


// Verify the token and retrieve the user_id
function verifyToken($token)
{
    global $conn;

    // Sanitize the token to prevent SQL injection
    $token = $conn->real_escape_string($token);

    // Query the api_tokens table to check if the token exists
    $sql = "SELECT user_id FROM api_tokens WHERE token = '$token'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Token is valid, retrieve the user_id
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        return $user_id;
    } else {
        // Token is invalid or not found
        // Close the database connection
        $conn->close();

        return null;
    }
}


// Get the appointment details from the request
$doctor_id = $_POST['doctor_id'];
$date = $_POST['date'];
$status = $_POST['status'];
$token = $_POST['token'];

// Verify the token and retrieve the user_id
$user_id = verifyToken($token);

// Store the appointment in the database
$sql = "INSERT INTO appointments (doctor_id, date, status, user_id) 
        VALUES ($doctor_id, '$date', '$status', $user_id)";

// Execute the SQL query
if ($conn->query($sql) === TRUE) {
    $response = array('success' => true, 'message' => 'Appointment stored successfully');
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Error registering user: ' . $conn->error
    );
}

// Return the response as JSON
echo json_encode($response);
