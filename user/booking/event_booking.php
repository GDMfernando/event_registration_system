<?php
session_start();
include "../../db_connect.php";

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$event = null;

// Check if it's a sample event
$sample_events = [
    9991 => [
        'event_id' => 9991,
        'title' => 'Summer Music Festival',
        'ticket_price' => 45.00,
        'venue_name' => 'City Park Arena',
        'event_date' => '2025-07-15'
    ],
    9992 => [
        'event_id' => 9992,
        'title' => 'Tech Innovators Summit',
        'ticket_price' => 120.00,
        'venue_name' => 'Convention Center',
        'event_date' => '2025-08-20'
    ],
    9993 => [
        'event_id' => 9993,
        'title' => 'Modern Art Exhibition',
        'ticket_price' => 15.00,
        'venue_name' => 'Downtown Gallery',
        'event_date' => '2025-09-05'
    ]
];

if (array_key_exists($event_id, $sample_events)) {
    $event = $sample_events[$event_id];
} else {
    // Try fetching from DB
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
    }
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle booking logic here
    // For now, just show a success message
    $msg = "Booking successful! (Simulation)";
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Book Event - Event Registration System</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="user.css">
</head>

<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="../home.php" class="nav-link">Home</a>
                <a href="../events.php" class="nav-link">Events</a>
                <a href="../events.php?cat=Sports" class="nav-link">Sports</a>
                <a href="../events.php?cat=Theatre" class="nav-link">Theatre</a>
                <a href="../about.php" class="nav-link">About</a>
            </div>

            <div class="nav-right">
                <a href="../login.php" class="btn-nav">Sign In</a>
                <a href="../register.php" class="btn-nav btn-nav-outline">Register</a>
            </div>
        </nav>
    </header>

    <main class="booking-container">
        <?php if ($msg): ?>
            <div class="alert"><?php echo $msg; ?></div>
        <?php endif; ?>

        <?php if ($event): ?>
            <?php
            $selected_seats = isset($_POST['seats']) ? $_POST['seats'] : [];
            $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
            $seat_count = count($selected_seats);
            ?>

            <h2>Book Tickets for <?php echo htmlspecialchars($event['title']); ?></h2>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue_name'] ?? 'TBA'); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <p><strong>Selected Seats (<?php echo $seat_count; ?>):</strong>
                    <?php echo implode(', ', $selected_seats); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>
            </div>

            <form method="post" action="checkout.php">
                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                <!-- Pass seat data to the final processing logic -->
                <?php foreach ($selected_seats as $seat): ?>
                    <input type="hidden" name="seats[]" value="<?php echo htmlspecialchars($seat); ?>">
                <?php endforeach; ?>
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <button type="submit" class="btn-submit">Confirm Booking</button>
            </form>
        <?php else: ?>
            <p>Event not found.</p>
            <a href="../home.php">Go Back</a>
        <?php endif; ?>
    </main>

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

    <script>
        // Dropdown script removed
    </script>
</body>

</html>