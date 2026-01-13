<?php
session_start();
include "../../db_connect.php";

// Redirect if accessed directly without POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../home.php");
    exit();
}

// 1. Collect POST Data
$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
// We need to determine user_id.
// Strategy: 
// 1. If logged in, use session user_id.
// 2. If not logged in, we currently don't have a registration flow in checkout.
//    However, usually systems either force login before checkout or create a guest user.
//    Based on previous steps, we are displaying "Welcome [Name]" if session exists.
//    Let's assumes user IS logged in or we fail (or use a placeholder/check logic).
//    For now, we enforce session user_id. If guest checkout is needed, we'd insert into users table first.

if (!isset($_SESSION['user_id'])) {
    // If not logged in, normally redirect to login.
    // But since we are at process stage, maybe handle error.
    die("Error: User must be logged in to complete payment.");
}
$user_id = $_SESSION['user_id'];

$total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
// Seats are passed as array. Quantity is count of seats.
$seats = isset($_POST['seats']) ? $_POST['seats'] : [];
$quantity = count($seats);

// Ticket ID?
// The requirement says 'ticket_id' is a column.
// Usually a booking HAS tickets. Is 'ticket_id' a foreign key to a tickets table?
// Or acts as a unique ticket identifier string?
// Looking at schema: ticket_id (int(50)).
// We might need to generate a ticket or insert into a tickets table first?
// Let's assume for this step we need to generate/insert a ticket. 
// However, the User Request just says "store in event_booking table columns ... ticket_id".
// If 1 booking has multiple seats, does it have 1 ticket_id per booking? Or 1 per seat?
// Schema has valid booking_id AND ticket_id in `event_booking`.
// If `event_booking` represents the whole order, `ticket_id` might be the "Master Ticket" ID or we might be misinterpreting.
// Let's check schemas again.
// `tickets` table exists: - ticket_id (int(11)) - qr_code (varchar(100)).
// It seems `event_booking` links to `tickets`.
// Let's create a dummy ticket entry for now to satisfy foreign key if it exists, or just generate an ID.
// For now, let's insert a row into `tickets` table to get a new `ticket_id`.

// Insert into tickets table to generate an ID?
// Or is ticket_id just a generated number?
// Let's assume we insert into `tickets` first.

// 2. Insert Logic
$booking_date = date("Y-m-d H:i:s");
$status = 'confirmed'; // Payment simulated as successful

// Check if event exists in DB, if not and it's a sample event, insert it.
$check_event_sql = "SELECT event_id FROM events WHERE event_id = $event_id";
$check_result = mysqli_query($conn, $check_event_sql);

if (mysqli_num_rows($check_result) == 0) {
    // Event not in DB. Check if it is a sample event.
    $sample_events = [
        9991 => ['title' => 'Summer Music Festival', 'venue_id' => 1, 'event_date' => '2025-07-15'],
        9992 => ['title' => 'Tech Innovators Summit', 'venue_id' => 2, 'event_date' => '2025-08-20'],
        9993 => ['title' => 'Modern Art Exhibition', 'venue_id' => 3, 'event_date' => '2025-09-05']
    ];

    if (array_key_exists($event_id, $sample_events)) {
        // Insert sample event into DB to satisfy Foreign Key
        // Ensure venue_id exists or use default 1
        $s_event = $sample_events[$event_id];
        $insert_event_sql = "INSERT INTO events (event_id, title, venue_id, event_date, category_id, start_time, end_time, capacity, available_seats, ticket_price, status, created_at) 
                             VALUES ($event_id, '{$s_event['title']}', {$s_event['venue_id']}, '{$s_event['event_date']}', 1, '10:00:00', '18:00:00', 500, 500, 0.00, 'active', NOW())";
        if (!mysqli_query($conn, $insert_event_sql)) {
            // If insert fails (maybe venue_id doesn't exist), try with minimal fields or venue_id=1
            // For robustness in this fix, assume venue_id 1 exists or is optional.
            // If error persists, user might need to ensure 'active' status enum is correct.
            die("Error creating synced event record: " . mysqli_error($conn));
        }
    } else {
        die("Error: Event ID $event_id does not exist in database and is not a recognized sample event.");
    }
}

// Generate a Ticket (Simplified)
$qr_code_data = "EVT-$event_id-USR-$user_id-" . time(); // Unique string
$ticket_sql = "INSERT INTO tickets (qr_code) VALUES ('$qr_code_data')";
if (mysqli_query($conn, $ticket_sql)) {
    $ticket_id = mysqli_insert_id($conn);
} else {
    die("Error creating ticket: " . mysqli_error($conn));
}

// 3. Insert into event_booking
$booking_sql = "INSERT INTO event_booking (user_id, event_id, quantity, total_price, ticket_id, booking_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $booking_sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iiidiss", $user_id, $event_id, $quantity, $total_price, $ticket_id, $booking_date, $status);

    if (mysqli_stmt_execute($stmt)) {
        $booking_id = mysqli_insert_id($conn);

        // Success! Redirect to a confirmation page.
        // We can pass booking_id to show details.
        header("Location: booking_confirmation.php?booking_id=$booking_id");
        exit();
    } else {
        die("Error processing booking: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
} else {
    die("Database error: " . mysqli_error($conn));
}
?>
