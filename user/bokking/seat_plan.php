<?php
session_start();
include "../db_connect.php";

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$event = null;

// Sample events data (duplicated for consistency)
$sample_events = [
    9991 => [
        'event_id' => 9991,
        'title' => 'Summer Music Festival',
        'venue_name' => 'City Park Arena',
        'event_date' => '2025-07-15'
    ],
    9992 => [
        'event_id' => 9992,
        'title' => 'Tech Innovators Summit',
        'venue_name' => 'Convention Center',
        'event_date' => '2025-08-20'
    ],
    9993 => [
        'event_id' => 9993,
        'title' => 'Modern Art Exhibition',
        'venue_name' => 'Downtown Gallery',
        'event_date' => '2025-09-05'
    ]
];

if (array_key_exists($event_id, $sample_events)) {
    $event = $sample_events[$event_id];
} else {
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
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
    <title>Select Seats - <?php echo htmlspecialchars($event['title']); ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="user.css">
</head>

<body>
    <!-- HEADER / NAVIGATION -->
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

    <main class="seat-container">
        <h2>Select Your Seats</h2>
        <p><?php echo htmlspecialchars($event['title']); ?> | <?php echo htmlspecialchars($event['event_date']); ?></p>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-box" style="border: 2px solid #ffd700; background: #ddd;"></div>
                VIP (10,000)
            </div>
            <div class="legend-item">
                <div class="legend-box" style="border: 2px solid #007bff; background: #ddd;"></div>
                Regular (5,000)
            </div>
            <div class="legend-item">
                <div class="legend-box" style="border: 2px solid #6c757d; background: #ddd;"></div>
                Balcony (3,000)
            </div>
            <div class="legend-item">
                <div class="legend-box" style="background: #28a745;"></div>
                Selected
            </div>
        </div>

        <div class="screen">STAGE</div>

        <form action="event_booking.php?event_id=<?php echo $event_id; ?>" method="POST" id="seatForm">
            <div class="seat-map">
                <!-- VIP Rows -->
                <?php for ($r = 1; $r <= 2; $r++): ?>
                    <div class="seat-row">
                        <?php for ($s = 1; $s <= 8; $s++):
                            $seatId = "VIP-$r-$s"; ?>
                            <div class="seat-vip">
                                <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="10000"
                                    id="<?php echo $seatId; ?>" class="seat-checkbox">
                                <label for="<?php echo $seatId; ?>" class="seat-label"
                                    title="VIP Row <?php echo $r; ?> Seat <?php echo $s; ?>">V<?php echo $s; ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>

                <!-- Regular Rows -->
                <?php for ($r = 1; $r <= 4; $r++): ?>
                    <div class="seat-row">
                        <?php for ($s = 1; $s <= 10; $s++):
                            $seatId = "REG-$r-$s"; ?>
                            <div class="seat-regular">
                                <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="5000"
                                    id="<?php echo $seatId; ?>" class="seat-checkbox">
                                <label for="<?php echo $seatId; ?>" class="seat-label"
                                    title="Regular Row <?php echo $r; ?> Seat <?php echo $s; ?>">R<?php echo $s; ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>

                <!-- Balcony Rows -->
                <div style="margin-top: 15px;"></div>
                <?php for ($r = 1; $r <= 2; $r++): ?>
                    <div class="seat-row">
                        <?php for ($s = 1; $s <= 12; $s++):
                            $seatId = "BAL-$r-$s"; ?>
                            <div class="seat-balcony">
                                <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="3000"
                                    id="<?php echo $seatId; ?>" class="seat-checkbox">
                                <label for="<?php echo $seatId; ?>" class="seat-label"
                                    title="Balcony Row <?php echo $r; ?> Seat <?php echo $s; ?>">B<?php echo $s; ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <input type="hidden" name="total_price" id="inputTotalPrice" value="0">

            <div class="summary-bar">
                <div>
                    <span id="count">0</span> seats selected
                </div>
                <div class="total-price">
                    Total: <span id="total">0</span>
                </div>
                <button type="submit" class="btn-main" id="proceedBtn" disabled>Proceed</button>
            </div>
        </form>
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
        const checkboxes = document.querySelectorAll('.seat-checkbox');
        const countSpan = document.getElementById('count');
        const totalSpan = document.getElementById('total');
        const inputTotal = document.getElementById('inputTotalPrice');
        const proceedBtn = document.getElementById('proceedBtn');

        checkboxes.forEach(seat => {
            seat.addEventListener('change', updateSummary);
        });

        function updateSummary() {
            let count = 0;
            let total = 0;

            checkboxes.forEach(seat => {
                if (seat.checked) {
                    count++;
                    total += parseInt(seat.getAttribute('data-price'));
                }
            });

            countSpan.innerText = count;
            totalSpan.innerText = total.toLocaleString();
            inputTotal.value = total;

            if (count > 0) {
                proceedBtn.disabled = false;
                proceedBtn.style.opacity = "1";
                proceedBtn.style.cursor = "pointer";
            } else {
                proceedBtn.disabled = true;
                proceedBtn.style.opacity = "0.6";
                proceedBtn.style.cursor = "not-allowed";
            }
        }

        // Dropdown script removed
    </script>

</body>

</html>