    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Event</h2>
            <form action="../manage_events/manage_events.php" method="POST" enctype="multipart/form-data">

                <label for="title">Event Title</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>

                <label for="category_id">Select Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <h3>Venue Details</h3>

                <label>
                    <input type="radio" name="venue_select_type" value="existing" checked onclick="toggleVenueFields('existing')"> Select Existing Venue
                </label>
                <label>
                    <input type="radio" name="venue_select_type" value="new" onclick="toggleVenueFields('new')"> Add New Venue
                </label>

                <div id="existingVenueFields">
                    <label for="venue_id_existing">Select Venue</label>
                    <select id="venue_id_existing" name="venue_id_existing" required>
                        <option value="">-- Select a Venue --</option>
                        <?php foreach ($venues as $venue): ?>
                            <option value="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                                <?php echo htmlspecialchars($venue['venue_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="newVenueFields" style="display:none;">
                    <label for="venue_name_new">New Venue Name</label>
                    <input type="text" id="venue_name_new" name="venue_name_new">

                    <label for="address_new">Address</label>
                    <input type="text" id="address_new" name="address_new">

                    <label for="capacity_new">Capacity</label>
                    <input type="number" id="capacity_new" name="capacity_new" min="1">
                </div>

                <label for="event_date">Date</label>
                <input type="date" id="event_date" name="event_date" required>

                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" required>

                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time">

                <h3>Ticket Pricing</h3>
                <div class="ticket_price" >
                    <div>
                        <label for="price_vip">VIP Price</label>
                        <input type="number" step="0.01" id="price_vip" name="price_vip" placeholder="0.00" required>
                    </div>
                    <div>
                        <label for="price_regular">Regular Price</label>
                        <input type="number" step="0.01" id="price_regular" name="price_regular" placeholder="0.00" required>
                    </div>
                    <div>
                        <label for="price_balcony">Balcony Price</label>
                        <input type="number" step="0.01" id="price_balcony" name="price_balcony" placeholder="0.00" required>
                    </div>
                </div>
                <div>
                    <label for="event_image">Event Image (Optional)</label>
                    <input type="file" id="event_image" name="event_image" accept="image/*">
                </div>
                <button type="submit" name="add_event">Save Event</button>
            </form>
        </div>
    </div>