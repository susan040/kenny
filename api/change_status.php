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

// Function to change the status of an appointment
function changeAppointmentStatus($appointment_id, $status)
{
    global $conn;

    // Sanitize the appointment ID and status to prevent SQL injection
    $appointment_id = $conn->real_escape_string($appointment_id);
    $status = $conn->real_escape_string($status);

    // Update the status of the appointment in the appointments table
    $sql = "UPDATE appointments SET status = '$status' WHERE id = '$appointment_id'";
    $result = $conn->query($sql);

    echo $status;

    if ($result) {
        // Status changed successfully
        return true;
    } else {
        // Error occurred while changing the status
        return false;
    }
}

// Get the token from the request headers or query parameters
$token = $_POST['token'] ?? '';

// Verify the token and retrieve the user ID
$user_id = verifyToken($token);

if ($user_id) {
    // Token is valid, check the user type (assuming you have a 'type' column in the users table)
    $sql = "SELECT type FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_type = $row['type'];

        // Check if the user is a doctor
        if ($user_type === 'doctor') {
            // Get the appointment ID and status from the request
            $appointment_id = $_POST['appointment_id'] ?? '';
            $status = $_POST['status'] ?? '';

            // Change the status of the appointment
            $success = changeAppointmentStatus($appointment_id, $status);

            if ($success) {
                // Status changed successfully
                $response = json_encode(['message' => 'Appointment status changed']);
                header('Content-Type: application/json');
                echo $response;
            } else {
                // Error occurred while changing the status
                $response = json_encode(['message' => 'Sorry Failed to change appointment status']);
                header('Content-Type: application/json');
                echo $response;
            }
        } else {
            // User is not a doctor, unauthorized access
            $response = json_encode(['message' => 'Unauthorized access']);
            header('Content-Type: application/json');
            echo $response;
        }
    } else {
        // User not found
        $response = json_encode(['message' => 'User not found']);
        header('Content-Type: application/json');
        echo $response;
    }
} else {
    // Invalid token or token not provided
    $response = json_encode(['message' => 'User Invalid token']);
    header('Content-Type: application/json');
    echo $response;
}

// Close the database connection
$conn->close();
