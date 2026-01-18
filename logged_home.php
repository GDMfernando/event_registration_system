<?php
session_start();
include "db_connect.php";

/*
  This home page is ONLY for logged-in users.
  If someone is not logged in, redirect them to home.php.
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Get the logged-in user's name
$user_name = isset($_SESSION['user_full_name']) ? $_SESSION['user_full_name'] : 'User';

// ---- Load categories and venues for the filters ----
$cat_result = mysqli_query($conn, "SELECT category_id, category_name FROM event_categories ORDER BY category_name");
$ven_result = mysqli_query($conn, "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name");

// ---- Read selected filters from GET ----
// ---- Single search text (category, venue or event name) ----
$search_text = isset($_GET['q']) ? trim($_GET['q']) : "";

// Try to query database events
$sql = "SELECT e.*, c.category_name, v.venue_name
        FROM events e
        LEFT JOIN event_categories c ON e.category_id = c.category_id
        LEFT JOIN event_venues v ON e.venue_id = v.venue_id
        WHERE e.status = 'active'
        AND e.event_date BETWEEN '2026-01-01' AND '2026-02-28'";

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
                <span class="welcome-text">Welcome,
                    <?php echo htmlspecialchars($user_name); ?>!
                </span>
                <a href="user/user_logout.php" class="btn-nav">Logout</a>
            </div>
        </nav>
    </header>

    <!-- HERO + SEARCH SECTION -->
    <section class="hero-search">
        <h1>Welcome to Your Event Hub</h1>
        <p>Find amazing gatherings, concerts, and learning opportunities near you</p>

        <form method="get" action="logged_home.php" class="search-bar" id="filterForm">
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
                // Load events from database
                while ($row = mysqli_fetch_assoc($events_result)) {
                    $events[] = $row;
                }
            }
            ?>

            <?php if (empty($events)): ?>
                <p>No events found. Please try different filters.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <?php
                        $img_path = $event['image_path'];
                        // Fix path if it starts with ../ or ../../
                        $img_path = str_replace(['../../', '../'], '', $img_path);
                        if (!empty($img_path)):
                            ?>
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($img_path); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
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
                            <div class="pricing-container">
                                <?php
                                // Check if at least one price is set
                                $has_pricing = ($event['price_vip'] > 0 || $event['price_regular'] > 0 || $event['price_balcony'] > 0);
                                ?>

                                <?php if ($has_pricing): ?>
                                    <h4 class="event-price">Prices:</h4>
                                    <div class="price-sections">

                                        <?php if ($event['price_vip'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">VIP</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_vip'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_regular'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Regular</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_regular'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_balcony'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Balcony</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_balcony'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p class="event-desc">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 120))); ?>...
                            </p>
                            <div class="card-actions">
                                <?php
                                $details_link = "event_details.php?id=" . $event['event_id'];
                                if ($event['title'] === 'Tech Innovators Summit')
                                    $details_link = "event1.php";
                                elseif ($event['title'] === 'Summer Music Festival')
                                    $details_link = "event2.php";
                                elseif ($event['title'] === 'Modern Art Exhibition')
                                    $details_link = "event3.php";
                                elseif ($event['title'] === 'Lankan Rugby Sevens 2026')
                                    $details_link = "event_4.php";
                                elseif ($event['title'] === 'Sri Lanka Football Premier League')
                                    $details_link = "event_5.php";
                                elseif ($event['title'] === 'Sri Lanka Cricket (SLC) T20 League')
                                    $details_link = "event_6.php";
                                elseif ($event['title'] === 'Madhura Jawanika - මධුර ජවනිකා')
                                    $details_link = "event_7.php";
                                ?>
                                <a href="<?php echo $details_link; ?>" class="btn-main-sm btn-outline">
                                    View Details
                                </a>
                                <a href="user/booking/seat_plan.php?event_id=<?php echo $event['event_id']; ?>"
                                    class="btn-main-sm">
                                    Buy Tickets
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- EVENTS SECTION -->
    <section class="container" style="margin-top: 50px;">
        <h2>Events</h2>

        <?php
        // Query for Tech, Music, Art events
        $special_events_sql = "SELECT e.*, c.category_name, v.venue_name
                       FROM events e
                       LEFT JOIN event_categories c ON e.category_id = c.category_id
                       LEFT JOIN event_venues v ON e.venue_id = v.venue_id
                       WHERE e.status = 'active'
                       AND c.category_name IN ('Tech', 'Concert', 'Musical Festival', 'Art', 'Music')
                       ORDER BY e.event_date ASC";
        $special_events_result = mysqli_query($conn, $special_events_sql);

        $special_events = [];
        if ($special_events_result && mysqli_num_rows($special_events_result) > 0) {
            while ($row = mysqli_fetch_assoc($special_events_result)) {
                $special_events[] = $row;
            }
        }
        ?>

        <div class="events-list">
            <?php if (empty($special_events)): ?>
                <p>No events found for Tech, Music, or Art.</p>
            <?php else: ?>
                <?php foreach ($special_events as $event): ?>
                    <article class="event-card">
                        <?php
                        $img_path = $event['image_path'];
                        $img_path = str_replace(['../../', '../'], '', $img_path);
                        if (!empty($img_path)):
                            ?>
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($img_path); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
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

                            <div class="pricing-container">
                                <?php
                                $has_pricing = ($event['price_vip'] > 0 || $event['price_regular'] > 0 || $event['price_balcony'] > 0);
                                ?>

                                <?php if ($has_pricing): ?>
                                    <h4 class="event-price">Prices:</h4>
                                    <div class="price-sections">

                                        <?php if ($event['price_vip'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">VIP</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_vip'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_regular'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Regular</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_regular'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_balcony'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Balcony</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_balcony'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p class="event-desc">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 120))); ?>...
                            </p>
                            <div class="card-actions">
                                <?php
                                $details_link = "event_details.php?id=" . $event['event_id'];
                                if ($event['title'] === 'Tech Innovators Summit')
                                    $details_link = "event1.php";
                                elseif ($event['title'] === 'Summer Music Festival')
                                    $details_link = "event2.php";
                                elseif ($event['title'] === 'Modern Art Exhibition')
                                    $details_link = "event3.php";
                                elseif ($event['title'] === 'Lankan Rugby Sevens 2026')
                                    $details_link = "event_4.php";
                                elseif ($event['title'] === 'Sri Lanka Football Premier League')
                                    $details_link = "event_5.php";
                                elseif ($event['title'] === 'Sri Lanka Cricket (SLC) T20 League')
                                    $details_link = "event_6.php";
                                elseif ($event['title'] === 'Madhura Jawanika - මධුර ජවනිකා')
                                    $details_link = "event_7.php";
                                ?>
                                <a href="<?php echo $details_link; ?>" class="btn-main-sm btn-outline">
                                    View Details
                                </a>
                                <a href="user/booking/seat_plan.php?event_id=<?php echo $event['event_id']; ?>"
                                    class="btn-main-sm">
                                    Buy Tickets
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- SPORTS SECTION -->
    <section class="container" style="margin-top: 50px;">
        <h2>Sports</h2>

        <?php
        // Query for Sports events
        $sports_sql = "SELECT e.*, c.category_name, v.venue_name
                       FROM events e
                       LEFT JOIN event_categories c ON e.category_id = c.category_id
                       LEFT JOIN event_venues v ON e.venue_id = v.venue_id
                       WHERE e.status = 'active'
                       AND c.category_name IN ('Rugby', 'Cricket', 'Football')
                       ORDER BY e.event_date ASC";
        $sports_result = mysqli_query($conn, $sports_sql);

        $sports_events = [];
        if ($sports_result && mysqli_num_rows($sports_result) > 0) {
            while ($row = mysqli_fetch_assoc($sports_result)) {
                $sports_events[] = $row;
            }
        }
        ?>

        <div class="events-list">
            <?php if (empty($sports_events)): ?>
                <p>No sports events found.</p>
            <?php else: ?>
                <?php foreach ($sports_events as $event): ?>
                    <article class="event-card">
                        <?php
                        $img_path = $event['image_path'];
                        $img_path = str_replace(['../../', '../'], '', $img_path);
                        if (!empty($img_path)):
                            ?>
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($img_path); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
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

                            <div class="pricing-container">
                                <?php
                                $has_pricing = ($event['price_vip'] > 0 || $event['price_regular'] > 0 || $event['price_balcony'] > 0);
                                ?>

                                <?php if ($has_pricing): ?>
                                    <h4 class="event-price">Prices:</h4>
                                    <div class="price-sections">

                                        <?php if ($event['price_vip'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">VIP</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_vip'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_regular'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Regular</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_regular'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_balcony'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Balcony</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_balcony'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p class="event-desc">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 120))); ?>...
                            </p>
                            <div class="card-actions">
                                <?php
                                $details_link = "event_details.php?id=" . $event['event_id'];
                                if ($event['title'] === 'Tech Innovators Summit')
                                    $details_link = "event1.php";
                                elseif ($event['title'] === 'Summer Music Festival')
                                    $details_link = "event2.php";
                                elseif ($event['title'] === 'Modern Art Exhibition')
                                    $details_link = "event3.php";
                                elseif ($event['title'] === 'Lankan Rugby Sevens 2026')
                                    $details_link = "event_4.php";
                                elseif ($event['title'] === 'Sri Lanka Football Premier League')
                                    $details_link = "event_5.php";
                                elseif ($event['title'] === 'Sri Lanka Cricket (SLC) T20 League')
                                    $details_link = "event_6.php";
                                elseif ($event['title'] === 'Madhura Jawanika - මධුර ජවනිකා')
                                    $details_link = "event_7.php";
                                ?>
                                <a href="<?php echo $details_link; ?>" class="btn-main-sm btn-outline">
                                    View Details
                                </a>
                                <a href="user/booking/seat_plan.php?event_id=<?php echo $event['event_id']; ?>"
                                    class="btn-main-sm">
                                    Buy Tickets
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- THEATRE SECTION -->
    <section class="container" style="margin-top: 50px; margin-bottom: 50px;">
        <h2>Theatre</h2>

        <?php
        // Query for Theatre events (Drama)
        $theatre_sql = "SELECT e.*, c.category_name, v.venue_name
                       FROM events e
                       LEFT JOIN event_categories c ON e.category_id = c.category_id
                       LEFT JOIN event_venues v ON e.venue_id = v.venue_id
                       WHERE e.status = 'active'
                       AND c.category_name = 'Drama'
                       ORDER BY e.event_date ASC";
        $theatre_result = mysqli_query($conn, $theatre_sql);

        $theatre_events = [];
        if ($theatre_result && mysqli_num_rows($theatre_result) > 0) {
            while ($row = mysqli_fetch_assoc($theatre_result)) {
                $theatre_events[] = $row;
            }
        }
        ?>

        <div class="events-list">
            <?php if (empty($theatre_events)): ?>
                <p>No theatre events found.</p>
            <?php else: ?>
                <?php foreach ($theatre_events as $event): ?>
                    <article class="event-card">
                        <?php
                        $img_path = $event['image_path'];
                        $img_path = str_replace(['../../', '../'], '', $img_path);
                        if (!empty($img_path)):
                            ?>
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($img_path); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
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

                            <div class="pricing-container">
                                <?php
                                $has_pricing = ($event['price_vip'] > 0 || $event['price_regular'] > 0 || $event['price_balcony'] > 0);
                                ?>

                                <?php if ($has_pricing): ?>
                                    <h4 class="event-price">Prices:</h4>
                                    <div class="price-sections">

                                        <?php if ($event['price_vip'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">VIP</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_vip'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_regular'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Regular</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_regular'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($event['price_balcony'] > 0): ?>
                                            <div class="price-box">
                                                <span class="label">Balcony</span>
                                                <span class="amount">Rs.<?php echo number_format($event['price_balcony'], 2); ?></span>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p class="event-desc">
                                <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 120))); ?>...
                            </p>
                            <div class="card-actions">
                                <?php
                                $details_link = "event_details.php?id=" . $event['event_id'];
                                if ($event['title'] === 'Tech Innovators Summit')
                                    $details_link = "event1.php";
                                elseif ($event['title'] === 'Summer Music Festival')
                                    $details_link = "event2.php";
                                elseif ($event['title'] === 'Modern Art Exhibition')
                                    $details_link = "event3.php";
                                elseif ($event['title'] === 'Lankan Rugby Sevens 2026')
                                    $details_link = "event_4.php";
                                elseif ($event['title'] === 'Sri Lanka Football Premier League')
                                    $details_link = "event_5.php";
                                elseif ($event['title'] === 'Sri Lanka Cricket (SLC) T20 League')
                                    $details_link = "event_6.php";
                                elseif ($event['title'] === 'Madhura Jawanika - මධුර ජවනිකා')
                                    $details_link = "event_7.php";
                                ?>
                                <a href="<?php echo $details_link; ?>" class="btn-main-sm btn-outline">
                                    View Details
                                </a>
                                <a href="user/booking/seat_plan.php?event_id=<?php echo $event['event_id']; ?>"
                                    class="btn-main-sm">
                                    Buy Tickets
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
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
                    <li><a href="logged_home.php">Home</a></li>
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
</body>

</html>