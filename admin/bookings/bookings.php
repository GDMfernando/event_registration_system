<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}
include('../../db_connect.php');

$success_msg = "";
$error = null;



if (isset($_GET['cancel_id']) || (isset($_POST['cancel_ids']) && $_SERVER['REQUEST_METHOD'] === 'POST')) {
    $ids_to_cancel = isset($_GET['cancel_id']) ? [(int)$_GET['cancel_id']] : array_map('intval', $_POST['cancel_ids']);

    if (!empty($ids_to_cancel)) {
        mysqli_begin_transaction($conn);
        try {
            foreach ($ids_to_cancel as $id) {
                $info_res = mysqli_query($conn, "SELECT event_id, quantity FROM event_booking WHERE booking_id = $id AND status != 'cancelled'");
                if ($booking_info = mysqli_fetch_assoc($info_res)) {
                    $event_id = $booking_info['event_id'];
                    $qty = $booking_info['quantity'];
                    mysqli_query($conn, "UPDATE event_booking SET status = 'cancelled' WHERE booking_id = $id");
                    mysqli_query($conn, "UPDATE events SET available_seats = available_seats + $qty WHERE event_id = $event_id");
                }
            }
            mysqli_commit($conn);
            $success_msg = "Selected bookings have been cancelled.";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error: " . $e->getMessage();
        }
    }
}

$requests = [['subject' => 'Canselation and refunding'], ['subject' => 'Renaming ticket'], ['subject' => 'Change event']];

$filter_name      = $_GET['user_name'] ?? '';
$filter_ticket_id = $_GET['ticket_id'] ?? '';
$filter_date      = $_GET['event_date'] ?? '';
$filter_request   = $_GET['request_subject'] ?? '';

$sql = "SELECT b.booking_id AS id, b.quantity, b.total_price, b.booking_date, b.status,
               u.full_name AS user_name, e.title AS event_title, b.ticket_id
        FROM event_booking b
        JOIN user u ON b.user_id = u.user_id
        JOIN events e ON b.event_id = e.event_id
        LEFT JOIN support_requests sr ON u.email = sr.email
        WHERE 1=1";

if (!empty($filter_name)) {
    $safe_name = mysqli_real_escape_string($conn, $filter_name);
    $sql .= " AND u.full_name LIKE '%$safe_name%'";
}
if (!empty($filter_ticket_id)) {
    $safe_ticket = mysqli_real_escape_string($conn, $filter_ticket_id);
    $sql .= " AND b.ticket_id LIKE '%$safe_ticket%'";
}
if (!empty($filter_date)) {
    $sql .= " AND e.event_date = '" . mysqli_real_escape_string($conn, $filter_date) . "'";
}
if (!empty($filter_request)) {
    $sql .= " AND sr.subject = '" . mysqli_real_escape_string($conn, $filter_request) . "'";
}
// ------------------------------------
// B. FETCH BOOKINGS FOR DISPLAY
// ------------------------------------
$sql .= " GROUP BY b.booking_id ORDER BY b.booking_date DESC";
$bookings_res = mysqli_query($conn, $sql);
$bookings = mysqli_fetch_all($bookings_res, MYSQLI_ASSOC);

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
    
 <div class="header">
           <h1>🎟️ Booking Management</h1>
            <a href="../dashboard/dashboard.php" class="btn-back">Back to Dashboard</a>
        </div>

<?php if ($success_msg): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:4px; margin-bottom:20px; border:1px solid #c3e6cb;"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <div class="filter-container">
            <form method="GET" action="bookings.php" class="filter-form">
                <div class="filter-group">
                    <label>Customer Name</label>
                    <input type="text" name="user_name" placeholder="Search name..." value="<?= htmlspecialchars($filter_name) ?>">
                </div>

                <div class="filter-group">
                    <label>Ticket ID</label>
                    <input type="text" name="ticket_id" placeholder="Search ID..." value="<?= htmlspecialchars($filter_ticket_id) ?>">
                </div>

                <div class="filter-group">
                    <label>Event Date</label>
                    <input type="date" name="event_date" value="<?= htmlspecialchars($filter_date) ?>">
                </div>

                <div class="filter-group">
                    <label>Support Request</label>
                    <select name="request_subject">
                        <option value="">All Requests</option>
                        <?php foreach ($requests as $r): ?>
                            <option value="<?= htmlspecialchars($r['subject']) ?>" <?= ($filter_request == $r['subject']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['subject']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
 
                <div class="filter-buttons">
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="bookings.php" class="btn-clear">Reset</a>
                </div>
            </form>
        </div>

        <form method="POST" action="bookings.php" onsubmit="return confirm('Cancel all selected bookings?');">
            <div style="margin-bottom: 15px;">
                <button type="submit" style="background:#dc3545; color:white; border:none; padding:10px 15px; border-radius:4px; cursor:pointer;">Cancel Selected</button>
            </div>

            <table class="event-table">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Event Title</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Ticket ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td style="text-align:center;">
                                    <?php if ($b['status'] !== 'cancelled'): ?>
                                        <input type="checkbox" name="cancel_ids[]" value="<?= $b['id'] ?>">
                                    <?php endif; ?>
                                </td>
                                <td>#<?= $b['id'] ?></td>
                                <td><?= htmlspecialchars($b['user_name']) ?></td>
                                <td><?= htmlspecialchars($b['event_title']) ?></td>
                                <td><?= $b['quantity'] ?></td>
                                <td>$<?= number_format($b['total_price'], 2) ?></td>
                                <td><?= date('Y-m-d', strtotime($b['booking_date'])) ?></td>
                                <td class="status-<?= strtolower($b['status']) ?>"><?= ucfirst($b['status']) ?></td>
                                <td><code><?= htmlspecialchars($b['ticket_id']) ?></code></td>
                                <td>
                                    <?php if ($b['status'] !== 'cancelled'): ?>
                                        <a href="bookings.php?cancel_id=<?= $b['id'] ?>" style="color:#d9534f; text-decoration:none; font-size:12px;" onclick="return confirm('Cancel this booking?')">Cancel</a>
                                    <?php else: ?>
                                        <small style="color:#999;">Cancelled</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center;">No bookings found matching filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>

    </div>

    <script src="../manage_events/manage_events.js"></script>
</body>

</html>