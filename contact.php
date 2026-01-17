<?php
session_start();
include "db_connect.php";

// Simple form handling
$messageSent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // You can store this in DB or email it later if required
    // For now, we simulate success
    $messageSent = true;
}

// Pre-fill user data if logged in
$user_name = '';
$user_email = '';
if (isset($_SESSION['user_id'])) {
    // If we have name in session
    if (isset($_SESSION['user_full_name'])) {
        $user_name = $_SESSION['user_full_name'];
    }
    // We could fetch email from DB if needed, but for now let's leave blank or rely on session if available
    // Assuming we might want to fetch it:
    if (isset($_SESSION['user_id'])) {
         $uid = $_SESSION['user_id'];
         $q = mysqli_query($conn, "SELECT email, full_name FROM user WHERE user_id = $uid");
         if($q && $row = mysqli_fetch_assoc($q)){
             $user_email = $row['email'];
             $user_name = $row['full_name']; // Ensure we have the latest
         }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Contact Us - Event Registration System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contact-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
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

    <main class="contact-container">

        <h2 style="margin-top:0;">Contact Us</h2>
        <p style="color: #666; margin-bottom: 30px;">If you have any questions or need help, please fill out the form below. We'd love to hear from you!</p>

        <?php if ($messageSent): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Your message has been sent successfully. We will get back to you soon.
            </div>
        <?php endif; ?>

        <form method="post" action="contact.php" class="contact-form">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($user_name); ?>">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($user_email); ?>">
            </div>

            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required placeholder="How can we help?">
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="6" required placeholder="Write your message here..."></textarea>
            </div>

            <button type="submit" class="btn-main" style="width: 100%; border:none; padding: 15px; font-size: 16px; cursor: pointer;">Send Message</button>
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
                    <li><a href="<?php echo isset($_SESSION['user_id']) ? 'logged_home.php' : 'home.php'; ?>">Home</a></li>
                    <li><a href="events.php">Events</a></li>
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
            <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>

</html>
