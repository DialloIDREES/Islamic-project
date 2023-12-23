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

// Vérifiez si l'utilisateur est un enseignant
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
          <a href="#">Accueil</a>
      </div>

      <div class="item">
          <a href="services/quran/coran.php">Coran</a>
      </div>

      
     <!-- Dans le fichier index.php -->
<div class="item">
    <?php
    if (isset($_SESSION['user_id']) && $enseignantResult->num_rows > 0) {
        // If the user is an enseignant, link to the personalized Enseignant.php page
        echo '<a href="enseignant.php?id=' . $_SESSION['user_id'] . '">Enseignement</a>';
    } else {
        // If the user is an etudiant or not logged in, link to the default Enseignement page
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
        echo '<a href="forum.php">Forum</a>';
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
    <style>
     
    </style>
    <body>
      
      <div id="cookie-banner" style="display: none;">
        <div id="cookie-content">
          <p>Ce site utilise des cookies pour améliorer votre expérience. Acceptez-vous?</p>
          <button id="accept-cookies">Accepter</button>
        </div>
      </div>
      <div class="container">
      <div class="welcome-container">
        <div class="welcome-text">
          <h2>In The Name Of Allah, <br> The Creator Of The <br>Universe</h2>
          <p class="welcome-paragraph">
            Bienvenue sur Masjid, une plateforme dédiée à l'apprentissage de l'Islam où tu peux trouver une grande variété de cours et de ressources pour t'aider à approfondir ta connaissance de ta religion.
          </p>

        </div>
        <?php
// Check if the user is logged in and is an etudiant
if (isset($_SESSION['user_id']) && $etudiantResult->num_rows > 0) {
    // Fetch the URL for the etudiant mail page
    $etudiantMailPageUrl = "mail_page_etudiant.php"; // Change this URL to the actual URL for the etudiant mail page

    echo '<a href="' . $etudiantMailPageUrl . '">
            <div id="messageBoxIcon">
                <i class="fas fa-envelope"></i>
                <span id="messageNotification" class="message-notification"></span>
            </div>
          </a>';
}
?>


<div id="messageBoxPopup" class="popup-container" style="display: none;">
    <h3 class="popup-title">Messages</h3>
    <div id="messageBoxContent" class="message-box-content"></div>
    <span onclick="closeMessageBox()" class="popup-close">&times;</span>
</div>

        <div class="image-container">
          <img src="img/sudjud.png" alt="Masjid image" class="welcome-image">
        </div>
  
      </div>

      <div class="about-masjid">
        <div class="masjid-image">
           <img src="img/white-masjid.jpg" alt="" class="about-image">
        </div>
        <div class="abt-desc">
          <div class="sec-tl">
            <span class="theme-clr">Notre Histoire</span>
            <img src="img/pshape.png" alt="">
          </div>
          <p itemprop="description">Masjid est une plateforme en ligne dédiée à l'enseignement de l'Islam. Nous proposons une large gamme de cours et de ressources pour aider les apprenants à approfondir leur connaissance de la religion musulmane.</p>
          <p>Que vous soyez débutant ou confirmé, vous trouverez sur Masjid des cours adaptés à votre niveau, ainsi que des outils pour vous aider à mieux comprendre les textes sacrés et à pratiquer votre foi au quotidien. Nous sommes convaincus que l'apprentissage de l'Islam est un processus continu et que chacun doit avoir accès à des ressources de qualité pour progresser dans sa connaissance de la religion.</p>
          <a href="services/whatabout.html"><button>En Savoir Plus</button></a>
        </div>
      </div>
      
      <div class="content"> 
        <h2 class="prayer-h2">Heure de prière</h2>
        <div class="prayer-timing-container">
          <div class="left-side">
            <div class="bg-image-container">
              <h3 class="prayer-name"></h3>
              <div class="date-container">
                <span class="islamic-date"></span>
                <span class="christian-date"></span>
              </div>
              <div class="next-prayer-container">
                <h3>Prochaine Prière:</h3>
                <div class="next-prayer-details">
                  <span class="next-prayer-name"></span>
                  <span class="next-prayer-time"></span>
                  <div class="user-location">
  <span class="user-city"></span>, <span class="user-country"></span>
</div>

                </div>
              </div>
            </div>

          </div>
          <div class="right-side">
            <div class="salah-container">
              <div class="salah">
              <img src="img/man-salah.png" alt="Salah Time" />
            </div>
            
              <h3>Prières</h3>
              <img src="img/pshape.png" alt="">
              <ul>
                <li>Fadjr</li>
                <li>Lever du soleil</li>
                <li>Dhuhr</li>
                <li>Asar</li>
                <li>Maghrib</li>
                <li>Isha</li>
              </ul>
            </div>
            <div class="azan-time-container">
              <div class="azan">
              <img src="img/clock.png" alt="Azan Logo" />
            </div>
              <h3>Heure de prière</h3>
              <img src="img/pshape.png" alt="">
              <ul>
                <li>05:34 am</li>
                <li>06:48 am</li>
                <li>13:07 pm</li>
                <li>17:15 pm</li>
                <li>07:26 pm</li>
                <li>08:37 pm</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="about-islam">
        <div class="video-container">
          <video  controls src="video/discover-more.mp4  " frameborder="0" allowfullscreen muted></video>
          <div class="play-icon"></div>
        </div>
      
        <div class="discover-more">
          <h1>En savoir plus  <br> sur l'islam,</h1>
          <p class="discover-paragraph">L'un des piliers de l'islam est la prière, qui est effectuée cinq fois par jour. Les musulmans suivent également un code moral strict qui les encourage à être honnêtes, justes et généreux envers les autres.</p> 
          <a href="services/islam.html"><button>Découvrir plus</button></a>
        </div>
      </div>
      <div class="services-container">
        <div class="service-title">
          <img src="img/Masjid-img.png" alt="Your logo">
          <h2>Services que Nous Offrons</h2>
        </div>
        <div class="service-text">
          <p>Voici un brief description de nos services:</p>
        </div>
        <div class="services-offered-container">
          <div class="service-box">
            <span class="service-box-quran">
            <div class="quran">
              <img src="img/quran.png" alt="Apprentissage du Coran">
            </div>
            <h2>Études coraniques</h2>
            <p>
              Notre programme d'études coraniques offre un cursus complet conçu pour fournir aux étudiants une compréhension approfondie du Coran et de ses enseignements. Les étudiants apprendront l'arabe, le tafsir et la mémorisation des versets coraniques. 
              <span class="read-more-text" style="display: none">
                Ils acquerront également une compréhension du contexte historique du Coran et de sa pertinence dans les temps contemporains. Nos instructeurs expérimentés sont dévoués à aider les étudiants à développer une forte connexion avec le Coran et l'islam.
              </span>
            </p>
            <span class="read-more-btn">Lire la suite...</span>
          </span>
          </div>
          <div class="service-box">
            <span class="service-box-masjid">
            <div class="masjid">
              <img src="img/mosquee.png" alt="Éducation islamique">
            </div>
            <h2>Éducation islamique</h2>
            <p>
              Notre programme d'éducation islamique offre aux étudiants une compréhension approfondie de l'islam et de ses enseignements. Le programme couvre une gamme de sujets. 
              <span class="read-more-text" style="display: none">
                Les étudiants apprendront également sur le Prophète Muhammad et sa vie, ainsi que sur les contributions des savants musulmans à travers l'histoire. Notre objectif est d'aider les étudiants à développer une compréhension solide de l'islam et de sa place dans le monde d'aujourd'hui.
              </span>
            </p>
            <span class="read-more-btn">Lire la suite...</span>
          </span>
          </div>
          <div class="service-box">
            <span class="service-box-quiz">
            <img src="img/quiz.png" alt="Études islamiques">
            <h2>Forum</h2>
            <p>
            Dans l'écosystème dynamique d'une plateforme d'apprentissage de l'Islam, le service de forum émerge comme un espace essentiel favorisant l'interaction, la communication, et le partage de connaissances au sein de la communauté éducative. 
              <span class="read-more-text" style="display: none">
              Le forum offre un lieu virtuel où les étudiants, et les passionnés peuvent se réunir pour discuter, poser des questions, et échanger des idées. C'est une agora numérique où la diversité des perspectives et des expériences se rencontre pour créer une communauté engagée.

              </span>
            </p>
            <span class="read-more-btn">Lire la suite...</span>
          </span>
          </div>
        </div>
          
       <div class="card-recom">
    <h2>Enseignants recommandés</h2>

    <?php
    include('config.php');

// Display recommended teachers for etudiant
if ($etudiantResult->num_rows > 0) {
    $etudiant = $etudiantResult->fetch_assoc();
    $etudiantCountryId = $etudiant['country_id'];

    // Fetch recommended enseignants based on the etudiant's country_id
    $recommendedTeachersSql = "SELECT * FROM enseignant WHERE country_id = ? AND user_id != ?";
    $recommendedTeachersStmt = $conn->prepare($recommendedTeachersSql);
    $recommendedTeachersStmt->bind_param("ii", $etudiantCountryId, $user_id);
    $recommendedTeachersStmt->execute();
    $recommendedTeachersResult = $recommendedTeachersStmt->get_result();

    $countryId = $etudiant['country_id'];
    $countryQuery = $conn->prepare("SELECT country_name FROM country WHERE country_id = ?");
    $countryQuery->bind_param('i', $countryId);
    $countryQuery->execute();
    $countryResult = $countryQuery->get_result();
    $country = $countryResult->fetch_assoc();
    $countryQuery->close();

    // Display recommended teachers
    if ($recommendedTeachersResult->num_rows > 0) {
        while ($teacher = $recommendedTeachersResult->fetch_assoc()) {
            echo '<div class="teacher-card">';
            echo '<img src="' . $teacher['photo'] . '" alt="Teacher Photo">';
            echo '<h3>' . $teacher['name'] . '</h3>';
            echo '<p><i class="location-icon fas fa-map-marker-alt"></i>' . $country['country_name'] . '</p>';
            echo '<p>' . $teacher['introduction'] . '</p>';
            // Inside your PHP loop that generates teacher cards
            echo '<button class="discussion-button" onclick="openMessagePopup(' . $teacher['user_id'] . ', \'' . $teacher['name'] . '\', \'' . $teacher['photo'] . '\')">Discuter</button>';
            echo '<button class="voir-contenu-button" onclick="voirContenu(' . $teacher['user_id'] . ')">Voir contenu</button>';
            echo '</div>';
        }
        echo '<button onclick="window.location.href=\'all_teachers.php\'">Voir plus</button>';
    } else {
        echo '<p>No teachers recommended for you at the moment.</p>';
    }

    $recommendedTeachersStmt->close();
}
$etudiantStmt->close();
$enseignantStmt->close();
?>
</div>


    <section aria-labelledby="contact">

<div class="contact-wrapper">
  <div class="contact-side reach-us">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d19081.799519048796!2d-17.451641208586665!3d14.744742794036505!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xec10d47189bf1b7%3A0xf86b3a161deb6130!2sMaristes!5e0!3m2!1sfr!2ssn!4v1700852897245!5m2!1sfr!2ssn" width="750" height="550" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>

  <div class="contact-side contact-me">
    <h2 id="contact" style="text-align: center;">Contactez-nous </h2>
    <form onsubmit="return validateForm(event)">
      <label for="name" placeholder></label>
      <input type="text" id="name" name="name" required="required" placeholder="Nom">
      <label for="email"></label>
      <input id="email_id" type="email" name="email" required="required" placeholder="Email">
      <label for="subject"></label>
      <input  type="text" id="subject" name="subject" required="required" placeholder="Sujet">
      <label for="message"></label>
      <textarea  id="message" name="message" required style="resize: none;" placeholder="Message"></textarea>
      <button onclick="SendMail(event)">Envoyer</button>
    </form>
  </div>
 
      </section>
    </div>
    </div>
</div>
    </body>


</section>
</div>
</body>
<div class="footer-img">
                <img src="../img/footer-img.png" alt="">
                </div>
          
          
                <footer>
                  <div class="footer-container">
                    <div class="container">
                      <div class="main-footer-logo">
                        <img src="../img/Masjid-img-icon.png" alt="Logo">
                        <span class="site-name"><h2>Masjid</h2></span>
                      </div>
                      <p class="footer-p">Masjid est une plateforme en ligne dédiée à l'enseignement de l'Islam. Nous proposons une large gamme de cours et de ressources pour aider les apprenants à approfondir leur connaissance de la religion musulmane.</p>
                    </div>
                
                    <div class="container">
                      <h4>Services</h4>
                      <ul>
                       <a href="islam.html"><li>Enseignement</li></a>
                        <li href="#">A propos de Masjid</li>
                        <a href="quran.html"><li>Coran</li></a>
                        <a href="quiz.html"><li> Quiz</li></a>


                       
                      </ul>
                    </div>
                
                    <div class="container">
                      <h4>Contact</h4>
                      <ul>
                        <li>Email: masjidmosque@gmail.com</li>
                        <li>Phone: 77-647-13-89</li>
                        <li>Address: Dakar, Maristes, Senegal</li>
                      </ul>
                    </div>
                
                 
                
                  <div class="bottom-bar">
                    <p>&copy; 2023 Masjid. All rights reserved.</p>
                  </div>
                </footer> 
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js
  "></script>
  <script src="https://cdn.emailjs.com/dist/email.min.js"></script>

  <script type="text/javascript">
   
            (function(){
    emailjs.init("hSEbsNPr_y8a-1IdP");
  })();
        
</script>
</html>
    
      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js
    "></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
(function(){
  emailjs.init("hSEbsNPr_y8a-1IdP");
})();
</script>
<script>
function openMessagePopup(receiverId, teacherName, teacherPhoto) {
    // Customize the popup UI using HTML and CSS
    var popupContent = `
        <div id="messagePopup" class="popup-container">
            <h3 class="popup-title">${teacherName}</h3>
            <input id="subjectMessage" placeholder="Subject" type="text"> <!-- Updated id -->
            <textarea id="messageContent" class="popup-textarea" placeholder="Type your message"></textarea>
            <button onclick="sendMessage(${receiverId})" class="popup-button">Send</button>
            <span onclick="closeMessagePopup()" class="popup-close">&times;</span>
        </div>
    `;

    // Append the popup content to the body
    var popupContainer = document.createElement('div');
    popupContainer.innerHTML = popupContent;
    document.body.appendChild(popupContainer);
}

function sendMessage(receiverId) {
    var subject = document.getElementById('subjectMessage').value; // Updated id
    var messageContent = document.getElementById('messageContent').value;

    // Use AJAX or fetch to send the message to sendMessage.php
    // You may need to customize this part based on your setup
    // Example using fetch:
    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `receiver_id=${receiverId}&subject=${subject}&message_content=${encodeURIComponent(messageContent)}`,
    })
    .then(response => response.json())
    .then(data => {
        // Handle the response, if needed
        console.log(data);
    });

    // Close the popup after sending the message
    closeMessagePopup();
}

function closeMessagePopup() {
    // Close the popup by removing the container element
    var popupContainer = document.getElementById('messagePopup');
    if (popupContainer) {
        popupContainer.remove();
    }
}
// Add this JavaScript to your existing script or in a separate script file

function toggleMessageBox() {
    var messageBoxPopup = document.getElementById('messageBoxPopup');
    messageBoxPopup.style.display = messageBoxPopup.style.display === 'none' ? 'block' : 'none';
}

function closeMessageBox() {
    document.getElementById('messageBoxPopup').style.display = 'none';
}

// Function to update the message notification count
function updateMessageNotification(count) {
    var notificationElement = document.getElementById('messageNotification');
    if (count > 0) {
        notificationElement.style.display = 'block';
        notificationElement.innerText = count;
    } else {
        notificationElement.style.display = 'none';
    }
}

// Fetch and display messages in the message box content
function fetchAndDisplayMessages() {
    // Use AJAX or fetch to get messages from the server
    // Update the #messageBoxContent with the fetched messages
    // You may need to customize this part based on your setup
    // Example using fetch:
    fetch('fetch_messages.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        // Update the message box content with the fetched messages
        var messageBoxContent = document.getElementById('messageBoxContent');
        messageBoxContent.innerHTML = ''; // Clear existing content

        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(message => {
                var messageItem = document.createElement('div');
                messageItem.innerHTML = `
                    <div class="message-item">
                        <strong>${message.sender}</strong>
                        <p>${message.content}</p>
                    </div>
                `;
                messageBoxContent.appendChild(messageItem);
            });
        } else {
            messageBoxContent.innerHTML = '<p>No messages found.</p>';
        }
    })
    .catch(error => {
        console.error('Error fetching messages:', error);
    });
}

// Fetch and display messages when the page loads
fetchAndDisplayMessages();

</script>


<style>
    /* Add your custom styles for the popup */
    body.popup-open {
        overflow: hidden; /* Prevent scrolling when popup is open */
    }

    .popup-container {
        position: fixed;
        top: 10px; /* Adjusted top position to be near the top */
        right: 10px; /* Adjusted right position to be on the right side */
        background: rgba(255, 255, 255, 0.8); /* Semi-transparent background with decreased opacity */
        backdrop-filter: blur(5px); /* Apply backdrop filter for blur effect */
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 999;
        width: 400px; /* Adjusted width */
        max-width: 80%; /* Added max-width for responsiveness */
        
    }

    .popup-title {
        font-size: 20px; /* Increased font size */
        margin-bottom: 10px; /* Added margin for spacing */
    }

    #subject {
        width: 100%;
        margin-bottom: 10px;
    }

    .popup-textarea {
        width: 100%;
        height: 200px; /* Increased height */
        margin-bottom: 10px; /* Added margin for spacing */
        resize: none;
    }

    .popup-button {
        background-color: #4caf50;
        color: #fff;
        padding: 10px 16px; /* Adjusted padding */
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        cursor: pointer;
    }
</style>


    
      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js
    "></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js
  "></script>
  <script src="https://cdn.emailjs.com/dist/email.min.js"></script>

  <script type="text/javascript">
   
            (function(){
    emailjs.init("hSEbsNPr_y8a-1IdP");
  })();
        
</script>
<script>
    // Function to redirect to enseignant.php with teacher_id parameter
    function voirContenu(teacherId) {
        window.location.href = 'enseignant.php?id=' + teacherId;
    }
</script>

</body>

</html>