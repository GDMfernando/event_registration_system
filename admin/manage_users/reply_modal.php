<div id="replyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReplyModal()">&times;</span>
        <h2>Reply to Customer</h2>

        <form id="replyForm" action="manage_users.php" method="POST">
            <div class="modal-body-scroll">

                <label>From (Admin)</label>
                <input type="email" name="sender_email" value="admin@eventsystem.com" readonly
                    style="background-color: #f0f0f0;">

                <label>To (Customer)</label>
                <input type="email" id="reply_to_email" name="customer_email" readonly
                    style="background-color: #f0f0f0;">

                <label>Subject</label>
                <input type="text" id="reply_subject" name="reply_subject" required>

                <label>Message</label>
                <textarea name="reply_message" rows="6" required placeholder="Type your reply here..."></textarea>
            </div>

            <button type="submit" name="send_reply" class="" style="width: 100%; margin-top: 15px;">
                Send Reply
            </button>
        </form>
    </div>
</div>