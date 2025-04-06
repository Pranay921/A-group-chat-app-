<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not authenticated');
}

$message = $_POST['message'] ?? '';
$file_name = '';
$file_path = '';

// Check if a file was uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = $_FILES['file']['name'];
    $file_path = $upload_dir . uniqid() . '_' . $file_name;
    
    move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
}

// Debug output
error_log("Sending message: " . $message);
error_log("User ID: " . $_SESSION['user_id']);
error_log("File name: " . $file_name);
error_log("File path: " . $file_path);

try {
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, file_name, file_path) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$_SESSION['user_id'], $message, $file_name, $file_path]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        error_log("Database error: " . print_r($stmt->errorInfo(), true));
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} catch (PDOException $e) {
    error_log("PDO Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>