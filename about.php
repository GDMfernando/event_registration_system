<?php
session_start();
include "db_connect.php";

// Pre-fill user data if logged in
$user_name = '';
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_full_name'])) {
        $user_name = $_SESSION['user_full_name'];
    } else {
        $uid = $_SESSION['user_id'];
        $q = mysqli_query($conn, "SELECT full_name FROM user WHERE user_id = $uid");
        if ($q && $row = mysqli_fetch_assoc($q)) {
            $user_name = $row['full_name'];
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>About Us - Event Registration System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .about-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            line-height: 1.6;
            color: #444;
        }

        .about-container h2 {
            color: #004b85;
            margin-top: 0;
            font-size: 28px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .about-container h3 {
            color: #333;
            margin-top: 30px;
            font-size: 22px;
        }

        .about-container p {
            margin-bottom: 15px;
        }

        .about-container ul {
            list-style-type: none;
            padding: 0;
        }

        .about-container ul li {
            margin-bottom: 15px;
            padding-left: 20px;
            position: relative;
        }

        .about-container ul li strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }

        .about-container ul li::before {
            content: "•";
            color: #45a1e2;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 18px;
            line-height: 1.4;
        }

        .hero-banner {
            background: linear-gradient(135deg, #004b85 0%, #0082c8 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .hero-banner h1 {
            margin: 0;
            font-size: 36px;
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
                        <a href="event.php">Cricket</a>
                        <a href="event.php">Football</a>
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

                <a href="help_buyer.php" class="nav-link">
                    Help
                </a>


                <a href="contact.php" class="nav-link">Contact Us</a>
            </div>

            <div class="nav-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">Welcome,
                        <?php echo htmlspecialchars($user_name); ?>!
                    </span>
                    <a href="user/user_logout.php" class="btn-nav">Logout</a>
                <?php else: ?>
                    <a href="user/user_login.php" class="btn-nav">Sign In</a>
                    <a href="user/user_register.php" class="btn-nav btn-nav-outline">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>


    <div class="hero-banner">
        <h1>About Us</h1>
        <p>Turning Your Vision into Reality</p>
    </div>

    <main class="about-container">
        <h2>Welcome to Event Registration System!</h2>
        <p>At Event Registration System, we specialize in making events memorable, seamless, and stress-free. Whether
            you're planning a corporate conference, a wedding, a concert, or any other special occasion, our all-in-one
            platform provides everything you need to streamline your event planning process from start to finish.</p>

        <h3>Our Mission</h3>
        <p>Our mission is to empower event organizers by providing a user-friendly, efficient, and innovative platform
            that simplifies the event management process. We aim to help you save time, reduce stress, and deliver
            extraordinary experiences for your attendees. With a range of customizable tools and real-time updates, we
            ensure that every detail of your event is meticulously planned and executed.</p>

        <h3>What We Do</h3>
        <ul>
            <li>
                <strong>Event Planning & Coordination:</strong>
                From creating your event schedule to managing attendees, our platform helps you stay organized
                throughout the entire event planning journey.
            </li>
            <li>
                <strong>Registration & Ticketing:</strong>
                Our system offers easy-to-use event registration and ticketing solutions, allowing your guests to sign
                up or purchase tickets effortlessly.
            </li>
            <li>
                <strong>Real-Time Analytics & Reporting:</strong>
                Stay on top of your event's progress with insightful analytics, helping you make data-driven decisions
                before, during, and after your event.
            </li>
            <li>
                <strong>Payment Processing:</strong>
                We offer secure and reliable payment processing to handle event fees, ticket sales, and any additional
                services or merchandise.
            </li>
            <li>
                <strong>Customizable Event Pages:</strong>
                Personalize your event page with your branding, event details, and more to create a professional, unique
                experience for your attendees.
            </li>
            <li>
                <strong>Attendee Engagement:</strong>
                Enhance attendee interaction with features like live polls, Q&A sessions, and feedback forms to foster
                engagement throughout your event.
            </li>
        </ul>

        <h3>Why Choose Us?</h3>
        <ul>
            <li>
                <strong>User-Friendly Interface:</strong>
                No technical skills required! Our platform is intuitive and easy to use, even for first-time event
                organizers.
            </li>
            <li>
                <strong>24/7 Support:</strong>
                Our dedicated customer service team is available around the clock to assist you with any questions or
                issues that may arise.
            </li>
            <li>
                <strong>Secure & Reliable:</strong>
                We prioritize the safety and security of your event data, ensuring that your attendees' information and
                transactions are always protected.
            </li>
            <li>
                <strong>Scalability:</strong>
                Whether you're organizing a small intimate gathering or a large-scale conference, our platform can scale
                to fit events of any size.
            </li>
        </ul>

        <p style="margin-top: 30px; font-style: italic;">
            At Event Registration System, we believe in turning every event into an unforgettable experience. Let us
            handle the details, so you can focus on creating lasting memories.
        </p>

        <h3>Join Us Today</h3>
        <p>Ready to make your next event a success? Sign up with us today and take the first step toward an effortless,
            exciting, and memorable event experience!</p>

        <div style="margin-top: 30px; text-align: center;">
            <a href="register.php" class="btn-nav" style="background:#28a745; padding: 15px 30px; font-size: 18px;">Get
                Started Now</a>
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
                    <li><a href="<?php echo isset($_SESSION['user_id']) ? 'logged_home.php' : 'home.php'; ?>">Home</a>
                    </li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
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