<?php
session_start();
include('config.php');

// Fetch messages for the logged-in enseignant
$enseignantId = $_SESSION['user_id'];
$fetchMessagesQuery = $conn->prepare("SELECT m.*, e.name AS sender_name FROM messages m
    INNER JOIN etudiant e ON m.sender_id = e.user_id
    WHERE receiver_id = ? ORDER BY m.created_at DESC");
$fetchMessagesQuery->bind_param("i", $enseignantId);
$fetchMessagesQuery->execute();
$messagesResult = $fetchMessagesQuery->get_result();
$fetchMessagesQuery->close();




$senderId = $_GET['sender_id'];
$senderName = $_GET['sender_name'];
$photoPath = $_GET['photo_path'];

// Store values in session for future use
$_SESSION['sender_id'] = $senderId;
$_SESSION['sender_name'] = $senderName;
$_SESSION['photo_path'] = $photoPath;
?>


<?php

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the logged-in user's information
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
// Check if the user is an etudiant
$etudiantSql = "SELECT * FROM etudiant WHERE user_id = ?";
$etudiantStmt = $conn->prepare($etudiantSql);
$etudiantStmt->bind_param("i", $user_id);
$etudiantStmt->execute();
$etudiantResult = $etudiantStmt->get_result();

// Check if the user is an enseignant
$enseignantSql = "SELECT * FROM enseignant WHERE user_id = ?";
$enseignantStmt = $conn->prepare($enseignantSql);
$enseignantStmt->bind_param("i", $user_id);
$enseignantStmt->execute();
$enseignantResult = $enseignantStmt->get_result();


?>

<?php
include('config.php');

// Function to fetch etudiant's messages
function fetchEtudiantMessages($senderId, $mysqli) {
    $query = "SELECT * FROM messages WHERE sender_id = $senderId";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Error: " . $mysqli->error);
    }

    return $result;
}

// Function to post a reply
function postReply($senderId, $content, $mysqli) {
    $userId = $_SESSION['user_id']; // Assuming you have a user_id in your session
    $content = mysqli_real_escape_string($mysqli, $content);

    $insertQuery = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, sender_role) VALUES (?, ?, ?, 'etudiant')");
    $insertQuery->bind_param('iis', $userId, $senderId, $content);

    if ($insertQuery->execute()) {
        // Reply posted successfully
        exit();
    } else {
        // Handle the error (e.g., display an error message)
        echo "Error: " . $insertQuery->error;
    }

    $insertQuery->close();
}

// Check if sender_id is set in the URL
if (isset($_GET['sender_id'])) {
    $senderId = $_GET['sender_id'];
}

    // Fetch etudiant's messages based on sender_id
    $etudiantMessages = fetchEtudiantMessages($senderId, $conn);

    ?>

<?php
include('config.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the logged-in user's information
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Fetch messages for the logged-in enseignant
$enseignantId = $_SESSION['user_id'];
$fetchMessagesQuery = $conn->prepare("SELECT m.*, e.name AS sender_name FROM messages m
    INNER JOIN etudiant e ON m.sender_id = e.user_id
    WHERE receiver_id = ? ORDER BY m.created_at DESC");
$fetchMessagesQuery->bind_param("i", $enseignantId);
$fetchMessagesQuery->execute();
$messagesResult = $fetchMessagesQuery->get_result();
$fetchMessagesQuery->close();

// Fetch etudiants for the logged-in enseignant
$fetchEtudiantsQuery = $conn->prepare("SELECT DISTINCT e.user_id, e.name, e.photo_path, e.country_id
    FROM etudiant e
    INNER JOIN messages m ON e.user_id = m.sender_id
    WHERE m.receiver_id = ?");
$fetchEtudiantsQuery->bind_param("i", $enseignantId);
$fetchEtudiantsQuery->execute();
$etudiantsResult = $fetchEtudiantsQuery->get_result();
$fetchEtudiantsQuery->close();
?>

<?php


// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the logged-in user's information
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
// Check if the user is an etudiant
$etudiantSql = "SELECT * FROM etudiant WHERE user_id = ?";
$etudiantStmt = $conn->prepare($etudiantSql);
$etudiantStmt->bind_param("i", $user_id);
$etudiantStmt->execute();
$etudiantResult = $etudiantStmt->get_result();

// Check if the user is an enseignant
$enseignantSql = "SELECT * FROM enseignant WHERE user_id = ?";
$enseignantStmt = $conn->prepare($enseignantSql);
$enseignantStmt->bind_param("i", $user_id);
$enseignantStmt->execute();
$enseignantResult = $enseignantStmt->get_result();


?>


<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="quran-style.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply Page</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #333;
            display: flex;
            align-items: center;
        }

        h2 img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .message i {
            margin-right: 5px;
        }

        .message {
            background-color: #fff;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #replyForm {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }

        .element-background {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 10px;
    margin-top: 50px;
    margin-bottom: 50px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}
/* styles pour la barre de recherche */
.quran-search {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}


#voice-search-button {
  margin-left: 8px;
  background-color: transparent;
  border: none;
  cursor: pointer;
  outline: none;
}

#voice-search-button svg {
  width: 20px;
  height: 20px;
  fill: #888;
}

#voice-search-button:hover svg {
  fill: #555;
}

.large-icon {
  width: 36px;
  height: 36px;
}



.quran-search input[type="text"] {
    padding: 10px;
    border-radius: 5px;
    border: none;
    margin-right: 10px;
    width: 70%;
    font-size: 16px;
}
.quran-search button {
    padding: 10px;
    border-radius: 5px;
    border: none;
    background-color: #019147;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}
    </style>

<body>
    <h2>
        <img src="<?php echo $photoPath; ?>" alt="Etudiant Image" class="etudiant-photo">
        <?php echo $senderName; ?>
    </h2>

    <?php
    // Fetch and display messages from both etudiant and enseignant
    include('config.php');
    $messagesQuery = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at");
    $messagesQuery->bind_param("ii", $senderId, $senderId);
    $messagesQuery->execute();
    $messagesResult = $messagesQuery->get_result();

    while ($message = $messagesResult->fetch_assoc()) :
    ?>
        <div class="message <?php echo $message['sender_id'] == $senderId ? 'sent' : 'received'; ?>">
            <p><i class="fas fa-paper-plane"></i> <?php echo $message['message_text']; ?></p>
            <p>Sent at: <?php echo $message['created_at']; ?></p>
        </div>
    <?php endwhile; ?>

    <!-- Reply form for enseignant -->
    <div id="replyForm">
        <form method="post" action="">
            <label for="messageTextReply">Message:</label>
            <div class="element-background">
                    <div class="quran-container">
                    <div class="quran-search">
                        <div id="resultat"></div>

                        <input type="text" id="messageTextReply" placeholder="Message..." name="messageTextReply" required>
            <button type="submit" name="postReply"><i class="fas fa-paper-plane"></i> Envoyer</button>
                        <!-- Add the recording button to your HTML -->
<button id="voice-search-button" title="Start recording">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic-fill" viewBox="0 0 16 16">
        <path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3z"/>
        <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/>
    </svg>
</button>
        </form>
    </div>

    <!-- Add your footer and script imports here -->
</body>

<?php
// Close the result set
$messagesQuery->close();

// Handle the form submission
if (isset($_POST['postReply'])) {
    postReply($senderId, $_POST['messageTextReply'], $conn);
}
?>
</html>
