<?php
session_start();
include "../db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($email) || empty($password)) {
    $error = "Please enter both email and password.";
  } else {
    // Prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if admin exists in user table
    $sql = "SELECT * FROM user WHERE email = '$email' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);
      if (password_verify($password, $row['password_hash'])) {
        $_SESSION['admin_id'] = $row['user_id'];
        $_SESSION['admin_username'] = $row['username']; // Keep username for dashboard display
        $_SESSION['admin_email'] = $row['email'];
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
      } else {
        $error = "Invalid email or password.";
      }
    } else {
      $error = "Invalid email or password, or you do not have admin access.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Event Registration System</title>
  <link rel="stylesheet" href="admin_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="admin_login.php" method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>

    <a href="../home.php" class="back-link">&larr; Back to Home</a>

    <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
      <a href="forgot_password.php" style="font-size: 14px; color: #666;">Forgot Password?</a>
      <br>
      <a href="admin_register.php" style="font-size: 14px; color: #666;">Register New Admin</a>
    </div>
  </div>

</body>

</html>