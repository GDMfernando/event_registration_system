<?php

session_start();

// 1. Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

include('../../db_connect.php');

// --- A. HANDLE FILTER INPUTS ---
$filter_title       = isset($_GET['title'])       ? $_GET['title']       : '';
$filter_category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$filter_venue_id    = isset($_GET['venue_id'])    ? $_GET['venue_id']    : '';
$filter_date        = isset($_GET['event_date'])  ? $_GET['event_date']  : '';

$upload_dir = '../../uploads/event_images/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- B. HELPER FUNCTIONS ---

function get_filtered_events($conn, $title, $cat_id, $ven_id, $date)
{
    $sql = "SELECT 
                e.event_id AS id, e.title, e.event_date, e.start_time, e.end_time,
                e.category_id, e.venue_id, e.image_path, v.venue_name, 
                c.category_name, e.available_seats, e.capacity, e.status, e.created_at,
                e.price_vip, e.price_regular, e.price_balcony
            FROM events e
            JOIN event_venues v ON e.venue_id = v.venue_id 
            JOIN event_categories c ON e.category_id = c.category_id
            WHERE 1=1";

    if (!empty($title)) {
        $safe_title = mysqli_real_escape_string($conn, $title);
        $sql .= " AND e.title LIKE '%$safe_title%'";
    }
    if (!empty($cat_id)) {
        $sql .= " AND e.category_id = " . (int)$cat_id;
    }
    if (!empty($ven_id)) {
        $sql .= " AND e.venue_id = " . (int)$ven_id;
    }
    if (!empty($date)) {
        $sql .= " AND e.event_date = '" . mysqli_real_escape_string($conn, $date) . "'";
    }

    $sql .= " ORDER BY e.event_date DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_event_by_id($conn, $id)
{
    $sql = "SELECT e.event_id AS id, e.title, e.description, e.event_date, e.start_time, e.end_time,
                e.category_id, e.venue_id, e.price_vip, e.price_regular, e.price_balcony,
                e.image_path, e.available_seats, e.capacity, e.status, v.venue_name, c.category_name 
            FROM events e
            JOIN event_venues v ON e.venue_id = v.venue_id 
            JOIN event_categories c ON e.category_id = c.category_id
            WHERE e.event_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $event = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $event;
    }
    return null;
}

function get_all_categories($conn)
{
    $sql = "SELECT category_id, category_name FROM event_categories ORDER BY category_name ASC";
    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : array();
}

function get_all_venues($conn)
{
    $sql = "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name ASC";
    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : array();
}

// --- C. PROCESS POST ACTIONS ---

// 1. ADD EVENT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $title           = mysqli_real_escape_string($conn, $_POST['title']);
    $description     = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id     = mysqli_real_escape_string($conn, $_POST['category_id']);
    $event_date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $start_time      = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time        = mysqli_real_escape_string($conn, $_POST['end_time']);
    $price_vip       = mysqli_real_escape_string($conn, $_POST['price_vip']);
    $price_regular   = mysqli_real_escape_string($conn, $_POST['price_regular']);
    $price_balcony   = mysqli_real_escape_string($conn, $_POST['price_balcony']);
    $capacity        = (int)$_POST['capacity'];
    $available_seats = $capacity;

    // Venue Selection
    $venue_select_type = isset($_POST['venue_select_type']) ? $_POST['venue_select_type'] : 'existing';
    $venue_id_existing = isset($_POST['venue_id_existing']) ? $_POST['venue_id_existing'] : '';
    $final_venue_id = null;

    if ($venue_select_type === 'new') {
        $venue_name = mysqli_real_escape_string($conn, $_POST['venue_name_new']);
        $address    = mysqli_real_escape_string($conn, $_POST['address_new']);
        if (!empty($venue_name) && !empty($address)) {
            $sql_v = "INSERT INTO event_venues (venue_name, address) VALUES (?, ?)";
            $stmt_v = mysqli_prepare($conn, $sql_v);
            mysqli_stmt_bind_param($stmt_v, "ss", $venue_name, $address);
            if (mysqli_stmt_execute($stmt_v)) $final_venue_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt_v);
        }
    } else {
        $final_venue_id = (int)$venue_id_existing;
    }

    // Image Upload
    $image_path = null;
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $new_file_name = uniqid('event_img_', true) . '.' . strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
        $target_file = $upload_dir . $new_file_name;
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    if ($final_venue_id) {
        $sql = "INSERT INTO events (title, description, category_id, venue_id, event_date, start_time, end_time, price_vip, price_regular, price_balcony, image_path, capacity, available_seats, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssiisssdddssi", $title, $description, $category_id, $final_venue_id, $event_date, $start_time, $end_time, $price_vip, $price_regular, $price_balcony, $image_path, $capacity, $available_seats);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_events.php?status=added");
            exit();
        }
    }
}

// 2. UPDATE EVENT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    $event_id    = (int)$_POST['event_id'];
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $venue_id    = (int)$_POST['venue_id'];
    $event_date  = $_POST['event_date'];
    $start_time  = $_POST['start_time'];
    $end_time    = $_POST['end_time'];
    $price_vip   = $_POST['price_vip'];
    $price_regular = $_POST['price_regular'];
    $price_balcony = $_POST['price_balcony'];
    $status      = $_POST['status'];
    $new_capacity = (int)$_POST['capacity'];

    // Sync Available Seats
    $current_data = get_event_by_id($conn, $event_id);
    $seats_sold = $current_data['capacity'] - $current_data['available_seats'];
    $new_available_seats = $new_capacity - $seats_sold;

    // Handle Image
    $new_image_path = $current_data['image_path'];
    if (isset($_FILES['edit_event_image']) && $_FILES['edit_event_image']['error'] == 0) {
        $target = $upload_dir . uniqid('event_img_', true) . '.' . pathinfo($_FILES['edit_event_image']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['edit_event_image']['tmp_name'], $target)) {
            if ($new_image_path && file_exists($new_image_path)) unlink($new_image_path);
            $new_image_path = $target;
        }
    }

    $sql = "UPDATE events SET title=?, description=?, category_id=?, venue_id=?, event_date=?, start_time=?, end_time=?, price_vip=?, price_regular=?, price_balcony=?, capacity=?, available_seats=?, image_path=?, status=? WHERE event_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiisssdddiiiss", $title, $description, $category_id, $venue_id, $event_date, $start_time, $end_time, $price_vip, $price_regular, $price_balcony, $new_capacity, $new_available_seats, $new_image_path, $status, $event_id);

    if (mysqli_stmt_execute($stmt)) {
        header("location: manage_events.php?status=updated");
        exit();
    }
}

// 3. DELETE EVENT
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM events WHERE event_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("location: manage_events.php?status=deleted");
        exit();
    }
}

// 4. FETCH EVENT (AJAX)
if (isset($_GET['action']) && $_GET['action'] === 'fetch_event' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $event = get_event_by_id($conn, (int)$_GET['id']);
    echo json_encode(['success' => !!$event, 'event' => $event]);
    exit();
}

// 5. ADD CATEGORY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    if (!empty($cat_name)) {
        $sql = "INSERT INTO event_categories (category_name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $cat_name);
        mysqli_stmt_execute($stmt);
        header("location: manage_events.php?status=category_added");
        exit();
    }
}

// --- D. DATA FETCH FOR DISPLAY ---
$events = get_filtered_events($conn, $filter_title, $filter_category_id, $filter_venue_id, $filter_date);
$venues = get_all_venues($conn);
$categories = get_all_categories($conn);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="manage_events.css">
    <link rel="stylesheet" href="../includes/navbar.css">
</head>

<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container">
        <div class="header">
            <h1>🗓️ Event Management</h1>
            <a href="../dashboard/dashboard.php" class="btn-back">Back to Dashboard</a>
        </div>

        <div class="filter-container">
            <form method="GET" action="manage_events.php" class="filter-form">
                <div class="filter-group">
                    <label for="title_filter">Event Title</label>
                    <input type="text" name="title" id="title_filter" placeholder="Search..." value="<?php echo htmlspecialchars($filter_title); ?>">
                </div>
                <div class="filter-group">
                    <label for="category_filter">Category</label>
                    <select name="category_id" id="category_filter">
                        <option value="">-- All Types --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['category_id']; ?>" <?php echo ($filter_category_id == $c['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="venue_filter">Venue</label>
                    <select name="venue_id" id="venue_filter">
                        <option value="">-- All Venues --</option>
                        <?php foreach ($venues as $v): ?>
                            <option value="<?php echo $v['venue_id']; ?>" <?php echo ($filter_venue_id == $v['venue_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($v['venue_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_filter">Event Date</label>
                    <input type="date" name="event_date" id="date_filter" value="<?php echo htmlspecialchars($filter_date); ?>">
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="manage_events.php" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>

        <button id="addNewEventBtn" class="add-event-btn">+ Add New Event</button>
        <button id="addNewCategoryBtn" class="add-event-btn">+ Add New Category</button>

        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date/Time</th>
                    <th>Venue</th>
                    <th>Prices</th>
                    <th>Capacity (Left/Total)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo $event['id']; ?></td>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['category_name']); ?></td>
                            <td><?php echo $event['event_date']; ?><br><small><?php echo $event['start_time'] . '-' . $event['end_time']; ?></small></td>
                            <td><?php echo htmlspecialchars($event['venue_name']); ?></td>
                            <td>V: <?php echo number_format($event['price_vip'], 2); ?><br>
                                R: <?php echo number_format($event['price_regular'], 2); ?><br>
                                B: <?php echo number_format($event['price_balcony'], 2); ?></td>
                            <td><?php echo $event['available_seats']; ?> / <?php echo $event['capacity']; ?></td>
                            <td><?php echo $event['status']; ?></td>
                            <td class="action-links">
                                <a href="javascript:void(0);" data-id="<?php echo $event['id']; ?>" class="edit">Edit</a>
                                <a href="manage_events.php?delete_id=<?php echo $event['id']; ?>" class="delete" onclick="return confirm('Delete this event?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No events found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include 'add_event_modal.php'; ?>
    <?php include 'edit_event_modal.php'; ?>

    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Category</h2>
                <span class="close">&times;</span>
            </div>

            <form action="manage_events.php" method="POST">
                <div class="modal-body-scroll">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="category_name" required placeholder="e.g. Concerts, Workshops">
                    </div>

                    <button type="submit" name="add_category" class="btn-update-full">Save Category</button>
                </div>
            </form>
        </div>
    </div>
    <script src="manage_events.js"></script>
</body>

</html>