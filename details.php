
<?php
session_start();
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Contenu</title>

    <!-- Ajoutez le lien vers votre fichier CSS ici -->
  <style>
    body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
}

.content-details {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f8f8f8;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #333;
}

p {
    color: #555;
}

img {
    max-width: 100%;
    height: auto;
    margin-top: 20px;
}

video {
    max-width: 600px;
    max-height: 600px;
    height: auto;
    margin-top: 20px;
}


  </style>
</head>
<body>

    <div class="content-details">
        <?php
        if (isset($_GET['id'])) {
            $contentId = $_GET['id'];

            // Effectuez une requête SQL pour récupérer les détails du contenu avec l'ID donné
            $selectSql = "SELECT * FROM contenu WHERE id = ?";
            $selectStmt = $conn->prepare($selectSql);
            $selectStmt->bind_param("i", $contentId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Récupérez les détails du contenu depuis la base de données
                $contentImage = $row['image'];
                $contentTitle = $row['title'];
                $contentContent = $row['content'];
                $contentAdditionalContent = $row['additionalContent']; // Assurez-vous d'avoir cette colonne dans votre base de données
             
                $contentVideo = $row['video'];
                echo '<img src="' . $contentImage . '" alt="Image">';


                // Affichez les détails comme nécessaire
                echo '<h1>' . $contentTitle . '</h1>';
                echo '<p>' . $contentContent . '</p>';
                echo '<p>' . $contentAdditionalContent . '</p>';
                echo '<video controls><source src="' . $contentVideo . '" type="video/mp4"></video>';
            } else {
                echo "Contenu non trouvé.";
            }
        } else {
            echo "ID du contenu non spécifié.";
        }
        ?>
    </div>

    <!-- Ajoutez le lien vers votre fichier JavaScript ici -->
    <script src="script.js"></script>
</body>
</html>
