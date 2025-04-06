<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Get user's favorites
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Student Chat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="script.js"></script>
    <style>
        /* Dark mode improvements for favorites section */
        .dark-mode .favorites-section h2 {
            color: #f0f0f0;
        }
        
        .dark-mode .course-card {
            background-color: #2d2d2d;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .course-title {
            color: #e0e0e0;
        }
        
        .dark-mode .instructor-name {
            color: #c9c9c9;
        }
        
        .dark-mode .course-info span,
        .dark-mode .course-info i {
            color: #b8b8b8;
        }
        
        .dark-mode .level-tag {
            color: #a0d2ff;
            background-color: rgba(30, 136, 229, 0.2);
        }
        
        .dark-mode .price {
            color: #4fc3f7;
            font-weight: 600;
        }
        
        .dark-mode .no-favorites p {
            color: #c9c9c9;
        }
        
        .dark-mode .btn {
            background-color: #1976d2;
            color: #ffffff;
            border: none;
        }
        
        .dark-mode .btn:hover {
            background-color: #1565c0;
        }
        
        /* Add spacing between text and button */
        .no-favorites p {
            margin-bottom: 25px;
        }
        
        .no-favorites .btn {
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; text-align: center;">
    <!-- Navbar has been removed -->
    
    <section class="favorites-section" style="width: 100%; max-width: 1200px;">
        <h2 style="text-align: center; margin-bottom: 30px;">My Favorite Courses</h2>
        
        <?php if (empty($favorites)): ?>
            <div class="no-favorites" style="text-align: center; padding: 50px 20px;">
                <p>You haven't added any courses to your favorites yet.</p>
                <a href="index.php#courses-section" class="btn">Browse Courses</a>
            </div>
        <?php else: ?>
            <div class="courses-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;">
                <?php foreach ($favorites as $course): ?>
                    <div class="course-card" data-course-id="<?php echo htmlspecialchars($course['course_id']); ?>" style="max-width: 350px; margin: 0 auto 20px;">
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($course['course_image']); ?>" alt="<?php echo htmlspecialchars($course['course_title']); ?>">
                            <span class="course-tag <?php echo strtolower(htmlspecialchars($course['course_tag'])); ?>"><?php echo htmlspecialchars($course['course_tag']); ?></span>
                            <button class="favorite-btn active"><i class="fas fa-heart"></i></button>
                        </div>
                        <div class="course-content" style="text-align: left;">
                            <div class="instructor">
                                <img src="<?php echo htmlspecialchars($course['instructor_image']); ?>" alt="<?php echo htmlspecialchars($course['instructor_name']); ?>" class="instructor-img">
                                <span class="instructor-name"><?php echo htmlspecialchars($course['instructor_name']); ?></span>
                            </div>
                            <h3 class="course-title"><?php echo htmlspecialchars($course['course_title']); ?></h3>
                            <div class="course-info">
                                <span><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($course['lessons']); ?> Lessons</span>
                                <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($course['students']); ?></span>
                                <span><i class="fas fa-star"></i> <?php echo htmlspecialchars($course['rating']); ?></span>
                            </div>
                            <div class="course-footer">
                                <span class="level-tag"><?php echo htmlspecialchars($course['level_tag']); ?></span>
                                <span class="price">$<?php echo htmlspecialchars($course['price']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme toggle functionality has been simplified since navbar is removed
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
            
            // Handle favorite button clicks
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const courseCard = this.closest('.course-card');
                    const courseId = courseCard.dataset.courseId;
                    
                    // Remove from favorites
                    fetch('handle_favorite.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=remove&course_id=${encodeURIComponent(courseId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the card with animation
                            courseCard.style.opacity = '0';
                            setTimeout(() => {
                                courseCard.remove();
                                
                                // Check if there are no more favorites
                                if (document.querySelectorAll('.course-card').length === 0) {
                                    const container = document.querySelector('.courses-container');
                                    container.innerHTML = `
                                        <div class="no-favorites">
                                            <p>You haven't added any courses to your favorites yet.</p>
                                            <a href="index.php#courses-section" class="btn">Browse Courses</a>
                                        </div>
                                    `;
                                }
                            }, 300);
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>