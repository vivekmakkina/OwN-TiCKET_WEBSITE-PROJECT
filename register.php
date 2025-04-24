<?php
include('db.php');

function isStrongPassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result && $result->num_rows > 0) {
        $message = "🚫 This email is already registered. Please try another.";
    } elseif (!isStrongPassword($password)) {
        $message = "⚠️ Password must be at least 8 characters long, with one uppercase letter, one number, and one special character.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
        if ($conn->query($sql)) {
            $message = "🎉 Registered successfully. <a href='login.php'>Login here</a>";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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

        .message-box a {
            color: #1dd1a1;
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
    <h1>Register</h1>

    <?php if (!empty($message)): ?>
        <div class="message-box"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Create Password" required>
        <button type="submit">Register</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
