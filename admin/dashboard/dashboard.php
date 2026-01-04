<?php

session_start();

// Check if admin is logged in, if not redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
    }
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


$total_events = get_total_event_count($conn);
$active_events = get_active_event_count($conn);
$total_users = 1500;
$total_bookings = 3200;

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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="../includes/navbar.css">
    <link rel="stylesheet" href="../manage_events/css/manage_events.css">
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
            <button id="dashboardAddEventBtn" class="btn">Add New Event</button>
            <button class="btn secondary-btn">View Reports</button>
        </div>

    </div>
    <?php include('../manage_events/add_event_modal.php'); ?>
    <script src="dashboard.js"></script>
    <script src="../manage_events/manage_events.js"></script>
</body>

</html>