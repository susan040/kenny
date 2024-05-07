<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
require_once('../database/db.php');

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

// Retrieve the user ID from the query parameter
$id = $_GET['id'];

// Delete the user record from the database
$sql = "DELETE FROM users WHERE id = '$id'";
$conn->query($sql);

// Redirect to the dashboard page
header("Location: users.php");
exit();