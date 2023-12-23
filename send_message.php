<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $senderId = $_SESSION['user_id'];
    $receiverId = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : null;
    $subject = isset($_POST['subject']) ? $_POST['subject'] : null;
    $messageContent = isset($_POST['message_content']) ? $_POST['message_content'] : null;

    if (!$receiverId || !$subject || !$messageContent) {
        // Invalid or missing parameters
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing parameters']);
        exit();
    }

    // Prepare and execute the message insertion query
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message_text) VALUES (?, ?, ?, ?)");

    if (!$insertMessageQuery) {
        // Error preparing query
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error preparing query: ' . $conn->error]);
        exit();
    }

    $insertMessageQuery->bind_param("iiss", $senderId, $receiverId, $subject, $messageContent);
    $insertMessageQuery->execute();

    if ($insertMessageQuery->errno) {
        // Error executing query
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error executing query: ' . $insertMessageQuery->error]);
        exit();
    }

    $insertMessageQuery->close();

    // Update message_notification for receiver (etudiant or enseignant)
    $tableToUpdate = ($_SESSION['is_etudiant'] ? 'enseignant' : 'etudiant');
    $updateNotificationQuery = $conn->prepare("UPDATE $tableToUpdate SET message_notification = 1 WHERE user_id = ?");
    $updateNotificationQuery->bind_param("i", $receiverId);
    $updateNotificationQuery->execute();
    $updateNotificationQuery->close();

    // Handle notifications or any other necessary actions

    // Send a response back to the client
    echo json_encode(['status' => 'success']);
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
