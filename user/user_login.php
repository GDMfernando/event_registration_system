<?php
session_start();
include "../db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = trim($_POST['full_name']);
  $password = trim($_POST['password']);

  if (empty($full_name) || empty($password)) {
    $error = "Please enter both full name and password.";
  } else {
    // Prevent SQL injection
    $full_name = mysqli_real_escape_string($conn, $full_name);

    // Check if user exists in user table
    $sql = "SELECT * FROM user WHERE full_name = '$full_name' AND role = 'user'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);
      if (password_verify($password, $row['password_hash'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_username'] = $row['username'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_full_name'] = $row['full_name'];
        header("Location: ../home.php"); // Redirect to home.php
        exit();
      } else {
        $error = "Invalid full name or password.";
      }
    } else {
      $error = "Invalid full name or password, or you do not have user access.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login - Event Registration System</title>
  <link rel="stylesheet" href="user_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>User Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="user_login.php" method="POST">
      <div class="form-group">
        <label for="full_name">User Name</label>
        <input type="text" id="full_name" name="full_name" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>

    <a href="../home.php" class="back-link">&larr; Back to Home</a>

    <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
      <a href="user_forgot_password.php" style="font-size: 14px; color: #c725a1;">Forgot Password?</a>
      <br>
      Don't have an account? <a href="user_register.php" style="font-size: 14px; color: #c725a1;">Register New User</a>
    </div>
  </div>

</body>

</html>
