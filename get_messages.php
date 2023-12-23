<?php
// Include your database connection and other necessary files

if (isset($_POST['etudiant_id'])) {
    $etudiantId = $_POST['etudiant_id'];

    // Fetch messages for the selected etudiant
    $fetchMessagesQuery = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? AND receiver_id = ?");
    $fetchMessagesQuery->bind_param("ii", $etudiantId, $enseignantId);  // Assuming $enseignantId is defined
    $fetchMessagesQuery->execute();
    $messagesResult = $fetchMessagesQuery->get_result();
    $fetchMessagesQuery->close();

    // Display messages
    while ($message = $messagesResult->fetch_assoc()) {
        echo '<p>' . $message['message_text'] . '</p>';
    }
}
?>
