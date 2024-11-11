<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="position-sticky">
        <div class="p-3 mb-3 text-white" style="background-color: #343a40; border-radius: 5px;">
            <h1 class="h5 mb-0"><?php echo $_SESSION['name']; ?></h1>
        </div>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'roles.php' || $current_page == 'create_role.php' || $current_page == 'edit_role.php') ? 'active' : ''; ?>" href="roles.php">Roles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'users.php' || $current_page == 'create_user.php' || $current_page == 'edit_user.php') ? 'active' : ''; ?>" href="users.php">Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>