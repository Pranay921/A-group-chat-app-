<?php
session_start();

// Check if logged in as teacher
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
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

// Get teacher information
$teacher_id = $_SESSION['teacher_id'];
$teacher_query = "SELECT * FROM teachers WHERE id = $teacher_id";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher = mysqli_fetch_assoc($teacher_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: #f5f5f5;
        }
        
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #5995fd;
            color: white;
            padding: 20px;
        }
        
        .sidebar h2 {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar ul li {
            margin-bottom: 15px;
        }
        
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #333;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 15px;
        }
        
        .logout-btn {
            background: #ff3860;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #e5304e;
        }
        
        .welcome-card {
            background: white;
            border-radius: 5px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-card h2 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .welcome-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .profile-section {
            background: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .profile-section h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-item label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }
        
        .info-item span {
            color: #666;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Teacher Panel</h2>
            <ul>
                <li><a href="teacher_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-book"></i> My Courses</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Messages</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Teacher Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($teacher['name']); ?></span>
                    <a href="teacher_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="welcome-card">
                <h2>Welcome to Your Dashboard</h2>
                <p>This is your teacher dashboard where you can manage your courses, interact with students, and update your profile information.</p>
            </div>
            
            <div class="profile-section">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <div class="info-item">
                        <label>Name</label>
                        <span><?php echo htmlspecialchars($teacher['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <span><?php echo htmlspecialchars($teacher['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <span><?php echo !empty($teacher['phone']) ? htmlspecialchars($teacher['phone']) : 'Not provided'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Expertise</label>
                        <span><?php echo htmlspecialchars($teacher['expertise']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Experience</label>
                        <span><?php echo htmlspecialchars($teacher['experience']); ?> years</span>
                    </div>
                    <div class="info-item">
                        <label>Qualification</label>
                        <span><?php echo htmlspecialchars($teacher['qualification']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>