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
    $sql = "SELECT e.*, v.venue_name 
            FROM events e
            LEFT JOIN event_venues v ON e.venue_id = v.venue_id
            WHERE e.event_id = $event_id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
    }
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle booking logic here
    // For now, just show a success message
    $msg = "Ticket Details - Tickets are not reserved yet";
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Book Event - Event Registration System</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <a href="event.php?cat=Concerts">Concerts</a>
                        <a href="event.php?cat=Musical Festival">Musical Festival</a>
                        <a href="event.php?cat=Tech">Tech</a>
                    </div>
                </div>

                <!-- SPORTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="sportsToggle">
                        Sports <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="sportsMenu">
                        <a href="event.php?cat=Rugby">Rugby</a>
                        <a href="event.php?cat=Cricket">Cricket</a>
                        <a href="event.php?cat=Football">Football</a>
                    </div>
                </div>

                <!-- THEATRE DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="theatreToggle">
                        Theatre <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="theatreMenu">
                        <a href="event.php?cat=Drama">Drama</a>
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

    <script>
        // Check login status and handle form submission
        document.addEventListener('DOMContentLoaded', function () {
            const bookingForm = document.querySelector('form[action="checkout.php"]');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function (e) {
                    // Check if user is logged in (passed from PHP)
                    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

                    if (!isLoggedIn) {
                        // Prevent form submission
                        e.preventDefault();

                        // Store booking data in sessionStorage for after login
                        const formData = new FormData(this);
                        const bookingData = {
                            event_id: formData.get('event_id'),
                            seats: formData.getAll('seats[]'),
                            total_price: formData.get('total_price')
                        };
                        sessionStorage.setItem('pending_booking', JSON.stringify(bookingData));

                        // Redirect to login page
                        window.location.href = '../../user/user_login.php';
                    }
                    // If logged in, form submits normally to checkout.php
                });
            }
        });
    </script>
    <script src="../../script.js"></script>
</body>

</html>
