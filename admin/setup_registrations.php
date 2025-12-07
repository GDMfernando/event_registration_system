<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create Table
$sql = "CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'registrations' created successfully.\n";
} else {
    die("Error creating table: " . $conn->error);
}

// Seed Data (Check if empty first)
$check = $conn->query("SELECT COUNT(*) as count FROM registrations");
$row = $check->fetch_assoc();

if ($row['count'] == 0) {
    // Get a user and an event to link
    $user_res = $conn->query("SELECT user_id FROM users LIMIT 1");
    $event_res = $conn->query("SELECT event_id FROM events LIMIT 1");

    if ($user_res->num_rows > 0 && $event_res->num_rows > 0) {
        $user_row = $user_res->fetch_assoc();
        $user_id = $user_row['user_id'];
        
        $event_row = $event_res->fetch_assoc();
        $event_id = $event_row['event_id'];
        
        $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id, status) VALUES (?, ?, 'confirmed')");
        if (!$stmt) {
             die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $user_id, $event_id);
        $stmt->execute();
        $stmt->execute(); // Add a couple
        echo "Dummy data inserted.\n";
    } else {
        echo "Could not seed data: Need at least one user and one event.\n";
    }
} else {
    echo "Table already has data.\n";
}

$conn->close();
?>
