<?php
include 'auth_check.php';
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_id = $_POST['role_id'] ?? null;
    $role_name = $_POST['role_name'] ?? null;
    $permissions = $_POST['permissions'] ?? [];

    if (!$role_id || !$role_name) {
        die("Role ID and Role Name are required.");
    }

    $stmt = $conn->prepare("UPDATE roles SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $role_name, $role_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $stmt->close();

    if (!empty($permissions)) {
        $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($permissions as $permission_id) {
            $stmt->bind_param("ii", $role_id, $permission_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    if (isset($_SESSION['user_id'])) {
        $session_role_id = $_SESSION['role_id'];

        $permissions_stmt = $conn->prepare("SELECT p.name AS permission_name, f.name AS feature_name
                                            FROM role_permissions rp
                                            INNER JOIN permissions p ON rp.permission_id = p.id
                                            INNER JOIN features f ON p.feature_id = f.id
                                            WHERE rp.role_id = ?");
        $permissions_stmt->bind_param("i", $session_role_id);
        $permissions_stmt->execute();
        $permissions_result = $permissions_stmt->get_result();

        $permissions = [];
        while ($permission = $permissions_result->fetch_assoc()) {
            $permissions[$permission['feature_name']][] = $permission['permission_name'];
        }

        $_SESSION['user_permissions'] = $permissions;

        header("Location: edit_role.php?id=" . $role_id);
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

$conn->close();
