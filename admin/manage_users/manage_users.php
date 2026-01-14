<?php
include('../../db_connect.php');

$filter_name  = isset($_GET['full_name']) ? $_GET['full_name'] : '';
$filter_email = isset($_GET['email'])     ? $_GET['email']     : '';
$filter_date  = isset($_GET['reg_date'])  ? $_GET['reg_date']  : '';


function get_filtered_users($conn, $name, $email, $date) {
    $sql = "SELECT user_id, username, full_name, email, phone, role, status, created_at 
            FROM user 
            WHERE 1=1";

    if (!empty($name)) {
        $safe_name = mysqli_real_escape_string($conn, $name);
        $sql .= " AND full_name LIKE '%$safe_name%'";
    }

    if (!empty($email)) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $sql .= " AND email LIKE '%$safe_email%'";
    }

    if (!empty($date)) {
        $safe_date = mysqli_real_escape_string($conn, $date);
        $sql .= " AND DATE(created_at) = '$safe_date'";
    }

    $sql .= " ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if (isset($_GET['fetch_user_id'])) {
    $id = (int)$_GET['fetch_user_id'];
    $sql = "SELECT user_id, username, full_name, email, phone, status FROM user WHERE user_id = $id";
    $result = mysqli_query($conn, $sql);
    
    if ($user = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit; // Stop execution so no HTML is sent with the JSON
}

// --- A. HELPER FUNCTIONS ---

function get_all_users($conn) {
    $sql = "SELECT user_id, username, full_name, email, phone, role, status, created_at 
            FROM user 
            ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// --- B. HANDLE ACTIONS (POST/GET) ---


if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    
    // Using Prepared Statement for Security
    $stmt = mysqli_prepare($conn, "DELETE FROM user WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_users.php?msg=deleted");
        exit();
    }
}

if (isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE user SET full_name='$full_name', email='$email', phone='$phone', username='$username', status='$status' WHERE user_id=$user_id";
    
    if (mysqli_query($conn, $query)) {
        // If a new password was provided, hash and update it
        if (!empty($_POST['new_password'])) {
            $hashed_pw = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE user SET password_hash = '$hashed_pw' WHERE user_id = $user_id");
        }
        header("Location: manage_users.php?msg=updated");
        exit();
    }
}

// Handle Status Toggle (Quick Active/Inactive)
if (isset($_GET['toggle_status']) && isset($_GET['current'])) {
    $id = (int)$_GET['toggle_status'];
    $new_status = ($_GET['current'] === 'active') ? 'inactive' : 'active';
    $stmt = mysqli_prepare($conn, "UPDATE user SET status = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php");
    exit();
}

// --- C. FETCH AND SEPARATE DATA ---
$all_users = get_filtered_users($conn, $filter_name, $filter_email, $filter_date);
$admins = array_filter($all_users, function($u) { return $u['role'] === 'admin'; });
$customers = array_filter($all_users, function($u) { return $u['role'] === 'user'; });

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../manage_events/manage_events.css"> 
    <link rel="stylesheet" href="../includes/navbar.css">

    
    <style>
        /* Tab Navigation Styling */
        .tab-container {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .tabs {
            display: flex;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 20px;
            gap: 5px;
        }
        .tab-btn {
            padding: 12px 25px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            color: #007bff;
            border-bottom: 3px solid #007bff;
        }
        .tab-btn:hover {
            background: #f8f9fa;
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .tab-content.active {
            display: block;
        }

        /* Status & Role Badges */
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
        .badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .badge-admin { background: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar.php'); ?>

    <div class="container">
     
       <div class="header">
           <h1>👥 User Management</h1>
            <a href="../dashboard/dashboard.php" class="btn-back">Back to Dashboard</a>
        </div>

        <div class="filter-container">
    <form method="GET" action="manage_users.php" class="filter-form">
        
        <div class="filter-group">
            <label for="name_filter">Name</label>
            <input type="text" name="full_name" id="name_filter" 
                   placeholder="Search name..." 
                   value="<?php echo htmlspecialchars($filter_name); ?>">
        </div>

        <div class="filter-group">
            <label for="email_filter">Email</label>
            <input type="text" name="email" id="email_filter" 
                   placeholder="Search email..." 
                   value="<?php echo htmlspecialchars($filter_email); ?>">
        </div>

        <div class="filter-group">
            <label for="date_filter">Reg. Date</label>
            <input type="date" name="reg_date" id="date_filter" 
                   value="<?php echo htmlspecialchars($filter_date); ?>">
        </div>

        <div class="filter-buttons">
            <button type="submit" class="btn-filter">Filter Users</button>
            <a href="manage_users.php" class="btn-clear">Reset</a>
        </div>
    </form>
</div>

        <div class="tab-container">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab(event, 'customers-section')">
                    Customers (<?php echo count($customers); ?>)
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'admins-section')">
                    Administrators (<?php echo count($admins); ?>)
                </button>
            </div>

            <div id="customers-section" class="tab-content active">
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Contact Info</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($customers)): ?>
                            <tr><td colspan="6">No registered customers found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($customers as $user): ?>
                            <tr>
                                <td>#<?php echo $user['user_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($user['email']); ?><br>
                                    <small><?php echo htmlspecialchars($user['phone'] ?? 'No Phone'); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="status-<?php echo $user['status']; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </td>
                                <td class="action-links">
                                    <a href="?toggle_status=<?php echo $user['user_id']; ?>&current=<?php echo $user['status']; ?>">Change Status</a>
                                    <a href="?delete_id=<?php echo $user['user_id']; ?>" 
                                       class="delete" 
                                       onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="admins-section" class="tab-content">
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff Member</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $user): ?>
                        <tr>
                            <td>#<?php echo $user['user_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                <span class="badge badge-admin">Staff</span>
                            </td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="status-<?php echo $user['status']; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </td>
                            <td class="action-links">
                                <a href="?toggle_status=<?php echo $user['user_id']; ?>&current=<?php echo $user['status']; ?>">Toggle Access</a>
                                <a href="javascript:void(0);" onclick="editUser(<?php echo $user['user_id']; ?>)" class="edit">
    Edit
</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include('edit_user_modal.php'); ?>

    <script>
        /**
         * Simple Tab Switching Logic
         */
        function switchTab(evt, tabId) {
            // Hide all contents
            const contents = document.getElementsByClassName("tab-content");
            for (let i = 0; i < contents.length; i++) {
                contents[i].classList.remove("active");
            }

            // Deactivate all buttons
            const buttons = document.getElementsByClassName("tab-btn");
            for (let i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove("active");
            }

            // Show current tab
            document.getElementById(tabId).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        /**
         * Placeholder for Edit Logic (to be linked with edit_user_modal.php)
         */
        function editUser(userId) {
            console.log("Edit User ID:", userId);
            // You can implement AJAX here to fetch user data and open your edit modal
        }
    </script>
    <script src="manage_users.js"></script>
</body>
</html>