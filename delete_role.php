<?php
include 'auth_check.php';
include 'db_connection.php';
if (isset($_SESSION['user_permissions']['Role']) && in_array('Delete', $_SESSION['user_permissions']['Role'])) {
} else {
    header("Location: dashboard.php");
    exit();
}
$role_id = $_GET['id'] ?? null;

if (!$role_id) {
    die("Role ID is required.");
}

$stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
$stmt->bind_param("i", $role_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
$stmt->bind_param("i", $role_id);
$stmt->execute();
$stmt->close();

header("Location: roles.php");
exit();
