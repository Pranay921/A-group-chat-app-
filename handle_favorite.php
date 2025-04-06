<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$response = ['success' => false];

if ($action === 'add') {
    // Add to favorites
    try {
        $stmt = $pdo->prepare("INSERT INTO favorites 
            (user_id, course_id, course_title, course_image, instructor_name, 
            instructor_image, course_tag, lessons, students, rating, level_tag, price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP");
        
        $stmt->execute([
            $user_id,
            $_POST['course_id'],
            $_POST['course_title'],
            $_POST['course_image'],
            $_POST['instructor_name'],
            $_POST['instructor_image'],
            $_POST['course_tag'],
            $_POST['lessons'],
            $_POST['students'],
            $_POST['rating'],
            $_POST['level_tag'],
            $_POST['price']
        ]);
        
        $response = ['success' => true, 'message' => 'Added to favorites'];
    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
} elseif ($action === 'remove') {
    // Remove from favorites
    try {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $_POST['course_id']]);
        
        $response = ['success' => true, 'message' => 'Removed from favorites'];
    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
} elseif ($action === 'check') {
    // Check if course is in favorites
    try {
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $_POST['course_id']]);
        
        $isFavorite = $stmt->rowCount() > 0;
        $response = ['success' => true, 'isFavorite' => $isFavorite];
    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>