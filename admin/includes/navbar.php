<?php 
// includes/navbar.php
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<nav class="navbar">
    <div class="logo">EventReg.</div>
    <ul class="nav-links">
        
        <li><a href="dashboard.php" <?php if($current_page == 'dashboard.php') echo 'class="active"'; ?>>Home</a></li>
        
        <li><a 
            href="../manage_events/manage_events.php" 
            <?php if($current_page == 'manage_events.php') echo 'class="active"'; ?>>
            Event Management
        </a></li>
        
        <li><a href="user_management.php">User Management</a></li>
        <li><a href="bookings.php">Bookings</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</nav>