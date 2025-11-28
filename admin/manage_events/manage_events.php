<?php

include('../../db/db_connect.php');

function get_all_events($conn)
{
    $sql = "SELECT 
                e.event_id AS id, 
                e.title, 
                e.event_date, 
                e.start_time, 
                e.end_time,
                e.category_id,
                e.ticket_price,
                v.venue_name, 
                c.category_name,
                e.available_seats,    
                e.status,              
                e.created_at
            FROM events e
            JOIN event_venues v ON e.venue_id = v.venue_id 
            JOIN event_categories c ON e.category_id = c.category_id
            ORDER BY e.event_date DESC";
    $result = mysqli_query($conn, $sql);

    if ($result === false) {
        die("MySQL Query Error in get_all_events: " . mysqli_error($conn) .
            "<br>Query: " . htmlspecialchars($sql));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {

    // 1. Get Event Data (No change here, but included for context)
    $title           = mysqli_real_escape_string($conn, $_POST['title']);
    $description     = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id     = mysqli_real_escape_string($conn, $_POST['category_id']);
    $event_date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $start_time      = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time        = mysqli_real_escape_string($conn, $_POST['end_time']);
    $ticket_price    = mysqli_real_escape_string($conn, $_POST['ticket_price']);


    // 2. Get Venue Data (New or Existing)
    $venue_select_type = isset($_POST['venue_select_type']) ? $_POST['venue_select_type'] : 'existing';
    $venue_id_existing = isset($_POST['venue_id_existing']) ? $_POST['venue_id_existing'] : '';
    $final_venue_id = null;

    if ($venue_select_type === 'new') {
        // --- Process New Venue ---
        $venue_name = mysqli_real_escape_string($conn, $_POST['venue_name_new']);
        $address    = mysqli_real_escape_string($conn, $_POST['address_new']);
        $capacity   = mysqli_real_escape_string($conn, $_POST['capacity_new']);

        if (!empty($venue_name) && !empty($address)) {
            $sql_venue = "INSERT INTO event_venues (venue_name, address, capacity) VALUES (?, ?, ?)";

            if ($stmt_venue = mysqli_prepare($conn, $sql_venue)) {
                $capacity_int = is_numeric($capacity) && $capacity > 0 ? (int)$capacity : null;
                mysqli_stmt_bind_param($stmt_venue, "ssi", $venue_name, $address, $capacity_int);

                if (mysqli_stmt_execute($stmt_venue)) {
                    // Success: Get the ID of the newly inserted venue
                    $final_venue_id = mysqli_insert_id($conn);
                } else {
                    error_log("MySQLi Execute Error (Venue): " . mysqli_stmt_error($stmt_venue));
                    echo "<script>alert('ERROR: Could not add new venue. Check PHP error log.');</script>";
                    mysqli_stmt_close($stmt_venue);
                    exit();
                }
                mysqli_stmt_close($stmt_venue);
            } else {
                error_log("MySQLi Prepare Error (Venue): " . mysqli_error($conn));
                echo "<script>alert('ERROR: Could not prepare venue query.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('ERROR: New Venue Name and Address are required.');</script>";
            exit();
        }
    } elseif ($venue_select_type === 'existing' && !empty($venue_id_existing)) {
        // --- Process Existing Venue ---
        $final_venue_id = (int)$venue_id_existing;
    } else {
        echo "<script>alert('ERROR: A venue selection is required.');</script>";
        exit();
    }


    // 3. Insert Event using $final_venue_id
    if ($final_venue_id !== null) {
        $sql = "INSERT INTO events (
                    title, description, category_id, venue_id, event_date, start_time, end_time, ticket_price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssiisssd",
                $title,
                $description,
                $category_id,
                $final_venue_id, // Use the dynamically determined ID
                $event_date,
                $start_time,
                $end_time,
                $ticket_price

            );

            if (mysqli_stmt_execute($stmt)) {
                header("location: manage_events.php?status=added");
                exit();
            } else {
                error_log("MySQLi Execute Error (Event): " . mysqli_stmt_error($stmt));
                echo "<script>alert('ERROR: Could not execute event query. Check PHP error log for details.');</script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("MySQLi Prepare Error (Event): " . mysqli_error($conn));
            echo "<script>alert('ERROR: Could not prepare event query. Check PHP error log for details.');</script>";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'fetch_event' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = (int)$_GET['id'];
    $event = get_event_by_id($conn, $event_id);

    // Set header to JSON content type
    header('Content-Type: application/json');

    if ($event) {
        // Successfully fetched data
        echo json_encode(['success' => true, 'event' => $event]);
    } else {
        // Failed to fetch data
        echo json_encode(['success' => false, 'message' => 'Event not found or database error.']);
    }
    // STOP execution for the AJAX request
    exit(); 
}

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM events WHERE event_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_events.php?status=deleted");
            exit();
        } else {
            echo "<script>alert('ERROR: Could not execute delete query.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    // 1. Get Event Data
    $event_id        = mysqli_real_escape_string($conn, $_POST['event_id']);
    $title           = mysqli_real_escape_string($conn, $_POST['title']);
    $description     = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id     = mysqli_real_escape_string($conn, $_POST['category_id']);
    $venue_id        = mysqli_real_escape_string($conn, $_POST['venue_id']); // Use name 'venue_id' from the edit modal
    $event_date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $start_time      = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time        = mysqli_real_escape_string($conn, $_POST['end_time']);
    $ticket_price    = mysqli_real_escape_string($conn, $_POST['ticket_price']);
    $status          = mysqli_real_escape_string($conn, $_POST['status']);


    // 2. Prepare Update Query
    $sql = "UPDATE events SET
                title = ?, 
                description = ?, 
                category_id = ?, 
                venue_id = ?, 
                event_date = ?, 
                start_time = ?, 
                end_time = ?, 
                ticket_price = ?,
                status = ?
            WHERE event_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssiisssdsi",
            $title,
            $description,
            $category_id,
            $venue_id,
            $event_date,
            $start_time,
            $end_time,
            $ticket_price,
            $status,
            $event_id // The WHERE clause parameter
        );

        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_events.php?status=updated");
            exit();
        } else {
            error_log("MySQLi Execute Error (Update Event): " . mysqli_stmt_error($stmt));
            echo "<script>alert('ERROR: Could not update event. Check PHP error log for details.');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("MySQLi Prepare Error (Update Event): " . mysqli_error($conn));
        echo "<script>alert('ERROR: Could not prepare update query. Check PHP error log for details.');</script>";
    }
}

function get_event_by_id($conn, $id)
{

    $sql = "SELECT 
                e.event_id AS id, 
                e.title, 
                e.description, 
                e.event_date, 
                e.start_time, 
                e.end_time,
                e.category_id,
                e.venue_id,      
                e.ticket_price,
                e.available_seats,     
                e.status, 
                v.venue_name, 
                c.category_name 
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
    } else {
        error_log("MySQLi Prepare Error (get_event_by_id): " . mysqli_error($conn));
        return null;
    }
}

function get_all_categories($conn)
{
    $sql = "SELECT category_id, category_name FROM event_categories ORDER BY category_name ASC";
    $result = mysqli_query($conn, $sql);

    if ($result === false) {
        error_log("MySQL Query Error in get_all_categories: " . mysqli_error($conn));
        return array();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// ------------------------------------
// C. FETCH EVENTS FOR DISPLAY
// ------------------------------------
$events = get_all_events($conn);
$venues = get_all_venues($conn);
$categories = get_all_categories($conn);

// Function to fetch all existing venues for the dropdown
function get_all_venues($conn)
{
    $sql = "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name ASC";
    $result = mysqli_query($conn, $sql);

    // Check for query execution failure
    if ($result === false) {
        error_log("MySQL Query Error in get_all_venues: " . mysqli_error($conn));
        return array();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// ------------------------------------
// D. HANDLE ADDING NEW EVENT CATEGORY
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
        $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

        $sql = "INSERT INTO event_categories (category_name) VALUES (?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind the category name parameter (s = string)
            mysqli_stmt_bind_param($stmt, "s", $category_name);

            if (mysqli_stmt_execute($stmt)) {
                // Success: Redirect to prevent form resubmission
                header("location: manage_events.php?status=category_added");;
                exit();
            } else {
                // Execution failed (e.g., duplicate entry if column is unique)
                error_log("MySQLi Execute Error (Category): " . mysqli_stmt_error($stmt));
                echo "<script>alert('ERROR: Could not execute category query. Check PHP error log for details.');</script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("MySQLi Prepare Error (Category): " . mysqli_error($conn));
            echo "<script>alert('ERROR: Could not prepare category query.');</script>";
        }
    } else {
        echo "<script>alert('ERROR: Category name cannot be empty.');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="css/manage_events.css">
    <link rel="stylesheet" href="../includes/navbar.css">

</head>

<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container">
        <h1>🗓️ Event Management</h1>

        <button id="addNewEventBtn" class="add-event-btn">+ Add New Event</button>
        <button id="addNewCategoryBtn" class="add-event-btn">+ Add New Category</button>
        <h2>Existing Events</h2>

        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Price</th>
                    <th>Seats Left</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['id']); ?></td>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($event['start_time']) . ' - ' . htmlspecialchars($event['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($event['venue_name']); ?></td>
                            <td><?php echo htmlspecialchars($event['ticket_price']); ?></td>
                            <td><?php echo htmlspecialchars($event['available_seats']); ?></td>
                            <td><?php echo htmlspecialchars($event['status']); ?></td>
                            <td><?php echo htmlspecialchars($event['created_at']); ?></td>
                            <td class="action-links">
                                <a href="javascript:void(0);" data-id="<?php echo $event['id']; ?>" class="edit">Edit</a>
                                <a href="manage_events.php?delete_id=<?php echo $event['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No events found. Start by adding one!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

    <?php include 'add_event_modal.php'; ?>
    <?php include 'edit_event_modal.php'; ?>

    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Category</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <label for="category_name">Category Name</label>
                <input type="text" id="category_name" name="category_name" required>

                <button type="submit" name="add_category">Save Category</button>
            </form>
        </div>
    </div>
    <script src="manage_events.js"></script>
</body>

</html>