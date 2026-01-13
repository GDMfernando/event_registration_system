<?php

include('../../db_connect.php');

// Function to fetch all bookings with associated User Name and Event Title
function get_all_bookings($conn)
{
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


if (isset($_GET['cancel_id']) && is_numeric($_GET['cancel_id'])) {
    $id = (int)$_GET['cancel_id'];

    // 1. Get the quantity and event_id before cancelling
    $info_query = "SELECT event_id, quantity FROM event_booking WHERE booking_id = $id";
    $info_result = mysqli_query($conn, $info_query);
    $booking_info = mysqli_fetch_assoc($info_result);

    if ($booking_info) {
        $event_id = $booking_info['event_id'];
        $qty = $booking_info['quantity'];

        // 2. Start Transaction to ensure both updates happen or none
        mysqli_begin_transaction($conn);

        try {
            // Update booking status
            mysqli_query($conn, "UPDATE event_booking SET status = 'cancelled' WHERE booking_id = $id");
            
            // Restore seats to the event table (Assuming your column is named 'available_seats')
            mysqli_query($conn, "UPDATE events SET available_seats = available_seats + $qty WHERE event_id = $event_id");

            mysqli_commit($conn);
            header("location: bookings.php?msg=cancelled");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Error processing cancellation.');</script>";
        }
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
    <link rel="stylesheet" href="../manage_events/manage_events.css">
    <link rel="stylesheet" href="../includes/navbar.css">
    <link rel="stylesheet" href="admin_bookings.css">
</head>

<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container">
        <h1>🎟️ Booking Management</h1>


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
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['event_title']); ?></td>
                            <td><?php echo htmlspecialchars($booking['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($booking['booking_date']))); ?></td>
                            <td class="status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($booking['ticket_id']); ?></td>
                            <td class="action-links">
                                <?php if ($booking['status'] !== 'cancelled'): ?>

                                    <a href="bookings.php?cancel_id=<?php echo $booking['id']; ?>"
                                        class="delete"
                                        style="color: #d9534f;"
                                        onclick="return confirm('Are you sure you want to cancel this booking? This will invalidate the ticket.');">
                                        Cancel Booking
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">No Actions Available</span>
                                <?php endif; ?>
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

    <script src="../manage_events/manage_events.js"></script>
</body>

</html>