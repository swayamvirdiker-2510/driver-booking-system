<?php
require_once "config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Booking System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav>
    <h2>Driver Booking System</h2>

    <div>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<section class="hero">
    <h1>Book a Driver Easily</h1>
    <p>Find reliable drivers for your vehicle and manage your bookings online.</p>

    <?php if (!isset($_SESSION["user_id"])): ?>
        <a class="button" href="register.php">Get Started</a>
    <?php else: ?>
        <a class="button" href="dashboard.php">Open Dashboard</a>
    <?php endif; ?>
</section>

</body>
</html>
