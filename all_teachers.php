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

</style>  

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
          <a href="services/islam.php">Enseignement</a>
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


<?php
include('config.php');

// Fetch all teachers with country names
$allTeachersSql = "SELECT enseignant.*, country.country_name 
                   FROM enseignant 
                   JOIN country ON enseignant.country_id = country.country_id";
$allTeachersResult = $conn->query($allTeachersSql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Teachers</title>
    <!-- Add any necessary styles or scripts here -->
</head>
<body>
    <h2>Enseignants</h2>

    <?php
    if ($allTeachersResult->num_rows > 0) {
        while ($teacher = $allTeachersResult->fetch_assoc()) {
            echo '<div class="teacher-card">';
            echo '<img src="' . $teacher['photo'] . '" alt="Teacher Photo">';
            echo '<p><i class="location-icon fas fa-map-marker-alt"></i>' . $teacher['country_name'] . '</p>';
            echo '<h3>' . $teacher['name'] . '</h3>';
            echo '<p>' . $teacher['introduction'] . '</p>';
            // Add other teacher information you want to display
            echo '</div>';
        }
    } else {
        echo '<p>No teachers available at the moment.</p>';
    }
    ?>
</body>
</html>
