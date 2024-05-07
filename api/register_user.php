<?php
require_once('../database/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $type = isset($_POST['type']) ? $_POST['type'] : 'customer'; // Set default value if type is not provided

    // Check if the email already exists in the database
    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
    $emailResult = $conn->query($checkEmailSql);

    if ($emailResult->num_rows > 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Email already exists'
        );
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement to insert the user into the database
        $insertSql = "INSERT INTO users (name, phone, email, password, type) VALUES ('$name','$phone','$email', '$hashedPassword', '$type')";

        if ($conn->query($insertSql) === TRUE) {
            $response = array(
                'status' => 'success',
                'message' => 'User registered successfully'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error registering user: ' . $conn->error
            );
        }
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
