<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['threadId'])) {
        $threadId = $_POST['threadId'];
        $userId = $_SESSION['user_id'];

        // Check if the user has already signaled this thread
        $checkSignalSql = "SELECT * FROM thread_signals WHERE thread_id = ? AND user_id = ?";
        $checkSignalStmt = $conn->prepare($checkSignalSql);
        $checkSignalStmt->bind_param("ii", $threadId, $userId);
        $checkSignalStmt->execute();
        $checkSignalResult = $checkSignalStmt->get_result();

        if ($checkSignalResult->num_rows === 0) {
            // User hasn't signaled this thread yet, insert a new signal
            $insertSignalSql = "INSERT INTO thread_signals (thread_id, user_id) VALUES (?, ?)";
            $insertSignalStmt = $conn->prepare($insertSignalSql);
            $insertSignalStmt->bind_param("ii", $threadId, $userId);
            $insertSignalStmt->execute();

            // Check if the thread has reached the required number of signals
            checkThreadSignals($threadId, $conn);
            
            echo json_encode(['success' => true, 'message' => 'Thread signaled successfully']);
        } else {
            // User has already signaled this thread
            echo json_encode(['success' => false, 'message' => 'You have already signaled this thread']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing threadId parameter']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

function checkThreadSignals($threadId, $conn) {
    // Get the count of unique users who signaled this thread
    $countSignalsSql = "SELECT COUNT(DISTINCT user_id) AS signal_count FROM thread_signals WHERE thread_id = ?";
    $countSignalsStmt = $conn->prepare($countSignalsSql);
    $countSignalsStmt->bind_param("i", $threadId);
    $countSignalsStmt->execute();
    $countSignalsResult = $countSignalsStmt->get_result();

    if ($countSignalsResult->num_rows > 0) {
        $countSignals = $countSignalsResult->fetch_assoc()['signal_count'];

        // If the required number of signals is reached (e.g., 5), perform actions
        if ($countSignals >= 5) {
            // Perform actions such as notifying admin, marking the thread as flagged, etc.
            // Example: Update the thread table with a 'flagged' status
            $updateThreadSql = "UPDATE threads SET flagged = 1 WHERE id = ?";
            $updateThreadStmt = $conn->prepare($updateThreadSql);
            $updateThreadStmt->bind_param("i", $threadId);
            $updateThreadStmt->execute();
        }
    }
}
?>
