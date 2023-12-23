<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $senderId = $_SESSION['user_id'];
    $enseignantId = isset($_POST['enseignantId']) ? $_POST['enseignantId'] : null;
    $messageText = isset($_POST['messageText']) ? $_POST['messageText'] : null;

    if (!$enseignantId || !$messageText) {
        // Invalid or missing parameters
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing parameters']);
        exit();
    }

    // Prepare and execute the message insertion query
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, sender_role) VALUES (?, ?, ?, 'etudiant')");

    if (!$insertMessageQuery) {
        // Error preparing query
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error preparing query: ' . $conn->error]);
        exit();
    }

    $insertMessageQuery->bind_param("iis", $senderId, $enseignantId, $messageText);
    $insertMessageQuery->execute();

    if ($insertMessageQuery->errno) {
        // Error executing query
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error executing query: ' . $insertMessageQuery->error]);
        exit();
    }

    $insertMessageQuery->close();

    // Handle notifications or any other necessary actions

    // Send a response back to the client
    echo json_encode(['status' => 'success']);
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
