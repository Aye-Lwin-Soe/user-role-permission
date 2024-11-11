<?php
include 'auth_check.php';
include 'db_connection.php';
if (isset($_SESSION['user_permissions']['User']) && in_array('Update', $_SESSION['user_permissions']['User'])) {
} else {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT u.id, u.name, u.username, u.email, u.phone, u.password, u.address, u.gender, u.role_id, u.is_active, r.name AS role_name 
            FROM admin_users u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.id = '$user_id'";

    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "User not found!";
        exit;
    }
} else {
    echo "No user ID specified!";
    exit;
}


$sql_roles = "SELECT id, name FROM roles";
$roles_result = $conn->query($sql_roles);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $role_id = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!empty($_POST['password'])) {
        $update_sql = "UPDATE admin_users SET name = '$name', username = '$username', email = '$email', phone = '$phone', 
                       password = '$password', address = '$address', gender = '$gender', role_id = '$role_id', 
                       is_active = '$is_active' WHERE id = '$user_id'";
    } else {
        $update_sql = "UPDATE admin_users SET name = '$name', username = '$username', email = '$email', phone = '$phone', 
                       address = '$address', gender = '$gender', role_id = '$role_id', is_active = '$is_active' 
                       WHERE id = '$user_id'";
    }

    if ($conn->query($update_sql) === TRUE) {
        if ($_SESSION['user_id'] == $user_id && $_SESSION['role_id'] != $role_id) {
            $permissions_stmt = $conn->prepare("SELECT p.name AS permission_name, f.name AS feature_name
                                            FROM role_permissions rp
                                            INNER JOIN permissions p ON rp.permission_id = p.id
                                            INNER JOIN features f ON p.feature_id = f.id
                                            WHERE rp.role_id = ?");
            $permissions_stmt->bind_param("i", $role_id);
            $permissions_stmt->execute();
            $permissions_result = $permissions_stmt->get_result();

            $permissions = [];
            while ($permission = $permissions_result->fetch_assoc()) {
                $permissions[$permission['feature_name']][] = $permission['permission_name'];
            }
            $_SESSION['user_permissions'] = $permissions;
        }
        header("Location: edit_user.php?id=" . $user_id);
        exit();
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10 mt-2">
                <h2>Update User</h2>
                <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="number" min="0" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep the same">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <div>
                            <label class="me-3">
                                <input type="radio" name="gender" value="0" <?php echo ($user['gender'] == '0') ? 'checked' : ''; ?> required> Male
                            </label>
                            <label class="me-3">
                                <input type="radio" name="gender" value="1" <?php echo ($user['gender'] == '1') ? 'checked' : ''; ?> required> Female
                            </label>
                            <label class="me-3">
                                <input type="radio" name="gender" value="2" <?php echo ($user['gender'] == '2') ? 'checked' : ''; ?> required> Other
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <?php while ($role = $roles_result->fetch_assoc()) { ?>
                                <option value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == $user['role_id']) ? 'selected' : ''; ?>>
                                    <?php echo $role['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo ($user['is_active'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active" id="statusLabel"><?php echo ($user['is_active'] == 1) ? 'Active' : 'Inactive'; ?></label>
                    </div>
                    <button type="submit" class="btn btn-primary mb-3">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('is_active').addEventListener('change', function() {
            const statusLabel = document.getElementById('statusLabel');
            if (this.checked) {
                statusLabel.textContent = 'Active';
            } else {
                statusLabel.textContent = 'Inactive';
            }
        });
    </script>
</body>

</html>