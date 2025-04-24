<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch movie details
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;
$movie_query = "SELECT * FROM movies WHERE id = '$movie_id'";
$movie_result = $conn->query($movie_query);

if ($movie_result && $movie_result->num_rows > 0) {
    $movie = $movie_result->fetch_assoc();
} else {
    echo "<p>Movie not found.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['title']); ?> Seating</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #ffffff;
        }
        .container {
            padding: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .movie-info {
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            padding: 20px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            text-align: center;
        }
        .movie-info h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .movie-info p {
            font-size: 18px;
            margin: 5px 0;
            color: #ddd;
        }
        h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .seating-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 12px;
            justify-items: center;
            align-items: center;
            max-width: 1000px;
        }
        .seat {
            width: 40px;
            height: 40px;
            cursor: pointer;
            position: relative;
            transition: transform 0.2s;
        }
        .seat:hover {
            transform: scale(1.1);
        }
        .seat img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .seat.booked img {
            opacity: 0.5;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            grid-column: span 10;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            color: #1dd1a1;
        }
        .selected-seats {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
            gap: 12px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
        }
        .seat-info {
            font-size: 18px;
            margin-bottom: 15px;
        }
        .book-btn {
            background-color: #1dd1a1;
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .book-btn:hover {
            background-color: #10ac84;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="movie-info">
        <h1><?php echo htmlspecialchars($movie['title']); ?> - Seating</h1>
        <p>Show Time: <?php echo htmlspecialchars($movie['show_time']); ?></p>
    </div>

    <h3>Select Your Seats</h3>

    <div class="seating-grid">
        <!-- Diamond Section (₹200) -->
        <div class="section-title">Diamond (₹200)</div>
        <?php for ($i = 1; $i <= 70; $i++) { 
            echo "<div class='seat diamond' id='seat$i' data-seat-number='$i' data-price='200' data-gst='12'>
                    <img src='assets/images/101.png' alt='Seat'>
                  </div>";
        } ?>

        <!-- Gold Section (₹150) -->
        <div class="section-title">Gold (₹150)</div>
        <?php for ($i = 71; $i <= 120; $i++) {
            echo "<div class='seat gold' id='seat$i' data-seat-number='$i' data-price='150' data-gst='9'>
                    <img src='assets/images/101.png' alt='Seat'>
                  </div>";
        } ?>

        <!-- Silver Section (₹90) -->
        <div class="section-title">Silver (₹90)</div>
        <?php for ($i = 121; $i <= 150; $i++) {
            echo "<div class='seat silver' id='seat$i' data-seat-number='$i' data-price='90' data-gst='7'>
                    <img src='assets/images/101.png' alt='Seat'>
                  </div>";
        } ?>
    </div>

    <!-- Selected Seats Container -->
    <div id="selectedSeats" class="selected-seats" style="display: none;">
        <form id="bookingForm" action="booking_summary.php" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
            <input type="hidden" name="selectedSeats" id="selectedSeatsInput">
            <input type="hidden" name="movieId" value="<?php echo $movie_id; ?>">
            <div id="seatInfo" class="seat-info"></div>
            <button type="submit" id="bookBtn" class="book-btn"></button>
        </form>
    </div>

</div>

<script>
// JavaScript to handle seat selection and show selected seats
const seats = document.querySelectorAll('.seat');
const selectedSeatsContainer = document.getElementById('selectedSeats');
const seatInfo = document.getElementById('seatInfo');
const bookBtn = document.getElementById('bookBtn');

let selectedSeats = [];

seats.forEach(seat => {
    seat.addEventListener('click', function() {
        seat.classList.toggle('booked');

        const seatNumber = seat.getAttribute('data-seat-number');

        if (seat.classList.contains('booked')) {
            selectedSeats.push(seatNumber);
        } else {
            selectedSeats = selectedSeats.filter(item => item !== seatNumber);
        }

        renderSelectedSeats(); 
    });
});

function renderSelectedSeats() {
    if (selectedSeats.length > 0) {
        selectedSeatsContainer.style.display = 'flex';
        seatInfo.innerHTML = `
            <strong>${selectedSeats.length}</strong> Seat(s) Selected:<br>
            ${selectedSeats.join(', ')}
        `;
        bookBtn.textContent = selectedSeats.length > 1 ? 'Book Your Seats' : 'Book Your Seat';

        // Set selected seats into the hidden input field
        document.getElementById('selectedSeatsInput').value = selectedSeats.join(',');
    } else {
        selectedSeatsContainer.style.display = 'none';
    }
}
</script>

</body>
</html>
