<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once('../database/db.php');

// Check if the appointment ID is provided
if (isset($_GET['id'])) {
    $appointmentId = $_GET['id'];

    // Delete the appointment from the database
    $sql = "DELETE FROM appointments WHERE id = '$appointmentId'";
    $conn->query($sql);

    // Redirect back to the appointments page
    header("Location: appointments.php");
    exit();
} else {
    // If the appointment ID is not provided, redirect back to the appointments page
    header("Location: appointments.php");
    exit();
}
