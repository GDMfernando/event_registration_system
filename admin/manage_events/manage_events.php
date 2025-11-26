<?php

include('../../db/db_connect.php');

function get_all_events($conn) {
    $sql = "SELECT 
                event_id AS id, 
                title, 
                event_date, 
                start_time, 
                end_time,
                venue_id,
                ticket_price, 
                category_id
            FROM events 
            ORDER BY event_date DESC"; // Changed ORDER BY to use event_date
    $result = mysqli_query($conn, $sql);
    
    if ($result === false) {
        die("MySQL Query Error in get_all_events: " . mysqli_error($conn) . 
            "<br>Query: " . htmlspecialchars($sql));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}



// ------------------------------------
// A. HANDLE ADDING NEW EVENT & VENUE
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    
    // 1. Get Event Data (No change here, but included for context)
    $title           = mysqli_real_escape_string($conn, $_POST['title']);
    $description     = mysqli_real_escape_string($conn, $_POST['description']);
    $event_date      = mysqli_real_escape_string($conn, $_POST['event_date']);
    $start_time      = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time        = mysqli_real_escape_string($conn, $_POST['end_time']);
    $ticket_price    = mysqli_real_escape_string($conn, $_POST['ticket_price']);
    $category_id     = mysqli_real_escape_string($conn, $_POST['category_id']);
    
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
                    title, description, venue_id, event_date, start_time, end_time, ticket_price, category_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssisssdi", 
                $title, 
                $description, 
                $final_venue_id, // Use the dynamically determined ID
                $event_date, 
                $start_time, 
                $end_time, 
                $ticket_price,
                $category_id
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

// ------------------------------------
// B. HANDLE DELETING EVENT
// ------------------------------------
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

// ------------------------------------
// C. FETCH EVENTS FOR DISPLAY
// ------------------------------------
$events = get_all_events($conn);
$venues = get_all_venues($conn);

// Function to fetch all existing venues for the dropdown
function get_all_venues($conn) {
    $sql = "SELECT venue_id, venue_name FROM event_venues ORDER BY venue_name ASC";
    $result = mysqli_query($conn, $sql);
    
    // Check for query execution failure
    if ($result === false) {
        error_log("MySQL Query Error in get_all_venues: " . mysqli_error($conn));
        // Use array() syntax for maximum compatibility
        return array(); 
    }
    
    // If successful but no rows are found, it will return an empty array anyway
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// ------------------------------------
// D. HANDLE ADDING NEW EVENT CATEGORY
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    // Check if category_name is present and sanitize it
    if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
        $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
        
        // Prepare the INSERT statement
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
<button id="addNewCategoryBtn" class="add-event-btn" >+ Add New Category</button>
        <h2>Existing Events</h2>
        
        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue ID</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['id']); ?></td>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($event['start_time']) . ' - ' . htmlspecialchars($event['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($event['venue_id']); ?></td>
                            <td><?php echo htmlspecialchars($event['ticket_price']); ?></td>
                            <td class="action-links">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="edit">Edit</a>
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

<div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Event</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <label for="title">Event Title</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>

                <label for="category_id">Category ID</label>
                <input type="number" id="category_id" name="category_id" required> 
                
                <h3>Venue Details</h3>
                
                <label>
                    <input type="radio" name="venue_select_type" value="existing" checked onclick="toggleVenueFields('existing')"> Select Existing Venue
                </label>
                <label>
                    <input type="radio" name="venue_select_type" value="new" onclick="toggleVenueFields('new')"> Add New Venue
                </label>
                
                <div id="existingVenueFields">
                    <label for="venue_id_existing">Select Venue</label>
                    <select id="venue_id_existing" name="venue_id_existing" required>
                        <option value="">-- Select a Venue --</option>
                        <?php foreach ($venues as $venue): ?>
                            <option value="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                                <?php echo htmlspecialchars($venue['venue_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="newVenueFields" style="display:none;">
                    <label for="venue_name_new">New Venue Name</label>
                    <input type="text" id="venue_name_new" name="venue_name_new">
                    
                    <label for="address_new">Address</label>
                    <input type="text" id="address_new" name="address_new">

                    <label for="capacity_new">Capacity</label>
                    <input type="number" id="capacity_new" name="capacity_new" min="1">
                </div>
                
                <label for="event_date">Date</label>
                <input type="date" id="event_date" name="event_date" required>
                
                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" required>

                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time">

                <label for="ticket_price">Ticket Price</label>
                <input type="number" step="0.01" id="ticket_price" name="ticket_price" required>
                
                <button type="submit" name="add_event">Save Event</button>
            </form>
        </div>
    </div>

    <div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span  class="close">&times;</span>
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