<?php

include('../../db_connect.php');

// Function to fetch all bookings with associated User Name and Event Title
function get_all_bookings($conn) {
    // Joining event_booking (b) with user (u) on user_id and events (e) on event_id
    $sql = "SELECT 
                b.booking_id AS id, 
                b.quantity, 
                b.total_price, 
                b.booking_date, 
                b.status,
                u.full_name AS user_name,  
                e.title AS event_title,  
                b.ticket_id
            FROM event_booking b
            JOIN user u ON b.user_id = u.user_id
            JOIN events e ON b.event_id = e.event_id
            ORDER BY b.booking_date DESC"; 
            
    $result = mysqli_query($conn, $sql);
    
    if ($result === false) {
        die("MySQL Query Error in get_all_bookings: " . mysqli_error($conn) . 
            "<br>Query: " . htmlspecialchars($sql));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ------------------------------------
// A. HANDLE DELETING BOOKING
// ------------------------------------
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Note: You might need to handle related records in the 'tickets' table 
    // depending on your database constraints (e.g., if ticket is linked to booking).
    // For simplicity, this only deletes the booking record itself.
    
    $sql = "DELETE FROM event_booking WHERE booking_id = ?"; 
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_bookings.php?status=deleted");
            exit();
        } else {
            error_log("MySQLi Execute Error (Delete Booking): " . mysqli_stmt_error($stmt));
            echo "<script>alert('ERROR: Could not execute delete query.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// ------------------------------------
// B. FETCH BOOKINGS FOR DISPLAY
// ------------------------------------
$bookings = get_all_bookings($conn);

mysqli_close($conn); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <link rel="stylesheet" href="../manage_events/css/manage_events.css"> 
    <link rel="stylesheet" href="../includes/navbar.css"> 
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container">
        <h1>🎟️ Booking Management</h1>
        
        <h2>Existing Bookings</h2>
        
        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Event Title</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Ticket ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>      <td><?php echo htmlspecialchars($booking['event_title']); ?></td>    <td><?php echo htmlspecialchars($booking['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($booking['booking_date']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                            <td><?php echo htmlspecialchars($booking['ticket_id']); ?></td>
                            <td class="action-links">
                                <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" class="edit">Edit</a>
                                <a href="manage_bookings.php?delete_id=<?php echo $booking['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

<script src="../manage_events/manage_events.js"></script> </body>
</html>