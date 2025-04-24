<?php
session_start();

// Ensure the user is logged in and has admin privileges
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            max-width: 450px;
            width: 90%;
            text-align: center;
            animation: fadeZoomIn 1.5s ease forwards;
        }

        h1 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .admin-action-btn {
            padding: 15px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            background: #1dd1a1;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .admin-action-btn:hover {
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
    <h1>Welcome Admin</h1>

    <div class="button-group">
        <form action="add_movie.php" method="get">
            <button type="submit" class="admin-action-btn">➕ Add Movie</button>
        </form>
        <form action="update_movie.php" method="get">
            <button type="submit" class="admin-action-btn">✏️ Update Movie</button>
        </form>
        <form action="delete_movie.php" method="get">
            <button type="submit" class="admin-action-btn">🗑️ Delete Movie</button>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
