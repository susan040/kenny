<?php

// Include database connection
include('../database/db.php');
include('../global.php');



// Get the token from the request headers or query parameters
$token = $_GET['token'] ?? '';
$status = $_GET['status'] ?? 'pending';


// Verify the token and retrieve the user ID
$user_id = verifyToken($token);


if ($user_id) {
    // Token is valid, get appointments based on user ID and type
    $appointments = getAppointments($user_id, $status);

    if ($appointments) {
        // Appointments found
        // You can encode the appointments as JSON and send the response
        $response = json_encode([
            'data' => $appointments
        ]);
        header('Content-Type: application/json');
        echo $response;
    } else {
        // No appointments found
        $response = json_encode(['message' => 'No appointments found']);
        header('Content-Type: application/json');
        echo $response;
    }
} else {
    // Invalid token or token not provided
    $response = json_encode(['message' => 'Invalid token']);
    header('Content-Type: application/json');
    echo $response;
}


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

        // Close the database connection
        // $conn->close();

        return $user_id;
    } else {
        // Token is invalid or not found
        // Close the database connection
        $conn->close();

        return null;
    }
}

// Function to get appointments based on user ID and user type
function getAppointments($user_id, $status)
{
    global $conn;

    // Sanitize the user ID to prevent SQL injection
    $user_id = $conn->real_escape_string($user_id);

    // Check the user's type (assuming you have a 'type' column in the users table)
    $sql = "SELECT type FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_type = $row['type'];

        // Retrieve appointments based on user type
        if ($user_type === 'doctor') {
            // For doctors, find the doctor row based on user ID
            $sql = "SELECT id FROM doctors WHERE user_id = '$user_id'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $doctor_id = $row['id'];
                // Get appointments for the specific doctor
                // $sql = "SELECT *
                // FROM doctors d
                // JOIN users u ON d.user_id = u.id
                // JOIN appointments a ON d.id = a.doctor_id
                // WHERE d.id = '$doctor_id' AND a.status = '$status'
                // ";
                // $result = $conn->query($sql);

                $sql = "SELECT * FROM appointments where doctor_id = '$doctor_id' and  status = '$status'";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    $appointments = [];
                    while ($row = $result->fetch_assoc()) {
                        $user_id = $row['user_id'];
                        $s = "SELECT * from users WHERE id = '$user_id'";
                        $r = $conn->query($s);
                        while ($userD = $r->fetch_assoc()) {
                            $row['customer'] = $userD;
                        }
                        $appointments[] = $row;
                    }

                    return $appointments;
                }
            }
        } else {
            // For customers or other user types, get appointments based on user ID
            $sql = "SELECT *
            FROM doctors d
            JOIN users u ON d.user_id = u.id
            JOIN appointments a ON d.id = a.doctor_id
            WHERE a.user_id = '$user_id' AND a.status = '$status'
            ";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $appointments = [];
                while ($row = $result->fetch_assoc()) {
                    $appointments[] = $row;
                }

                return $appointments;
            }
        }
    }

    // No appointments found or user not found
    return null;
}

// Close the database connection
$conn->close();
