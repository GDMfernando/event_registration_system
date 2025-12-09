<?php
session_start();
include "../db_connect.php";

$error = "";
$success = "";
$token = "";

if (isset($_GET['token'])) {
  $token = $_GET['token'];
  $token_hash = hash('sha256', $token);
  // Verify token in user table
  $sql = "SELECT * FROM user WHERE reset_token_hash = '$token_hash' AND reset_token_expires_at > NOW()";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) == 0) {
    $error = "Invalid or expired token.";
  }
} else {
  if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $error = "No token provided.";
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $token = $_POST['token'];
  $token_hash = hash('sha256', $token);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

  if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    // Update password in user table
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE user SET password_hash = '$hashed_password', reset_token_hash = NULL, reset_token_expires_at = NULL WHERE reset_token_hash = '$token_hash'";
    if (mysqli_query($conn, $sql)) {
      $success = "Password has been reset successfully! <a href='user_login.php'>Login here</a>.";
    } else {
      $error = "Error updating password.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - Event Registration System</title>
  <link rel="stylesheet" href="user_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>Reset Password</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success-msg"
        style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px;">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <?php if (empty($success) && (isset($_GET['token']) || isset($_POST['token'])) && empty($error) || (isset($error) && $error == "Passwords do not match.")): ?>
      <form action="user_reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-login">Update Password</button>
      </form>
    <?php endif; ?>

    <a href="user_login.php" class="back-link">Back to Login</a>
  </div>

</body>

</html>