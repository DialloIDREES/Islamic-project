<?php
// Start the session
session_start();
include('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Initialize userLocation and registrationDate
    $registrationDate = date('Y-m-d H:i:s');
    $profileImage = 'default_profile.jpg'; // Default profile image

   

    // Check if a file was uploaded
    if (!empty($_FILES['newImage']['name'])) {
        $uploadDir = 'uploads/';  // Create a directory named 'uploads' to store profile images
        $uploadFile = $uploadDir . basename($_FILES['newImage']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES['newImage']['tmp_name']);
        if ($check !== false) {
            // Check if the file already exists
            if (file_exists($uploadFile)) {
                echo json_encode(array('success' => false, 'message' => 'Sorry, file already exists.'));
                exit; // Add exit to stop further execution
            } else {
                // Check file size (limit to 2MB)
                if ($_FILES['newImage']['size'] > 2 * 1024 * 1024) {
                    echo json_encode(array('success' => false, 'message' => 'Sorry, your file is too large. Max file size is 2MB.'));
                    exit; // Add exit to stop further execution
                } else {
                    // Allow only certain file formats
                    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($imageFileType, $allowedFormats)) {
                        // Move the uploaded file to the specified directory
                        if (move_uploaded_file($_FILES['newImage']['tmp_name'], $uploadFile)) {
                            $profileImage = $uploadFile;  // Set the profile image path
                        } else {
                            echo json_encode(array('success' => false, 'message' => 'Sorry, there was an error uploading your file.'));
                            exit; // Add exit to stop further execution
                        }
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG, and GIF files are allowed.'));
                        exit; // Add exit to stop further execution
                    }
                }
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'File is not an image.'));
            exit; // Add exit to stop further execution
        }
    }

    // Check if the email or username already exists
    $checkQuery = "SELECT id, email, username FROM users WHERE email='$email' OR username='$username'";
    $checkResult = $conn->query($checkQuery);

    if (!$checkResult) {
        echo json_encode(array('success' => false, 'message' => 'Database error: ' . $conn->error));
        exit; // Add exit to stop further execution
    }

    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        if ($row['email'] === $email) {
            echo json_encode(array('success' => false, 'message' => 'Email address already exists'));
            exit; // Add exit to stop further execution
        } elseif ($row['username'] === $username) {
            echo json_encode(array('success' => false, 'message' => 'Username is already taken'));
            exit; // Add exit to stop further execution
        }
    }

    // Example registration code, modify accordingly
    $sql = "INSERT INTO users (username, email, password, registrationDate)
            VALUES (?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(array('success' => false, 'message' => 'Database error: ' . $conn->error));
        exit; // Add exit to stop further execution
    }

    $stmt->bind_param("sss", $username, $email, $password);

    // Execute the SQL statement
    if ($stmt->execute()) {
         // Registration successful, set user_id and username in the session
         $_SESSION['user_id'] = $stmt->insert_id;
         $_SESSION['username'] = $username;

        // Redirect to the specified page after successful registration
        echo json_encode(array('success' => true, 'redirect' => '../form.php'));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Error registering user: ' . $stmt->error));
    }

    $stmt->close();
    $conn->close();
}
?>