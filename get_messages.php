<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not authenticated');
}

$stmt = $pdo->query("
    SELECT m.*, u.username, 
    CASE WHEN m.user_id = {$_SESSION['user_id']} THEN 1 ELSE 0 END as is_own
    FROM messages m
    JOIN users u ON m.user_id = u.id
    ORDER BY m.created_at DESC
    LIMIT 50
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(array_reverse($messages));
?>