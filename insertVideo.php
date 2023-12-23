<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le fichier vidéo
    $video = $_FILES['videoFile'];
    $videoFileName = uploadFile($video);

    // Récupérer l'ID de l'enseignant connecté
    $teacher_id = $_SESSION['user_id'];

    // Insérer les données dans la base de données
    $insertSql = "INSERT INTO contenu (video, user_id) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("si", $videoFileName, $teacher_id);

    if ($insertStmt->execute()) {
        // Succès : retourner le nom du fichier vidéo inséré
        echo $videoFileName;
    } else {
        // Erreur lors de l'insertion : retourner une chaîne vide
        echo "";
    }
}

function uploadFile($file)
{
    $targetDirectory = "uploads/";
    $targetFileName = $targetDirectory . basename($file['name']);
    $fileType = strtolower(pathinfo($targetFileName, PATHINFO_EXTENSION));

    // Vérifier si le fichier est une vidéo
    $allowedVideoTypes = ["mp4", "avi", "mov"];

    if (in_array($fileType, $allowedVideoTypes)) {
        // C'est une vidéo, traiter l'upload comme une vidéo
        if (move_uploaded_file($file['tmp_name'], $targetFileName)) {
            return $targetFileName;
        } else {
            // Erreur lors de l'upload de la vidéo
            return "";
        }
    } else {
        // Format de fichier non autorisé : retourner une chaîne vide
        return "";
    }
}
?>
