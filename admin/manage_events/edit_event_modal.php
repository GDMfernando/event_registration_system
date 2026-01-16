<div id="editEventModal" class="modal">
    <div class="modal-content">
         <div class="modal-header">
                 <h2>Edit Event</h2>
       <span class="close" onclick="closeEditModal()">&times;</span>
   
        </div>
        <form id="editEventForm" action="manage_events.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body-scroll">
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

            <div>
                <h3>Venue Details</h3>
                <label for="edit_venue_id">Venue</label>
                <select id="edit_venue_id" name="venue_id" required>
                    <option value="">-- Select a Venue --</option>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                            <?php echo htmlspecialchars($venue['venue_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label for="edit_event_date">Date</label>
            <input type="date" id="edit_event_date" name="event_date" required>

            <label for="edit_start_time">Start Time</label>
            <input type="time" id="edit_start_time" name="start_time" required>

            <label for="edit_end_time">End Time</label>
            <input type="time" id="edit_end_time" name="end_time">

            <h3>Ticket Pricing</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                <div>
                    <label for="edit_price_vip">VIP Price</label>
                    <input type="number" step="0.01" id="edit_price_vip" name="price_vip" placeholder="0.00" required>
                </div>
                <div>
                    <label for="edit_price_regular">Regular Price</label>
                    <input type="number" step="0.01" id="edit_price_regular" name="price_regular" placeholder="0.00" required>
                </div>
                <div>
                    <label for="edit_price_balcony">Balcony Price</label>
                    <input type="number" step="0.01" id="edit_price_balcony" name="price_balcony" placeholder="0.00" required>
                </div>
            </div>

            <div>
                            <label for="edit_capacity">Total Capacity (Total Seats)</label>
            <input type="number" id="edit_capacity" name="capacity" min="1" required>
            </div>


            <label for="edit_status">Status</label>
            <select id="edit_status" name="status">
                <option value="Active">Active</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Postponed">Postponed</option>
                <option value="Completed">Completed</option>
            </select>
            <div>

                <label for="edit_event_image">Update Event Image (Optional)</label>
                <input type="file" id="edit_event_image" name="edit_event_image" accept="image/*">
                <small></small>

                <div id="current_image_preview"></div>
            </div>
            <button type="submit" name="update_event" class="btn-update-full">Update Event</button>
            </div>
        </form>
    </div>
</div>