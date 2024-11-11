<?php
include 'auth_check.php';
include 'db_connection.php';

if (isset($_SESSION['user_permissions']['User']) && in_array('Read', $_SESSION['user_permissions']['User'])) {
    $no_permission = false;
} else {
    $no_permission = true;
}

$sql = "SELECT u.id, u.name, u.address, u.email, u.phone, u.is_active, r.name AS role_name FROM admin_users u
        LEFT JOIN roles r ON r.id = u.role_id";
$result = $conn->query($sql);

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
            <div class="col-md-10 mt-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Users</h2>
                    <?php if (isset($_SESSION['user_permissions']['User']) && in_array('Create', $_SESSION['user_permissions']['User'])) { ?>
                        <a href="create_user.php" class="btn btn-success ms-auto">Add New User</a>
                    <?php }
                    ?>
                </div>
                <?php if ($no_permission == false) { ?>
                    <table id="usersTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $userId => $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($result['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($result['phone'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($result['role_name'] ?? ''); ?></td>
                                    <td>
                                        <span class="user-badge <?php echo ($result['is_active'] == 1) ? 'badge-active' : 'badge-inactive'; ?>"
                                            style="padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 1rem; font-weight: bold; text-transform: uppercase; color: white; background-color: <?php echo ($result['is_active'] == 1) ? '#28a745' : '#dc3545'; ?>;">
                                            <?php echo ($result['is_active'] == 1) ? 'Active' : 'Inactive'; ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?php if (isset($_SESSION['user_permissions']['User']) && in_array('Update', $_SESSION['user_permissions']['User'])) { ?>
                                            <a href="edit_user.php?id=<?php echo $result['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <?php } ?>
                                        <?php if (isset($_SESSION['user_permissions']['User']) && in_array('Delete', $_SESSION['user_permissions']['User'])) { ?>
                                            <a href="delete_user.php?id=<?php echo $result['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
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
            $('#usersTable').DataTable({
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