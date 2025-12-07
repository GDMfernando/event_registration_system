<?php
include "../db_connect.php";

echo "<h3>Checking 'user' table:</h3>";
$result = mysqli_query($conn, "DESCRIBE user");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
} else {
    echo "Table 'user' not found or error: " . mysqli_error($conn) . "<br>";
}

echo "<h3>Checking 'users' table:</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
} else {
    echo "Table 'users' not found or error: " . mysqli_error($conn) . "<br>";
}
?>