    <?php
    session_start();
    include('config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio'])) {
        $audioData = $_FILES['audio'];
        $senderId = $_SESSION['user_id'];

        // Specify the folder where you want to save the audio files
        $uploadFolder = 'audio/';

        // Generate a unique filename for the audio file
        $audioFileName = uniqid('audio_') . '.wav';
        $audioFilePath = $uploadFolder . $audioFileName;

        // Move the uploaded audio file to the specified folder
        if (move_uploaded_file($audioData['tmp_name'], $audioFilePath)) {
            // Save the audio file information to the messages table
            $insertQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, audio, sender_role) VALUES (?, ?, ?, 'etudiant')");
            $insertQuery->bind_param('iiss', $senderId, $_SESSION['sender_id'], $audioFilePath);

            if ($insertQuery->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Error saving audio information']);
            }

            $insertQuery->close();
        } else {
            echo json_encode(['error' => 'Error moving uploaded file']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
    ?>
