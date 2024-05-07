<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
include('../database/db.php');

// Check if doctor ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: doctors.php");
    exit();
}

$doctorId = $_GET['id'];

// Delete the doctor and its associated user
$deleteSql = "DELETE doctors, users FROM doctors
              INNER JOIN users ON doctors.user_id = users.id
              WHERE doctors.id = $doctorId";
$conn->query($deleteSql);

header("Location: doctors.php");
exit();