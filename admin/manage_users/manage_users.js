document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editUserModal');
    
    window.editUser = function(userId) {
        // Updated URL to point to manage_users.php instead of a separate helper
        fetch(`manage_users.php?fetch_user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_user_id').value = data.user.user_id;
                    document.getElementById('edit_full_name').value = data.user.full_name;
                    document.getElementById('edit_email').value = data.user.email;
                    document.getElementById('edit_phone').value = data.user.phone || '';
                    document.getElementById('edit_username').value = data.user.username;
                    document.getElementById('edit_status').value = data.user.status;

                    editModal.style.display = 'block';
                    document.body.style.overflow = 'hidden'; 
                } else {
                    alert("Could not retrieve user data.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    };

    window.closeEditModal = function() {
        editModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    window.onclick = function(event) {
        if (event.target == editModal) {
            closeEditModal();
        }
    };
});