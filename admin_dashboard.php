<?php
session_start();

// Check if logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
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

// Get total users count
$users_query = "SELECT COUNT(*) as total_users FROM users";
$users_result = mysqli_query($conn, $users_query);
$users_data = mysqli_fetch_assoc($users_result);
$total_users = $users_data['total_users'];

// Get recent users
$recent_users_query = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #e5304e;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .stat-card i {
            font-size: 30px;
            color: #5995fd;
            margin-bottom: 10px;
        }
        
        .stat-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .stat-card p {
            color: #777;
        }
        
        .recent-users {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .recent-users h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th, .users-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .users-table th {
            background: #f9f9f9;
            color: #333;
        }
        
        .users-table tr:hover {
            background: #f5f5f5;
        }
        
        .delete-btn {
            background: #ff3860;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: background 0.3s;
        }
        
        .delete-btn i {
            margin-right: 5px;
        }
        
        .delete-btn:hover {
            background: #e5304e;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Messages</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="admin_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'user_deleted'): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> User has been successfully deleted.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php 
                        if ($_GET['error'] == 'invalid_id') echo "Invalid user ID.";
                        else if ($_GET['error'] == 'delete_failed') echo "Failed to delete user.";
                        else echo "An error occurred.";
                    ?>
                </div>
            <?php endif; ?>
            <style>

                .alert {
                    padding: 15px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                    display: flex;
                    align-items: center;
                }
                
                .alert i {
                    margin-right: 10px;
                    font-size: 18px;
                }
                
                .alert.success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                
                .alert.error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
            </style>
            
            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <h3>0</h3>
                    <p>Total Messages</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user-plus"></i>
                    <h3>0</h3>
                    <p>New Users Today</p>
                </div>
            </div>
            
            <div class="recent-users">
                <h2>Recent Users</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($recent_users_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>