<?php
// navbar.php
require_once 'config.php';
?>
<nav class="navbar navbar-custom navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="img/CleaningLogo-min.png" alt="Cleaning JP" style="height: 35px; object-fit: contain;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="register_user.php">Register User</a></li>
                        <li class="nav-item"><a class="nav-link" href="register_client.php">Register Client</a></li>
                        <li class="nav-item"><a class="nav-link" href="register_item.php">Register Item</a></li>
                        <li class="nav-item"><a class="nav-link" href="update_items.php">Update Items</a></li>
                        <li class="nav-item"><a class="nav-link" href="job_form_admin.php">Add a Job</a></li>
                        <li class="nav-item"><a class="nav-link" href="users_list.php">List of Users</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="user_dashboard.php">My Jobs</a></li>
                        <li class="nav-item"><a class="nav-link" href="job_form.php">New Job</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><span class="navbar-text me-2">Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Login</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>