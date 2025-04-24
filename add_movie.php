<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $poster_image = $_FILES['poster_image']['name'];
    $genre = $_POST['genre'];
    $rating = $_POST['rating'];
    $show_time = $_POST['show_time'];
    $day = $_POST['day'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($poster_image);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['poster_image']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES['poster_image']['size'] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES['poster_image']['tmp_name'], $target_file)) {
            $conn = new mysqli("localhost", "root", "root", "movie_booking");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "INSERT INTO movies (title, poster_image, genre, rating, show_time, day)
                    VALUES ('$title', '$target_file', '$genre', '$rating', '$show_time', '$day')";

            if ($conn->query($sql) === TRUE) {
                echo "New movie added successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Movie</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            max-width: 450px;
            width: 90%;
            animation: fadeZoomIn 1.5s ease forwards;
        }

        h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        form label {
            color: #eee;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        form input[type="text"],
        form input[type="time"],
        form input[type="date"],
        form input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 12px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-size: 1rem;
            outline: none;
        }

        form input[type="text"]::placeholder,
        form input[type="time"]::placeholder,
        form input[type="date"]::placeholder {
            color: #ccc;
        }

        form button {
            width: 100%;
            padding: 15px;
            background: #1dd1a1;
            color: white;
            border: none;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        form button:hover {
            background: #10ac84;
            transform: scale(1.05);
        }

        @keyframes fadeZoomIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Movie</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" name="title" required>

        <label for="poster_image">Poster Image</label>
        <input type="file" name="poster_image" required>

        <label for="genre">Genre</label>
        <input type="text" name="genre" required>

        <label for="rating">Rating</label>
        <input type="text" name="rating" required>

        <label for="show_time">Show Time</label>
        <input type="time" name="show_time" required>

        <label for="day">Day</label>
        <input type="date" name="day" required>

        <button type="submit" name="add_movie">➕ Add Movie</button>
    </form>
</div>

</body>
</html>
