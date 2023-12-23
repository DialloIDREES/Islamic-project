<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $receiverId = $_POST['receiverId'];
    $senderId = $_SESSION['user_id'];

    // Insert the message into the database
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
    $insertMessageQuery->bind_param("iis", $senderId, $receiverId, $message);

    if ($insertMessageQuery->execute()) {
        echo 'Message sent successfully!';
    } else {
        echo 'Error sending message.';
    }

    $insertMessageQuery->close();
} else {
    echo 'Invalid request.';
}
?>
