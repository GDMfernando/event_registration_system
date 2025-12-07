<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = array('events', 'registrations', 'users');

foreach ($tables as $table) {
    echo "\nTable: $table\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " | " . $row['Type'] . "\n";
        }
    } else {
        echo "Table '$table' does not exist or error: " . $conn->error . "\n";
    }
}

$conn->close();
?>
