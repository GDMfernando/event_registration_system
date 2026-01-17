<?php
session_start();
include "db_connect.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Help - Ticket Purchases</title>
    <link rel="stylesheet" href="style.css">
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

    <main class="container">
        <div class="help-page-layout">
            <aside class="help-sidebar">
                <h3><a href="help_buyer.php" style="text-decoration: none; color: #ffd700;">I am a ticket buyer</a></h3>
                <ul class="sidebar-list">
                    <li><a href="ticket_purchases.php">How can I buy a Ticket?</a></li>
                    <li><a href="change_ticket_name.php">Changing A Ticket Name</a></li>
                    <li><a href="accessibility.php">Accessibility</a></li>
                    <li><a href="refunds.php">Refunds</a></li>
                    <li><a href="event_infor.php">Event Information</a></li>
                </ul>
            </aside>

            <div class="help-main-content">
                <h2>Ticket Purchases</h2>
                <div class="help-content">
                    <h3>How can I buy a ticket?</h3>
                    <p>Buying a ticket is quick and easy! Just follow these steps:</p>
                    <ol style="margin-bottom: 20px; margin-left: 20px;">
                        <li><strong>Browse Events:</strong> Go to the <a href="home.php">Home</a> page to see all
                            upcoming events.</li>
                        <img src="help_images/browse_events_demo.png" alt="Browse Events Demo"
                            style="max-width: 100%; border-radius: 8px; margin: 10px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <li><strong>Select an Event:</strong> Click on "View Details" to learn more about the event or
                            "Buy Tickets" to go directly to the seat selection.</li>
                        <li><strong>Choose Your Seats:</strong> On the seat plan page, select your preferred seats by
                            clicking on them.</li>
                        <img src="help_images/seat_selection_demo.png" alt="Seat Selection Demo"
                            style="max-width: 100%; border-radius: 8px; margin: 10px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <li><strong>Confirm Selection:</strong> Once you've selected your seats, click the "Confirm
                            Selection" button.</li>
                        <img src="help_images/booking_confirmation_demo.png" alt="Booking Confirmation Demo"
                            style="max-width: 100%; border-radius: 8px; margin: 10px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <li><strong>Checkout:</strong> Review your order on the checkout page and proceed to payment.
                        </li>
                        <img src="help_images/checkout_demo.png" alt="Checkout Demo"
                            style="max-width: 100%; border-radius: 8px; margin: 10px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <li><strong>Receive Confirmation:</strong> After successful payment, you'll receive an email
                            confirmation and can view your ticket in your account.</li>
                        <img src="help_images/payment_success_new.png" alt="Payment Success Demo"
                            style="max-width: 100%; border-radius: 8px; margin: 10px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    </ol>

                    <h3>I haven't received my tickets, what should I do?</h3>
                    <p>Be sure to check all incoming email folders, including your spam/junk. Alternatively you can view
                        your order by navigating to the 'View Your Order' page located in the Support section of the
                        event store page.</p>
                    <p>If it's not there we recommend contacting our support team via our contact numbers. Please use
                        the contact numbers provided on our <a href="contact.php">Contact Us</a> page.</p>
                    <h3>I've tried to pay but your site won't accept payment</h3>
                    <p>Are you sure you've entered all of your details correctly? Yes, we know that's an annoying
                        question but it's very commonly the reason.
                        But if you have and you're still facing difficulties you can leave a message with one of our
                        team via the submitting the request</p>
                    <h3>I haven't received my email confirmation, what do I do?</h3>
                    <p>Be sure to check all incoming email folders, including your spam/junk. Alternatively you can view
                        your order by navigating to the 'View Your Order' page located in the Support section of the
                        event store page.</p>
                    <p>If it's not there we recommend contacting our support team via contact number</p>
                    <h3>The checkout page timed out when I was trying to pay</h3>
                    <p>your checkout session timed out while you were making the payment.
                        This can happen if the page stays inactive for too long or due to a network issue.
                        Please refresh the page and try again. If the issue continues, contact our support team for
                        help.</p>
                </div>
                <div class="help-footer">
                    <h3>Have more questions?</h3>
                    <p><a href="request.php" class="btn-request">Submit a request</a></p>
                </div>
            </div>
        </div>
    </main>

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
                    <li><a href="events.php">Events</a></li>
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
            <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>

</html>