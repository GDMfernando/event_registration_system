<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$token = isset($_GET['token']) ? $_GET['token'] : "";

if (empty($token)) {
    die("Invalid request. No token provided.");
}

$token_hash = hash("sha256", $token);

// Validate token
$sql = "SELECT user_id, reset_token_expires_at FROM users WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid token. The token does not exist in the database.");
}

$row = $result->fetch_assoc();
$expiry = $row['reset_token_expires_at'];
$current_time = date("Y-m-d H:i:s");

// Check expiry (PHP side check to be sure)
if (strtotime($expiry) < time()) {
    die("Token expired. Expiry: $expiry, Current Time: $current_time");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password and clear token
        $update_sql = "UPDATE users SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE reset_token_hash = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $password_hash, $token_hash);

        if ($update_stmt->execute()) {
            echo "<script>alert('Password reset successful! You can now login.'); window.location.href='admin_login.html';</script>";
            exit;
        } else {
            $message = "Error updating password.";
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { width: 100%; padding: 12px; margin-top: 10px; border: none; background: #007BFF; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <form method="POST" action="">
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php if ($message): ?>
        <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
