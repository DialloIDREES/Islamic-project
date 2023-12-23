<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the submitted form
    $role = $_POST['role'];

    if ($role === 'Enseignant' || $role === 'Etudiant') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $introduction = isset($_POST['about']) ? $_POST['about'] : null;

        // Insert data into the appropriate table based on the selected role
        $sql = "";

        if ($role === 'Enseignant' || $role === 'Etudiant') {
            // Process file upload
            if (isset($_FILES['photo'])) {
                $file = $_FILES['photo'];
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_error = $file['error'];

                // Check if file is uploaded successfully
                if ($file_error === 0) {
                    // Generate a unique name for the file
                    $file_destination = 'uploads/' . uniqid('', true) . $file_name;

                    // Move the file to the destination folder
                    move_uploaded_file($file_tmp, $file_destination);

                    // Insert data into the appropriate table
                    if ($role === 'Enseignant') {
                        $sql = "INSERT INTO enseignant (user_id, name, phone, introduction, photo, country_id)
                                VALUES (?, ?, ?, ?, ?, ?)";
                    } elseif ($role === 'Etudiant') {
                        $sql = "INSERT INTO etudiant (user_id, name, phone, introduction, country_id, photo_path)
                                VALUES (?, ?, ?, ?, ?, ?)";
                    }

                    $stmt = $conn->prepare($sql);

                    if (isset($_SESSION['user_id'])) {
                        // Assuming $_POST['countryCode'] contains the selected country code
                        $countryCode = $_POST['countryCode'];
                        $countryId = getCountryIdByCode($countryCode, $conn);

                        if ($role === 'Enseignant') {
                            $stmt->bind_param("issssi", $_SESSION['user_id'], $name, $phone, $introduction, $file_destination, $countryId);
                        } elseif ($role === 'Etudiant') {
                            $stmt->bind_param("isssis", $_SESSION['user_id'], $name, $phone, $introduction, $countryId, $file_destination);
                        }

                        // Execute the SQL statement for the main insertion
                        if ($stmt->execute()) {
                            // Redirect to index.php after successful form submission
                            header('Location: index.php');
                            exit();
                        } else {
                            echo '<p style="color: red;">Error submitting form: ' . $stmt->error . '</p>';
                        }
                    } else {
                        echo '<p style="color: red;">User ID is not set in the session.</p>';
                        exit();
                    }
                } else {
                    echo '<p style="color: red;">Error uploading file: ' . $file_error . '</p>';
                    exit();
                }
            }
        } else {
            echo '<p style="color: red;">Invalid role selected.</p>';
        }

        // Close $stmt if it's not null
        if ($stmt !== null) {
            $stmt->close();
        }

        $conn->close();
    }
}

// Helper function to get country_id by country_code
function getCountryIdByCode($countryCode, $conn) {
    $sql = "SELECT country_id FROM country WHERE country_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $countryCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['country_id'];
}

?>

<!-- Your HTML form goes here with enctype="multipart/form-data" for file uploads -->



   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masjid - Progressive Information Collection</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            width: 400px;
            max-width: 100%;
        }

        .navigation {
            background-color: #007bff;
            padding: 10px;
            text-align: center;
        }

        .navigation ol {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .navigation ol li {
            display: inline;
            margin-right: 10px;
        }

        .navigation ol li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: color 0.3s;
        }

        .navigation ol li a:hover {
            color: #ffd700;
        }

        .sign-form {
            padding: 20px;
        }

        .questions {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .questions li {
            display: none;
        }

        .questions li.active {
            display: block;
        }

        .questions span {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .questions input,
        .questions select,
        .questions textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            margin-bottom: 10px;
        }

        .questions button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .questions button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>



    <div class="container">
        <div class="navigation">
            <ol>
                <li><a href="#" onclick="nextQuestion('role')">Role</a></li>
                <li><a href="#" onclick="nextQuestion('name')">Name</a></li>
                <li><a href="#" onclick="nextQuestion('phone')">Phone</a></li>
                <li><a href="#" onclick="nextQuestion('about')">About</a></li>
            </ol>
        </div>
        <form id="sign-form" class="sign-form" method="post" action="form.php" enctype="multipart/form-data">
            <ol class="questions">
                <li data-ref="role" class="active">
                    <span><label for="role">Quelle est votre statut?</label></span>
                    <select name="role" id="role" required>
                        <option value="Etudiant">Etudiant</option>
                        <option value="Enseignant">Enseignant</option>
                    </select>
                    <button type="button" onclick="nextQuestion('name')">Next</button>
                </li>
                <li data-ref="name">
                    <span><label for="name">Hi, What is your Name?</label></span>
                    <input id="name" name="name" type="text" placeholder="Enter your full name" autofocus required/>
                    <button type="button" onclick="nextQuestion('phone')">Next</button>
                </li>
                <li data-ref="phone">
    <span><label for="phone">Enter your phone number</label></span>
    <select name="countryCode" class="country" required>
        <?php
        // Fetch countries from the database
        $result = $conn->query("SELECT * FROM country");
        while ($row = $result->fetch_assoc()) {
            echo '<option data-countryCode="' . $row['country_code'] . '" value="' . $row['country_code'] . '">
                (+' . $row['country_code'] . ') ' . $row['country_name'] . '
            </option>';
        }
        ?>
    </select>
    <input id="phone" name="phone" type="text" autofocus required/>
    <button type="button" onclick="nextQuestion('about')">Next</button>
</li>
                <li data-ref="about">
    <span><label for="about">Tell us a bit about yourself...</label></span>
    <textarea id="about" name="about" cols="30" rows="10" placeholder="Parlez-nous un peu de vous..."></textarea>
    <button type="button" onclick="nextQuestion('photo')">Next</button>
</li>

<li data-ref="photo">
    <span><label for="photo">Upload your photo</label></span>
    <input type="file" id="photo" name="photo" accept="image/*" />
    <button type="submit">Submit</button>
</li>
            </ol>
        </form>
    </div>

    <script>
    function nextQuestion(ref) {
        const currentQuestion = document.querySelector('.questions li.active');
        const nextQuestion = document.querySelector(`.questions li[data-ref="${ref}"]`);

        if (currentQuestion && nextQuestion) {
            currentQuestion.classList.remove('active');
            nextQuestion.classList.add('active');

            // If transitioning to the "photo" section, change the form's submit button text
            if (ref === 'photo') {
                document.querySelector('button[type="submit"]').innerText = 'Upload Photo';
            } else if (ref === 'submit') {
                // If transitioning to the "submit" section, submit the form
                document.querySelector('#sign-form').submit();
            }
        }
    }
</script>


</body>
</html>