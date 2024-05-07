<?php
require_once 'global.php';
?>
<aside class="main-sidebar bg-info elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Bahaal</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?php echo $base; ?>/dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo $base; ?>/users/users.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo $base; ?>/doctors/doctors.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Properties
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo $base; ?>/appointments/appointments.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Appointments
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>