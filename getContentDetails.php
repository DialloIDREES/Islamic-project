<?php
// getContentDetails.php

// Include your database connection configuration
include('config.php');

// Check if the content ID is provided in the POST request
if (isset($_POST['content_id'])) {
    $contentId = $_POST['content_id'];

    // Prepare and execute the query to retrieve content details
    $stmt = $conn->prepare("SELECT * FROM your_content_table WHERE id = ?");
    $stmt->bind_param("i", $contentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $contentDetails = $result->fetch_assoc();
    $stmt->close();

    // Output content details as JSON
    header('Content-Type: application/json');
    echo json_encode($contentDetails);
} else {
    // Handle the case where content ID is not provided
    echo json_encode(['error' => 'Content ID not provided']);
}
?>
