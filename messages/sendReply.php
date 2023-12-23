<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'];
    $subject = $_POST['subject'];
    $messageContent = $_POST['message_content'];

    // Validate inputs (you should perform proper validation here)
    if (empty($subject) || empty($messageContent)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Subject and message content are required']);
        exit();
    }

    // Insert the message into the database
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message_text) VALUES (?, ?, ?, ?)");

    if (!$insertMessageQuery) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Error preparing query: ' . $conn->error]);
        exit();
    }

    $insertMessageQuery->bind_param("iiss", $senderId, $receiverId, $subject, $messageContent);
    $insertMessageQuery->execute();

    if ($insertMessageQuery->errno) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Error executing query: ' . $insertMessageQuery->error]);
        exit();
    }

    $insertMessageQuery->close();

    // Update message_notification for receiver (etudiant or enseignant)
    $updateNotificationQuery = $conn->prepare("UPDATE " . ($_SESSION['is_etudiant'] ? 'enseignant' : 'etudiant') . " SET message_notification = 1 WHERE user_id = ?");
    $updateNotificationQuery->bind_param("i", $receiverId);
    $updateNotificationQuery->execute();
    $updateNotificationQuery->close();

    // Send a response back to the client
    echo json_encode(['status' => 'success']);
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
