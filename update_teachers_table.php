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

// Check if columns already exist
$check_query = "SHOW COLUMNS FROM teachers LIKE 'username'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) == 0) {
    // First add the columns without constraints
    $sql1 = "ALTER TABLE teachers 
            ADD COLUMN username VARCHAR(50),
            ADD COLUMN password VARCHAR(255)";

    if (mysqli_query($conn, $sql1)) {
        // Update existing records with unique usernames based on their IDs
        $update_query = "UPDATE teachers SET username = CONCAT('teacher', id), password = '$2y$10$defaultpasswordhashforexistingteachers'";
        
        if (mysqli_query($conn, $update_query)) {
            // Now add the UNIQUE constraint
            $sql2 = "ALTER TABLE teachers MODIFY username VARCHAR(50) NOT NULL UNIQUE";
            
            if (mysqli_query($conn, $sql2)) {
                echo "Teachers table updated successfully with username and password fields";
            } else {
                echo "Error adding constraints: " . mysqli_error($conn);
            }
        } else {
            echo "Error updating existing records: " . mysqli_error($conn);
        }
    } else {
        echo "Error adding columns: " . mysqli_error($conn);
    }
} else {
    echo "Username and password columns already exist in the teachers table";
}

mysqli_close($conn);
?>