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
    .card {
    background: #fff;
    transition: .5s;
    border: 0;
    margin-bottom: 30px;
    border-radius: .55rem;
    position: relative;
    width: 100%;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
}
.chat-app .people-list {
    width: 280px;
    position: absolute;
    left: 0;
    top: 0;
    padding: 20px;
    z-index: 7
}
.row {
    height: 800px; /* Adjust the height as needed */
}
@media only screen and (min-width: 992px) {
        .row {
            height: 800px; /* Adjust the height as needed */
        }
    }
.chat-app .chat {
    margin-left: 280px;
    border-left: 1px solid #eaeaea
}

.people-list {
    -moz-transition: .5s;
    -o-transition: .5s;
    -webkit-transition: .5s;
    transition: .5s
}

.people-list .chat-list li {
    padding: 10px 15px;
    list-style: none;
    border-radius: 3px
}

.people-list .chat-list li:hover {
    background: #efefef;
    cursor: pointer
}

.people-list .chat-list li.active {
    background: #efefef
}

.people-list .chat-list li .name {
    font-size: 15px
}

.people-list .chat-list img {
    width: 45px;
    border-radius: 50%
}

.people-list img {
    float: left;
    border-radius: 50%
}

.people-list .about {
    float: left;
    padding-left: 8px
}

.people-list .status {
    color: #999;
    font-size: 13px
}

.chat .chat-header {
    padding: 15px 20px;
    border-bottom: 2px solid #f4f7f6
}

.chat .chat-header img {
    float: left;
    border-radius: 40px;
    width: 40px
}

.chat .chat-header .chat-about {
    float: left;
    padding-left: 10px
}

.chat .chat-history {
    padding: 20px;
    border-bottom: 2px solid #fff
}

.chat .chat-history ul {
    padding: 0
}

.chat .chat-history ul li {
    list-style: none;
    margin-bottom: 30px
}

.chat .chat-history ul li:last-child {
    margin-bottom: 0px
}

.chat .chat-history .message-data {
    margin-bottom: 15px
}

.chat .chat-history .message-data img {
    border-radius: 40px;
    width: 40px
}

.chat .chat-history .message-data-time {
    color: #434651;
    padding-left: 6px
}

.chat .chat-history .message {
    color: #444;
    padding: 18px 20px;
    line-height: 26px;
    font-size: 16px;
    border-radius: 7px;
    display: inline-block;
    position: relative
}

.chat .chat-history .message:after {
    bottom: 100%;
    left: 7%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #fff;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .my-message {
    background: #efefef
}

.chat .chat-history .my-message:after {
    bottom: 100%;
    left: 30px;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #efefef;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .other-message {
    background: #e8f1f3;
    text-align: right
}

.chat .chat-history .other-message:after {
    border-bottom-color: #e8f1f3;
    left: 93%
}

.chat .chat-message {
    padding: 20px
}

.online,
.offline,
.me {
    margin-right: 2px;
    font-size: 8px;
    vertical-align: middle
}

.online {
    color: #86c541
}

.offline {
    color: #e47297
}

.me {
    color: #1d8ecd
}

.chat-history {
            display: none; /* Hide chat history initially */
        }

.float-right {
    float: right
}

.clearfix:after {
    visibility: hidden;
    display: block;
    font-size: 0;
    content: " ";
    clear: both;
    height: 0
}

@media only screen and (max-width: 767px) {
    .chat-app .people-list {
        height: 465px;
        width: 100%;
        overflow-x: auto;
        background: #fff;
        left: -400px;
        display: none
    }
    .chat-app .people-list.open {
        left: 0
    }
    .chat-app .chat {
        margin: 0
    }
    .chat-app .chat .chat-header {
        border-radius: 0.55rem 0.55rem 0 0
    }
    .chat-app .chat-history {
        height: 300px;
        overflow-x: auto
    }
}

@media only screen and (min-width: 768px) and (max-width: 992px) {
    .chat-app .chat-list {
        height: 650px;
        overflow-x: auto
    }
    .chat-app .chat-history {
        height: 600px;
        overflow-x: auto
    }
}

@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 1) {
    .chat-app .chat-list {
        height: 480px;
        overflow-x: auto
    }
    .chat-app .chat-history {
        height: calc(100vh - 350px);
        overflow-x: auto
    }
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

      <div class="item">
          <a href="services/islam.php">Enseignement</a>
      </div>
<!-- Modify the Forum link to conditionally render based on the user's role -->
<div class="item">
    <?php
    // Check if the user is logged in and is an enseignant
    if (isset($_SESSION['user_id']) && $enseignantResult->num_rows > 0) {
        // If the user is an enseignant, link to the Mailpage.php
        echo '<a href="mailpage.php">Mail</a>';
    } else {
        // If the user is an etudiant or not logged in, link to the Forum
        echo '<a href="login/forum.php">Forum</a>';
    }
    ?>
</div>

      <?php
 

 
      if (isset($_SESSION['user_id'])) {
          // User is logged in, show the username
          $username = $_SESSION['username'];
          echo '<div class="item-button"><a href="login/profile.php"><i class="fa fa-user"></i> ' . $username . '</a></div>';
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
    <style>
     
    </style>
    <body>
      
    


</body>

<div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                <div class="people-list">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Search...">
        </div>
        <?php
        while ($message = $messagesResult->fetch_assoc()) {
            echo '<div class="message">';
            while ($etudiant = $etudiantsResult->fetch_assoc()) {
                echo '<ul class="list-unstyled chat-list mt-2 mb-0">';
                echo '<li class="clearfix" data-etudiant-id="' . $etudiant['user_id'] . '">';
                echo '<img src="' . $etudiant['photo_path'] . '" alt="avatar" class="etudiant-photo">';
                echo '<div class="about">';
                echo '<div class="name">' . $etudiant['name'] . '</div>';
                echo '<div class="status"> <i class="fa fa-circle online"></i> Online </div>';
                echo '</div>';
                echo '<div class="message-text" style="display: none;">' . $message['message_text'] . '</div>';
                echo '</li>';
                echo '</ul>';
            }
            echo '</div>';
        }
        ?>
                    </div>
                    <!-- Chat Interface -->
                    <div class="col-lg-8">
                        <div class="chat">
                            <!-- Chat Header -->
                            <div class="chat-header clearfix">
                                <!-- Display selected etudiant's information here -->
                                <h6 class="m-b-0" id="selectedEtudiantName"></h6>
                            </div>

                            <div class="chat-history">
      <?php foreach ($messages as $message) : ?>
        <div class="message">
          <div class="message-data">
            <img src="<?php echo $message['sender_photo_path']; ?>" alt="avatar" class="etudiant-photo">
            <div class="about">
              <div class="name">
                <?php echo $message['sender_name']; ?>
              </div>
              <div class="status">
                <?php if ($message['sender_id'] == $user_id) : ?>
                  <i class="fa fa-circle online"></i>
                <?php else : ?>
                  <i class="fa fa-circle offline"></i>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="message-text">
            <?php echo $message['message_text']; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="chat-message clearfix">
    <form id="messageForm">
    <div class="input-group mb-0">
        <input type="text" class="form-control" placeholder="Search..." id="searchInput">
        <input type="hidden" id="selectedStudentId" name="selectedStudentId" value="">
        <div class="input-group-append">
            <button class="btn btn-primary" type="button" id="sendMessageBtn">Send</button>
        </div>
    </div>
</form>

    </div>
  </div>
</div>
</div>


    <!-- Add your script imports here -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Handle click on etudiant in the people list
            $('.people-list .clearfix').on('click', function () {
                var etudiantName = $(this).find('.name').text();
                var etudiantText = $(this).find('.message-text').text();

                $('#selectedEtudiantName').text(etudiantName);
                $('.chat-history').text(etudiantText);
                $('.chat-history').show();
            });

            // Handle click on the "Send" button
    $('#sendMessageBtn').on('click', function () {
        // Fetch message and selected student ID
        var message = $('#searchInput').val();
        var selectedStudentId = $('#selectedStudentId').val();

        // Check if the message is not empty and a student is selected
        if (message.trim() !== '' && selectedStudentId !== '') {
            // Use AJAX to send the message to the server
            $.ajax({
                type: 'POST',
                url: 'sendMessage.php', // Create this PHP file to handle message sending
                data: {
                    message: message,
                    receiverId: selectedStudentId
                },
                success: function (response) {
                    // Handle success (if needed)
                    console.log(response);
                },
                error: function (error) {
                    // Handle error (if needed)
                    console.error(error);
                }
            });
        } else {
            // Handle case where message or student is not selected
            console.log('Message or student not selected.');
        }
    });

        });

        
    </script>

</script>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>

</html>