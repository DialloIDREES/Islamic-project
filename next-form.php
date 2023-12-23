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
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> Responsive Registration Form | CodingLab </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
        }

        .container {
            max-width: 700px;
            width: 100%;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 5px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .container .title {
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }

        .container .title::before {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 30px;
            border-radius: 5px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
        }

        .content form .user-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0 12px 0;
        }

        form .user-details .input-box {
            margin-bottom: 15px;
            width: calc(100% / 2 - 20px);
        }

        form .input-box span.details {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .user-details .input-box input {
            height: 45px;
            width: 100%;
            outline: none;
            font-size: 16px;
            border-radius: 5px;
            padding-left: 15px;
            border: 1px solid #ccc;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }

        .user-details .input-box input:focus,
        .user-details .input-box input:valid {
            border-color: #9b59b6;
        }

        form .gender-details .gender-title {
            font-size: 20px;
            font-weight: 500;
        }

        form .category {
            display: flex;
            width: 80%;
            margin: 14px 0;
            justify-content: space-between;
        }

        form .category label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        form .category label .dot {
            height: 18px;
            width: 18px;
            border-radius: 50%;
            margin-right: 10px;
            background: #d9d9d9;
            border: 5px solid transparent;
            transition: all 0.3s ease;
        }

        #dot-1:checked~.category label .one,
        #dot-2:checked~.category label .two,
        #dot-3:checked~.category label .three {
            background: #9b59b6;
            border-color: #d9d9d9;
        }

        form input[type="radio"] {
            display: none;
        }

        form .button {
            height: 45px;
            margin: 35px 0;
        }

        form .button input {
            height: 100%;
            width: 100%;
            border-radius: 5px;
            border: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
        }

        form .button input:hover {
            background: linear-gradient(-135deg, #71b7e6, #9b59b6);
        }

        @media(max-width: 584px) {
            .container {
                max-width: 100%;
            }

            form .user-details .input-box {
                margin-bottom: 15px;
                width: 100%;
            }

            form .category {
                width: 100%;
            }

            .content form .user-details {
                max-height: 300px;
                overflow-y: scroll;
            }

            .user-details::-webkit-scrollbar {
                width: 5px;
            }
        }

        @media(max-width: 459px) {
            .container .content .category {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="#">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details">Nom complet</span>
                        <input type="text" placeholder="Entrer votre nom" required>
                    </div>
               
                 
                  
                    <div class="input-box">
                        <span class="details">Pays</span>
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
                    </div>

                    <div class="input-box">
                        <span><label for="about">Tell us a bit about yourself...</label></span>
                        <textarea id="about" name="about" cols="20" rows="05" placeholder="Parlez-nous un peu de vous..."></textarea>
                    </div>
                   
                    <div class="input-box">
                        <span><label for="photo">Upload your photo</label></span>
                        <input type="file" id="photo" name="photo" accept="image/*" />
                    </div>
                </div>
                <div class="gender-details">
                    <input type="radio" name="gender" id="dot-1">
                    <input type="radio" name="gender" id="dot-2">
                    <input type="radio" name="gender" id="dot-3">
                    <span class="gender-title">Rôle</span>
                    <div class="category">
                        <label for="dot-1">
                            <span class="dot one"></span>
                            <span class="gender">Étudiant</span>
                        </label>
                        <label for="dot-2">
                            <span class="dot two"></span>
                            <span class="gender">Enseignant</span>
                        </label>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Register">
                </div>
            </form>
        </div>
    </div>
</body>

</html>
