<?php
session_start();
include "db_connect.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $order_reference = mysqli_real_escape_string($conn, $_POST['order_reference']);

    // Validate Ticket ID (order_reference) against event_booking table
    if (!empty($order_reference)) {
        $check_ticket_sql = "SELECT ticket_id FROM event_booking WHERE ticket_id = '$order_reference'";
        $check_ticket_result = mysqli_query($conn, $check_ticket_sql);

        if (mysqli_num_rows($check_ticket_result) == 0) {
            $message = "Your Ticked Id is not correct";
            $message_type = "error";
        }
    }

    if ($message === "") {
        $attachment_paths = array();
        $upload_dir = 'uploads/support_attachments/';

        if (!empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                $file_name = time() . '_' . basename($_FILES['attachments']['name'][$key]);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $attachment_paths[] = $target_file;
                }
            }
        }

        // Join paths with commas for the VARCHAR(255) column
        $attachment_path = implode(',', $attachment_paths);
        if (strlen($attachment_path) > 255) {
            $attachment_path = substr($attachment_path, 0, 252) . '...';
        }

        $sql = "INSERT INTO support_requests (email, subject, description, ticket_id, attachment_path, status, submitted_at) 
            VALUES ('$email', '$subject', '$description', '$order_reference', '$attachment_path', 'New', NOW())";

        if (mysqli_query($conn, $sql)) {
            $message = "Your request has been successfully submitted. We will get back to you shortly.";
            $message_type = "success";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a request - Event Registration System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .request-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .request-form-card {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .request-form-card h1 {
            color: #004b85;
            margin-bottom: 30px;
            font-size: 28px;
            border-bottom: 2px solid #f4f4f4;
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 15px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #45a1e2;
            box-shadow: 0 0 0 3px rgba(69, 161, 226, 0.1);
            outline: none;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-hint {
            display: block;
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .file-upload-wrapper {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .file-upload-wrapper:hover {
            background: #fcfcfc;
            border-color: #45a1e2;
        }

        .btn-submit {
            background: #004b85;
            color: #fff;
            border: none;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
        }

        .btn-submit:hover {
            background: #003a66;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 600px) {
            .request-form-card {
                padding: 25px;
            }
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
        <div class="help-page-layout">
            <aside class="help-sidebar">
                <h3>I am a ticket buyer</h3>
                <ul class="sidebar-list">

                    <li><a href="change_ticket_name.php">Changing A Ticket Name</a></li>
                    <li><a href="accessibility.php">Accessibility</a></li>
                    <li><a href="refunds.php">Refunds</a></li>
                    <li><a href="ticket_purchases.php">Ticket Purchases</a></li>
                    <li><a href="event_infor.php">Event Information</a></li>

                </ul>
            </aside>

            <div class="help-main-content">
                <div class="request-form-card">
                    <h1>Submit a request</h1>

                    <?php if ($message): ?>
                        <div class="<?php echo ($message_type == 'success') ? 'success-message' : 'error-message'; ?>">
                            <i
                                class="fas <?php echo ($message_type == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="request.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="email">Your email address <span style="color: red;">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span style="color: red;">*</span></label>
                            <?php $current_subject = isset($_GET['subject']) ? $_GET['subject'] : ''; ?>
                            <select id="subject" name="subject" required>
                                <option value="" disabled <?php echo empty($current_subject) ? 'selected' : ''; ?>>
                                    Select a subject</option>
                                <option value="cancellation and refunding" <?php echo ($current_subject == 'cancellation and refunding') ? 'selected' : ''; ?>>cancellation and refunding</option>
                                <option value="Renaming ticket" <?php echo ($current_subject == 'Renaming ticket') ? 'selected' : ''; ?>>Renaming ticket</option>
                                <option value="Change event" <?php echo ($current_subject == 'Change event') ? 'selected' : ''; ?>>Change event</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span style="color: red;">*</span></label>
                            <textarea id="description" name="description" required
                                placeholder="Please enter the details of your request. A member of our support staff will respond as soon as possible."></textarea>
                            <span class="form-hint">Please include as much detail as possible so we can help you
                                efficiently.</span>
                        </div>

                        <div class="form-group">
                            <label for="order_reference">Ticket id</label>
                            <input type="text" id="order_reference" name="order_reference"
                                placeholder="e.g. TKT-123456">
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <div class="file-upload-wrapper" onclick="document.getElementById('attachments').click();">
                                <i class="fas fa-cloud-upload-alt"
                                    style="font-size: 30px; color: #45a1e2; margin-bottom: 10px;"></i>
                                <p>Click to upload or drag and drop files here</p>
                                <input type="file" id="attachments" name="attachments[]" multiple
                                    style="display: none;">
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">Submit Request</button>
                    </form>
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
            <p>&copy; <?php echo date('Y'); ?> Event Registration System. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Basic sidebar active link handling
        document.addEventListener('DOMContentLoaded', function () {
            const currentPath = window.location.pathname.split('/').pop() || 'index.php';
            const sidebarLinks = document.querySelectorAll('.sidebar-list a');

            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.style.fontWeight = 'bold';
                    link.style.color = '#004b85';
                    link.style.paddingLeft = '5px';
                }
            });
        });

        // Simple file input display update
        document.getElementById('attachments').addEventListener('change', function (e) {
            const fileNameList = Array.from(e.target.files).map(f => f.name).join(', ');
            if (fileNameList) {
                const wrapper = document.querySelector('.file-upload-wrapper p');
                wrapper.textContent = "Selected: " + fileNameList;
                wrapper.style.color = "#004b85";
                wrapper.style.fontWeight = "bold";
            }
        });
    </script>
</body>

</html>