<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "demochatapp";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];  // Changed from email to username
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $db_username, $db_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (password_verify($password, $db_password)) {  
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['first_letter'] = strtoupper($db_username[0]);

            echo "<script>
                    alert('Login successful!');
                    window.location.href='index.php';
                  </script>";
            exit();
        } else {
            // Redirect to newform.php with signin parameter
            echo "<script>alert('Incorrect password!'); window.location.href='newform.php?action=signin';</script>";
            exit();
        }
    } else {
        // Redirect to newform.php with signin parameter
        echo "<script>alert('No user found!'); window.location.href='newform.php?action=signin';</script>";
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
