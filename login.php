<?php
session_start();
include('db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "❌ Invalid password. Please try again.";
        }
    } else {
        $message = "⚠️ User not found. Please check your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            max-width: 420px;
            width: 90%;
            text-align: center;
            animation: fadeZoomIn 1.5s ease forwards;
        }

        .container h1 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        .message-box {
            color: #ffb3b3;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input {
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.3s ease;
            outline: none;
        }

        input::placeholder {
            color: #e0e0e0;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px #1dd1a1;
        }

        button {
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
        }

        button:hover {
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

        footer {
            margin-top: 30px;
            color: #ccc;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Login</h1>

    <?php if (!empty($message)): ?>
        <div class="message-box"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
