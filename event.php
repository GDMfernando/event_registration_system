<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "event_registration_and_ticketing";

// Connect to database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional search filter
$search_text = $_GET['q'] ?? '';

// Fetch events from events table (with venue and category info if available)
$sql = "
SELECT 
    e.event_id,
    e.title,
    e.description,
    e.venue_id,
    e.event_date,
    e.start_time,
    e.end_time,
    e.capacity,
    e.available_seats,
    e.price_vip,
    e.price_regular,
    e.price_balcony,
    e.image_path
FROM events e
";

// Add search filter if user searched something
if (!empty($search_text)) {
    $sql .= " WHERE e.title LIKE ? OR c.name LIKE ? OR v.name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    $param = "%$search_text%";
    mysqli_stmt_bind_param($stmt, "sss", $param, $param, $param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
}

$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="home.php" class="nav-link active">Home</a>
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Events <i class="fas fa-caret-down arrow"></i></a>
                    <div class="dropdown-menu">
                        <a href="home.php?q=Concerts">Concerts</a>
                        <a href="home.php?q=Musical Festival">Musical Festival</a>
                        <a href="home.php?q=Tech">Tech</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-link">Contact Us</a>
            </div>
            <div class="nav-right">
                <a href="user/user_login.php" class="btn-nav">Sign In</a>
                <a href="user/user_register.php" class="btn-nav btn-nav-outline">Register</a>
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
        <h2>Upcoming Events</h2>
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
                                <img src="<?= htmlspecialchars($display_image_path) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="event-content">
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="event-meta">
                                <?= htmlspecialchars($event['category_name'] ?? 'General') ?>
                                <?php if (!empty($event['category_name']) && !empty($event['venue_name'])) echo " | "; ?>
                                <?= htmlspecialchars($event['venue_name'] ?? 'TBA') ?>
                            </p>
                            <p>Date: <?= htmlspecialchars($event['event_date']) ?> | Time: <?= htmlspecialchars($event['start_time']) ?> - <?= htmlspecialchars($event['end_time']) ?></p>
                            <p>Available Seats: <?= htmlspecialchars($event['available_seats']) ?> / <?= htmlspecialchars($event['capacity']) ?></p>
                            

                            <div class="pricing-container">
                                <?php if ($event['price_vip']>0 || $event['price_regular']>0 || $event['price_balcony']>0): ?>
                                    <h4>Prices:</h4>
                                    <?php if ($event['price_vip']>0) echo "<div>VIP: Rs.".number_format($event['price_vip'],2)."</div>"; ?>
                                    <?php if ($event['price_regular']>0) echo "<div>Regular: Rs.".number_format($event['price_regular'],2)."</div>"; ?>
                                    <?php if ($event['price_balcony']>0) echo "<div>Balcon: Rs.".number_format($event['price_balcony'],2)."</div>"; ?>
   
                                    
                                <?php else: ?>
                                    <div class="free-badge">Free</div>
                                <?php endif; ?>
                            </div>

                            <p><?= nl2br(htmlspecialchars(substr($event['description'],0,120))) ?>...</p>
                           
                            <a href="user/booking/seat_plan.php?event_id=<?= $event['event_id'] ?>" class="btn-main-sm">Buy Tickets</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
