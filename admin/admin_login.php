<?php
session_start();
include "../db_connect.php";

$error = "";
$success = "";

if (isset($_GET['msg'])) {
  $success = htmlspecialchars($_GET['msg']);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($email) || empty($password)) {
    $error = "Please enter both email and password.";
  } else {
    // Validate Email logic
    $email_valid = true;
    if (substr_count($email, '@') !== 1) {
       $email_valid = false;
       $error = "Invalid email format.";
    } else {
       list($local, $domain) = explode('@', $email);
       if (strlen($local) < 1 || strlen($local) > 64) { 
           $email_valid = false; $error = "Local part must be 1-64 characters.";
       } elseif ($local[0] === '.' || substr($local, -1) === '.') { 
           $email_valid = false; $error = "Local part cannot start or end with a dot.";
       } elseif (!preg_match('/^[A-Za-z0-9._%+-]+$/', $local)) { 
           $email_valid = false; $error = "Local part contains invalid characters.";
       } elseif (strpos($domain, '.') === false) { 
           $email_valid = false; $error = "Domain part must contain at least one dot.";
       } else {
           foreach (explode('.', $domain) as $label) {
               if (strlen($label) < 1 || strlen($label) > 63) { 
                   $email_valid = false; $error = "Domain label must be 1-63 characters."; break; 
               }
               if ($label[0] === '-' || substr($label, -1) === '-') { 
                   $email_valid = false; $error = "Domain label cannot start or end with a hyphen."; break; 
               }
               if (!preg_match('/^[A-Za-z0-9-]+$/', $label)) { 
                   $email_valid = false; $error = "Domain label contains invalid characters."; break; 
               }
           }
       }
    }

    if (empty($error)) {
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
        $_SESSION['admin_fullname'] = $row['full_name'];
        $_SESSION['admin_email'] = $row['email'];
        header("Location: dashboard/dashboard.php"); // Redirect to dashboard
        exit();
      } else {
        $error = "Invalid email or password.";
      }
    } else {
      $error = "Invalid email or password, or you do not have admin access.";
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
  <title>Admin Login - Event Registration System</title>
  <link rel="stylesheet" href="admin_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($success)): ?>
      <div class="success-msg" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

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
  </div>

</body>

</html>
