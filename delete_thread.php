<?php
    session_start();
    include('config.php'); // Database connection configuration

    if (!isset($_SESSION['user_id'])) {
        header('Location: register.html');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['threadId'])) {
            $threadId = $_POST['threadId'];
            $userId = $_SESSION['user_id'];

            // Check if the current user is the creator of the thread
            $checkThreadSql = "SELECT * FROM threads WHERE id = ? AND user_id = ?";
            $checkThreadStmt = $conn->prepare($checkThreadSql);
            $checkThreadStmt->bind_param("ii", $threadId, $userId);
            $checkThreadStmt->execute();
            $checkThreadResult = $checkThreadStmt->get_result();

            if ($checkThreadResult->num_rows > 0) {
                // User is the creator, delete the thread
                $deleteThreadSql = "DELETE FROM threads WHERE id = ?";
                $deleteThreadStmt = $conn->prepare($deleteThreadSql);
                $deleteThreadStmt->bind_param("i", $threadId);
                $deleteThreadStmt->execute();

                // Handle the result or redirect to the forum page
                if ($deleteThreadStmt->affected_rows > 0) {
                    echo 'Thread deleted successfully';
                } else {
                    echo 'Error deleting thread';
                }
            } else {
                // User is not the creator, handle as needed
                echo 'You are not the creator of this thread';
            }
        }
    }
?>
