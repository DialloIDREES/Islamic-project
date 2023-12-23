<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the user is an etudiant
    $checkEtudiantSql = "SELECT * FROM etudiant WHERE user_id = ?";
    $checkEtudiantStmt = $conn->prepare($checkEtudiantSql);
    $checkEtudiantStmt->bind_param('i', $_SESSION['user_id']);
    $checkEtudiantStmt->execute();
    $isEtudiant = $checkEtudiantStmt->fetch(); // Will be true if user is an etudiant

    // Determine the table name
    $tableName = ($isEtudiant) ? 'etudiant_messages' : 'enseignant_messages';

    // Fetch messages for the logged-in user
    $fetchMessagesSql = "SELECT sender_id, subject, content FROM $tableName WHERE receiver_id = ?";
    $fetchMessagesStmt = $conn->prepare($fetchMessagesSql);
    $fetchMessagesStmt->bind_param('i', $_SESSION['user_id']);
    $fetchMessagesStmt->execute();
    $fetchMessagesResult = $fetchMessagesStmt->get_result();
    $messages = array();

    while ($row = $fetchMessagesResult->fetch_assoc()) {
        $messages[] = array(
            'sender_id' => $row['sender_id'],
            'subject' => $row['subject'],
            'content' => $row['content']
        );
    }

    $fetchMessagesStmt->close();

    // Send the messages as JSON
    echo json_encode(['messages' => $messages]);
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
