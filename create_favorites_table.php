<?php
require_once 'config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id VARCHAR(255) NOT NULL,
        course_title VARCHAR(255) NOT NULL,
        course_image VARCHAR(255) NOT NULL,
        instructor_name VARCHAR(255) NOT NULL,
        instructor_image VARCHAR(255) NOT NULL,
        course_tag VARCHAR(50) NOT NULL,
        lessons INT NOT NULL,
        students INT NOT NULL,
        rating DECIMAL(3,2) NOT NULL,
        level_tag VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_course (user_id, course_id)
    )";
    
    $pdo->exec($sql);
    echo "Favorites table created successfully";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>