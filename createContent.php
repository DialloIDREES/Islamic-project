<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $title = $_POST['title'];
    $content = $_POST['content'];
    $additionalContent = $_POST['additionalContent']; // Add this line to get additional content

    // Récupérer le fichier image
    $image = $_FILES['image'];
    $imageFileName = uploadFile($image);

    // Récupérer le fichier vidéo
    $video = $_FILES['video'];
    $videoFileName = uploadFile($video);

    // Récupérer l'ID de l'enseignant connecté
    $teacher_id = $_SESSION['user_id'];

    // Insérer les données dans la base de données
    $insertSql = "INSERT INTO contenu (title, content, additionalContent, image, video, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sssssi", $title, $content, $additionalContent, $imageFileName, $videoFileName, $teacher_id);

    if ($insertStmt->execute()) {
        // Succès : rediriger vers une page de confirmation ou autre
        header("Location: confirmation.php");
        exit();
    } else {
        // Erreur lors de l'insertion : rediriger vers une page d'erreur
        header("Location: erreur.php");
        exit();
    }
}

function uploadFile($file)
{
    $targetDirectory = "uploads/";
    $targetFileName = $targetDirectory . basename($file['name']);
    $fileType = strtolower(pathinfo($targetFileName, PATHINFO_EXTENSION));

    // Vérifier si le fichier est une image ou une vidéo
    $allowedImageTypes = ["jpg", "jpeg", "png", "gif"];
    $allowedVideoTypes = ["mp4", "avi", "mov"];

    if (in_array($fileType, $allowedImageTypes)) {
        // C'est une image, traiter l'upload comme une image
        if (move_uploaded_file($file['tmp_name'], $targetFileName)) {
            return $targetFileName;
        } else {
            // Erreur lors de l'upload de l'image
            echo "Erreur lors de l'upload de l'image.";
            exit();
        }
    } elseif (in_array($fileType, $allowedVideoTypes)) {
        // C'est une vidéo, traiter l'upload comme une vidéo
        if (move_uploaded_file($file['tmp_name'], $targetFileName)) {
            return $targetFileName;
        } else {
            // Erreur lors de l'upload de la vidéo
            echo "Erreur lors de l'upload de la vidéo.";
            exit();
        }
    } else {
        // Format de fichier non autorisé : rediriger vers une page d'erreur
        echo "Format de fichier non autorisé.";
        exit();
    }
}
