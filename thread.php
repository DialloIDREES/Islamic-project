<?php

include('config.php');
session_start();

$threadId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch thread details using prepared statement
$threadQuery = $conn->prepare("SELECT threads.*, users.username, languages.language_name
    FROM threads 
    JOIN users ON threads.user_id = users.id 
    LEFT JOIN languages ON threads.language_id = languages.id
    WHERE threads.id = ?");
$threadQuery->bind_param('i', $threadId);
$threadQuery->execute();



// Check for errors in the query
if ($threadQuery->error) {
    die('Error in thread query: ' . $threadQuery->error);
}

$threadResult = $threadQuery->get_result();
$thread = $threadResult->fetch_assoc();
$threadQuery->close();

$repliesQuery = $conn->query("SELECT replies.*, users.username
    FROM replies 
    JOIN users ON replies.user_id = users.id 
    WHERE replies.thread_id = $threadId");
$replies = $repliesQuery->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['postReply'])) {
    postReply();
}

function postReply() {
    global $conn;
    $threadId = $_POST['threadId'];
    $content = mysqli_real_escape_string($conn, $_POST['replyContent']);
    $userId = $_SESSION['user_id'];

    $insertQuery = $conn->prepare("INSERT INTO replies (user_id, thread_id, content) VALUES (?, ?, ?)");
    $insertQuery->bind_param('iis', $userId, $threadId, $content);

    if ($insertQuery->execute()) {
        // Reply posted successfully
        header('Location: thread.php?id=' . $threadId);
        exit();
    } else {
        // Handle the error (e.g., display an error message)
        echo "Error: " . $insertQuery->error;
    }

    $insertQuery->close();
}

// Fetch threads and replies
$threads = $conn->query("SELECT * FROM threads ORDER BY created_at DESC");
// Fetch threads and usernames
$threads = $conn->query("SELECT threads.*, users.username, languages.language_name
FROM threads 
JOIN users ON threads.user_id = users.id 
LEFT JOIN languages ON threads.language_id = languages.id
ORDER BY threads.created_at DESC");

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
<title><?php echo $thread['title']; ?> - Thread</title>
    <link rel="stylesheet" href="style-cookie.css">

    <link rel="stylesheet" href="fontawesome/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css
    ">
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" async></script>
    <script src="cookie.js" async=""></script>
    <link rel="stylesheet" href="names.css">


    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container-forum {
    max-width: 1200px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.forum-nav {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.nav-button {
    padding: 10px 15px;
    background-color: #3498db;
    color: #fff;
    border: none;
    cursor: pointer;
}

.language-buttons {
    margin-bottom: 10px;
}

.lang-button {
    margin-right: 10px;
    padding: 5px 10px;
    background-color: #2ecc71;
    color: #fff;
    border: none;
    cursor: pointer;
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


#search-bar button:hover {
    border-color: #007bff; /* Change the border color on hover */
}


#createThreadForm {
    display: none;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.threads-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    max-width: 1200px;
    margin: 20px auto;
}

.thread-card {
    width: 100%;
    max-width: 300px;
    margin: 10px;
    padding: 10px;
    background: #fff;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.thread-card:hover {
    transform: scale(1.05);
}

.thread-card img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 10px;
}

.thread-info h2 {
    color: #333;
    margin-bottom: 10px;
}

.thread-content p {
    color: #555;
}

.reply-form {
    margin-top: 15px;
}

.reply-form textarea {
    height: 80px;
}

.reply {
    background-color: #f5f5f5;
    padding: 10px;
    margin-top: 10px;
    border-radius: 4px;
}

.user-info {
    font-weight: bold;
}

hr {
    border: 1px solid #ddd;
    margin: 20px 0;
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
.quran-search button:hover {
    background-color: #08bd60;
}
/* styles pour la liste des chapitres */
.quran-chapters h2 {
    font-size: 24px;
    margin-top: 0;
    margin-bottom: 10px;
}


    </style>

    <style>
        /* Modal styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-content label,
.modal-content select {
    display: block;
    margin-bottom: 10px;
}

.modal-content input,
.modal-content textarea,
.modal-content select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
}

.modal-content button {
    background-color: #4CAF50;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content button:hover {
    background-color: #45a049;
}

/* Close button inside the modal */
.modal-content button.close-btn {
    background-color: #d9534f;
}

.modal-content button.close-btn:hover {
    background-color: #c9302c;
}

/* Style for the navigation button */
.nav-button {
    background-color: #3498db;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.nav-button:hover {
    background-color: #2980b9;
}

/* Style for other elements in your forum */
/* Add your existing styles for topic cards, language buttons, etc. */

/* Modal styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-content label,
.modal-content select {
    display: block;
    margin-bottom: 10px;
}

.modal-content input,
.modal-content textarea,
.modal-content select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
}

.modal-content button {
    background-color: #4CAF50;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content button:hover {
    background-color: #45a049;
}

/* Close button inside the modal */
.modal-content button.close-btn {
    background-color: #d9534f;
}

.modal-content button.close-btn:hover {
    background-color: #c9302c;
}

/* Style for the navigation button */
.nav-button {
    background-color: #3498db;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.nav-button:hover {
    background-color: #2980b9;
}

.thread-background: {
    background-color: #f9f9f9;
}

.reply-form {
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

textarea {
    width: 100%;
    height: 100px;
    padding: 8px;
    margin-bottom: 16px;
    box-sizing: border-box;
}

button {
    background-color: #4caf50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

.login-message {
    margin-top: 20px;
    color: #ff0000; /* Red color for the login message */
}
.banner-thread {
    display: block;
    margin-left: auto;
    margin-right: auto;
    max-width: 800px; /* Set your desired maximum width */
    width: 100%;
}
.banner-thread img {
    background-size: cover;
    background-position: center;
    height: 400px;
}
/* Add this to your existing styles */
.reply-card {
    border: 1px solid #ccc;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    position: relative;
}

.reply-card img {
    width: 50px; /* Adjust the size of the image */
    height: 50px;
    border-radius: 50%; /* Make the image round */
    object-fit: cover;
    position: absolute;
    top: -10px; /* Adjust the position of the image */
    left: -10px;
}

.reply-card .reply-content {
    margin-left: 60px; /* Adjust the margin to accommodate the image */
}

.reply-card .user-info {
    font-weight: bold;
}




    </style>
    
   
</head>

<header>
  <div class="container-fluid">
      <div class="logo-container">
          <img src="../img/Masjid-img.png" alt="" class="logo" alt="my-logo">
          <h1>Masjid</h1>
      </div>
  </div>

  <div class="navb-items d-none d-xl-flex">
      <div class="item">
          <a href="../index.php">Accueil</a>
      </div>

      <div class="item">
          <a href="../services/quran/coran.php">Coran</a>
      </div>

      <div class="item">
          <a href="../services/islam.php">Enseignement</a>
      </div>

      <div class="item">
          <a href="forum.php">Forum</a>
      </div>

      <?php
  

 
      if (isset($_SESSION['user_id'])) {
          // User is logged in, show the username
          $username = $_SESSION['username'];
          echo '<div class="item-button"><a href="profile.php"><i class="fa fa-user"></i> ' . $username . '</a></div>';
        } else {
         
          echo '<div class="item-button"><a href="register.html" type="button">S\'identifier</a></div>';
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

                                <a href="services/about.html" class="navb-button" type="button">À propos de nous</a>
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

    <div class="shade"></div>
  <div class="banner-thread">
  <?php if (!empty($thread['image_path'])) : ?>
            <img src="<?php echo $thread['image_path']; ?>" alt="Thread Image">
        <?php endif; ?>
  </div>
      
  <body>
    <div class="content">
      <div class="thread-background">
        <h2 class="color" class="thread-background"><?php echo $thread['title']; ?>

        </h2>


        </span></p>
  
                <p class="thread-background"><?php echo $thread['content']; ?></p>
    
    
                <div class="replies-container" id="repliesContainer">
                <div class="replies-container" id="repliesContainer">
    <h3>Replies:</h3>
    <?php foreach ($replies as $reply) : ?>
        <div class="reply-card">
            <p>Posted by: 
                <?php
                // Check if the user is an etudiant or enseignant
                $etudiantSql = "SELECT * FROM etudiant WHERE user_id = ?";
                $etudiantStmt = $conn->prepare($etudiantSql);
                $etudiantStmt->bind_param("i", $reply['user_id']);
                $etudiantStmt->execute();
                $etudiantResult = $etudiantStmt->get_result();

                $enseignantSql = "SELECT * FROM enseignant WHERE user_id = ?";
                $enseignantStmt = $conn->prepare($enseignantSql);
                $enseignantStmt->bind_param("i", $reply['user_id']);
                $enseignantStmt->execute();
                $enseignantResult = $enseignantStmt->get_result();

                if ($etudiantResult->num_rows > 0) {
                    echo '<span class="user-info"><i class="fas fa-user-graduate"></i> ' . $reply['username'] . '</span>';
                } elseif ($enseignantResult->num_rows > 0) {
                    // Enseignant icon with link
                    $teacher = $enseignantResult->fetch_assoc();
                    echo '<span class="user-info">' .
                        '<a href="enseignant.php?id=' . $teacher['user_id'] . '">' .
                        '<i class="fas fa-chalkboard-teacher"></i> ' . $reply['username'] . '</a>' .
                        '</span>';
                }
                ?>
            </p>
            <p><?php echo $reply['content']; ?></p>
            <p>Posted at: <?php echo $reply['created_at']; ?></p>
        </div>
    <?php endforeach; ?>
</div>


<!-- Reply Form -->
<?php if (isset($_SESSION['user_id'])) : ?>
    <form method="post" class="reply-form">
        <label for="replyContent">Your Reply:</label>
        <textarea id="replyContent" name="replyContent" placeholder="Type your reply here..." required></textarea>
        <input type="hidden" name="threadId" value="<?php echo $threadId; ?>">
        <button type="submit" name="postReply">Post Reply</button>
    </form>
<?php else : ?>
    <p class="login-message">Please <a href="register.html">log in</a> to post replies.</p>
<?php endif; ?>


</div>

</div> <!-- Close thread-container div -->
    </div>
    </div>
<!-- Footer Section (similar to your existing footer) -->
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
              <div class="styleHover">
            <li> <a href="services/islam.html">Enseignement</a></li>
              <li> <a href="services/about.html">A propos de Masjid </a></li>
              <li><a href="services/quran.html">Coran</a></li>
             <li> <a href="services/quiz.php"> Quiz </a> </li>

            </div>
             
            </ul>
          </div>
      
          <div class="container">
            <h4>Contact</h4>
            <ul>
              <li>Email: themasjidmosque@gmail.com</li>
              <li>Phone: 77-647-13-89</li>
              <li>Address: Guediawaye, Gadaye, Senegal</li>
            </ul>
          </div>
      
       
      
        <div class="bottom-bar">
          <p>&copy; 2023 Masjid. All rights reserved.</p>
        </div>
      </footer>
      <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>



</body>
</html>
