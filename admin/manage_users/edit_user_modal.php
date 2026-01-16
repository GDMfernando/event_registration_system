<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Administrator</h2>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        
        <form id="editUserForm" action="manage_users.php" method="POST">
            <div class="modal-body-scroll">
                <input type="hidden" id="edit_user_id" name="user_id">

                <label>Full Name</label>
                <input type="text" id="edit_full_name" name="full_name" required>

                <label>Email</label>
                <input type="email" id="edit_email" name="email" required>

                <label>Phone</label>
                <input type="text" id="edit_phone" name="phone">

                <label>Username</label>
                <input type="text" id="edit_username" name="username" required>

                <hr>

                <p class="password-warning">Leave blank to keep current password.</p>
                <label>New Password</label>
                <input type="password" id="edit_password" name="new_password">

                <label>Status</label>
                <select id="edit_status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" name="update_user" class="btn-update-full">
                Update Administrator
            </button>
        </form>
    </div>
</div>