<?php
include "../db_connect.php";

echo "<h1>Database Inspection</h1>";

// Check User Table Columns
$result = mysqli_query($conn, "SHOW COLUMNS FROM user");
if ($result) {
    echo "<h2>Columns in 'user' table:</h2>";
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error showing columns: " . mysqli_error($conn);
}
?>
