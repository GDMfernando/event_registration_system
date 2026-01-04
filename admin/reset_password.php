<?php
session_start();
// Include the database connection file
include "../db_connect.php"; 

$error = "";
$success = "";
$token = "";

// --- 1. HANDLE INITIAL TOKEN VERIFICATION (GET REQUEST) ---
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = hash('sha256', $token);

    // Verify token in user table
    $sql = "SELECT * FROM user WHERE reset_token_hash = '$token_hash' AND reset_token_expires_at > NOW()";
    $result = mysqli_query($conn, $sql);
    
    // Check for query failure before using mysqli_num_rows
    if ($result === false) {
        $error = "Database Error during initial token verification: " . mysqli_error($conn);
        $token = ""; // Prevent form from showing
    } elseif (mysqli_num_rows($result) == 0) {
        $error = "Invalid or expired token. Please restart the password reset process.";
        $token = ""; // Clear token so the form doesn't show
    }
} else {
    // Only set an error if it's not a POST request that has already submitted a token
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $error = "No token provided. Please use the complete reset link.";
    }
}


// --- 2. HANDLE PASSWORD UPDATE SUBMISSION (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch token from the hidden field in the submitted form
    if (isset($_POST['token'])) {
        $token = $_POST['token'];
        $token_hash = hash('sha256', $token);
    } else {
        $error = "Form submission error: Missing token.";
    }
    
    // Sanitize and escape input
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Re-verify the token before processing the password change
    // *** FIX APPLIED HERE: Changed 'id' to 'user_id' ***
    $sql_verify = "SELECT user_id FROM user WHERE reset_token_hash = '$token_hash' AND reset_token_expires_at > NOW()";
    $result_verify = mysqli_query($conn, $sql_verify);

    // CRITICAL ERROR CHECK FOR QUERY FAILURE
    if ($result_verify === false) {
        $error = "Database Error during token verification: " . mysqli_error($conn);
    } 
    // Continue with the original logic only if the query succeeded
    elseif (mysqli_num_rows($result_verify) == 0) {
        $error = "Invalid or expired token. Please restart the password reset process.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // --- CORE UPDATE LOGIC ---
        // 1. Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 2. Prepare the UPDATE query
        // This query updates the password and CLEARS the reset tokens.
        $sql = "UPDATE user SET password_hash = '$hashed_password', reset_token_hash = NULL, reset_token_expires_at = NULL WHERE reset_token_hash = '$token_hash'";
        
        // 3. Execute the query and check for success/failure
        if (mysqli_query($conn, $sql)) {
            // Check if any row was actually updated
            if (mysqli_affected_rows($conn) > 0) {
                // SUCCESS: Redirect and exit
                header("Location: admin_login.php?msg=Password reset successfully. Please login with your new password.");
                exit();
            } else {
                // FAILURE: Query executed, but no row matched the WHERE clause
                $error = "Error: Token not found or password already updated (No rows affected). Please restart the password reset process.";
            }
        } else {
            // This will catch any errors in the UPDATE query
            $error = "Database Error updating password: " . mysqli_error($conn);
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
    <link rel="stylesheet" href="admin_style.css"> 
</head>

<body class="login-body">

    <div class="login-container">
        <h2>Reset Password</h2>

        <?php if (!empty($error)): ?>
            <div class="error-msg"
                style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-msg"
                style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: left; font-size: 14px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php 
        // Condition to show the form: 
        $show_form = !empty($token) && (empty($error) || $error === "Passwords do not match.");
        
        if ($show_form): 
        ?>
            <form action="reset_password.php" method="POST">
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

        <a href="admin_login.php" class="back-link">Back to Login</a>
    </div>

</body>

</html>
