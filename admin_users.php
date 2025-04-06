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

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);

// Handle user deletion
$success_message = '';
$error_message = '';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Delete user from database
    $delete_query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: admin_users.php?success=user_deleted");
        exit();
    } else {
        $error_message = "Error deleting user: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}

// Refresh user list after operations
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
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
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        
        .users-table th, .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .users-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .users-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .delete-btn {
            background: #ff3860;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: background 0.3s;
        }
        
        .delete-btn:hover {
            background: #e5304e;
        }
        
        .delete-btn i {
            margin-right: 5px;
        }
        
        .user-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 5px;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card i {
            font-size: 30px;
            margin-bottom: 10px;
            color: #5995fd;
        }
        
        .stat-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .stat-card p {
            color: #666;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .user-stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin_teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Messages</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>User Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="admin_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> 
                    <?php 
                        if ($_GET['success'] == 'user_deleted') echo "User has been successfully deleted.";
                        else echo "Operation completed successfully.";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="user-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <?php 
                    $total_users_query = "SELECT COUNT(*) as count FROM users";
                    $total_users_result = mysqli_query($conn, $total_users_query);
                    $total_users = mysqli_fetch_assoc($total_users_result)['count'];
                    ?>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user-plus"></i>
                    <?php 
                    $new_users_query = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()";
                    $new_users_result = mysqli_query($conn, $new_users_query);
                    $new_users = mysqli_fetch_assoc($new_users_result)['count'];
                    ?>
                    <h3><?php echo $new_users; ?></h3>
                    <p>New Users Today</p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <?php 
                    // The error is in this query - last_login column doesn't exist
                    // Let's modify it to use created_at instead or remove the condition
                    $active_users_query = "SELECT COUNT(*) as count FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY";
                    $active_users_result = mysqli_query($conn, $active_users_query);
                    $active_users = 0;
                    if ($active_users_result) {
                        $active_users = mysqli_fetch_assoc($active_users_result)['count'];
                    }
                    ?>
                    <h3><?php echo $active_users; ?></h3>
                    <p>Active Users (7 days)</p>
                </div>
            </div>
            
            <h2>All Users</h2>
            <?php if (mysqli_num_rows($users_result) > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : 'N/A'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="admin_users.php?action=delete&id=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>