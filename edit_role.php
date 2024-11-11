<?php
include 'auth_check.php';
include 'db_connection.php';
if (isset($_SESSION['user_permissions']['Role']) && in_array('Update', $_SESSION['user_permissions']['Role'])) {
} else {
    header("Location: dashboard.php");
    exit();
}

$role_id = $_GET['id'] ?? null;
if (!$role_id) {
    die("Role ID is required.");
}

$stmt = $conn->prepare("SELECT id, name FROM roles WHERE id = ?");
$stmt->bind_param("i", $role_id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();
$stmt->close();

if (!$role) {
    die("Role not found.");
}

$sql_features = "SELECT f.id as feature_id, f.name as feature_name, p.id as permission_id, p.name as permission_name
                 FROM features f
                 LEFT JOIN permissions p ON f.id = p.feature_id
                 ORDER BY p.id ASC";
$result = $conn->query($sql_features);

$features = [];
while ($row = $result->fetch_assoc()) {
    $features[$row['feature_id']]['name'] = $row['feature_name'];
    $features[$row['feature_id']]['permissions'][] = [
        'id' => $row['permission_id'],
        'name' => $row['permission_name']
    ];
}

$existing_permissions = [];
$stmt = $conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$stmt->bind_param("i", $role_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $existing_permissions[] = $row['permission_id'];
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10 mt-5">
                <h2 class="mb-4">Update Role</h2>
                <form action="update_role.php" method="POST">
                    <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($role['id']); ?>">

                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="role_name" name="role_name"
                            value="<?php echo htmlspecialchars($role['name']); ?>" required>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Feature Name</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($features as $feature_id => $feature): ?>
                                <tr>

                                    <td><?php echo htmlspecialchars($feature['name']); ?></td>
                                    <td>
                                        <?php if (!empty($feature['permissions'])): ?>
                                            <?php foreach ($feature['permissions'] as $permission): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permission_<?php echo $permission['id']; ?>"
                                                        name="permissions[]"
                                                        value="<?php echo $permission['id']; ?>"
                                                        <?php echo in_array($permission['id'], $existing_permissions) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label"
                                                        for="permission_<?php echo $permission['id']; ?>">
                                                        <?php echo htmlspecialchars($permission['name']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No permissions available for this feature.</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>