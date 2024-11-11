<?php
include 'auth_check.php';
include 'db_connection.php';
if (isset($_SESSION['user_permissions']['User']) && in_array('Delete', $_SESSION['user_permissions']['User'])) {
} else {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql_check = "SELECT id FROM admin_users WHERE id = '$user_id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {

        $sql_delete = "DELETE FROM admin_users WHERE id = '$user_id'";

        if ($conn->query($sql_delete) === TRUE) {
            header('Location: users.php');
            exit;
        } else {
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        echo "User not found!";
    }
} else {
    echo "No user ID specified!";
}

$conn->close();
