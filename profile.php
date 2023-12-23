<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: register.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch role-specific information from etudiant
$etudiantQuery = $conn->prepare("SELECT name, introduction, country_id, photo_path FROM etudiant WHERE user_id = ?");
$etudiantQuery->bind_param('i', $user_id);
$etudiantQuery->execute();
$etudiantResult = $etudiantQuery->get_result();
$etudiant = $etudiantResult->fetch_assoc();
$etudiantQuery->close();

// Fetch role-specific information from enseignant
$enseignantQuery = $conn->prepare("SELECT name, introduction, country_id, photo FROM enseignant WHERE user_id = ?");
$enseignantQuery->bind_param('i', $user_id);
$enseignantQuery->execute();
$enseignantResult = $enseignantQuery->get_result();
$enseignant = $enseignantResult->fetch_assoc();
$enseignantQuery->close();

// Fetch registrationDate from users table
$registrationDateQuery = $conn->prepare("SELECT registrationDate FROM users WHERE id = ?");
$registrationDateQuery->bind_param('i', $user_id);
$registrationDateQuery->execute();
$registrationDateResult = $registrationDateQuery->get_result();
$registrationDateData = $registrationDateResult->fetch_assoc();
$registrationDateQuery->close();
$registrationDate = $registrationDateData['registrationDate'];
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
            font-family: 'Arial', sans-serif;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background-color: #1da1f2;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .profile-header h2 {
            font-size: 1.5rem;
            margin: 0;
        }

        .profile-form {
            padding: 20px;
        }

        .profile-form label {
            display: block;
            margin-bottom: 8px;
            color: #1da1f2;
        }

        .profile-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #1da1f2;
            border-radius: 5px;
        }

        .profile-form button {
            background-color: #1da1f2;
            color: #ffffff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-form button:hover {
            background-color: #0c7cbf;
        }

        .profile-footer {
            text-align: center;
            margin-top: 20px;
        }

        .profile-footer button {
            color: #e0245e;
            border: none;
            background: none;
            cursor: pointer;
        }

        .profile-footer button:hover {
            text-decoration: underline;
        }
        /* Add these styles to your CSS stylesheet */
.logout-button {
    background-color: #ff0000; /* Red background color */
    color: #ffffff; /* White text color */
    padding: 10px 15px; /* Padding around the button */
    border: none; /* Remove border */
    border-radius: 5px; /* Add a slight border radius for rounded corners */
    cursor: pointer; /* Change cursor on hover */
}

.logout-button:hover {
   text-decoration: underline;
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

      <div class="item">
          <a href="forum.php">Forum</a>
      </div>

      <?php

 
 
      if (isset($_SESSION['user_id'])) {
          // User is logged in, show the username
          $username = $_SESSION['username'];
          echo '<div class="item-button"><a href="profile.php"><i class="fa fa-user"></i> ' . $username . '</a></div>';
        } else {
         
          echo '<div class="item-button"><a href="#" type="button">S\'identifier</a></div>';
      }
      ?>
  </div>
    </header>
      </div>
  </div>


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
<div class="profile-container">
    <div class="profile-header">
        <?php if (!empty($etudiant['photo_path'])) : ?>
            <img id="profileImage" src="<?php echo $etudiant['photo_path']; ?>" alt="Profile Image">
        <?php elseif (!empty($enseignant['photo'])) : ?>
            <img id="profileImage" src="<?php echo $enseignant['photo']; ?>" alt="Profile Image">
        <?php else : ?>
            <!-- Default image if no profile image is set -->
            <img src="default_profile_image.jpg" alt="Default Profile Image">
        <?php endif; ?>
        <h2><?php echo $etudiant['name'] ?? $enseignant['name']; ?></h2>
    </div>
    <div class="profile-info">
        <?php if (!empty($etudiant['country_id'])) : ?>
            <?php
            $countryId = $etudiant['country_id'];
            $countryQuery = $conn->prepare("SELECT country_name FROM country WHERE country_id = ?");
            $countryQuery->bind_param('i', $countryId);
            $countryQuery->execute();
            $countryResult = $countryQuery->get_result();
            $country = $countryResult->fetch_assoc();
            $countryQuery->close();
            ?>
            <p>Pays: <?php echo !empty($country['country_name']) ? $country['country_name'] : 'Unknown'; ?></p>
        <?php endif; ?>

        <?php if (!empty($etudiant['introduction'])) : ?>
            <p>À propos: <?php echo $etudiant['introduction']; ?></p>
        <?php elseif (!empty($enseignant['introduction'])) : ?>
            <p>À propos: <?php echo $enseignant['introduction']; ?></p>
        <?php endif; ?>

        <?php if (!empty($registrationDate)) : ?>
            <p>Membre depuis: <?php echo $registrationDate; ?></p>
        <?php endif; ?>
    </div>



    <div class="profile-form">
        <label for="newImage">Change Profile Image:</label>
        <input type="file" id="newImage" name="newImage" accept="image/*">

        <label for="newUsername">Change Username:</label>
        <input type="text" id="newUsername" name="newUsername" required>

        <label for="newPassword">Change Password:</label>
        <input type="password" id="newPassword" name="newPassword" required>

        <button type="button" onclick="updateProfile()">Update Profile</button>
    </div>

    <div class="profile-footer">
        <button type="button" onclick="confirmDeleteAccount()">Delete Account</button>

        <button type="button" onclick="logout()" class="logout-button">Logout</button>
    </div>
</div>




<script>
  // script.js

  // Set the initial username in localStorage
  localStorage.setItem('username', '<?php echo $username; ?>');

  function updateProfile() {
    const newUsername = document.getElementById('newUsername').value;
    const newPassword = document.getElementById('newPassword').value;

    // Use fetch to send an AJAX request to the server
    fetch('update_profile.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `newUsername=${newUsername}&newPassword=${newPassword}`,
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update the profile information on the client side
          document.getElementById('username').textContent = newUsername;
          showAlert('success', 'Profile updated successfully!');

          // Update the username in localStorage
          localStorage.setItem('username', newUsername);

          // Log out the user and redirect to the login page
          logout();

          // Clear the password field
          document.getElementById('newPassword').value = '';

          // Auto refresh the page
          window.location.reload();
        } else {
          showAlert('error', data.error || 'Failed to update profile. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('success', 'Profile updated successfully!');
      });
  }

  function logout() {
    // Use fetch to send an AJAX request to the server
    fetch('logout.php', {
      method: 'GET',
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Redirect to the register.html page after successful logout
          window.location.href = 'register.html';
        } else {
          showAlert('error', 'Failed to logout. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred. Please try again later.');
      });
  }
  function uploadImage() {
    const newImageInput = document.getElementById('newImage');
    const newImage = newImageInput.files[0];

    // Use FormData to handle file upload
    const formData = new FormData();
    formData.append('newImage', newImage);

    // Use fetch to send an AJAX request to the server
    fetch('upload_image.php', {
      method: 'POST',
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update the profile image on the client side
          document.getElementById('profileImage').src = data.newProfileImage;
          showAlert('success', 'Profile image updated successfully!');
        } else {
          showAlert('error', data.error || 'Failed to update profile image. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating the profile image.');
      });
  }
  function deleteAccount() {
    // Use fetch to send an AJAX request to the server
    fetch('delete_account.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Redirect to a login page or show a message
          showAlert('success', 'Account deleted successfully!');
          window.location.href = 'register.html';
        } else {
          showAlert('error', 'Failed to delete account. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred. Please try again later.');
      });
  }

  function confirmDeleteAccount() {
    Swal.fire({
      title: 'Are you sure you want to delete your account?',
      text: 'This action is irreversible.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete my account',
      cancelButtonText: 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        deleteAccount();
      }
    });
  }

  function showAlert(icon, message) {
    Swal.fire({
      icon: icon,
      title: message,
      showConfirmButton: false,
      timer: 2000,
    });
  }
</script>




<!-- ... Rest of your HTML ... -->

</body>

      <footer>
        <div class="footer-container">
          <div class="container">
            <div class="main-footer-logo">
              <img src="img/Masjid-img-icon.png" alt="Logo">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>



</body>

</html>