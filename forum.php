    <?php
    session_start();
    include('config.php'); // Database connection configuration

    if (!isset($_SESSION['user_id'])) {
        header('Location: register.html');
        exit();
    }

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['createThread'])) {
            createThread();
        } elseif (isset($_POST['postReply'])) {
            postReply();
        }
    }
    function createThread() {
        global $conn;
        $title = mysqli_real_escape_string($conn, $_POST['postTitle']);
        $content = mysqli_real_escape_string($conn, $_POST['postContent']);
        $userId = $_SESSION['user_id'];
        $imagePath = uploadImage();
        $language = $_POST['postLanguage'];
    
        // Retrieve language_id based on the selected language
        $languageResult = $conn->query("SELECT id FROM languages WHERE language_name = '$language'");
        $languageId = ($languageResult->num_rows > 0) ? $languageResult->fetch_assoc()['id'] : null;
    
        $sql = "INSERT INTO threads (user_id, title, content, image_path, language_id) 
                VALUES ('$userId', '$title', '$content', '$imagePath', '$languageId')";
        $result = $conn->query($sql);

        // Check if the user has created a thread in the last 2 minutes
    $lastThreadTime = isset($_SESSION['last_thread_time']) ? $_SESSION['last_thread_time'] : 0;
    $currentTime = time();

    if ($currentTime - $lastThreadTime < 120) {
        // Show an alert to the user
        echo json_encode(['success' => false, 'error' => 'You must wait 2 minutes before creating another thread']);
        exit;
    }

    $_SESSION['last_thread_time'] = $currentTime;
    
        if ($result) {
            // Thread created successfully
            header('Location: forum.php'); // Redirect to the forum page or wherever you want
            exit();
        } else {
            // Handle the error (e.g., display an error message)
        }
    }
    
    function uploadImage() {
        // Implement image upload logic here and return the image path
        // Make sure to validate and sanitize the uploaded image
        // Example implementation:
        $targetDir = "uploads/"; // Set the target directory where you want to store the uploaded images
        $targetFile = $targetDir . basename($_FILES["postImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        // Check if the image file is a actual image or fake image
        $check = getimagesize($_FILES["postImage"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    
    
        // Check file size
        if ($_FILES["postImage"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
    
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
    
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["postImage"]["tmp_name"], $targetFile)) {
                return $targetFile;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    

    function postReply() {
        global $conn;
        $threadId = $_POST['threadId'];
        $content = mysqli_real_escape_string($conn, $_POST['replyContent']);
        $userId = $_SESSION['user_id'];

        $sql = "INSERT INTO replies (user_id, thread_id, content) VALUES ('$userId', '$threadId', '$content')";
        $result = $conn->query($sql);

        if ($result) {
            // Reply posted successfully
            header('Location: forum.php'); // Redirect to the forum page or wherever you want
            exit();
        } else {
            // Handle the error (e.g., display an error message)
        }
    }

    // Fetch threads and replies
    $threads = $conn->query("SELECT * FROM threads ORDER BY created_at DESC");
    // Fetch threads and usernames
    $threads = $conn->query("SELECT threads.*, users.username, languages.language_name
    FROM threads 
    JOIN users ON threads.user_id = users.id 
    LEFT JOIN languages ON threads.language_id = languages.id
    ORDER BY threads.created_at DESC");
    
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
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" async></script>
    <script src="cookie.js" async=""></script>

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
    padding: 20px;
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

/* Style for other elements in your forum */
/* Add your existing styles for topic cards, language buttons, etc. */


    </style>
    
   
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

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
          <a href="quran/coran.php">Coran</a>
      </div>

      <div class="item">
          <a href="services/islam.php">Enseignement</a>
      </div>

      <div class="item">
          <a href="#">Forum</a>
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
        

<!-- Navigation -->
<nav class="forum-nav">
    <?php
    // Check if the user is logged in and is not an enseignant
    if (isset($_SESSION['user_id']) && $enseignantResult->num_rows == 0) {
        // If the user is not an enseignant, display the "Create a Thread" button
        echo '<button class="nav-button" id="createThreadBtn" onclick="openCreatePostModal()">+ Create a Thread</button>';
    }
    ?>


        <button class="nav-button" id="privacyPolicyBtn" onclick="openPrivacyPolicyModal()">Privacy Policy</button>
            <button class="nav-button" onclick="window.location.href='https://www.instagram.com/the_masjid_sn/?next=%2F'">Instagram Group</button>
    </nav>

   <!-- Language Buttons -->
<div class="language-buttons">
    <?php
    // Fetch counts for each language
    $languageCounts = $conn->query("SELECT language_name, COUNT(*) AS count FROM threads
                                    LEFT JOIN languages ON threads.language_id = languages.id
                                    GROUP BY language_name");

    // Create language buttons dynamically
    while ($languageCount = $languageCounts->fetch_assoc()) : ?>
        <button class="lang-button" data-lang="<?php echo strtolower($languageCount['language_name']); ?>">
            <?php echo $languageCount['language_name']; ?>  (<span id="<?php echo strtolower($languageCount['language_name']); ?>Count">
                <?php echo $languageCount['count']; ?></span>)
        </button>
    <?php endwhile; ?>
</div>

    <div class="element-background">
                    <div class="quran-container">
                    <div class="quran-search">
    <div id="resultat"></div>
    <input type="text" id="search-input" placeholder="Rechercher un sujet, une langue, utilisateur...">
    <button type="button" id="search-button">Rechercher</button>
</div>


  
    <!-- Topic Cards -->
    <div class="topic-cards">
        <!-- Display topics dynamically using JavaScript/PHP -->
    </div>

    <hr>

<!-- Create Post Form -->
<div class="modal" id="createPostModal">
    <div class="modal-content">
        <form method="post" class="create-post-form" enctype="multipart/form-data" id="createPostForm">
            <h2>Create a new post</h2>
            <label for="postTitle">Title:</label>
            <input type="text" id="postTitle" name="postTitle" required>

            <label for="postContent">Content:</label>
            <textarea id="postContent" name="postContent" required></textarea>

            <label for="postImage">Image:</label>
            <input type="file" id="postImage" name="postImage">

            <label for="postLanguage">Language:</label>
            <select id="postLanguage" name="postLanguage">
                <?php
                // Fetch languages from the database
                $languageQuery = $conn->query("SELECT id, language_name FROM languages");
                while ($language = $languageQuery->fetch_assoc()) {
                    echo "<option value='{$language['language_name']}'>{$language['language_name']}</option>";
                }
                ?>
            </select>

            <button type="submit" name="createThread">Create Thread</button>
        </form>
        <button onclick="closeCreatePostModal()">Close</button>
    </div>
</div>


    </div>
</div>
<!-- Privacy Policy Modal -->
<div class="modal" id="privacyPolicyModal">
    <div class="modal-content">
        <button class="close-button" onclick="closePrivacyPolicyModal()">Fermer</button>
        <center><img src="../img/Masjid-img-icon.png" alt="" width="150px"></center>
        <h2>Masjid Politique de Confidentialité</h2>
        <h3>Introduction</h3>
        <p>Cette politique décrit les informations que nous collectons lorsque vous utilisez l'application Masjid. Il fournit également des informations sur la manière dont nous stockons, transférons, utilisons et supprimons ces informations, ainsi que sur les choix dont vous disposez concernant ces informations.
            Cette politique s'applique lorsque nous agissons en tant que contrôleur de données en ce qui concerne les données personnelles des utilisateurs de nos services ; en d'autres termes, où nous déterminons les finalités et les moyens du traitement de ces données personnelles. Pour le contenu et les données que vous téléchargez ou rendez disponibles via le Service (« Contenu utilisateur »), vous êtes responsable de vous assurer que ce contenu est conforme à nos Conditions d'utilisation et qu'il ne viole pas la vie privée des autres utilisateurs.
        </p>

        <h3>Collecte d'informations</h3>
        <p>Nous recueillons des informations lorsque vous créez un compte sur notre plateforme. Cela peut inclure votre nom, votre adresse e-mail, et d'autres informations pertinentes.</p>

        <h3>Utilisation des informations</h3>
        <p>Les informations que nous recueillons sont utilisées pour personnaliser votre expérience et pour fournir des services que vous avez demandés.</p>

        <!-- Include the full privacy policy text here in French -->
        <p>...</p>
    </div>
</div>


<div class="threads-container">
    <?php while ($thread = $threads->fetch_assoc()) : ?>
        <div class="thread-card thread-<?php echo $thread['id']; ?>" data-language="<?php echo strtolower($thread['language_name']); ?>">
            <?php if (!empty($thread['image_path'])) : ?>
                <img src="<?php echo $thread['image_path']; ?>" alt="Thread Image">
            <?php endif; ?>
            <div class="thread-info">
                <h2><?php echo $thread['title']; ?></h2>
                <div class="thread-content">
                    <p><?php echo $thread['content']; ?></p>
                </div>
                <p>Created by: <span class="user-info"><?php echo $thread['username']; ?></span></p>

                <p>Created at: <?php echo $thread['created_at']; ?></p>
                <p style="color: <?php echo getColorForLanguage($thread['language_name']); ?>">
                    Language: <?php echo $thread['language_name']; ?>
                </p>
            </div>

            <!-- Link to the dedicated thread page -->
            <a href="thread.php?id=<?php echo $thread['id']; ?>">View Thread</a>

            <?php
                // Check if the current user is the creator of the thread
                if (isset($_SESSION['user_id']) && $thread['user_id'] !== $_SESSION['user_id']) {
                    // Display the "Signaler" button for users other than the creator
                    echo '<button class="signal-thread-btn" data-thread-id="' . $thread['id'] . '">Signaler</button>';
                }
            ?>

<?php
    // Check if the current user is the creator of the thread
    if (isset($_SESSION['user_id']) && $thread['user_id'] === $_SESSION['user_id']) {
        // Display the "Delete" button for the thread creator
        echo '<button class="delete-thread-btn" data-thread-id="' . $thread['id'] . '">Delete</button>';
    }
?>

        </div>
    <?php endwhile; ?>
</div>

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

      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js
    "></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Function to open the create post modal
function openCreatePostModal() {
    document.getElementById('createPostModal').style.display = 'block';
}

// Function to close the create post modal
function closeCreatePostModal() {
    document.getElementById('createPostModal').style.display = 'none';
}

// Event listener for the create post button
document.getElementById('createPostBtn').addEventListener('click', openCreatePostModal);

document.addEventListener('DOMContentLoaded', function () {
    // Set up event listeners for language buttons
    const filterButtons = document.querySelectorAll('.lang-button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const lang = this.dataset.lang;
            filterThreadsByLanguage(lang);
        });
    });




    // Initial filter with the first language selected
    const initialLang = document.querySelector('.lang-button').dataset.lang;
    filterThreadsByLanguage(initialLang);
});

function filterThreadsByLanguage(language) {
    // Hide all threads
    const allThreads = document.querySelectorAll('.thread-card');
    allThreads.forEach(thread => {
        thread.style.display = 'none';
    });

    // Show threads based on the selected language
    if (language === 'all') {
        // Show all threads
        allThreads.forEach(thread => {
            thread.style.display = 'block';
        });
    } else {
        // Show threads for the selected language
        const languageThreads = document.querySelectorAll(`.thread-card[data-language="${language}"]`);
        languageThreads.forEach(thread => {
            thread.style.display = 'block';
        });
    }
}

<?php
function getColorForLanguage($language)
{
    // Define colors for each language
    $languageColors = [
        'all' => '#3498db',     // Color for All
        'english' => '#2ecc71', // Color for English
        'french' => '#e74c3c',  // Color for French
        // Add more colors for other languages as needed
    ];

    // Convert language to lowercase
    $languageLower = strtolower($language);

    // Check if the language has a predefined color, default to black if not found
    return isset($languageColors[$languageLower]) ? $languageColors[$languageLower] : '#000000';
}
?>



    </script>
    <script>
    // Function to open the privacy policy modal
    function openPrivacyPolicyModal() {
        document.getElementById('privacyPolicyModal').style.display = 'block';
    }

    // Function to close the privacy policy modal
    function closePrivacyPolicyModal() {
        document.getElementById('privacyPolicyModal').style.display = 'none';
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        $("#search-button").click(function () {
            var searchTerm = $("#search-input").val();
            
            $.ajax({
                url: "search.php",
                method: "POST",
                data: { searchTerm: searchTerm },
                success: function (response) {
                    $(".topic-cards").html(response);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('.signal-thread-btn').on('click', function () {
            var threadId = $(this).data('thread-id');

            // Call the PHP script to handle the signaling
            $.ajax({
                type: 'POST',
                url: 'signalThread.php', // Create this file to handle signaling
                data: { threadId: threadId },
                success: function (response) {
                    // Handle success (e.g., update UI)
                    console.log(response);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
    });
</script>
<script>
    // Add this script at the end of your HTML file, before the </body> tag

    document.addEventListener('DOMContentLoaded', function () {
        // Attach click event to delete buttons
        var deleteButtons = document.querySelectorAll('.delete-thread-btn');
        
        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var threadId = button.getAttribute('data-thread-id');
                
                // Display SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User confirmed, send AJAX request to delete thread
                        deleteThread(threadId);
                    }
                });
            });
        });

        function deleteThread(threadId) {
            // Send AJAX request to delete thread
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_thread.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Thread deleted successfully, refresh the page or handle as needed
                        location.reload();
                    } else {
                        // Handle error
                        console.error('Error deleting thread');
                    }
                }
            };
            xhr.send('threadId=' + encodeURIComponent(threadId));
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</body>

</html>