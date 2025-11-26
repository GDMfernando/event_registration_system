<?php

include('../../db/db_connect.php');

// --- CRUD Operations PHP Logic ---

// Function to fetch all events
function get_all_events($conn) {
    // FIX 1: Updated column names in the SELECT query
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
    
    // --- ADDED CRITICAL CHECK ---
    if ($result === false) {
        // Output the specific MySQL error for debugging
        die("MySQL Query Error in get_all_events: " . mysqli_error($conn) . 
            "<br>Query: " . htmlspecialchars($sql));
    }
    // ----------------------------

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ------------------------------------
// A. HANDLE ADDING NEW EVENT
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    // FIX 2: Collect all new fields from the POST request
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $venue_id      = mysqli_real_escape_string($conn, $_POST['venue_id']);
    $event_date    = mysqli_real_escape_string($conn, $_POST['event_date']);
    $start_time    = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time      = mysqli_real_escape_string($conn, $_POST['end_time']);
    $ticket_price  = mysqli_real_escape_string($conn, $_POST['ticket_price']);
    $category_id     = mysqli_real_escape_string($conn, $_POST['category_id']);
   
    // FIX 3: Updated INSERT query with all columns
    $sql = "INSERT INTO events (
            title, description, venue_id, event_date, start_time, end_time, ticket_price, category_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // FIX 4: Updated bind_param types and variables (s=string, i=int, d=double)
        mysqli_stmt_bind_param($stmt, "ssisssdi", 
            $title, 
            $description, 
            $venue_id, 
            $event_date, 
            $start_time, 
            $end_time, 
            $ticket_price,
            $category_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to prevent form resubmission
            header("location: manage_events.php?status=added");
            exit();
        } else {
            // Added error logging for debugging
            error_log("MySQLi Execute Error: " . mysqli_stmt_error($stmt));
            echo "<script>alert('ERROR: Could not execute query. Check PHP error log for details.');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
         error_log("MySQLi Prepare Error: " . mysqli_error($conn));
         echo "<script>alert('ERROR: Could not prepare query. Check PHP error log for details.');</script>";
    }
}

// ------------------------------------
// B. HANDLE DELETING EVENT
// ------------------------------------
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // FIX 5: Use 'event_id' column for deletion
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

                <label for="venue_id">Venue ID</label>
                <input type="number" id="venue_id" name="venue_id" required>

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