<?php
session_start();

// Check if logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?error=invalid_id");
    exit();
}

$user_id = $_GET['id'];

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "demochatapp";

// Create a connection
$conn = mysqli_connect($servername, $db_username, $db_password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete user
$delete_query = "DELETE FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $delete_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    header("Location: admin_dashboard.php?success=user_deleted");
} else {
    header("Location: admin_dashboard.php?error=delete_failed");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>