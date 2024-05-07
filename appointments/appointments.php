<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once('../database/db.php');

// Pagination variables
$page = $_GET['page'] ?? 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filter variables
$appointmentId = $_GET['appointment_id'] ?? '';
$appointmentDate = $_GET['appointment_date'] ?? '';

// Prepare the SQL query with filters
$sql = "SELECT appointments.id, appointments.date, appointments.status, appointments.doctor_id, appointments.user_id, users.name AS user_name
        FROM appointments
        LEFT JOIN users ON appointments.user_id = users.id
        WHERE appointments.id LIKE '%$appointmentId%' AND appointments.date LIKE '%$appointmentDate%'
        ORDER BY appointments.id DESC
        LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

$doctorName = "";

// Count the total number of appointments for pagination
$countSql = "SELECT COUNT(*) AS total
             FROM appointments
             LEFT JOIN users ON appointments.user_id = users.id
             WHERE appointments.id LIKE '%$appointmentId%' AND appointments.date LIKE '%$appointmentDate%'";
$countResult = $conn->query($countSql);
$countRow = $countResult->fetch_assoc();
$totalAppointments = $countRow['total'];
$totalPages = ceil($totalAppointments / $perPage);

function getUserName($userId)
{
    global $conn;

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a row is returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "User not found";
    }
}
function getDoctorName($doctorId)
{
    global $conn;

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT users.name FROM doctors INNER JOIN users ON doctors.user_id = users.id WHERE doctors.id = ?");
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a row is returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Doctor not found";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Appointments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Add the Font Awesome CDN link below -->
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
                                    <h3 class="card-title">Appointments</h3>
                                </div>
                                <div class="card-body">
                                    <form class="mb-3" method="GET">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="appointment_id">Appointment Id</label>
                                                    <input type="text" class="form-control" id="appointment_id"
                                                        name="appointment_id" value="<?php echo $appointmentId; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="appointment_date">Appointment Date</label>
                                                    <input type="date" class="form-control" id="appointment_date"
                                                        name="appointment_date" value="<?php echo $appointmentDate; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Appointment Date</th>
                                                <th>Status</th>
                                                <th>Doctor ID</th>
                                                <th>Doctor Name</th>
                                                <th>User ID</th>
                                                <th>User Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td><?php echo $row['status']; ?>
                                                </td>
                                                <td><?php echo $row['doctor_id']; ?></td>
                                                <td><?php echo getDoctorName($row['doctor_id']); ?></td>
                                                <td><?php echo $row['user_id']; ?></td>
                                                <td><?php echo $row['user_name']; ?></td>
                                                <td>
                                                    <a href="cancel.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-sm btn-primary">Cancel</a>
                                                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Pagination links -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php
                                            if ($page > 1) {
                                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                                            }

                                            for ($i = 1; $i <= $totalPages; $i++) {
                                                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                            }

                                            if ($page < $totalPages) {
                                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                                            }
                                            ?>
                                        </ul>
                                    </nav>


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