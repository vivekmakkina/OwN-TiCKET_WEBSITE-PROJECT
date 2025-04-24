<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch genres
$genre_query = "SELECT DISTINCT genre FROM movies";
$genres_result = $conn->query($genre_query);

// Fetch show times
$showtime_query = "SELECT DISTINCT show_time FROM movies";
$showtime_result = $conn->query($showtime_query);

$today = date('Y-m-d');

// Check if user clicked "Explore Upcoming Movies"
$isUpcoming = isset($_GET['upcoming']) && $_GET['upcoming'] == '1';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movies In Your City</title>
    <style>
        /* Your styles */
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1f1c2c, #928dab); min-height: 100vh; display: flex; flex-direction: column; }
        .container { display: flex; padding: 30px; flex: 1; }
        .sidebar { width: 250px; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(15px); padding: 20px; border-radius: 20px; height: fit-content; }
        .filters h2 { font-size: 24px; margin-bottom: 20px; color: #ffffff; }
        .filter-group { margin-bottom: 20px; }
        .filter-group h4 { margin-bottom: 10px; color: #1dd1a1; }
        .filter-group button { background: rgba(255, 255, 255, 0.2); border: none; padding: 8px 15px; margin: 5px 5px 5px 0; border-radius: 10px; cursor: pointer; color: #fff; font-weight: bold; transition: background 0.3s; }
        .filter-group button:hover { background: #10ac84; }
        .main { flex: 1; padding-left: 40px; color: white; }
        .main h1 { font-size: 32px; margin-bottom: 20px; }
        .coming-soon { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .coming-soon strong { font-size: 20px; }
        .coming-soon a { color: #1dd1a1; text-decoration: none; font-weight: bold; }
        .movie-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 25px; }
        .movie-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden; text-align: center; transition: transform 0.3s; position: relative; }
        .movie-card:hover { transform: scale(1.05); }
        .movie-card img { width: 100%; height: 260px; object-fit: cover; }
        .movie-card-content { padding: 15px; }
        .movie-card-content h3 { margin: 10px 0 5px; font-size: 18px; }
        .movie-card-content p { font-size: 14px; margin: 5px 0; color: #ddd; }
        .tag { background: crimson; color: white; font-size: 12px; padding: 5px 10px; position: absolute; top: 10px; left: 10px; border-radius: 6px; z-index: 10; }
        .likes { font-size: 13px; margin-top: 5px; }
        .logout { position: absolute; top: 20px; right: 30px; font-size: 14px; }
        .logout a { text-decoration: none; color: #1dd1a1; font-weight: bold; }
        footer { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); padding: 15px; text-align: center; color: #ddd; font-size: 14px; border-top: 1px solid rgba(255, 255, 255, 0.2); margin-top: auto; }
        .movie-image-container { position: relative; }
    </style>
</head>
<body>

<div class="logout">
    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a>
</div>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar filters">
        <h2>Filters</h2>

        <!-- Genre Filter -->
        <div class="filter-group">
            <h4>Genres</h4>
            <button class="genre-filter" data-genre="All">All</button>
            <?php
            if ($genres_result && $genres_result->num_rows > 0) {
                while ($genre_row = $genres_result->fetch_assoc()) {
                    $genre = htmlspecialchars($genre_row['genre']);
                    echo "<button class='genre-filter' data-genre='$genre'>$genre</button>";
                }
            }
            ?>
        </div>

        <!-- Show Timing Filter -->
        <div class="filter-group">
            <h4>Show Timings</h4>
            <button class="showtime-filter" data-showtime="All">All</button>
            <?php
            if ($showtime_result && $showtime_result->num_rows > 0) {
                while ($showtime_row = $showtime_result->fetch_assoc()) {
                    $showtime = htmlspecialchars($showtime_row['show_time']);
                    echo "<button class='showtime-filter' data-showtime='$showtime'>$showtime</button>";
                }
            }
            ?>
        </div>

        <!-- Day Filter -->
        <div class="filter-group">
            <h4>Days</h4>
            <?php
            for ($i = 0; $i <= 7; $i++) {
                $date = date('Y-m-d', strtotime("+$i day"));
                $label = ($i == 0) ? "Today" : (($i == 1) ? "Tomorrow" : date('D, M j', strtotime("+$i day")));
                echo "<button class='day-filter' data-day='$date'>$label</button>";
            }
            ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h1 id="city-title"><?php echo $isUpcoming ? "Upcoming Movies In Your City" : "Movies In Your City"; ?></h1>

        <?php if (!$isUpcoming): ?>
            <div class="coming-soon">
                <strong>Coming Soon</strong>
                <a href="?upcoming=1">Explore Upcoming Movies →</a>
            </div>
        <?php else: ?>
            <div class="coming-soon">
                <strong><a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" style="color: #1dd1a1;">← Back to Today's Movies</a></strong>
            </div>
        <?php endif; ?>

        <div class="movie-grid" id="movie-grid">
            <?php
            if ($isUpcoming) {
                $sql = "SELECT * FROM movies WHERE day > '$today' ORDER BY day ASC, rating DESC";
            } else {
                $sql = "SELECT * FROM movies WHERE day = '$today' ORDER BY rating DESC";
            }

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rating = floatval($row['rating']);
                    echo "<div class='movie-card' data-genre='" . htmlspecialchars($row['genre']) . "' data-showtime='" . htmlspecialchars($row['show_time']) . "' data-day='" . htmlspecialchars($row['day']) . "'>";
                    if ($rating > 4.0) {
                        echo "<span class='tag'>PROMOTED</span>";
                    }
                    echo "<div class='movie-image-container'>
                            <img src='" . htmlspecialchars($row['poster_image']) . "' alt='Movie Poster'>
                        </div>
                        <div class='movie-card-content'>
                            <h3>" . htmlspecialchars($row['title']) . "</h3>
                            <p>" . htmlspecialchars($row['genre']) . "</p>
                            <p class='likes'>⭐ " . htmlspecialchars(number_format($rating, 1)) . "</p>
                            <p class='show-time'>" . htmlspecialchars($row['show_time']) . "</p>
                            <a href='movie_seating.php?movie_id=" . $row['id'] . "' class='seating-link'>View Seating</a>
                        </div>
                    </div>";
                }
            } else {
                echo "<p>No " . ($isUpcoming ? "upcoming" : "available") . " movies found.</p>";
            }
            ?>
        </div>

    </div>
</div>

<footer>
    <?php include('includes/footer.php'); ?>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                const data = await response.json();
                const city = data.address.city || data.address.town || data.address.village || 'your city';
                document.getElementById('city-title').textContent = "<?php echo $isUpcoming ? "Upcoming Movies In " : "Movies In "; ?>" + city;
            } catch (error) {
                console.error('Error fetching city:', error);
            }
        }, function(error) {
            console.error('Error getting location:', error);
        });
    }

    // Filtering
    const genreButtons = document.querySelectorAll('.genre-filter');
    const showtimeButtons = document.querySelectorAll('.showtime-filter');
    const dayButtons = document.querySelectorAll('.day-filter');
    const movieCards = document.querySelectorAll('.movie-card');

    let selectedGenre = 'All';
    let selectedShowtime = 'All';
    let selectedDay = '<?php echo $today; ?>';

    function filterMovies() {
        movieCards.forEach(card => {
            const genre = card.dataset.genre;
            const showtime = card.dataset.showtime;
            const day = card.dataset.day;

            const genreMatch = (selectedGenre === 'All' || genre === selectedGenre);
            const showtimeMatch = (selectedShowtime === 'All' || showtime === selectedShowtime);
            const dayMatch = (day === selectedDay);

            if (genreMatch && showtimeMatch && dayMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    genreButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedGenre = this.dataset.genre;
            filterMovies();
        });
    });

    showtimeButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedShowtime = this.dataset.showtime;
            filterMovies();
        });
    });

    dayButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedDay = this.dataset.day;
            filterMovies();
        });
    });
});
</script>

</body>
</html>
