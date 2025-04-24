<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Ticket Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="main-layout">
    <!-- Left Poster Scroll -->
    <div class="poster-strip left-strip">
        <div class="poster-track">
            <img src="assets/posters/1.jpg" alt="Movie Poster">
            <img src="assets/posters/2.jpg" alt="Movie Poster">
            <img src="assets/posters/3.jpg" alt="Movie Poster">
            <img src="assets/posters/4.jpg" alt="Movie Poster">
            <img src="assets/posters/9.jpg" alt="Movie Poster">
        </div>
    </div>

    <!-- Branding -->
    <div class="branding">
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <span class="brand-title">MoVIE <span class="zone">ZoNE</span></span>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1 class="main-title">🎬 Welcome to Movie Ticket Booking</h1>
        <p class="subtext">Book your seats now for the latest movies!</p>

        <div class="button-group">
            <!-- Normal User Login -->
            <form action="login.php" method="get">
                <button type="submit" class="btn login-btn">Login</button>
            </form>

            <!-- Register for New User -->
            <form action="register.php" method="get">
                <button type="submit" class="btn register-btn">Register</button>
            </form>

            <!-- Admin Login -->
            <form action="admin_login.php" method="get">   <!-- Add this form -->
                <button type="submit" class="btn admin-login-btn" style="background-color: #ff4d4d;">Admin Login</button>
            </form>
        </div>
    </div>

    <!-- Right Poster Scroll -->
    <div class="poster-strip right-strip">
        <div class="poster-track">
            <img src="assets/posters/5.jpg" alt="Movie Poster">
            <img src="assets/posters/6.jpg" alt="Movie Poster">
            <img src="assets/posters/7.jpg" alt="Movie Poster">
            <img src="assets/posters/8.jpg" alt="Movie Poster">
            <img src="assets/posters/10.jpg" alt="Movie Poster">
        </div>
    </div>
</div>

</body>
</html>
