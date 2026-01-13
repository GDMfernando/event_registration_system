<?php

session_start();

// Check if admin is logged in, if not redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
    }

    $admin_display_name = isset($_SESSION['admin_fullname']) ? $_SESSION['admin_fullname'] : 'Admin';

include('../../db_connect.php');

function get_all_categories($conn)
{
    $sql = "SELECT category_id, category_name FROM event_categories ORDER BY category_name ASC";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        error_log("Error: " . mysqli_error($conn));
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_all_venues($conn)
{
    $sql = "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name ASC";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        error_log("Error: " . mysqli_error($conn));
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_total_event_count($conn)
{
    $sql = "SELECT COUNT(event_id) AS total_events FROM events";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        error_log("Error in get_total_event_count: " . mysqli_error($conn));
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total_events'];
}

function get_active_event_count($conn)
{

    $sql = "SELECT COUNT(event_id) AS active_events 
            FROM events 
            WHERE event_date >= CURDATE() AND status = 'Active'";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        error_log("Error in get_active_event_count: " . mysqli_error($conn));
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return (int)$row['active_events'];
}

function get_user_count_by_role($conn, $role) {
    $sql = "SELECT COUNT(user_id) AS count FROM user WHERE role = '$role'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['count'];
}

function get_total_bookings_count($conn) {
    // Only count confirmed/attended bookings if you want to be accurate
    $sql = "SELECT COUNT(booking_id) AS count FROM event_booking WHERE status != 'cancelled'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['count'];
}

$total_events = get_total_event_count($conn);
$active_events = get_active_event_count($conn);
$total_users = get_user_count_by_role($conn, 'user');
$total_bookings = get_total_bookings_count($conn);

$categories = get_all_categories($conn);
$venues = get_all_venues($conn);

mysqli_close($conn);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="../includes/navbar.css">
    <link rel="stylesheet" href="../manage_events/manage_events.css">
</head>

<body>
    <?php include('../includes/navbar.php'); ?>

    <div class="container">

<div class="welcome-section">
        <h2>Welcome back, <span class="admin-name"><?php echo htmlspecialchars($admin_display_name); ?></span>! 👋</h2>
        <p>System operational. Here is your current overview.</p>
    </div>

    <h1>📊 Dashboard Statistics</h1>

        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total Events</h3>
                <p class="stat-number" id="total-events"><?php echo $total_events; ?></p>
                <small>Across all categories</small>
            </div>

            <div class="stat-card primary">
    <h3><span class="pulse-indicator"></span>Active Events</h3>
    <p class="stat-number"><?php echo $active_events; ?></p>
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
                <div class="progress-bar-container">
        <div class="progress-fill" style="width: 65%;"></div> </div>
                <small>Total tickets reserved</small>
            </div>

        </div>

        <div class="content-placeholder">
        <h2>⚡ Quick Actions</h2>
        <p>Commonly used administrative tasks.</p>
        
        <div class="action-group">
            <a style="text-decoration : none" id="dashboardAddEventBtn" class="btn">➕ Add New Event</a>
            <a style="text-decoration : none" href="../bookings/bookings.php" class="btn">🎟️ Manage Bookings</a>
        </div>
    </div>

    </div>
    <?php include('../manage_events/add_event_modal.php'); ?>
    <script src="dashboard.js"></script>
    <script src="../manage_events/manage_events.js"></script>
</body>

</html>