<?php
session_start();
include "db_connect.php";

$search_text = $_GET['q'] ?? '';
$category_filter = $_GET['cat'] ?? '';

// 2. Build the query string
// Using LEFT JOIN ensures you get the category name from the other table
$sql = "SELECT e.*, c.category_name 
        FROM events e
        LEFT JOIN event_categories c ON e.category_id = c.category_id
        WHERE 1=1";

$params = [];
$types = "";

// 3. Add Category Filter if 'cat' exists in URL
if (!empty($category_filter)) {
    $sql .= " AND c.category_name = ?";
    $params[] = $category_filter;
    $types .= "s";
}

// 4. Add Search Filter if 'q' exists in URL
if (!empty($search_text)) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $searchTerm = "%$search_text%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// 5. Order by newest first
$sql .= " ORDER BY e.event_date ASC";

// 6. Execute Statement
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query Error: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Pre-fill user name if logged in
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Registration System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- HEADER -->
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


    <!-- HERO + SEARCH -->
    <section class="hero-search">
        <h1>Welcome to Your Event Hub</h1>
        <p>Find amazing gatherings, concerts, and learning opportunities near you</p>

        <form method="get" action="home.php" class="search-bar">
            <div class="search-icon">&#128269;</div>
            <input type="text" name="q" placeholder="Search by category, venue or event"
                value="<?= htmlspecialchars($search_text) ?>">
            <button type="submit" class="btn-main">Search</button>
            <a href="home.php" class="btn-secondary">Reset</a>
        </form>
    </section>

    <!-- EVENTS LIST -->
    <main class="container">
        <h2>Events</h2>
        <div class="events-list">
            <?php if (empty($events)): ?>
                <p>No events found. Please try different filters.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <?php if (!empty($event['image_path'])): ?>
                            <?php
                            // Convert admin-relative path to root-relative path
                            $display_image_path = str_replace('../../', '', $event['image_path']);
                            ?>
                            <div class="event-image">
                                <img src="<?= htmlspecialchars($display_image_path) ?>"
                                    alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="event-meta">
                                <?= htmlspecialchars($event['category_name'] ?? 'General') ?>
                                <?php if (!empty($event['category_name']) && !empty($event['venue_name']))
                                    echo " | "; ?>
                                <?= htmlspecialchars($event['venue_name'] ?? 'TBA') ?>
                            </p>
                            <p>Date: <?= htmlspecialchars($event['event_date']) ?> | Time:
                                <?= htmlspecialchars($event['start_time']) ?> - <?= htmlspecialchars($event['end_time']) ?>
                            </p>
                            <p>Available Seats: <?= htmlspecialchars($event['available_seats']) ?> /
                                <?= htmlspecialchars($event['capacity']) ?>
                            </p>


                            <div class="pricing-container">
                                <?php if ($event['price_vip'] > 0 || $event['price_regular'] > 0 || $event['price_balcony'] > 0): ?>
                                    <h4>Prices:</h4>
                                    <?php if ($event['price_vip'] > 0)
                                        echo "<div>VIP: Rs." . number_format($event['price_vip'], 2) . "</div>"; ?>
                                    <?php if ($event['price_regular'] > 0)
                                        echo "<div>Regular: Rs." . number_format($event['price_regular'], 2) . "</div>"; ?>
                                    <?php if ($event['price_balcony'] > 0)
                                        echo "<div>Balcony: Rs." . number_format($event['price_balcony'], 2) . "</div>"; ?>


                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p><?= nl2br(htmlspecialchars(substr($event['description'], 0, 120))) ?>...</p>

                            <a href="user/booking/seat_plan.php?event_id=<?= $event['event_id'] ?>" class="btn-main-sm">Buy
                                Tickets</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
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