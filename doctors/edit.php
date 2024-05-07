<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
include('../database/db.php');
include('../global.php');

// Check if doctor ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: doctors.php");
    exit();
}

$doctorId = $_GET['id'];

// Get doctor details from the database
$sql = "SELECT * FROM doctors WHERE id = $doctorId";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: doctors.php");
    exit();
}

$doctor = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $patients = $_POST['patients'];
    $experience = $_POST['experience'];
    $bioData = $_POST['bio_data'];
    $status = $_POST['status'];
    $time = $_POST['time'];

    // Handle image upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $imgTmpName = $_FILES['img']['tmp_name'];
        $imgName = $_FILES['img']['name'];
        $imgPath = "../uploads/" . $imgName;

        // Move uploaded image to the uploads directory
        move_uploaded_file($imgTmpName, $imgPath);

        // Update the doctor details in the database including the image path
        $updateSql = "UPDATE doctors SET category = '$category', patients = '$patients', experience = '$experience', bio_data = '$bioData', status = '$status', time = '$time', display_image = '$imgPath' WHERE id = $doctorId";
        $conn->query($updateSql);
    } else {
        // Update the doctor details in the database without changing the image path
        $updateSql = "UPDATE doctors SET category = '$category', patients = '$patients', experience = '$experience', bio_data = '$bioData', status = '$status', time = '$time' WHERE id = $doctorId";
        $conn->query($updateSql);
    }
    header("Location: doctors.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Doctor</title>
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
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Doctor</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="edit.php?id=<?php echo $doctorId; ?>"
                                        enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <input type="text" class="form-control" id="category" name="category"
                                                value="<?php echo $doctor['category']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="patients">Patients</label>
                                            <input type="number" class="form-control" id="patients" name="patients"
                                                value="<?php echo $doctor['patients']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="experience">Experience</label>
                                            <textarea class="form-control" id="experience" name="experience"
                                                required><?php echo $doctor['experience']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="bio_data">Bio Data</label>
                                            <textarea class="form-control" id="bio_data" name="bio_data"
                                                required><?php echo $doctor['bio_data']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <input type="text" class="form-control" id="status" name="status"
                                                value="<?php echo $doctor['status']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="time">Time</label>
                                            <input type="text" class="form-control" id="time" name="time"
                                                value="<?php echo $doctor['time']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="img">Image</label><br>
                                            <img src="<?php echo $img_base . $doctor['display_image'] ?>" alt="Doctor"
                                                width="100" height="100"><br>
                                            <input type="file" class="form-control" id="img" name="img">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Doctor</button>
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