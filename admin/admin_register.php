<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

// 1. Establish the connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Stop execution immediately on connection failure
    die("Database connection failed: " . $conn->connect_error);
}

// 2. Process form submission only if POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize/Clean the inputs before using them
    $full_name = trim($_POST['full_name']); // Use trim to remove whitespace
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password']; // Raw password for hashing
    
    // Input Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
    // Use JavaScript alert for error as well for consistency
    echo "<script>alert('Please fill all required fields!'); window.history.back();</script>";
    exit; // Stop execution after the script is echoed
}

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    
    // 3. Check for Duplicate Email
    $check_sql = "SELECT user_id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);

    // Check if prepare failed
    if ($check_stmt === false) {
         die('Email Check Prepare failed: ' . $conn->error);
    }

    // Bind the email parameter
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result(); // Store the result set for row counting

   if ($check_stmt->num_rows > 0) {
       // If the count is > 0, the email already exists
       echo "<script>alert('Registration failed: An account with this email already exists!'); window.history.back();</script>";
       $check_stmt->close();
       // OPTIONAL: $conn->close(); 
       exit; // Stop the script execution
    }

     // Close the check statement before proceeding to the INSERT
    $check_stmt->close();



    // 4. Prepare the statement
    $insert_sql = "INSERT INTO users (full_name, email, phone, password_hash, role) 
                        VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($insert_sql);
 
    // CRITICAL FIX: Add error check for prepare (returns false on failure)
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    
    // 5. Bind the parameters (s = string, for all four placeholders)
    $stmt->bind_param("ssss", $full_name, $email, $phone, $password_hash);
    
    // 6. Execute the statement and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='admin_login.html';</script>";
        exit;
    } else {
        // Failure message, including the error details
        echo "Error executing query: " . $stmt->error;
        exit;
    }
    
    //  Close the statement
    $stmt->close();
} 
$conn->close(); 
?>


   