<?php
session_start();
include "db_connect.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Help - Event Information</title>
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
                    <li><a href="change_ticket_name.php">Changing A Ticket Name</a></li>
                    <li><a href="accessibility.php">Accessibility</a></li>
                    <li><a href="refunds.php">Refunds</a></li>
                    <li><a href="ticket_purchases.php">Ticket Purchases</a></li>
                    <li><a href="event_infor.php">Event Information</a></li>
                </ul>
            </aside>

            <div class="help-main-content">
                <h2>Event Information</h2>
                <div class="help-content">
                    <h3>Where can I find more details about an event?</h3>
                    <p>You can find all the details about an event on the event detail page. Simply click on the event name or image from the home or category pages to see the full description, venue details, and date.</p>
                    
                    <h3>What if an event is cancelled or rescheduled?</h3>
                    <p>If an event is cancelled or rescheduled, we will notify you via the email address used for registration. You can also check the event page for updates.</p>

                    <h3>Can I contact the event organiser directly?</h3>
                    <p>Yes, if the organiser has provided contact details, they will be listed on the event page under the 'Organiser Information' section.</p>
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
