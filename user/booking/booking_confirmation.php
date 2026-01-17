<?php
session_start();
include "../../db_connect.php";

if (!isset($_GET['booking_id'])) {
    header("Location: ../home.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

// Fetch booking details
$sql = "SELECT b.*, e.title, e.event_date, v.venue_name, u.full_name, u.email 
        FROM event_booking b 
        JOIN events e ON b.event_id = e.event_id 
        LEFT JOIN event_venues v ON e.venue_id = v.venue_id
        JOIN user u ON b.user_id = u.user_id 
        WHERE b.booking_id = $booking_id";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching booking: " . mysqli_error($conn));
}
if (mysqli_num_rows($result) > 0) {
    $booking = mysqli_fetch_assoc($result);
} else {
    die("Booking not found.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed - Event Registration System</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../user.css"> <!-- Fixed path -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .confirmation-title {
            color: #333;
            margin-bottom: 10px;
        }

        .confirmation-details {
            margin-top: 30px;
            text-align: left;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .btn-home {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: #004b85;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-home:hover {
            background: #003366;
        }
    </style>
</head>

<body>
       <!-- HEADER / NAVIGATION -->
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="home.php" class="nav-link active">Home</a>

                <!-- EVENTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="eventsToggle">
                        Events <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="eventsMenu">
                        <a href="user/event.php?cat=Concerts">Concerts</a>
                        <a href="user/event.php?cat=Musical Festival">Musical Festival</a>
                        <a href="user/event.php?cat=Tech">Tech</a>
                    </div>
                </div>

                <!-- SPORTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="sportsToggle">
                        Sports <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="sportsMenu">
                        <a href="user/event.php?cat=Rugby">Rugby</a>
                        <a href="user/event.php?cat=Cricket">Cricket</a>
                        <a href="user/event.php?cat=Football">Football</a>
                    </div>
                </div>

                <!-- THEATRE DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="theatreToggle">
                        Theatre <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="theatreMenu">
                        <a href="user/event.php?cat=Drama">Drama</a>
                    </div>
                </div>

                <!-- HELP DROPDOWN -->
       
                    <a href="help_buyer.php" class="nav-link" >
                        Help 
                    </a>
              

                <a href="contact.php" class="nav-link">Contact Us</a>
            </div>

            <div class="nav-right">
                <a href="user/user_login.php" class="btn-nav">Sign In</a>
                <a href="user/user_register.php" class="btn-nav btn-nav-outline">Register</a>
            </div>
        </nav>
    </header>

    <div class="confirmation-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h1 class="confirmation-title">Payment Successful!</h1>
        <p>Your booking has been confirmed.</p>

        <div class="confirmation-details">
            <div class="detail-row">
                <span><strong>Booking ID:</strong></span>
                <span>#<?php echo $booking['booking_id']; ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Event:</strong></span>
                <span><?php echo htmlspecialchars($booking['title']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Venue:</strong></span>
                <span><?php echo htmlspecialchars($booking['venue_name']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Date:</strong></span>
                <span><?php echo htmlspecialchars($booking['event_date']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Customer Name:</strong></span>
                <span><?php echo htmlspecialchars($booking['full_name']); ?></span>
            </div>
            <div class="detail-row">
                <span><strong>Quantity:</strong></span>
                <span><?php echo $booking['quantity']; ?> Tickets</span>
            </div>
            <div class="detail-row">
                <span><strong>Total Paid:</strong></span>
                <span style="color: #28a745; font-weight: bold;">Rs.
                    <?php echo number_format($booking['total_price'], 2); ?></span>
            </div>
        </div>

        <a href="../../home.php" class="btn-home">Return to Home</a>
    </div>

    <!-- FOOTER -->
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
                    <li><a href="../../home.php">Home</a></li>
                    <li><a href="../../all_events.php">Events</a></li>
                    <li><a href="../../about.php">About Us</a></li>
                    <li><a href="../../contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact & Follow Us</h3>
                <p>Email: support@eventsystem.com</p>
                <p>Phone: +1 (555) 123-4567</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
        </div>
    </footer>

    <script src="../../script.js"></script>
</body>

</html>
