<?php
session_start();
include "db_connect.php";

// Simple form handling
$messageSent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // You can store this in DB or email it later if required
    $messageSent = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Event System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <nav class="nav">
        <div class="nav-left">
            <a href="home.php" class="nav-link">Home</a>
            <a href="events.php" class="nav-link">Events</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="contact.php" class="nav-link active">Contact Us</a>
        </div>

        <div class="nav-right">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn-nav">Sign In</a>
                <a href="register.php" class="btn-nav btn-nav-outline">Register</a>
            <?php else: ?>
                <span>Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-nav">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="container">

    <h2>Contact Us</h2>
    <p>If you have any questions or need help, please fill out the form below.</p>

    <?php if($messageSent): ?>
        <div class="success-message">
            ✅ Your message has been sent successfully. We will contact you soon.
        </div>
    <?php endif; ?>

    <form method="post" class="contact-form">
        <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" required>
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn-main">Send Message</button>
    </form>
</main>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
</footer>

</body>
</html>
