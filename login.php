<?php
include 'db_connection.php';

session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $user_password = password_hash($user['password'], PASSWORD_DEFAULT);

                if (password_verify($password, $user_password)) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];

                    $permissions_stmt = $conn->prepare("SELECT p.name AS permission_name, f.name AS feature_name
                                                        FROM role_permissions rp
                                                        INNER JOIN permissions p ON rp.permission_id = p.id
                                                        INNER JOIN features f ON p.feature_id = f.id
                                                        WHERE rp.role_id = ?");
                    $permissions_stmt->bind_param("i", $user['role_id']);
                    $permissions_stmt->execute();
                    $permissions_result = $permissions_stmt->get_result();

                    $permissions = [];
                    while ($permission = $permissions_result->fetch_assoc()) {
                        $permissions[$permission['feature_name']][] = $permission['permission_name'];
                    }
                    $_SESSION['user_permissions'] = $permissions;

                    header("Location: dashboard.php");
                    $stmt->close();
                    exit();
                } else {
                    $message = "Invalid email or password.";
                }
            } else {
                die("No matching user found. Email used: " . json_encode($email));
            }
        } else {
            die("Query execution failed: " . $stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5" style="max-width: 400px;">
        <h2>Login</h2>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>