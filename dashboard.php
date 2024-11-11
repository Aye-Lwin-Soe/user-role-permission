<?php
// Include database connection file
include 'auth_check.php';
include 'db_connection.php';

// Query to get the count of users
$sql = "SELECT COUNT(*) AS user_count FROM admin_users";
$result = $conn->query($sql);
$userCount = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userCount = $row['user_count'];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-4 mt-5">
                <div class="card text-center">
                    <div class="card-header bg-primary text-white">
                        User Count
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $userCount; ?></h5>
                        <p class="card-text">Total Users</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>