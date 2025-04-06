<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];
    $category = $_POST['category'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, rating, message, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $rating, $message, $category]);
        
        // Store success message in session instead of variable
        $_SESSION['feedback_success'] = "Thank you for your feedback!";
        
        // Redirect to the same page to prevent form resubmission on refresh
        header("Location: feedback.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error submitting feedback: " . $e->getMessage();
    }
}

// Check if there's a success message in the session
if (isset($_SESSION['feedback_success'])) {
    $success_message = $_SESSION['feedback_success'];
    // Clear the message from session after displaying it once
    unset($_SESSION['feedback_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Student Chat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .feedback-container {
            max-width: 800px;
            margin: 80px auto 50px; /* Reduced top margin since we're removing the navbar */
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feedback-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* Add a back button for better navigation */
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #1E90FF;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-button i {
            margin-right: 5px;
        }
        
        
        .back-button:hover {
            text-decoration: underline;
        }
        
        /* Remove the body padding-top since we're removing the navbar */
        body {
            padding-top: 0;
        }
        
        .feedback-header h1 {
            color: #1E88E5;
            margin-bottom: 10px;
        }
        
        .feedback-header p {
            color: #666;
        }
        
        .feedback-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        
        .form-group select,
        .form-group textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            transition: color 0.2s;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #FFD700;
        }
        
        .submit-btn {
            background-color: #1E88E5;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #1565C0;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1E88E5;
            text-decoration: none;
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        /* Dark mode styles */
        body.dark-mode .feedback-container {
            background-color: #2a2a2a;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        body.dark-mode .feedback-header h2 {
            color: #ffffff; /* Changed from default to white for better visibility */
        }
        
        body.dark-mode .feedback-header p,
        body.dark-mode .form-group label {
            color: #e0e0e0;
        }
        
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background-color: #3a3a3a;
            border-color: #4a4a4a;
            color: #e0e0e0;
        }
        
        body.dark-mode .back-link {
            color: #1E88E5;
        }
        
        body.dark-mode .success-message {
            background-color: #1e3a2d;
            color: #a3e9c1;
        }
        
        body.dark-mode .error-message {
            background-color: #3a1e1e;
            color: #e9a3a3;
        }
        
        /* Fix for stars in dark mode */
        body.dark-mode .star-rating label {
            color: #555; /* Darker gray so they're visible in dark mode */
        }
        
        body.dark-mode .star-rating label:hover,
        body.dark-mode .star-rating label:hover ~ label,
        body.dark-mode .star-rating input:checked ~ label {
            color: #FFD700; /* Keep gold color for selected/hovered stars */
        }
        
        /* Make back button more visible in dark mode */
        body.dark-mode .back-button {
            color: #4fc3f7; /* Lighter blue for better visibility */
        }
        
        body.dark-mode .back-button:hover {
            color: #81d4fa; /* Even lighter on hover */
        }
    </style>
</head>
<body>
    <!-- Removed the navbar section that was here -->
    
    <div class="feedback-container">
        <a href="index.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Home</a>
        
        <div class="feedback-header">
            <h2>Share Your Feedback</h2>
            <p>We value your opinion! Please let us know about your experience.</p>
        </div>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form class="feedback-form" method="POST" action="feedback.php">
            <div class="form-group">
                <label for="category">What are you providing feedback about?</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="courses">Courses</option>
                    <option value="instructors">Instructors</option>
                    <option value="website">Website Experience</option>
                    <option value="support">Customer Support</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>How would you rate your experience?</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5" class="fas fa-star"></label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" class="fas fa-star"></label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" class="fas fa-star"></label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" class="fas fa-star"></label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" class="fas fa-star"></label>
                </div>
                <div class="rating-alert" style="display: none; color: #e74c3c; margin-top: 5px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> Please select a star rating
                </div>
            </div>
            
            <div class="form-group">
                <label for="message">Tell us more about your experience</label>
                <textarea id="message" name="message" placeholder="Please share your thoughts, suggestions, or concerns..." required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
        
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    
    <style>
        .feedback-container {
            max-width: 800px;
            margin: 80px auto 50px;
            padding: 40px;
            background: linear-gradient(145deg, #ffffff, #f5f9ff);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(30, 136, 229, 0.15);
            border-top: 5px solid #1E88E5;
            position: relative;
            overflow: hidden;
        }
        
        .feedback-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(30, 136, 229, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 0;
        }
        
        .feedback-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .feedback-header h1 {
            color: #1E88E5;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .feedback-header p {
            color: #666;
            font-size: 16px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            color: #1E88E5;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 30px;
            background: rgba(30, 136, 229, 0.1);
        }
        
        .back-button:hover {
            background: rgba(30, 136, 229, 0.2);
            transform: translateX(-5px);
        }
        
        .back-button i {
            margin-right: 8px;
        }
        
        .feedback-form {
            position: relative;
            z-index: 1;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 15px;
            color: #333;
            transition: all 0.3s ease;
        }
        
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #1E88E5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
            outline: none;
            background-color: #fff;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #FFD700;
            transform: scale(1.2);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1E88E5, #26E7C8);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 30px auto 0;
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.4);
            background: linear-gradient(135deg, #26E7C8, #1E88E5);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .alert-success {
            background-color: rgba(38, 231, 200, 0.15);
            color: #26E7C8;
            border-left: 4px solid #26E7C8;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Dark mode styles */
        body.dark-mode .feedback-container {
            background: linear-gradient(145deg, #1a2a3a, #0a1929);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-top: 5px solid #1E88E5;
        }
        
        body.dark-mode .feedback-header h1 {
            color: #1E88E5;
        }
        
        body.dark-mode .feedback-header p {
            color: #b8b8b8;
        }
        
        body.dark-mode .form-group label {
            color: #e0e0e0;
        }
        
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background-color: #1a2a3a;
            border-color: #2a3a4a;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-group select:focus,
        body.dark-mode .form-group textarea:focus {
            border-color: #1E88E5;
            background-color: #2a3a4a;
        }
        
        body.dark-mode .back-button {
            background: rgba(30, 136, 229, 0.2);
        }
        
        body.dark-mode .back-button:hover {
            background: rgba(30, 136, 229, 0.3);
        }
        
        body.dark-mode .star-rating label {
            color: #444;
        }
        
        body.dark-mode .star-rating label:hover,
        body.dark-mode .star-rating label:hover ~ label,
        body.dark-mode .star-rating input:checked ~ label {
            color: #FFD700;
        }
        
        body.dark-mode .alert-success {
            background-color: rgba(38, 231, 200, 0.1);
            color: #26E7C8;
        }
        
        body.dark-mode .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .feedback-container {
                padding: 25px;
                margin: 60px auto 40px;
            }
            
            .feedback-header h1 {
                font-size: 26px;
            }
            
            .star-rating {
                gap: 5px;
            }
            
            .star-rating label {
                font-size: 24px;
            }
            
            .submit-btn {
                width: 100%;
            }
        }
    </style>
</body>
</html>