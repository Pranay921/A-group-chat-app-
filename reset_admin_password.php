<?php
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

// Reset admin password to 'admin123'
$username = "admin";
$new_password = "admin123";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$update_query = "UPDATE admin_users SET password = ? WHERE username = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $username);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin password reset successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error resetting admin password: " . mysqli_error($conn) . "<br>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>