<?php
session_start();
include "db_connect.php";

// Pre-fill user name if logged in
$user_name = '';
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_full_name'])) {
        $user_name = $_SESSION['user_full_name'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Lankan Rugby Sevens 2026 - Event Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .details-container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .event-header-img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .event-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .event-info-meta {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .event-date-badge {
            display: inline-block;
            background: #edf2f7;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .payment-methods-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #004b85;
        }

        .payment-icons {
            display: flex;
            gap: 15px;
            font-size: 30px;
            margin-top: 10px;
            color: #2d3748;
        }

        .policy-section {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 30px;
        }

        .policy-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .policy-content {
            line-height: 1.7;
            color: #4a5568;
        }

        .policy-content ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .buy-tickets-btn {
            display: block;
            width: 100%;
            text-align: center;
            background: #004b85;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 18px;
            text-decoration: none;
            margin-top: 30px;
            transition: background 0.3s;
        }

        .buy-tickets-btn:hover {
            background: #00335d;
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

    <main class="container">
        <div class="details-container">
            <img src="uploads/event_images/event_img_69687c97edce75.08151091.jpg" class="event-header-img"
                alt="Lankan Rugby Sevens 2026">

            <h1 class="event-title">Lankan Rugby Sevens 2026</h1>
            <div class="event-info-meta">
                <span>Sports</span> |
                <span>Race Course International Stadium</span> |
                <span>Rugby Ground</span>
            </div>

            <div class="event-date-badge">
                <i class="far fa-calendar-alt"></i> Date: 2026-02-22
            </div>

            <div class="payment-methods-section">
                <strong>Available Payment Methods</strong>
                <div class="payment-icons">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                </div>
                <p style="margin-top: 10px; font-size: 14px; color: #718096;">You can select the payment method in the
                    checkout page.</p>
            </div>

            <div class="policy-section">
                <h2 class="policy-title">Booking Policy</h2>
                <div class="policy-content">
                    <p>Only the E-Ticket provided by EventHub will be accepted as proof of purchase.</p>
                    <p>Tickets will not be Scanned for any forwarded or screenshots.</p>
                    <p>A valid NIC or Passport will be required if needed during redemption.</p>
                    <p><strong>All Tickets Purchased Are Non-Refundable</strong></p>
                </div>

                <h2 class="policy-title" style="margin-top:30px;">Child Policy</h2>
                <div class="policy-content">
                    <strong>1. Age Restriction</strong>
                    <ul>
                        <li>Children under 05 years of age will not be permitted inside the venue.</li>
                        <li>Entry will be strictly denied regardless of ticket purchase if the child does not meet the
                            age requirement.</li>
                    </ul>

                    <strong>2. Ticketing</strong>
                    <ul>
                        <li>All attendees above the minimum age must hold a valid ticket.</li>
                        <li>No lap seating or complimentary entry for children will be allowed.</li>
                    </ul>

                    <strong>3. Supervision & Responsibility</strong>
                    <ul>
                        <li>Minors aged 05–17 years must be accompanied by a parent or legal guardian.</li>
                        <li>Parents/guardians are fully responsible for their child’s behavior and safety at all times.
                        </li>
                    </ul>

                    <strong>4. Noise & Disturbance</strong>
                    <ul>
                        <li>Any attendee (including minors) causing disturbance will be required to leave the stadium
                            immediately, with no ticket refund.</li>
                    </ul>

                    <strong>5. Content Advisory</strong>
                    <ul>
                        <li>The event may include strong sound, lighting, and/or thematic content that may not be
                            suitable for younger audiences. Viewer discretion is advised.</li>
                    </ul>

                    <p
                        style="margin-top: 15px; border-left: 3px solid #28a745; padding-left: 10px; font-style: italic;">
                        Entry permitted for children (10 Yrs and above) with a valid ticket only.
                    </p>
                </div>
            </div>

            <a href="user/booking/seat_plan.php?event_id=9996" class="buy-tickets-btn">Buy Tickets Now</a>
        </div>
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
                    <li><a href="home.php">Home</a></li>
                    <li><a href="all_events.php">Events</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
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
            <p>&copy;
                <?php echo date('Y'); ?> Event Registration System. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>

</html>