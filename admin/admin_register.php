<?php
session_start();
include "includes/admin_functions.php";
include "../db_connect.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);

  if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
    $error = "All fields are required.";
  } elseif ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    // Validate Phone logic
    $phone_res = validate_admin_phone($phone);
    if ($phone_res !== true) {
      $error = $phone_res;
    } else {
      // Validate Email logic
      $email_res = validate_admin_email($email);
      if ($email_res !== true) {
        $error = $email_res;
      }
    }

    if (empty($error)) {
      // Prevent SQL injection
      // Generate username from email (part before @)
      $email_parts = explode('@', $email);
      $username = $email_parts[0] . rand(1000, 9999);
      // Ensure username is unique by appending a random number if needed (simple version for now)
      $username = mysqli_real_escape_string($conn, $username);
      $full_name = mysqli_real_escape_string($conn, $full_name);
      $email = mysqli_real_escape_string($conn, $email);
      $phone = mysqli_real_escape_string($conn, $phone);
      $password = mysqli_real_escape_string($conn, $password);

      // Check if username or email already exists in user table
      $check_sql = "SELECT * FROM user WHERE email = '$email'";
      $check_result = mysqli_query($conn, $check_sql);

      if (mysqli_num_rows($check_result) > 0) {
        $error = "Email already exists.";
      } else {
        // Insert new admin into user table with role='admin'
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (username, full_name, email, phone, password_hash, role) VALUES ('$username', '$full_name', '$email', '$phone', '$hashed_password', 'admin')";

        if (mysqli_query($conn, $sql)) {
          $success = "Registration successful! You can now <a href='admin_login.php'>login</a>.";
        } else {
          $error = "Error: " . mysqli_error($conn);
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration - Event Registration System</title>
  <link rel="stylesheet" href="admin_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>Admin Register</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success-msg"
        style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px;">
        <?php echo $success; ?>
      </div>
      <script>
        alert("Your account is created successfully");
      </script>
    <?php endif; ?>

    <form action="admin_register.php" method="POST">
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required
          value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
          value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" required
          value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn-login">Register</button>
    </form>

    <a href="admin_login.php" class="back-link">Already have an account? Login here</a>
    <a href="../home.php" class="back-link">&larr; Back to Home</a>
  </div>

</body>

</html>
