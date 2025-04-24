<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get movie and seat data from POST
$movie_id = isset($_POST['movieId']) ? (int)$_POST['movieId'] : 0;
$selectedSeats = isset($_POST['selectedSeats']) ? explode(',', $_POST['selectedSeats']) : [];
$seatPrice = 0;
$gstPrice = 0;
$additionalCharge = 10; // Additional charge

// Fetch movie details
$movie_query = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($movie_query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie_result = $stmt->get_result();
$movie = $movie_result->fetch_assoc();

// If movie not found, stop
if (!$movie) {
    echo "Movie not found!";
    exit();
}

// Calculate total cost based on selected seats
if (!empty($selectedSeats)) {
    $placeholders = implode(',', array_fill(0, count($selectedSeats), '?'));
    $seatQuery = "SELECT * FROM seats WHERE seat_number IN ($placeholders)";
    $stmt_seats = $conn->prepare($seatQuery);
    $stmt_seats->bind_param(str_repeat('i', count($selectedSeats)), ...array_map('intval', $selectedSeats));
    $stmt_seats->execute();
    $seatResult = $stmt_seats->get_result();

    while ($seatData = $seatResult->fetch_assoc()) {
        $seatPrice += $seatData['price'];
        $gstPrice += $seatData['gst'];
    }
}

// Calculate total
$totalPrice = $seatPrice + $gstPrice + $additionalCharge;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Summary</title>
    <style>
        body { 
            margin: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #1f1c2c, #928dab); 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            color: #fff;
        }
        .container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            text-align: center;
        }
        h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #1dd1a1;
        }
        .summary {
            text-align: left;
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .summary strong {
            color: #1dd1a1;
        }
        .total-price {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 20px;
            text-align: center;
        }
        form {
            margin-top: 30px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px 8px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 16px;
        }
        input[type="text"]::placeholder,
        input[type="email"]::placeholder {
            color: #ccc;
        }
        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 30px;
            background: #27ae60;
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 18px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background: #2ecc71;
            transform: scale(1.05);
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 14px;
        }
        .logout a {
            text-decoration: none;
            color: #1dd1a1;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="logout">
    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a>
</div>

<div class="container">
    <h2>Booking Summary</h2>

    <div class="summary">
        <strong>Movie:</strong> <?php echo htmlspecialchars($movie['title']); ?><br>
        <strong>Show Time:</strong> <?php echo htmlspecialchars($movie['show_time']); ?><br>
        <strong>Seats Selected:</strong> <?php echo htmlspecialchars(implode(", ", $selectedSeats)); ?><br>
        <strong>Price Per Seat:</strong> ₹<?php echo number_format($seatPrice / count($selectedSeats), 2); ?><br>
        <strong>Total GST:</strong> ₹<?php echo number_format($gstPrice, 2); ?><br>
        <strong>Additional Charge:</strong> ₹<?php echo number_format($additionalCharge, 2); ?><br>
    </div>

    <div class="total-price">
        Total Price: ₹<?php echo number_format($totalPrice, 2); ?>
    </div>

    <!-- Form to collect user information (mobile number and email) -->
    <form action="payment_page.php" method="post">
        <input type="hidden" name="movieId" value="<?php echo htmlspecialchars($movie_id); ?>">
        <input type="hidden" name="selectedSeats" value="<?php echo htmlspecialchars(implode(',', $selectedSeats)); ?>">
        
        <label for="email">Email: </label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mobile">Mobile Number: </label>
        <input type="text" id="mobile" name="mobile" required><br><br>

        <button type="submit" class="btn">Proceed to Payment</button>
    </form>
</div>

</body>
</html>
