<div id="addEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Event</h2>
            <span class="close" onclick="closeAddModal()">&times;</span>
        </div>

        <form action="../manage_events/manage_events.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body-scroll">
                
                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" id="title" name="title" required placeholder="Enter event title">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" placeholder="Enter event details..."></textarea>
                </div>

               
                    <div class="form-group">
                        <label for="category_id">Select Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">-- Select a Category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" required>
                    </div>

                      <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" id="end_time" name="end_time">
                    </div>
              
                </div>

                <hr>

                <div class="form-group">
                    <label style="margin-bottom: 10px; color: #19273a;">Venue Details</label>
                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                        <label style="font-weight: normal; cursor: pointer;">
                            <input type="radio" name="venue_select_type" value="existing" checked onclick="toggleVenueFields('existing')"> Select Existing
                        </label>
                        <label style="font-weight: normal; cursor: pointer;">
                            <input type="radio" name="venue_select_type" value="new" onclick="toggleVenueFields('new')"> Add New Venue
                        </label>
                    </div>

                    <div id="existingVenueFields">
                        <select id="venue_id_existing" name="venue_id_existing" required>
                            <option value="">-- Select a Venue --</option>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?php echo htmlspecialchars($venue['venue_id']); ?>">
                                    <?php echo htmlspecialchars($venue['venue_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="newVenueFields" style="display:none;" class="form-row">
                        <div class="form-group">
                            <input type="text" id="venue_name_new" name="venue_name_new" placeholder="New Venue Name">
                        </div>
                        <div class="form-group">
                            <input type="text" id="address_new" name="address_new" placeholder="Address">
                        </div>
                    </div>
                </div>

                <hr>

              

                <label>Ticket Pricing</label>
                <div class="ticket_price">
                    <div class="form-group">
                        <label for="price_vip" style="font-size: 11px;">VIP</label>
                        <input type="number" step="0.01" id="price_vip" name="price_vip" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="price_regular" style="font-size: 11px;">Regular</label>
                        <input type="number" step="0.01" id="price_regular" name="price_regular" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="price_balcony" style="font-size: 11px;">Balcony</label>
                        <input type="number" step="0.01" id="price_balcony" name="price_balcony" placeholder="0.00" required>
                    </div>
                </div>

         
                    <div class="form-group">
                        <label for="capacity">Total Capacity</label>
                        <input type="number" id="capacity" name="capacity" min="1" required placeholder="Total seats">
                    </div>
                    <div class="form-group">
                        <label for="event_image">Event Image (Optional)</label>
                        <input type="file" id="event_image" name="event_image" accept="image/*" style="padding: 7px;">
                    </div>
        
            </div>

            <div class="modal-footer">
                <button type="submit" name="add_event" class="btn-update-full">Save Event</button>
            </div>
        </form>
    </div>
</div>