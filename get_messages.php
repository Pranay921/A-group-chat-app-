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

// Define encryption key - same as in send_message.php
$encryption_key = md5('demochatapp_secure_salt' . $dbname);

// Process messages - try to decrypt if encrypted
foreach ($messages as &$message) {
    if (!empty($message['message'])) {
        // Try to decrypt the message
        $decrypted = openssl_decrypt(
            $message['message'], 
            'AES-128-CBC', 
            $encryption_key, 
            0, 
            substr($encryption_key, 0, 16)
        );
        
        // If decryption was successful, use the decrypted message
        if ($decrypted !== false) {
            $message['message'] = $decrypted;
        }
        // If decryption fails, the message is likely not encrypted, so keep it as is
    }
}

echo json_encode(array_reverse($messages));
?>