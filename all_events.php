<?php
session_start();
include "db_connect.php";

// Fetch categories and venues for search/filter if needed
$cat_result = mysqli_query($conn, "SELECT category_id, category_name FROM event_categories ORDER BY category_name");
$ven_result = mysqli_query($conn, "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name");

$search_text = isset($_GET['q']) ? trim($_GET['q']) : "";

// Fetch all active events with category and venue details
$sql = "SELECT e.*, c.category_name, v.venue_name
        FROM events e
        LEFT JOIN event_categories c ON e.category_id = c.category_id
        LEFT JOIN event_venues v ON e.venue_id = v.venue_id
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
<html>

<head>
    <meta charset="UTF-8">
    <title>All Events - Event Registration System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <!-- HEADER / NAVIGATION -->
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'logged_home.php' : 'home.php'; ?>"
                    class="nav-link">Home</a>

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
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="helpToggle">
                        Help <i class="fas fa-caret-down arrow"></i>
                    </a>
                    <div class="dropdown-menu" id="helpMenu">
                        <a href="help_buyer.php?cat=user">I am a ticket buyer</a>
                    </div>
                </div>

                <a href="contact.php" class="nav-link">Contact Us</a>
            </div>

            <div class="nav-right">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="user/user_login.php" class="btn-nav">Sign In</a>
                    <a href="user/user_register.php" class="btn-nav btn-nav-outline">Register</a>
                <?php else: ?>
                    <span class="welcome-text" style="color: white; margin-right: 15px; font-weight: 600;">Welcome,
                        <?php echo htmlspecialchars($user_name); ?>!
                    </span>
                    <a href="user/user_logout.php" class="btn-nav">Logout</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- HERO + SEARCH SECTION -->
    <section class="hero-search" style="padding: 60px 20px;">
        <h1>Explores All Events</h1>
        <p>Find your next adventure from our complete collection of events</p>

        <form method="get" action="all_events.php" class="search-bar" id="filterForm">
            <div class="search-icon">&#128269;</div>
            <input type="text" name="q" id="q" placeholder="Search by name, category or venue"
                value="<?php echo htmlspecialchars($search_text); ?>">
            <button type="submit" class="btn-main">Search</button>
            <button type="button" class="btn-secondary" id="resetBtn"
                onclick="window.location.href='all_events.php'">Reset</button>
        </form>
    </section>

    <!-- EVENTS LIST -->
    <main class="container" style="margin-top: 40px; margin-bottom: 80px;">
        <div class="admin-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="margin: 0;">Discovery All Events</h2>
            <div class="status-badge"
                style="background: #e1f5fe; color: #01579b; padding: 5px 15px; border-radius: 20px; font-weight: 600;">
                <?php echo mysqli_num_rows($events_result); ?> Events Found
            </div>
        </div>

        <div class="events-grid"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php if (mysqli_num_rows($events_result) == 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px 20px;">
                    <i class="fas fa-calendar-times" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <p style="font-size: 18px; color: #666;">No events matched your search. Try different keywords.</p>
                </div>
            <?php else: ?>
                <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                    <article class="event-card" style="margin: 0; height: 100%; display: flex; flex-direction: column;">
                        <?php
                        $img_path = $event['image_path'];
                        $img_path = str_replace(['../../', '../'], '', $img_path);
                        ?>
                        <div class="event-image" style="height: 200px; position: relative;">
                            <img src="<?php echo htmlspecialchars($img_path ? $img_path : 'assets/images/placeholder.jpg'); ?>"
                                alt="<?php echo htmlspecialchars($event['title']); ?>" style="height: 100%; object-fit: cover;">
                            <div
                                style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.6); color: white; padding: 5px 12px; border-radius: 4px; font-size: 14px; font-weight: 600;">
                                <?php echo htmlspecialchars($event['category_name'] ?? 'Event'); ?>
                            </div>
                        </div>

                        <div class="event-content" style="flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="margin-bottom: 10px;">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </h3>

                            <div style="margin-bottom: 15px; color: #666; font-size: 14px;">
                                <div style="margin-bottom: 5px;">
                                    <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #004b85;"></i>
                                    <?php echo htmlspecialchars($event['venue_name'] ?? 'TBA'); ?>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-alt" style="margin-right: 8px; color: #004b85;"></i>
                                    <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                </div>
                            </div>

                            <div class="pricing-container"
                                style="margin-bottom: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                                <?php if ($event['price_balcony'] > 0): ?>
                                    <span style="font-size: 14px; font-weight: 600; color: #444;">Starting from: </span>
                                    <span style="font-size: 20px; font-weight: 700; color: #28a745;">Rs.
                                        <?php echo number_format($event['price_balcony'], 2); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="free-badge"
                                        style="background: #28a745; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">FREE</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-actions"
                                style="margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
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
                                <a href="<?php echo $details_link; ?>" class="btn-main-sm btn-outline"
                                    style="text-align: center; width: 100%; padding: 10px 0;">
                                    Details
                                </a>
                                <a href="user/booking/seat_plan.php?event_id=<?php echo $event['event_id']; ?>"
                                    class="btn-main-sm" style="text-align: center; width: 100%; padding: 10px 0;">
                                    Buy Tickets
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
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