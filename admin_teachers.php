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

// Get all teachers
$teachers_query = "SELECT * FROM teachers ORDER BY created_at DESC";
$teachers_result = mysqli_query($conn, $teachers_query);

// Handle form submission for adding a new teacher
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    if (isset($_POST['name']) && isset($_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $expertise = $_POST['expertise'];
        $experience = (int)$_POST['experience'];
        $qualification = $_POST['qualification'];
        $bio = $_POST['bio'];
        $social_linkedin = $_POST['social_linkedin'];
        $social_twitter = $_POST['social_twitter'];
        $social_facebook = $_POST['social_facebook'];
        
        // Get the new username and password fields
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        
        // Image upload handling
        $image_path = "";
        
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/teachers/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Check if image file is an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                // Check file size (limit to 5MB)
                if ($_FILES['image']['size'] < 5000000) {
                    // Allow certain file formats
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    if ($file_type == "jpg" || $file_type == "png" || $file_type == "jpeg" || $file_type == "gif") {
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                            $image_path = $target_file;
                        } else {
                            $error_message = "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    }
                } else {
                    $error_message = "Sorry, your file is too large. Maximum size is 5MB.";
                }
            } else {
                $error_message = "File is not an image.";
            }
        }
        
        if (empty($error_message)) {
            // Insert teacher data into database with username and password
            $insert_query = "INSERT INTO teachers (name, email, phone, image, expertise, experience, qualification, bio, social_linkedin, social_twitter, social_facebook, username, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            
            mysqli_stmt_bind_param($stmt, "sssssisssssss", $name, $email, $phone, $image_path, $expertise, $experience, $qualification, $bio, $social_linkedin, $social_twitter, $social_facebook, $username, $password);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Teacher added successfully!";
                // Refresh the page to show the new teacher
                header("Location: admin_teachers.php?success=teacher_added");
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle teacher deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $teacher_id = (int)$_GET['id'];
    
    // Get teacher image path before deleting
    $image_query = "SELECT image FROM teachers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $image_query);
    mysqli_stmt_bind_param($stmt, "i", $teacher_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image_path);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    // Delete teacher from database
    $delete_query = "DELETE FROM teachers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $teacher_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Delete the image file if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
        
        header("Location: admin_teachers.php?success=teacher_deleted");
        exit();
    } else {
        $error_message = "Error deleting teacher: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}

// Refresh teacher list after operations
$teachers_result = mysqli_query($conn, $teachers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Teachers Management</title>
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
        
        .teachers-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .teacher-card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 20px);
            overflow: hidden;
            position: relative;
        }
        
        .teacher-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .teacher-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .teacher-content {
            padding: 15px;
        }
        
        .teacher-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .teacher-expertise {
            color: #5995fd;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .teacher-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .teacher-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: background 0.3s;
        }
        
        .edit-btn {
            background: #5995fd;
            color: white;
        }
        
        .edit-btn:hover {
            background: #4a85e9;
        }
        
        .delete-btn {
            background: #ff3860;
            color: white;
        }
        
        .delete-btn:hover {
            background: #e5304e;
        }
        
        .edit-btn i, .delete-btn i {
            margin-right: 5px;
        }
        
        .add-teacher-btn {
            background: #5995fd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            transition: background 0.3s;
        }
        
        .add-teacher-btn:hover {
            background: #4a85e9;
        }
        
        .add-teacher-btn i {
            margin-right: 10px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 50px auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #5995fd;
            outline: none;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .submit-btn {
            background: #5995fd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #4a85e9;
        }
        
        .social-links {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .social-link {
            color: #5995fd;
            font-size: 16px;
            transition: color 0.3s;
        }
        
        .social-link:hover {
            color: #4a85e9;
        }
        
        @media (max-width: 992px) {
            .teacher-card {
                width: calc(50% - 20px);
            }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .teacher-card {
                width: 100%;
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
                <h1>Teachers Management</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="admin_logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> 
                    <?php 
                        if ($_GET['success'] == 'teacher_added') echo "Teacher has been successfully added.";
                        else if ($_GET['success'] == 'teacher_deleted') echo "Teacher has been successfully deleted.";
                        else echo "Operation completed successfully.";
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <button class="add-teacher-btn" id="openAddTeacherModal">
                <i class="fas fa-plus"></i> Add New Teacher
            </button>
            
            <div class="teachers-container">
                <?php if (mysqli_num_rows($teachers_result) > 0): ?>
                    <?php while ($teacher = mysqli_fetch_assoc($teachers_result)): ?>
                        <div class="teacher-card">
                            <div class="teacher-image">
                                <?php if (!empty($teacher['image']) && file_exists($teacher['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($teacher['image']); ?>" alt="<?php echo htmlspecialchars($teacher['name']); ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <div class="teacher-content">
                                <h3 class="teacher-name"><?php echo htmlspecialchars($teacher['name']); ?></h3>
                                <p class="teacher-expertise"><?php echo htmlspecialchars($teacher['expertise']); ?></p>
                                <div class="teacher-info">
                                    <span><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($teacher['qualification']); ?></span>
                                    <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($teacher['experience']); ?> years experience</span>
                                    <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($teacher['email']); ?></span>
                                    <?php if (!empty($teacher['phone'])): ?>
                                        <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($teacher['phone']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="social-links">
                                    <?php if (!empty($teacher['social_linkedin'])): ?>
                                        <a href="<?php echo htmlspecialchars($teacher['social_linkedin']); ?>" class="social-link" target="_blank"><i class="fab fa-linkedin"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($teacher['social_twitter'])): ?>
                                        <a href="<?php echo htmlspecialchars($teacher['social_twitter']); ?>" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($teacher['social_facebook'])): ?>
                                        <a href="<?php echo htmlspecialchars($teacher['social_facebook']); ?>" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
                                    <?php endif; ?>
                                </div>
                                <div class="teacher-actions">
                                    <a href="admin_teachers.php?action=delete&id=<?php echo $teacher['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this teacher?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No teachers found. Add your first teacher using the button above.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Teacher Modal -->
    <div id="addTeacherModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Teacher</h2>
            <form action="admin_teachers.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <!-- New username field -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <!-- New password field -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="expertise">Expertise/Specialization</label>
                        <input type="text" id="expertise" name="expertise" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="experience">Years of Experience</label>
                        <input type="number" id="experience" name="experience" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" id="qualification" name="qualification" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="social_linkedin">LinkedIn Profile</label>
                        <input type="url" id="social_linkedin" name="social_linkedin" class="form-control" placeholder="https://linkedin.com/in/username">
                    </div>
                    <div class="form-group">
                        <label for="social_twitter">Twitter Profile</label>
                        <input type="url" id="social_twitter" name="social_twitter" class="form-control" placeholder="https://twitter.com/username">
                    </div>
                    <div class="form-group">
                        <label for="social_facebook">Facebook Profile</label>
                        <input type="url" id="social_facebook" name="social_facebook" class="form-control" placeholder="https://facebook.com/username">
                    </div>
                    <div class="form-group">
                        <label for="image">Profile Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group full-width">
                        <label for="bio">Biography</label>
                        <textarea id="bio" name="bio" class="form-control" placeholder="Write a short bio about the teacher..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" name="add_teacher" class="submit-btn">Add Teacher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functionality
        const modal = document.getElementById("addTeacherModal");
        const btn = document.getElementById("openAddTeacherModal");
        const span = document.getElementsByClassName("close")[0];
        
        btn.onclick = function() {
            modal.style.display = "block";
        }
        
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>