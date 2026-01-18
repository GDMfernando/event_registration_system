<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_registration_and_ticketing";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red; text-align:center;'>Connection failed: " . $conn->connect_error . "</p>");
}

// SQL query
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

// Check if query succeeded
if (!$result) {
    die("<p style='color:red; text-align:center;'>Error in SQL query: " . $conn->error . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="css/event.css">
</head>

<body>



    <div class="container">
        <h1>Event Details</h1>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Title</th><th>Description</th><th>Category</th><th>Venue</th><th>Start Time</th><th>End Time</th><th>Capacity</th><th>Available seats</th><th>Ticket Price</th>
        <th>Status</th><th>created</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['event_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['category_id'] . "</td>";
                echo "<td>" . $row['venue_id'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                echo "<td>" . $row['available_seats'] . "</td>";
                echo "<td>Rs. " . $row['ticket_price'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['created'] . "</td>";


                echo "<td>
        <img src='uploads/" . $row['image'] . "' width='200'>
      </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<div class='no-data'>No data found.</div>";
        }

        $conn->close();
        ?>
    </div>

    <a href="book.php?id=<?php echo $row['id']; ?>" class="book-now-fixed">
        Book Now
    </a>


</body>

</html>