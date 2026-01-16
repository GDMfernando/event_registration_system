<?php
session_start();
include "includes/user_functions.php";
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
    // Validate Phone
     $phone_res = validate_user_phone($phone);
    if ($phone_res !== true) {
      $error = $phone_res;
    } else {
      // Validate Email
      $email_res = validate_user_email($email);
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
        // Insert new user into user table with role='user'
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (full_name, email, phone, password_hash, role) VALUES ('$full_name', '$email', '$phone', '$hashed_password', 'user')";

        if (mysqli_query($conn, $sql)) {
          $success = "Registration successful! You can now <a href='user_login.php'>login</a>.";
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
  <title>User Registration - Event Registration System</title>
  <link rel="stylesheet" href="user_style.css">
</head>

<body class="login-body">

  <div class="login-container">
    <h2>User Register</h2>

    <?php if (!empty($error)): ?>
      <div class="error-msg">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success-msg"
        style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px;">
        <?php echo $success; ?>
      </div>
      <script>
        alert("Your account is created successfully");
        window.location.href = 'user_login.php';
        function togglePassword(inputId, icon) {
          const input = document.getElementById(inputId);
          const svg = icon.querySelector('svg');
          if (input.type === 'password') {
            input.type = 'text';
            svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
          } else {
            input.type = 'password';
            svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
          }
        }
      </script>
    <?php endif; ?>

    <form action="user_register.php" method="POST">
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required
          value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
          value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        <div id="email-error" style="color: red; font-size: 12px; margin-top: 5px;"></div>
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" required
          value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
        <div id="phone-error" style="color: red; font-size: 12px; margin-top: 5px;"></div>
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

    <a href="user_login.php" class="back-link">Already have an account? Login here</a>
    <a href="../home.php" class="back-link">&larr; Back to Home</a>
  </div>

  </div>

  <script>
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const registerForm = document.querySelector('form');

    // Regex for strict email validation
    // 1. Local Part: 1-64 chars, no start/end dot, allowed: A-Z a-z 0-9 . _ % + -
    // 2. Domain Part: labels 1-63 chars, no start/end hyphen, dot required
    const emailPattern = /^(?!\.)[A-Za-z0-9._%+-]{1,64}(?<!\.)@([A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z0-9]([A-Za-z0-9-]{0,61}[A-Za-z0-9])?$/;

    // Phone regex: Exactly 10 digits
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone-error');
    const phonePattern = /^\d{10}$/;

    function validateEmail() {
      if (!emailPattern.test(emailInput.value)) {
        emailError.textContent = "Invalid email. Example: user@domain.com (No leading/trailing dots, proper domain required)";
        return false;
      } else {
        emailError.textContent = "";
        return true;
      }
    }

    function validatePhone() {
      if (!phonePattern.test(phoneInput.value)) {
        phoneError.textContent = "Invalid phone number. Must be exactly 10 digits.";
        return false;
      } else {
        phoneError.textContent = "";
        return true;
      }
    }

    emailInput.addEventListener('input', validateEmail);
    phoneInput.addEventListener('input', validatePhone);

    registerForm.addEventListener('submit', function (event) {
      let valid = true;
      if (!validateEmail()) valid = false;
      if (!validatePhone()) valid = false;

      if (!valid) {
        event.preventDefault();
        // Focus on the first invalid field
        if (!validateEmail()) emailInput.focus();
        else if (!validatePhone()) phoneInput.focus();
      }
    });
    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
      const svg = icon.querySelector('svg');
      if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      } else {
        input.type = 'password';
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
    }
  </script>
</body>

</html>
