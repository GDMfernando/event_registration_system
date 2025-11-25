document.addEventListener('DOMContentLoaded', function() {
    // Event Modal Elements
    var eventModal = document.getElementById("addEventModal");
    var eventBtn = document.getElementById("addNewEventBtn");
    
    // Select the close button for the event modal (assuming it's the first one in the DOM)
    // FIX: Using querySelector to target the first close button in the specific modal to avoid conflicts
    var eventClose = eventModal.querySelector(".close"); 

    // Category Modal Elements
    var categoryModal = document.getElementById("addCategoryModal");
    var categoryBtn = document.getElementById("addNewCategoryBtn");
    
    // Select the close button for the category modal (assuming it's the close button within that modal)
    var categoryClose = categoryModal.querySelector(".close"); 

    // ----------------------------------------------------
    // Event Modal Handlers
    if (eventBtn) {
        eventBtn.onclick = function() {
            eventModal.style.display = "block";
        }
    }

    if (eventClose) {
        eventClose.onclick = function() {
            eventModal.style.display = "none";
        }
    }

    // ----------------------------------------------------
    // Category Modal Handlers
    if (categoryBtn) {
        categoryBtn.onclick = function() {
            categoryModal.style.display = "block";
        }
    }

    if (categoryClose) {
        categoryClose.onclick = function() {
            categoryModal.style.display = "none";
        }
    }
    // ----------------------------------------------------

    // Close Modals on outside click
    window.onclick = function(event) {
        if (event.target == eventModal) {
            eventModal.style.display = "none";
        }
        if (event.target == categoryModal) {
            categoryModal.style.display = "none";
        }
    }
});