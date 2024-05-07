<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="hold-transition login-page py-5">
    <div class="container">

        <div class="row">
            <div class="col-md-4" style="margin: 0 auto;">
                <div class="login-box">

                    <div class="card">
                        <div class="card-body login-card-body">
                            <p class="login-box-msg">Sign in to start your session</p>
                            <form method="POST" action="./functions/login.php">
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>

                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" name="password" placeholder="Password"
                                        required>

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>