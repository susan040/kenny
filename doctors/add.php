<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
include('../database/db.php');

// Initialize variables
$name = '';
$email = '';
$category = '';
$patients = '';
$experience = '';
$bioData = '';
$status = '';
$time = '';

// Check if the email already exists in the users table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $emailExistsSql = "SELECT id FROM users WHERE email = '$email'";
    $emailExistsResult = $conn->query($emailExistsSql);

    if ($emailExistsResult->num_rows > 0) {
        // Email already exists, set flash message and redirect back with form data
        $_SESSION['flash_message'] = "Email already exists!";
        $_SESSION['form_data'] = $_POST;
        header("Location: add.php");
        exit();
    }

    // Retrieve form data
    $name = $_POST['name'];
    $category = $_POST['category'];
    $patients = $_POST['patients'];
    $experience = $_POST['experience'];
    $bioData = $_POST['bio_data'];
    $status = $_POST['status'];
    $time = $_POST['time'];

    // Hash the password
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create a new user
    $userSql = "INSERT INTO users (name, email, password, type) VALUES ('$name','$email', '$hashedPassword', 'doctor')";
    $conn->query($userSql);

    // Get the user_id of the newly created user
    $user_id = $conn->insert_id;

    // Handle the image upload
    $img = $_FILES['img']['name'];
    $targetDir = "../uploads/";
    $targetFilePath = $targetDir . basename($img);
    move_uploaded_file($_FILES['img']['tmp_name'], $targetFilePath);

    // Insert the doctor into the "doctors" table
    $doctorSql = "INSERT INTO doctors (user_id, category, patients, experience, bio_data, status, time, display_image) VALUES ('$user_id', '$category', '$patients', '$experience', '$bioData', '$status', '$time', '$img')";
    $conn->query($doctorSql);

    // Set success flash message
    $_SESSION['flash_message'] = "Doctor added successfully!";

    // Redirect to the doctors list page
    header("Location: doctors.php");
    exit();
}

// Fill form data if redirected back due to existing email
if (isset($_SESSION['form_data'])) {
    $name = $_SESSION['form_data']['name'];
    $email = $_SESSION['form_data']['email'];
    $category = $_SESSION['form_data']['category'];
    $patients = $_SESSION['form_data']['patients'];
    $experience = $_SESSION['form_data']['experience'];
    $bioData = $_SESSION['form_data']['bio_data'];
    $status = $_SESSION['form_data']['status'];
    $time = $_SESSION['form_data']['time'];
    unset($_SESSION['form_data']);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Doctor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Sidebar -->
        <?php include('../sidebar.php'); ?>

        <!-- Header -->
        <?php include('../header.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Main content -->
            <section class="content py-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php

                            if (isset($_SESSION['flash_message'])) {
                                echo '<div class="alert alert-info">' . $_SESSION['flash_message'] . '</div>';
                                unset($_SESSION['flash_message']);
                            }

                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Add Doctor</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="add.php" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="email">Name</label>
                                            <input type="text" class="form-control" id="email" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <input type="text" class="form-control" id="category" name="category"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="patients">Patients</label>
                                            <input type="number" class="form-control" id="patients" name="patients"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="experience">Experience</label>
                                            <textarea class="form-control" id="experience" name="experience"
                                                required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="bio_data">Bio Data</label>
                                            <textarea class="form-control" id="bio_data" name="bio_data"
                                                required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <input type="text" class="form-control" id="status" name="status" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="time">Time</label>
                                            <input type="time" class="form-control" id="time" name="time" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="img">Image</label>
                                            <input type="file" class="form-control" id="img" name="img" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Doctor</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
</body>

</html>