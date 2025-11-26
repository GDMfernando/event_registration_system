
function toggleVenueFields(type) {
    const existingFields = document.getElementById('existingVenueFields');
    const newFields = document.getElementById('newVenueFields');
    const existingSelect = document.getElementById('venue_id_existing');
    const newNameInput = document.getElementById('venue_name_new');
    const newAddressInput = document.getElementById('address_new');

    if (type === 'existing') {
        existingFields.style.display = 'block';
        newFields.style.display = 'none';
        
        // Make existing venue select required, remove required from new venue fields
        existingSelect.setAttribute('required', 'required');
        newNameInput.removeAttribute('required');
        newAddressInput.removeAttribute('required');
    } else if (type === 'new') {
        existingFields.style.display = 'none';
        newFields.style.display = 'block';

        // Make new venue name and address required, remove required from existing select
        existingSelect.removeAttribute('required');
        newNameInput.setAttribute('required', 'required');
        newAddressInput.setAttribute('required', 'required');
    }
}

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

    toggleVenueFields('existing');
});