<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="card">
            <h3>Overview</h3>
            <p>This is the admin dashboard. You can manage events and users here.</p>
        </div>

        <!-- Example Table Structure for future use -->
        <!-- 
        <div class="card">
            <h3>Recent Events</h3>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Summer Festival</td>
                            <td>2025-07-15</td>
                            <td><a href="#" class="btn btn-primary">Edit</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        -->
    </div>
</body>

</html>