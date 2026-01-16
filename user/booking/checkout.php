<?php
session_start();
include "../../db_connect.php";

// Redirect if accessed directly without POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['event_id'])) {
    header("Location: ../home.php");
    exit();
}

$event_id = intval($_POST['event_id']);
$seats = isset($_POST['seats']) ? $_POST['seats'] : [];
$total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;

// Initialize user details
$name = '';
$email = '';

// Fetch user details from database if logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_sql = "SELECT full_name, email FROM user WHERE user_id = $user_id";
    $user_result = mysqli_query($conn, $user_sql);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $name = $user_row['full_name'];
        $email = $user_row['email'];
    }
} else {
    // If not logged in, rely on POST data (though flow suggests login required)
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
}

// Fetch event details
$event = null;
$sample_events = [
    9991 => ['title' => 'Summer Music Festival', 'venue_name' => 'City Park Arena', 'event_date' => '2025-07-15'],
    9992 => ['title' => 'Tech Innovators Summit', 'venue_name' => 'Convention Center', 'event_date' => '2025-08-20'],
    9993 => ['title' => 'Modern Art Exhibition', 'venue_name' => 'Downtown Gallery', 'event_date' => '2025-09-05']
];

if (array_key_exists($event_id, $sample_events)) {
    $event = $sample_events[$event_id];
} else {
    $sql = "SELECT e.*, v.venue_name 
            FROM events e
            LEFT JOIN event_venues v ON e.venue_id = v.venue_id
            WHERE e.event_id = $event_id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
    }
}

if (!$event) {
    die("Event not found.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Checkout - Event Registration System</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../user.css"> <!-- Fixed path for sibling css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .checkout-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .summary-box,
        .payment-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            flex: 1;
            min-width: 300px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .total-row {
            font-size: 1.2em;
            font-weight: bold;
            color: #004b85;
            border-top: 2px solid #eee;
            padding-top: 15px;
        }

        .payment-form input,
        .payment-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .card-row {
            display: flex;
            gap: 15px;
        }

        .btn-pay {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-pay:hover {
            background: #218838;
        }

        .section-title {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #004b85;
            padding-bottom: 10px;
            display: inline-block;
        }

        .payment-icons {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: center;
        }

        .payment-icons i {
            font-size: 32px;
            color: #555;
        }
    </style>
</head>

<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="../../home.php" class="nav-link">Home</a>

                <!-- EVENTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="eventsToggle">
                        Events <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="eventsMenu">
                        <a href="../event.php?cat=Concerts">Concerts</a>
                        <a href="../event.php?cat=Musical Festival">Musical Festival</a>
                        <a href="../event.php?cat=Tech">Tech</a>
                    </div>
                </div>

                <!-- SPORTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="sportsToggle">
                        Sports <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="sportsMenu">
                        <a href="../event.php?cat=Rugby">Rugby</a>
                        <a href="../event.php?cat=Cricket">Cricket</a>
                        <a href="../event.php?cat=Football">Football</a>
                    </div>
                </div>

                <!-- THEATRE DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="theatreToggle">
                        Theatre <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="theatreMenu">
                        <a href="../event.php?cat=Drama">Drama</a>
                    </div>
                </div>

                <!-- HELP DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="helpToggle">
                        Help <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="helpMenu">
                        <a href="../../help_buyer.php?cat=user">I am a ticket buyer</a>
                    </div>
                </div>

                <a href="../../contact.php" class="nav-link">Contact Us</a>
            </div>

            <div class="nav-right">
                <?php if (isset($_SESSION['user_id'])):
                    $user_name = isset($_SESSION['user_full_name']) ? $_SESSION['user_full_name'] : 'User';
                    ?>
                    <span class="welcome-text" style="color: white; margin-right: 15px; font-weight: 600;">Welcome,
                        <?php echo htmlspecialchars($user_name); ?>!</span>
                    <a href="../../user/user_logout.php" class="btn-nav">Logout</a>
                <?php else: ?>
                    <a href="../../user/user_login.php" class="btn-nav">Sign In</a>
                    <a href="../../user/user_register.php" class="btn-nav btn-nav-outline">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="checkout-container">
        <!-- Booking Summary -->
        <div class="summary-box">
            <h2 class="section-title">Booking Summary</h2>

            <div class="summary-row">
                <span><strong>Event:</strong></span>
                <span><?php echo htmlspecialchars($event['title']); ?></span>
            </div>
            <div class="summary-row">
                <span><strong>Venue:</strong></span>
                <span><?php echo htmlspecialchars($event['venue_name'] ?? 'TBA'); ?></span>
            </div>
            <div class="summary-row">
                <span><strong>Date:</strong></span>
                <span><?php echo htmlspecialchars($event['event_date']); ?></span>
            </div>
            <div class="summary-row">
                <span><strong>Name:</strong></span>
                <span><?php echo htmlspecialchars($name); ?></span>
            </div>
            <div class="summary-row">
                <span><strong>Email:</strong></span>
                <span><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="summary-row">
                <span><strong>Seats:</strong></span>
                <span><?php echo implode(', ', $seats); ?></span>
            </div>

            <div class="summary-row total-row">
                <span>Total Amount:</span>
                <span>Rs. <?php echo number_format($total_price, 2); ?></span>
            </div>
        </div>

        <!-- Payment Gateway -->
        <div class="payment-box">
            <h2 class="section-title">Payment Details</h2>
            <p style="margin-bottom: 20px; color: #666; font-size: 14px;">Secure Payment Gateway (Simulation)</p>

            <form action="process_booking.php" method="post" class="payment-form">
                <!-- Pass all data to processing page -->
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <?php foreach ($seats as $seat): ?>
                    <input type="hidden" name="seats[]" value="<?php echo htmlspecialchars($seat); ?>">
                <?php endforeach; ?>

                <label>Payment Method</label>
                <div class="payment-icons">
                    <i class="fab fa-cc-visa" style="color: #1A1F71;"></i>
                    <i class="fab fa-cc-mastercard" style="color: #EB001B;"></i>
                </div>
                <select name="payment_method" required>
                    <option value="" disabled selected>Select Payment Method</option>
                    <option value="visa">Visa</option>
                    <option value="mastercard">MasterCard</option>
                </select>

                <label>Cardholder Name</label>
                <input type="text" name="cardholder_name" placeholder="John Doe" required
                    value="<?php echo htmlspecialchars($name); ?>">

                <label>Card Number</label>
                <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>

                <div class="card-row">
                    <div style="flex: 1;">
                        <label>Expiry Date</label>
                        <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    <div style="flex: 1;">
                        <label>CVV</label>
                        <input type="text" name="cvv" placeholder="123" maxlength="3" required>
                    </div>
                </div>

                <button type="submit" class="btn-pay">Pay Rs. <?php echo number_format($total_price, 2); ?></button>
            </form>
        </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const expiryInput = document.querySelector('input[name="expiry"]');
            const form = document.querySelector('.payment-form');

            // Auto-format expiry date (MM/YY)
            expiryInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }

                e.target.value = value;
            });

            // Validate on form submission
            form.addEventListener('submit', function (e) {
                const expiryValue = expiryInput.value;
                const regex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;

                if (!regex.test(expiryValue)) {
                    alert('Please enter a valid expiry date in MM/YY format.');
                    e.preventDefault();
                    return;
                }

                // Check if card is expired
                const parts = expiryValue.split('/');
                const month = parseInt(parts[0], 10);
                const year = parseInt('20' + parts[1], 10); // Assume 20xx

                const now = new Date();
                const currentMonth = now.getMonth() + 1;
                const currentYear = now.getFullYear();

                if (year < currentYear || (year === currentYear && month < currentMonth)) {
                    alert('Your card has expired.');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
    <script src="../../script.js"></script>
</body>

</html>
