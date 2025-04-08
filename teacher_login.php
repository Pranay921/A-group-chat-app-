<?php
session_start();

// Check if already logged in as teacher
if (isset($_SESSION['teacher_id'])) {
    header("Location: teacher_dashboard.php");
    exit();
}

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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, name FROM teachers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $db_username, $db_password, $name);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (password_verify($password, $db_password)) {
            $_SESSION['teacher_id'] = $id;
            $_SESSION['teacher_username'] = $db_username;
            $_SESSION['teacher_name'] = $name;
            
            header("Location: teacher_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Teacher account not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 40px;
        }
        
        .login-container h2 {
            text-align: center;
            color: #5995fd;
            margin-bottom: 30px;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-group i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #999;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .input-group input:focus {
            border-color: #5995fd;
            outline: none;
        }
        
        .btn {
            width: 100%;
            background: #5995fd;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #4d84e2;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #5995fd;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .login-options {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .login-option {
            margin: 0 10px;
            padding: 8px 15px;
            background: #f0f0f0;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .login-option:hover {
            background: #e0e0e0;
        }
        
        .login-option.active {
            background: #5995fd;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Teacher Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="login-options">
            <a href="admin_login.php" class="login-option">Admin Login</a>
            <a href="teacher_login.php" class="login-option active">Teacher Login</a>
            <a href="newform.php" class="login-option">Student Login</a>
        </div>
        
        <a href="newform.php" class="back-link">Back to User Login</a>
    </div>
</body>
</html>