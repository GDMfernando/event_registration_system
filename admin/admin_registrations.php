<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_registration_id'])) {
    $reg_id = $_POST['cancel_registration_id'];
    $stmt = $conn->prepare("UPDATE registrations SET status = 'cancelled' WHERE registration_id = ?");
    $stmt->bind_param("i", $reg_id);
    if ($stmt->execute()) {
        $message = "Registration #$reg_id cancelled successfully.";
    } else {
        $error = "Error cancelling registration: " . $conn->error;
    }
    $stmt->close();
}

// Build Query
$sql = "SELECT r.registration_id, r.registration_date, r.status, u.full_name, e.title as event_title 
        FROM registrations r 
        JOIN users u ON r.user_id = u.user_id 
        JOIN events e ON r.event_id = e.event_id 
        WHERE 1=1";

$params = array();
$types = "";

// Filters
$filter_event = isset($_GET['event_id']) ? $_GET['event_id'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

if (!empty($filter_event)) {
    $sql .= " AND r.event_id = ?";
    $params[] = $filter_event;
    $types .= "i";
}
if (!empty($filter_status)) {
    $sql .= " AND r.status = ?";
    $params[] = $filter_status;
    $types .= "s";
}
if (!empty($filter_date)) {
    $sql .= " AND DATE(r.registration_date) = ?";
    $params[] = $filter_date;
    $types .= "s";
}

$sql .= " ORDER BY r.registration_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $bind_params = array();
    $bind_params[] = &$types;
    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_params);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch events for filter dropdown
$events_result = $conn->query("SELECT event_id, title FROM events ORDER BY title");

// Include the view file
include 'admin_registrations_view.php';

$conn->close();
?>
