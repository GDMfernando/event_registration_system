<?php
session_start();
include "../../db_connect.php";

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
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- HEADER / NAVIGATION -->
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
                        <a href="../../events.php?cat=Concerts">Concerts</a>
                        <a href="../../events.php?cat=Musical Festival">Musical Festival</a>
                        <a href="../../events.php?cat=Tech">Tech</a>
                    </div>
                </div>

                <!-- SPORTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="sportsToggle">
                        Sports <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="sportsMenu">
                        <a href="../../events.php?cat=Rugby">Rugby</a>
                        <a href="../../events.php?cat=Cricket">Cricket</a>
                        <a href="../../events.php?cat=Football">Football</a>
                    </div>
                </div>

                <!-- THEATRE DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="theatreToggle">
                        Theatre <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="theatreMenu">
                        <a href="../../events.php?cat=Drama">Drama</a>
                    </div>
                </div>

                <a href="../../about.php" class="nav-link">About</a>
            </div>

            <div class="nav-right">
                <a href="../../login.php" class="btn-nav">Sign In</a>
                <a href="../../register.php" class="btn-nav btn-nav-outline">Register</a>
            </div>
        </nav>
    </header>

    <main class="seat-container">
        <h2>Select Your Seats</h2>
        <p><?php echo htmlspecialchars($event['title']); ?> | <?php echo htmlspecialchars($event['event_date']); ?></p>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-box"
                    style="border: 3px solid #ffd700; background: linear-gradient(135deg, #fef5e7 0%, #fdebd0 100%);">
                </div>
                VIP - Rs. 10,000
            </div>
            <div class="legend-item">
                <div class="legend-box"
                    style="border: 3px solid #4299e1; background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);">
                </div>
                Regular - Rs. 5,000
            </div>
            <div class="legend-item">
                <div class="legend-box"
                    style="border: 3px solid #718096; background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%);">
                </div>
                Balcony - Rs. 3,000
            </div>
            <div class="legend-item">
                <div class="legend-box" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);"></div>
                Selected
            </div>
        </div>

        <div class="screen">STAGE</div>

        <form action="event_booking.php?event_id=<?php echo $event_id; ?>" method="POST" id="seatForm">
            <div class="seat-map">

                <!-- VIP SECTION (Front 3 rows - Premium seats) -->
                <div style="margin-bottom: 20px; width: 100%;">
                    <h4 style="color: #b8860b; font-size: 14px; margin-bottom: 10px; font-weight: 700;">VIP SECTION</h4>

                    <?php for ($r = 1; $r <= 3; $r++): ?>
                        <div class="seat-row" data-row="V<?php echo $r; ?>">
                            <?php
                            // VIP rows have 10 seats each
                            for ($s = 1; $s <= 10; $s++):
                                $seatId = "VIP-$r-$s";
                                ?>
                                <div class="seat-vip">
                                    <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="10000"
                                        id="<?php echo $seatId; ?>" class="seat-checkbox">
                                    <label for="<?php echo $seatId; ?>" class="seat-label"
                                        title="VIP Row <?php echo $r; ?> Seat <?php echo $s; ?>">
                                        <?php echo $s; ?>
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- REGULAR SECTION (Middle 6 rows with center aisle) -->
                <div style="margin-bottom: 25px; width: 100%;">
                    <h4 style="color: #2c5282; font-size: 14px; margin-bottom: 10px; font-weight: 700;">REGULAR SECTION
                    </h4>

                    <?php for ($r = 1; $r <= 6; $r++): ?>
                        <div class="seat-row" data-row="R<?php echo $r; ?>">
                            <?php
                            // Left side - 6 seats
                            for ($s = 1; $s <= 6; $s++):
                                $seatId = "REG-$r-$s";
                                ?>
                                <div class="seat-regular">
                                    <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="5000"
                                        id="<?php echo $seatId; ?>" class="seat-checkbox">
                                    <label for="<?php echo $seatId; ?>" class="seat-label"
                                        title="Regular Row <?php echo $r; ?> Seat <?php echo $s; ?>">
                                        <?php echo $s; ?>
                                    </label>
                                </div>
                            <?php endfor; ?>

                            <!-- Center Aisle -->
                            <div style="width: 30px;"></div>

                            <?php
                            // Right side - 6 seats
                            for ($s = 7; $s <= 12; $s++):
                                $seatId = "REG-$r-$s";
                                ?>
                                <div class="seat-regular">
                                    <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="5000"
                                        id="<?php echo $seatId; ?>" class="seat-checkbox">
                                    <label for="<?php echo $seatId; ?>" class="seat-label"
                                        title="Regular Row <?php echo $r; ?> Seat <?php echo $s; ?>">
                                        <?php echo $s; ?>
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- BALCONY SECTION (Back 4 rows - Budget seats) -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px dashed rgba(0,0,0,0.1); width: 100%;">
                    <h4 style="color: #2d3748; font-size: 14px; margin-bottom: 10px; font-weight: 700;">BALCONY SECTION
                    </h4>

                    <?php for ($r = 1; $r <= 4; $r++): ?>
                        <div class="seat-row" data-row="B<?php echo $r; ?>">
                            <?php
                            // Balcony rows have 14 seats each (wider section)
                            for ($s = 1; $s <= 14; $s++):
                                $seatId = "BAL-$r-$s";
                                ?>
                                <div class="seat-balcony">
                                    <input type="checkbox" name="seats[]" value="<?php echo $seatId; ?>" data-price="3000"
                                        id="<?php echo $seatId; ?>" class="seat-checkbox">
                                    <label for="<?php echo $seatId; ?>" class="seat-label"
                                        title="Balcony Row <?php echo $r; ?> Seat <?php echo $s; ?>">
                                        <?php echo $s; ?>
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>

            </div>

            <input type="hidden" name="total_price" id="inputTotalPrice" value="0">

            <div class="summary-bar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chair" style="color: #667eea; font-size: 20px;"></i>
                    <span style="font-size: 16px; color: #4a5568;">
                        <span id="count" style="font-size: 24px; font-weight: 700; color: #667eea;">0</span> 
                        <span style="font-weight: 600;">seats selected</span>
                    </span>
                </div>
                <div class="total-price">
                    <span style="font-size: 14px; color: #718096; font-weight: 600;">Total Amount:</span>
                    <span style="display: block; font-size: 28px; font-weight: 700; color: #48bb78;">
                        Rs. <span id="total">0</span>
                    </span>
                </div>
                <button type="submit" class="btn-main" id="proceedBtn" disabled>
                    <i class="fas fa-arrow-right" style="margin-right: 8px;"></i>
                    Proceed to Booking
                </button>
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
                    <li><a href="../../home.php">Home</a></li>
                    <li><a href="../../events.php">Events</a></li>
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

    <script src="../../script.js"></script>

</body>

</html>