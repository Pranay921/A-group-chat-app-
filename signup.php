<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    // Connecting to the Database
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "demochatapp";

    // Create a connection
    $conn = mysqli_connect($servername, $db_username, $db_password, $database);

    if (!$conn) {
        die("Sorry, we failed to connect: " . mysqli_connect_error());
    }

    // Check if username, email or phone number already exists
    $check_user = "SELECT username, email, phone_number FROM users WHERE username = ? OR email = ? OR phone_number = ?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param("sss", $username, $email, $phone_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username, email or phone number already exists! Please try different credentials.'); history.back();</script>";
        exit();
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into users table
        $sql = "INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $phone_number, $hashed_password);
        $result = $stmt->execute();

        if ($result) {
            // Changed redirect to newform.php instead of newform.html
            echo "<script>alert('Account created successfully!'); window.location.href='newform.php';</script>";
        } else {
            echo "<script>alert('Failed to register. Please try again.'); history.back();</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
