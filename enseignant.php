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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masjid</title>
    <link rel="stylesheet" href="style-cookie.css">

    <link rel="stylesheet" href="fontawesome/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css
    ">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-rC1L86ft5RitZDF6G+de8rsfiO8KtpxpoYd09/X4JmqI62rj1prqBuPh9KDrFwUxhwg0ZlTZ9pMlOJn8AMVW4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" async></script>
    <script src="cookie.js" async=""></script>

    <style>
      .user-city .user-country{
        font-weight: lighter;
        font-style: italic;
      }
    </style>
    
    <style>
    .card-recom {
        margin-top: 20px;
    }

    .teacher-card {
        display: inline-block; /* Display cards inline */
        border: 1px solid #ccc;
        border-radius: 30px;
        padding: 65px;
        margin-right: 35px; /* Add some space between cards */
        margin-bottom: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .teacher-card img {
        border-radius: 50%;
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .teacher-card h3 {
        margin-bottom: 5px;
    }

    .teacher-card p {
        margin: 0;
    }
    .teacher-card:hover {
        transform: scale(1.05);
    }
    teacher-card h3 {
        margin-bottom: 5px;
        color: #333;
    }

    .teacher-card p {
        margin: 0;
        color: #666;
    }

    .teacher-card .location-icon {
        margin-right: 5px;
        color: red; /* Red color for the location icon */
    }
    #messagePopup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 999;
}
/* Add this CSS to your existing styles or in a separate stylesheet */

#messageBoxIcon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #3498db;
    color: #fff;
    border: none;
    border-radius: 50%;
    padding: 15px;
    cursor: pointer;
    font-size: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#messageNotification {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: red;
    color: #fff;
    padding: 5px;
    border-radius: 50%;
    display: none; /* Initially hidden */
}



.message-box-content {
    max-height: 300px; /* Adjust the max-height as needed */
    overflow-y: auto;
}

.contact-wrapper {
    display: flex;
    justify-content: space-between;
    margin-top: 50px;
  }
  
  .contact-side {
    width: 48%;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  .contact-me {
    background-color: #f5f5f5;
    font-family: 'Poppins' ,sans-serif;
  }
  
  .reach-us {
  
    color: #fff;
  }
  
  .contact-me h2,
  .reach-us h2 {
    margin-top: 0;
  }
  
  .contact-me form {
    display: flex;
    flex-direction: column;
  }
  
  .contact-me label {
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  .contact-me input,
  .contact-me textarea {
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  .contact-me textarea {
    height: 150px;
  }
  
  .contact-me input[type="submit"] {
    background-color: #ff9822;
    color: #fff;
    cursor: pointer;
  }
  
  .contact-me input[type="submit"]:hover {
    background-color: #e67b02;
  }
  
  .reach-us ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .reach-us li {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    color: #fff;
  }
  
  .reach-us li i {
    margin-right: 10px;

    font-size: 20px;
  }
  .item a{
    color: #000;
  }

</style>  

<!-- Ajoutez ces liens CDN dans la section head de votre document pour utiliser Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<header>
    
  <div class="container-fluid">
      <div class="logo-container">
          <img src="img/Masjid-img.png" alt="" class="logo" alt="my-logo">
          <h1>Masjid</h1>
      </div>
  </div>

  <div class="navb-items d-none d-xl-flex">
      <div class="item">
          <a href="index.php">Accueil</a>
      </div>

      <div class="item">
          <a href="services/quran/coran.php">Coran</a>
      </div>

      
      <div class="item">
            <?php
            if (isset($_SESSION['user_id']) && $enseignantResult->num_rows > 0) {
                // If the user is an enseignant, link to the Enseignant.php page
                echo '<a href="enseignant.php">Enseignement</a>';
            } else {
                // If the user is an etudiant or not logged in, link to the Forum
                echo '<a href="services/islam.php">Enseignement</a>';
            }
            ?>
        </div>
<!-- Modify the Forum link to conditionally render based on the user's role -->
<div class="item">
    <?php
    // Check if the user is logged in and is an enseignant
    if (isset($_SESSION['user_id']) && $enseignantResult->num_rows > 0) {
        // If the user is an enseignant, link to the Mailpage.php
        echo '<a href="login/mailpage.php">Mail</a>';
    } else {
        // If the user is an etudiant or not logged in, link to the Forum
        echo '<a href="forum.php">Forum</a>';
    }
    ?>
</div>

      <?php
 

 
      if (isset($_SESSION['user_id'])) {
          // User is logged in, show the username
          $username = $_SESSION['username'];
          echo '<div class="item-button"><a href="profile.php"><i class="fa fa-user"></i> ' . $username . '</a></div>';
        } else {
         
          echo '<div class="item-button"><a href="login/register.html" type="button">S\'identifier</a></div>';
      }
      ?>
  </div>
</header>

                <!-- Button trigger modal -->
                <div class="mobile-toggler d-lg-none">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#navbModal">
                        <i class="fa-solid fa-bars"></i>
                    </a>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="navbModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <img src="img/Masjid-img-icon.png" alt="Logo">
                                <span class="h1-menu"><h1 id="color-h1">Masjid</h1></span>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>

                            <div class="modal-body">
                                
                                <div class="modal-line">
                                    <i class="fa-solid fa-house"></i><a href="#">Acceuil</a>
                                </div>

                                <div class="modal-line">
                                    <i class="fa-solid fa-bell-concierge"></i><a href="services/quran.html">Coran</a>
                                </div>

                                <div class="modal-line">
                                    <i class="fa-solid fa-file-lines"></i> <a href="services/islam.html">Enseignement</a>
                                </div>

                                <div class="modal-line">
                                    <i class="fa-solid fa-circle-info"></i><a href="services/quiz.php">Quiz</a>
                                </div>

                                <a href="services/whatabout.html" class="navb-button" type="button">À propos de nous</a>
                            </div>

                            <div class="mobile-modal-footer">
                                
                                <a target="_blank" href="#"><i class="fa-brands fa-instagram"></i></a>
                                <a target="_blank" href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                <a target="_blank" href="#"><i class="fa-brands fa-youtube"></i></a>
                                <a target="_blank" href="#"><i class="fa-brands fa-facebook"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </header>

    <body>
        
    <?php

// Fonction pour obtenir le nom de l'enseignant
function getEnseignantName($enseignant_id, $conn) {
    // Remplacez cette requête par celle qui récupère le nom de l'enseignant en fonction de $enseignant_id
    $query = "SELECT name FROM enseignant WHERE user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $enseignant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $enseignant_name = $row['name'];
        return $enseignant_name;
    } else {
        // Gérer le cas où aucun enseignant n'est trouvé
        return "Enseignant introuvable";
    }
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur est un enseignant
    $enseignantSql = "SELECT * FROM enseignant WHERE user_id = ?";
    $enseignantStmt = $conn->prepare($enseignantSql);
    $enseignantStmt->bind_param("i", $user_id);
    $enseignantStmt->execute();
    $enseignantResult = $enseignantStmt->get_result();

    if ($enseignantResult->num_rows > 0) {
        // L'utilisateur est un enseignant

        // Récupérer l'identifiant de l'enseignant à partir de l'URL
        $enseignant_id = $_GET['id'];

        // Assurez-vous que l'enseignant_id correspond à l'utilisateur connecté
        if ($enseignant_id == $user_id) {
            // TODO: Implémenter le code pour charger le contenu de l'enseignant
            // ...

            // Exemple statique
            echo '<h2>Contenu de l\'enseignant</h2>';
            echo '<p>Vous pouvez ajouter des cours et des vidéos ici.</p>';
            echo '<button id="createContentBtn" class="btn btn-primary">Créer Contenu</button>';
            echo '<div class="container mt-4">';
            echo '<h2>Vos Contenus</h2>';
            // Afficher les contenus existants
            echo '<div id="contentList">';
            // TODO: Afficher les contenus existants de l'enseignant
            // ...
            echo '</div>';
        } else {
            // Rediriger vers une page d'erreur si l'enseignant_id ne correspond pas à l'utilisateur connecté
            header("Location: erreur.php");
            exit();
        }
    } else {
        // L'utilisateur est un étudiant
        // TODO: Implémenter le code pour charger le contenu de l'étudiant
        // ...
        $user_id = $_GET['id'];

        // Obtenir le nom de l'enseignant
        $enseignant_name = getEnseignantName($user_id, $conn);
    
        echo '<h2>Contenu de ' . $enseignant_name . '</h2>';
        echo '<p>Vous pouvez voir des cours et des vidéos ici.</p>';
        echo '<div id="contentList">';
        // TODO: Afficher les contenus existants de l'étudiant
        // ...
        echo '</div>';
    }

} else {
    // Rediriger vers une page d'erreur si l'utilisateur n'est pas connecté
    header("Location: erreur.php");
    exit();
}
?>


<!-- Inserer une video Modal -->
<div class="modal" tabindex="-1" role="dialog" id="insertVideoModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Insérer une vidéo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Video insertion form -->
                <form id="videoForm" enctype="multipart/form-data" action="insertVideo.php" method="post">
                    <div class="form-group">
                        <label for="videoFile">Importer une vidéo :</label>
                        <input type="file" class="form-control-file" id="videoFile" name="videoFile" accept="video/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Insérer la vidéo</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Le reste de votre code HTML spécifique à la page enseignant.php -->
<!-- Modal pour créer un nouveau contenu -->
<div class="modal" tabindex="-1" role="dialog" id="createContentModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un nouveau contenu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            
            <div class="modal-body">
                <!-- Formulaire de création de contenu -->
                <form id="contentForm" enctype="multipart/form-data" action="createContent.php" method="post">
                    <div class="form-group">
                        <label for="title">Titre :</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Contenu :</label>
                        <textarea class="form-control" id="content" name="content" required></textarea>
                    </div>
                    <div class="form-group">
    <label for="additionalContent">Contenu détaillé :</label>
    <textarea class="form-control" id="additionalContent" name="additionalContent"></textarea>
</div>

                    <div class="form-group">
                        <label for="image">Importer une image :</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="video">Ajouter une vidéo :</label>
                        <input type="file" class="form-control-file" id="video" name="video" accept="video/*">
                    </div>
                    <!-- Ajoutez d'autres champs selon vos besoins -->
                    <button type="submit" class="btn btn-primary">Créer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Le reste de votre code HTML spécifique à la page enseignant.php -->


    <div id="contenusContainer" class="card-container">
        <!-- Les cartes de contenu seront affichées ici -->
    </div>
</div>

<style>
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .content-card {
        width: 30%;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        box-sizing: border-box;
    }

    .content-card img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
    }
</style>


<script>
    // Lorsque le bouton "Créer Contenu" est cliqué
    $('#createContentBtn').click(function () {
        // Afficher le modal
        $('#createContentModal').modal('show');
    });

      // When the "Insérer une vidéo" button is clicked
      $('#insertVideoBtn').click(function () {
        // Show the insert video modal
        $('#insertVideoModal').modal('show');
    });

    // Lorsque le formulaire est soumis
    $('#contentForm').submit(function (event) {
        event.preventDefault();

        // Récupérer les données du formulaire
        var formData = new FormData(this);

        // Envoyer les données au serveur via Ajax
        $.ajax({
            type: 'POST',
            url: 'createContent.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Traiter la réponse du serveur
                console.log(response);
            },
            error: function(error) {
                // Gérer les erreurs
                console.log(error);
            }
        });

        // Cacher le modal après soumission du formulaire
        $('#createContentModal').modal('hide');
    });
</script>


<!-- Ajoutez ce script à la fin de votre page enseignant.php -->
<script>
 $(document).ready(function () {
    // Récupérer l'ID de l'enseignant à partir des paramètres d'URL
    var teacherId = <?php echo isset($_GET['id']) ? $_GET['id'] : 0; ?>;

    // Appeler le script PHP pour récupérer les contenus de l'enseignant
    $.ajax({
        type: 'POST',
        url: 'getTeacherContents.php',
        data: { teacher_id: teacherId },
        dataType: 'json',
        success: function (response) {
            // Afficher les contenus
            displayContents(response);
        },
        error: function (error) {
            console.log(error);
        }
    });

    function displayContents(contents) {
        var contenusContainer = $('#contenusContainer');

        contents.forEach(function (content) {
            var cardHtml = '<div class="content-card">';
            cardHtml += '<img src="' + content.image + '" alt="Image">';
            cardHtml += '<h4>' + content.title + '</h4>';
            cardHtml += '<p>' + content.content + '</p>';
            cardHtml += '<a href="details.php?id=' + content.id + '" class="btn btn-primary">Voir plus</a>';
            cardHtml += '</div>';

            contenusContainer.append(cardHtml);

            console.log("Content ID:", content.id); // Log the content ID for debugging
        });
    }
});


</script>
<!-- Add this script to handle video insertion and dynamic display -->
<!-- Add this script to handle video insertion and dynamic display -->
<script>
    // Lorsque le formulaire d'insertion de vidéo est soumis
    $('#videoForm').submit(function (event) {
        event.preventDefault();

        // Récupérer les données du formulaire
        var formData = new FormData(this);

        // Envoyer les données au serveur via Ajax
        $.ajax({
            type: 'POST',
            url: 'insertVideo.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                // Traiter la réponse du serveur
                if (response !== "") {
                    // Si la réponse n'est pas vide, afficher la vidéo dynamiquement
                    var contenusContainer = $('#contenusContainer');
                    var videoHtml = '<div class="content-card">';
                    videoHtml += '<video controls><source src="' + response + '" type="video/mp4"></video>';
                    videoHtml += '<a href="javascript:void(0);" class="btn btn-primary" onclick="showVideoDetails(\'' + response + '\')">Voir plus</a>';
                    videoHtml += '</div>';
                    contenusContainer.append(videoHtml);
                }
            },
            error: function (error) {
                // Gérer les erreurs
                console.log(error);
            }
        });

        // Cacher le modal après soumission du formulaire
        $('#insertVideoModal').modal('hide');
    });

    // Function to show video details
    function showVideoDetails(videoSrc) {
        // Implement the logic to display video details as needed
        // You can open a modal or update a specific section on the page
        console.log("Video source:", videoSrc);
    }
</script>

