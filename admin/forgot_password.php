<?php
session_start();
include "../db_connect.php";

$error = "";
$success = "";

// Self-healing: Check if reset_token_hash column exists in user table, if not add it
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM user LIKE 'reset_token_hash'");
if (mysqli_num_rows($check_col) == 0) {
  mysqli_query($conn, "ALTER TABLE user ADD COLUMN reset_token_hash VARCHAR(64) NULL");
  mysqli_query($conn, "ALTER TABLE user ADD COLUMN reset_token_expires_at DATETIME NULL");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  // Check if user exists and is an admin
  $sql = "SELECT * FROM user WHERE email = '$email' AND role = 'admin'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $token = bin2hex(random_bytes(32)); // 64 characters
    $token_hash = hash('sha256', $token);
    $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $update_sql = "UPDATE user SET reset_token_hash = '$token_hash', reset_token_expires_at = '$expire' WHERE email = '$email'";
    if (mysqli_query($conn, $update_sql)) {
      // In a real application, send email here.
      // For demo purposes, we'll show the link.
      $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
      $success = "A password reset link has been generated (Simulated Email): <br><a href='$resetLink'>$resetLink</a>";
    } else {
      $error = "Something went wrong. Please try again.";
    }
  } else {
    $error = "No admin account found with that email.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - Event Registration System</title>
  <link rel="stylesheet" href="admin_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>Forgot Password</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success-msg"
        style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px; word-break: break-all;">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST">
      <div class="form-group">
        <label for="email">Enter your Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <button type="submit" class="btn-login">Reset Password</button>
    </form>

    <a href="admin_login.php" class="back-link">Back to Login</a>
  </div>

</body>

</html>