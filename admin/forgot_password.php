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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Please enter your email address.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes expiry

            $stmt->close();

            // Update user with token
            $update_stmt = $conn->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE email = ?");
            $update_stmt->bind_param("ss", $token_hash, $email);
            
            if ($update_stmt->execute()) {
                // SIMULATE EMAIL SENDING
                $reset_link = "http://localhost/event_registration_and_ticketing/reset_password.php?token=" . $token;
                $message = "Password reset link generated (Simulation): <br> <a href='$reset_link'>$reset_link</a>";
            } else {
                $message = "Something went wrong. Please try again.";
            }
            $update_stmt->close();
        } else {
            $message = "No account found with that email.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { width: 100%; padding: 12px; margin-top: 10px; border: none; background: #007BFF; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { text-align: center; margin-top: 15px; color: green; word-break: break-all; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <a href="admin_login.html" class="back-link">Back to Login</a>
</div>

</body>
</html>
