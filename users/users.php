<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once('../database/db.php');

// Retrieve the filter values
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$type = $_GET['type'] ?? '';

// Prepare the WHERE clause for the SQL query based on the filter values
$whereClause = '';
if (!empty($name)) {
    $whereClause .= "AND name LIKE '%$name%'";
}
if (!empty($email)) {
    $whereClause .= "AND email LIKE '%$email%'";
}
if (!empty($type)) {
    $whereClause .= "AND type = '$type'";
}

// Pagination configuration
$recordsPerPage = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Retrieve the total number of records
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM users WHERE 1 $whereClause";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Calculate the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Retrieve the filtered list of users from the database with pagination
$sql = "SELECT * FROM users WHERE 1 $whereClause LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
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
                                    <a href="add.php" class="btn btn-success">Add New</a>
                                </div>
                                <div class="card-body">
                                    <form class="mb-3" method="GET">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        value="<?php echo $name; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="<?php echo $email; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="type">Type</label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value="">All</option>
                                                        <option value="doctor"
                                                            <?php if ($type == 'doctor') echo 'selected'; ?>>Doctor
                                                        </option>
                                                        <option value="customer"
                                                            <?php if ($type == 'customer') echo 'selected'; ?>>Customer
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td><?php echo $row['type']; ?></td>
                                                <td>
                                                    <a href="edit.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                                                        class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    <nav aria-label="Pagination">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($page > 1) : ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $page - 1; ?>&name=<?php echo $name; ?>&email=<?php echo $email; ?>&type=<?php echo $type; ?>"
                                                    aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link"
                                                    href="?page=<?php echo $i; ?>&name=<?php echo $name; ?>&email=<?php echo $email; ?>&type=<?php echo $type; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                            <?php endfor; ?>

                                            <?php if ($page < $totalPages) : ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $page + 1; ?>&name=<?php echo $name; ?>&email=<?php echo $email; ?>&type=<?php echo $type; ?>"
                                                    aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
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