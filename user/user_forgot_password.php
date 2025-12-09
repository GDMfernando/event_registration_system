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

  // Check if user exists and is a user
  $sql = "SELECT * FROM user WHERE email = '$email' AND role = 'user'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $token = bin2hex(random_bytes(32)); // 64 characters
    $token_hash = hash('sha256', $token);
    // Use MySQL timestamp to avoid timezone issues between PHP and MySQL
    $update_sql = "UPDATE user SET reset_token_hash = '$token_hash', reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$email'";
    if (mysqli_query($conn, $update_sql)) {
      // Redirect directly to user_reset_password.php with the token
      header("Location: user_reset_password.php?token=" . $token);
      exit();
    } else {
      $error = "Database Error: " . mysqli_error($conn);
    }
  } else {
    $error = "No user account found with that email.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - Event Registration System</title>
  <link rel="stylesheet" href="user_style.css">
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

    <form action="user_forgot_password.php" method="POST">
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