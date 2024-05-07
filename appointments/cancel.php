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

    // Update the appointment status to "cancelled"
    $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = '$appointmentId'";
    $conn->query($sql);

    // Redirect back to the appointments page
    header("Location: appointments.php");
    exit();
} else {
    // If the appointment ID is not provided, redirect back to the appointments page
    header("Location: appointments.php");
    exit();
}
