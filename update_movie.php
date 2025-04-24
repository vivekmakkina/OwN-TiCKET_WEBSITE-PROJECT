<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "movie_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update Movie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_movie'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $rating = $_POST['rating'];
    $show_time = $_POST['show_time'];
    $day = $_POST['day'];

    $sql = "UPDATE movies SET 
                title='$title',
                genre='$genre',
                rating='$rating',
                show_time='$show_time',
                day='$day'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Movie updated successfully!'); window.location.href='';</script>";
    } else {
        echo "<script>alert('Error updating movie: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Movie</title>
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        .container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            max-width: 1000px;
            width: 95%;
            margin: 40px 0;
            text-align: center;
            animation: fadeZoomIn 1.5s ease forwards;
        }

        h2 {
            color: #ffffff;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            color: #fff;
        }

        th {
            background: #1dd1a1;
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.1);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        input[type="text"],
        input[type="time"],
        input[type="date"] {
            width: 90%;
            padding: 8px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1rem;
            outline: none;
        }

        input::placeholder {
            color: #ccc;
        }

        button {
            padding: 10px 20px;
            background: #1dd1a1;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #10ac84;
            transform: scale(1.05);
        }

        form {
            margin: 0;
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

        footer {
            margin-top: 30px;
            color: #ccc;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Movies</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Rating</th>
            <th>Show Time</th>
            <th>Day</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT * FROM movies";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>
        <tr>
            <form method="POST" action="">
                <td><?= $row['id'] ?><input type="hidden" name="id" value="<?= $row['id'] ?>"></td>
                <td><input type="text" name="title" value="<?= htmlspecialchars($row['title']); ?>" required></td>
                <td><input type="text" name="genre" value="<?= htmlspecialchars($row['genre']); ?>" required></td>
                <td><input type="text" name="rating" value="<?= htmlspecialchars($row['rating']); ?>" required></td>
                <td><input type="time" name="show_time" value="<?= $row['show_time'] ?>" required></td>
                <td><input type="date" name="day" value="<?= $row['day'] ?>" required></td>
                <td><button type="submit" name="update_movie">Update</button></td>
            </form>
        </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='7'>No movies found.</td></tr>";
        endif;
        ?>
    </table>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>

<?php
$conn->close();
?>
