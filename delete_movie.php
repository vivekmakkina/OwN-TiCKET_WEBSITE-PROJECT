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

// Delete Movie
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Delete poster image
    $sql = "SELECT poster_image FROM movies WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (file_exists($row['poster_image'])) {
            unlink($row['poster_image']);
        }
    }

    // Delete from database
    $sql = "DELETE FROM movies WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Movie deleted successfully.";
    } else {
        echo "Error deleting movie: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Movie</title>
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
            overflow: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            max-width: 900px;
            width: 90%;
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

        a {
            color: #ff6b81;
            font-weight: bold;
            text-decoration: none;
            background: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        a:hover {
            background: #ff4d61;
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

        footer {
            margin-top: 30px;
            color: #ccc;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Delete Movies</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Poster</th>
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
            <td><?= $row['id'] ?></td>
            <td><img src="<?= $row['poster_image'] ?>" width="50"></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['genre'] ?></td>
            <td><?= $row['rating'] ?></td>
            <td><?= $row['show_time'] ?></td>
            <td><?= $row['day'] ?></td>
            <td>
                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this movie?')">Delete</a>
            </td>
        </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='8'>No movies found.</td></tr>";
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
