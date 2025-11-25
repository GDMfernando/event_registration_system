<?php
// PHP logic (Hardcoded Statistics - Replace with database queries later)

$total_events = 45;
$active_events = 12;
$total_users = 1500;
$total_bookings = 3200;

// You would typically include database connection and session checks here.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="../includes/navbar.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <div class="container">
        <h1>📊 Dashboard Statistics</h1>
        
        <div class="stats-grid">
            
            <div class="stat-card">
                <h3>Total Events</h3>
                <p class="stat-number" id="total-events"><?php echo $total_events; ?></p>
                <small>Across all categories</small>
            </div>
            
            <div class="stat-card primary">
                <h3>Active Events</h3>
                <p class="stat-number" id="active-events"><?php echo $active_events; ?></p>
                <small>Currently open for registration</small>
            </div>

            <div class="stat-card secondary">
                <h3>Registered Users</h3>
                <p class="stat-number" id="total-users"><?php echo $total_users; ?></p>
                <small>Total accounts created</small>
            </div>

            <div class="stat-card accent">
                <h3>Total Bookings</h3>
                <p class="stat-number" id="total-bookings"><?php echo $total_bookings; ?></p>
                <small>Total tickets reserved</small>
            </div>

        </div>

        <div class="content-placeholder">
            <h2>Quick Actions</h2>
            <p>This area can be used for quick links, recent activities, or future charts.</p>
            <button class="btn">Add New Event</button>
            <button class="btn secondary-btn">View Reports</button>
        </div>

    </div>

    <script src="dashboard.js"></script>
</body>
</html>