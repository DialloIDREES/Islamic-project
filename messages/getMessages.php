<?php
session_start();
include('config.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch messages where the logged-in user is the receiver
    $fetchMessagesQuery = "SELECT * FROM messages WHERE receiver_id = ?";
    $fetchMessagesStmt = $conn->prepare($fetchMessagesQuery);
    $fetchMessagesStmt->bind_param("i", $user_id);
    $fetchMessagesStmt->execute();
    $messagesResult = $fetchMessagesStmt->get_result();
    $fetchMessagesStmt->close();

    $messages = array();

    // Fetch all messages into an array
    while ($row = $messagesResult->fetch_assoc()) {
        $messages[] = $row;
    }

    // Return messages as JSON
    echo json_encode($messages);
} else {
    // Redirect or handle the case where the user is not logged in
    header("Location: login.php");
    exit();
}
?>
