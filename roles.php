<?php
include 'auth_check.php';
include 'db_connection.php';
if (isset($_SESSION['user_permissions']['Role']) && in_array('Read', $_SESSION['user_permissions']['Role'])) {
    $no_permission = false;
} else {
    $no_permission = true;
}
$sql = "SELECT r.id, r.name, p.name AS permission FROM roles r 
        LEFT JOIN role_permissions rp ON r.id = rp.role_id
        LEFT JOIN permissions p ON rp.permission_id = p.id
        ORDER BY rp.id ASC";
$result = $conn->query($sql);

$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[$row['id']]['name'] = $row['name'];
    $roles[$row['id']]['permissions'][] = $row['permission'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles and Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10">
                <div class="d-flex mt-5 justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Roles and Permissions</h2>
                    <?php if (isset($_SESSION['user_permissions']['Role']) && in_array('Create', $_SESSION['user_permissions']['Role'])) { ?>
                        <a href="create_role.php" class="btn btn-success ms-auto">Add New Role</a>
                    <?php } ?>
                </div>
                <?php if ($no_permission == false) { ?>
                    <table id="rolesTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $roleId => $role): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($role['name']); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($role['permissions'])) {
                                            echo implode(', ', array_map('htmlspecialchars', $role['permissions']));
                                        } else {
                                            echo 'No permissions';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_SESSION['user_permissions']['Role']) && in_array('Update', $_SESSION['user_permissions']['Role'])) { ?>
                                            <a href="edit_role.php?id=<?php echo $roleId; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <?php } ?>
                                        <?php if (isset($_SESSION['user_permissions']['Role']) && in_array('Delete', $_SESSION['user_permissions']['Role'])) { ?>
                                            <a href="delete_role.php?id=<?php echo $roleId; ?>" class="btn btn-danger btn-sm">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "language": {
                    "search": "Filter records:"
                }
            });

        });
    </script>
</body>

</html>