<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['teacher_id'])) {
        // Récupérer l'ID de l'enseignant à partir des données POST
        $teacherId = $_POST['teacher_id'];

        // Effectuer une requête SQL pour récupérer les contenus de l'enseignant spécifié
        $selectSql = "SELECT * FROM contenu WHERE user_id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("i", $teacherId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();

        $contents = array();

        // Parcourir les résultats et les stocker dans un tableau
        while ($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }

        // Retourner les contenus sous forme de JSON
        echo json_encode($contents);
    } else {
        // Paramètre manquant
        echo json_encode(array('error' => 'Paramètre teacher_id manquant.'));
    }
} else {
    // Méthode de requête incorrecte
    echo json_encode(array('error' => 'Méthode de requête incorrecte.'));
}
?>
