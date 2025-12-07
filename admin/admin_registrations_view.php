<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Registrations</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .filters { display: flex; gap: 10px; margin-bottom: 20px; background: #eee; padding: 15px; border-radius: 5px; }
        .filters select, .filters input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .filters button { padding: 8px 15px; background: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007BFF; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .btn-cancel { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .btn-cancel:hover { background: #c82333; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .nav-link { display: inline-block; margin-bottom: 15px; color: #007BFF; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="nav-link">&larr; Back to Dashboard</a>
    <h2>Event Registrations</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form class="filters" method="GET">
        <select name="event_id">
            <option value="">All Events</option>
            <?php while($evt = $events_result->fetch_assoc()): ?>
                <option value="<?php echo $evt['event_id']; ?>" <?php if($filter_event == $evt['event_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($evt['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="status">
            <option value="">All Statuses</option>
            <option value="confirmed" <?php if($filter_status == 'confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="cancelled" <?php if($filter_status == 'cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <input type="date" name="date" value="<?php echo $filter_date; ?>">

        <button type="submit">Filter</button>
        <a href="admin_registrations.php" style="padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Event</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['registration_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_title']); ?></td>
                        <td><?php echo $row['registration_date']; ?></td>
                        <td class="status-<?php echo strtolower($row['status']); ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'confirmed'): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this registration?');">
                                    <input type="hidden" name="cancel_registration_id" value="<?php echo $row['registration_id']; ?>">
                                    <button type="submit" class="btn-cancel">Cancel</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No registrations found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
