<?php
session_start();
include "db_connect.php";

/*
  This home page is ONLY for not-logged-in users.
  If someone is logged in, redirect them elsewhere (e.g. user_home.php).
*/
if (isset($_SESSION['user_id'])) {
    header("Location: user_home.php"); // change to your logged-in home page
    exit();
}

// ---- Load categories and venues for the filters ----
$cat_result = mysqli_query($conn, "SELECT category_id, category_name FROM event_categories ORDER BY category_name");
$ven_result = mysqli_query($conn, "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name");

// ---- Read selected filters from GET ----
// ---- Single search text (category, venue or event name) ----
$search_text = isset($_GET['q']) ? trim($_GET['q']) : "";

$sql = "SELECT e.*, c.category_name, v.venue_name
        FROM events e
        LEFT JOIN categories c ON e.category_id = c.category_id
        LEFT JOIN venues v ON e.venue_id = v.venue_id
        WHERE e.status = 'active'";

if ($search_text !== "") {
    $safe = mysqli_real_escape_string($conn, $search_text);
    $sql .= " AND (
                e.title LIKE '%$safe%' OR
                c.category_name LIKE '%$safe%' OR
                v.venue_name LIKE '%$safe%'
             )";
}

$sql .= " ORDER BY e.event_date ASC";
$events_result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Home - Event Registration System</title>
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
                        <a href="events.php?cat=Concerts">Concerts</a>
                        <a href="events.php?cat=Musical Festival">Musical Festival</a>
                        <a href="events.php?cat=Tech">Tech</a>
                    </div>
                </div>

                <!-- SPORTS DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="sportsToggle">
                        Sports <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="sportsMenu">
                        <a href="events.php?cat=Rugby">Rugby</a>
                        <a href="events.php?cat=Cricket">Cricket</a>
                        <a href="events.php?cat=Football">Football</a>
                    </div>
                </div>

                <!-- THEATRE DROPDOWN -->
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="theatreToggle">
                        Theatre <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="theatreMenu">
                        <a href="events.php?cat=Drama">Drama</a>
                    </div>
                </div>

                <a href="about.php" class="nav-link">About</a>
            </div>

            <div class="nav-right">
                <a href="login.php" class="btn-nav">Sign In</a>
                <a href="register.php" class="btn-nav btn-nav-outline">Register</a>
            </div>
        </nav>
    </header>

    <!-- HERO + SEARCH SECTION -->
    <section class="hero-search">
        <h1>Welcome to Your Event Hub</h1>
        <p>Find amazing gatherings, concerts, and learning opportunities near you</p>

        <form method="get" action="home.php" class="search-bar" id="filterForm">
            <div class="search-icon">&#128269;</div>

            <input type="text" name="q" id="q" placeholder="Search by category, venue or event"
                value="<?php echo htmlspecialchars($search_text); ?>">

            <button type="submit" class="btn-main">Search</button>
            <button type="button" class="btn-secondary" id="resetBtn">Reset</button>
        </form>
    </section>

    <!-- EVENTS LIST -->
    <main class="container">
        <h2>Upcoming Events</h2>

        <div class="events-list">
            <?php
            $events = [];
            if ($events_result && mysqli_num_rows($events_result) > 0) {
                while ($row = mysqli_fetch_assoc($events_result)) {
                    $events[] = $row;
                }
            } elseif ($search_text === "") {
                // Sample events data - only show if not searching and no DB events
                $events = [
                    [
                        'event_id' => 9991,
                        'title' => 'Summer Music Festival',
                        'category_name' => 'Music',
                        'venue_name' => 'City Park Arena',
                        'event_date' => '2025-07-15',
                        'ticket_price' => 45.00,
                        'description' => 'Join us for a day of amazing live music and food trucks under the sun.'
                    ],
                    [
                        'event_id' => 9992,
                        'title' => 'Tech Innovators Summit',
                        'category_name' => 'Conference',
                        'venue_name' => 'Convention Center',
                        'event_date' => '2025-08-20',
                        'ticket_price' => 120.00,
                        'description' => 'A gathering of the brightest minds in technology sharing their vision for the future.'
                    ],
                    [
                        'event_id' => 9993,
                        'title' => 'Modern Art Exhibition',
                        'category_name' => 'Art',
                        'venue_name' => 'Downtown Gallery',
                        'event_date' => '2025-09-05',
                        'ticket_price' => 15.00,
                        'description' => 'Explore contemporary works from local and international artists.'
                    ]
                ];
            }
            ?>

            <?php if (empty($events)): ?>
                <p>No events found. Please try different filters.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="event-meta">
                            <?php echo htmlspecialchars($event['category_name'] ?? 'General'); ?>
                            <?php if (!empty($event['category_name']) && !empty($event['venue_name']))
                                echo " | "; ?>
                            <?php echo htmlspecialchars($event['venue_name'] ?? 'TBA'); ?>
                        </p>
                        <p class="event-date">
                            Date: <?php echo htmlspecialchars($event['event_date']); ?>
                        </p>
                        <p class="event-price">
                            Price:
                            <?php
                            if ($event['ticket_price'] > 0) {
                                echo '$' . number_format($event['ticket_price'], 2);
                            } else {
                                echo "Free";
                            }
                            ?>
                        </p>
                        <p class="event-desc">
                            <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 120))); ?>...
                        </p>
                        <div class="card-actions">
                            <a href="event_details.php?id=<?php echo $event['event_id']; ?>" class="btn-main-sm btn-outline">
                                View Details
                            </a>
                            <a href="user/seat_plan.php?event_id=<?php echo $event['event_id']; ?>" class="btn-main-sm">
                                Buy Tickets
                            </a>
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
