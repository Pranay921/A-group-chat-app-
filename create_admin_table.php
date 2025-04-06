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

// Create admin_users table
$admin_table_sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $admin_table_sql)) {
    echo "Admin users table created successfully<br>";
} else {
    echo "Error creating admin users table: " . mysqli_error($conn) . "<br>";
}

// Check if default admin user already exists
$check_admin = "SELECT id FROM admin_users WHERE username = 'admin'";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    // Create default admin user with username 'admin' and password 'admin123'
    $default_username = "admin";
    $default_password = password_hash("admin123", PASSWORD_DEFAULT);
    $default_email = "admin@example.com";
    $default_name = "Administrator";
    
    $insert_admin = "INSERT INTO admin_users (username, password, email, full_name) 
                     VALUES (?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insert_admin);
    mysqli_stmt_bind_param($stmt, "ssss", $default_username, $default_password, $default_email, $default_name);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Default admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating default admin user: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Default admin user already exists<br>";
}

mysqli_close($conn);
?>