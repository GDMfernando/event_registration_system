<div id="editEventModal" class="modal">
    <div class="modal-content">
        <span class="close edit-close">&times;</span>
        <h2>Edit Event</h2>
        <form id="editEventForm" action="manage_events.php" method="POST">
            <input type="hidden" id="edit_event_id" name="event_id" required>

            <label for="edit_title">Event Title</label>
            <input type="text" id="edit_title" name="title" required>

            <label for="edit_description">Description</label>
            <textarea id="edit_description" name="description"></textarea>

            <label for="edit_category_id">Select Category</label>
            <select id="edit_category_id" name="category_id" required>
                <option value="">-- Select a Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_venue_id">Venue</label>
            <select id="edit_venue_id" name="venue_id" required>
                <option value="">-- Select a Venue --</option>
                <?php foreach ($venues as $venue): ?>
                    <option value="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                        <?php echo htmlspecialchars($venue['venue_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_event_date">Date</label>
            <input type="date" id="edit_event_date" name="event_date" required>

            <label for="edit_start_time">Start Time</label>
            <input type="time" id="edit_start_time" name="start_time" required>

            <label for="edit_end_time">End Time</label>
            <input type="time" id="edit_end_time" name="end_time">

            <label for="edit_ticket_price">Ticket Price</label>
            <input type="number" step="0.01" id="edit_ticket_price" name="ticket_price" required>
            
            <label for="edit_status">Status</label>
            <select id="edit_status" name="status">
                <option value="Active">Active</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Postponed">Postponed</option>
                <option value="Completed">Completed</option>
            </select>


            <button type="submit" name="update_event">Update Event</button>
        </form>
    </div>
</div>