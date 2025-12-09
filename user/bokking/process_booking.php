<?php
session_start();
include "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../home.php");
    exit();
}

// In a real app, you would process the payment here and insert into the database.
// For this simulation, we'll just show a success message.

$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : 'Guest';
$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$seats = isset($_POST['seats']) ? $_POST['seats'] : [];

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .confirmation-box {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            color: #28a745;
            font-size: 60px;
            margin-bottom: 20px;
        }

        .btn-home {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #004b85;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-home:hover {
            background: #003660;
        }
    </style>
</head>

<body>
    <div class="confirmation-box">
        <div class="success-icon">&#10004;</div>
        <h1>Payment Successful!</h1>
        <p>Thank you, <strong><?php echo $name; ?></strong>.</p>
        <p>Your booking for Event ID #<?php echo $event_id; ?> has been confirmed.</p>
        <?php if (!empty($seats)): ?>
            <p>Seats: <?php echo implode(', ', $seats); ?></p>
        <?php endif; ?>
        <p>A confirmation email has been sent to your address.</p>

        <a href="../home.php" class="btn-home">Return to Home</a>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>We are dedicated to bringing you the best events in town. From concerts to tech conferences, we
                    handle it all with passion and precision.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="../home.php">Home</a></li>
                    <li><a href="../events.php">Events</a></li>
                    <li><a href="../about.php">About Us</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact & Follow Us</h3>
                <p>Email: support@eventsystem.com</p>
                <p>Phone: +1 (555) 123-4567</p>
                <div class="social-links">
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                    <a href="#">LinkedIn</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>