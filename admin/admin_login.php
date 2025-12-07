<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill both email and password!";
        header("Location: admin_login.html");
        exit();
    }

    // Fetch user info for admin
    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash FROM users WHERE email = ? AND role = 'admin'");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // No admin found with this email
        $_SESSION['error'] = "Invalid email or password!";
        header("Location: admin_login.html");
        exit();
    }

    $stmt->bind_result($user_id, $full_name, $hashed_password);
    $stmt->fetch();




    if (password_verify($password, $hashed_password)) {
        // Successful login
        $_SESSION['admin_id'] = $user_id;
        $_SESSION['admin_name'] = $full_name;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password!";
        header("Location: admin_login.html");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
