<?php

// Include database connection
include('database/db.php');
include('global.php');

// Function to generate and store fake appointments data
function storeFakeAppointments($numAppointments)
{
    global $conn;
    // Generate random appointment details
    $doctors = array(3, 8, 3);
    $statuses = array('pending', 'confirmed', 'cancelled');
    $tokens = array('649b0fb3c2436', '649b0ffc19c56', '649b0ffc19c56');

    for ($i = 0; $i < $numAppointments; $i++) {
        $doctor_id = $doctors[array_rand($doctors)];
        $date = date('Y-m-d H:i:s');
        $status = $statuses[array_rand($statuses)];
        $token = $tokens[array_rand($tokens)];

        // Verify the token and retrieve the user_id
        $user_id = verifyToken($token);

        // Store the appointment in the database
        $sql = "INSERT INTO appointments (doctor_id, date, status, user_id) 
                VALUES ($doctor_id, '$date', '$status', $user_id)";

        // Execute the SQL query
        $result = $conn->query($sql);

        if ($result) {
            echo "Appointment stored successfully. ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error storing appointment: " . $conn->error . "<br>";
        }
    }
}

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
        return null;
    }
}


// Usage: Generate and store 5 fake appointments
storeFakeAppointments(5);