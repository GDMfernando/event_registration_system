function toggleVenueFields(type) {
  const existingFields = document.getElementById("existingVenueFields");
  const newFields = document.getElementById("newVenueFields");
  const existingSelect = document.getElementById("venue_id_existing");
  const newNameInput = document.getElementById("venue_name_new");
  const newAddressInput = document.getElementById("address_new");

  if (type === "existing") {
    existingFields.style.display = "block";
    newFields.style.display = "none";
    existingSelect.setAttribute("required", "required");
    newNameInput.removeAttribute("required");
    newAddressInput.removeAttribute("required");
  } else if (type === "new") {
    existingFields.style.display = "none";
    newFields.style.display = "block";
    existingSelect.removeAttribute("required");
    newNameInput.setAttribute("required", "required");
    newAddressInput.setAttribute("required", "required");
  }
}

function fetchEventDetails(eventId, modal) {

    fetch(`manage_events.php?action=fetch_event&id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.event;

                // Set ALL fields directly from the database response
                document.getElementById('edit_event_id').value = event.id; 
                document.getElementById('edit_title').value = event.title;
                document.getElementById('edit_description').value = event.description;
                document.getElementById('edit_category_id').value = event.category_id;
                document.getElementById('edit_venue_id').value = event.venue_id;
                document.getElementById('edit_event_date').value = event.event_date;
                document.getElementById('edit_start_time').value = event.start_time ? event.start_time.substring(0, 5) : ''; 
                document.getElementById('edit_end_time').value = event.end_time ? event.end_time.substring(0, 5) : '';
                document.getElementById('edit_price_vip').value = event.price_vip;
                document.getElementById('edit_price_regular').value = event.price_regular;
                document.getElementById('edit_price_balcony').value = event.price_balcony;
                document.getElementById('edit_status').value = event.status;
                document.getElementById('edit_capacity').value = event.capacity;
              
                const imagePreviewDiv = document.getElementById('current_image_preview');
                imagePreviewDiv.innerHTML = ''; // Clear previous content

                if (event.image_path) {
                    const label = document.createElement('p');
                    label.textContent = 'Current Image:';
                    label.style.fontWeight = 'bold';
                    label.style.marginBottom = '5px';
                    
                    const img = document.createElement('img');
                    // Use the image_path received from the PHP server
                    img.src = event.image_path; 
                    img.alt = 'Current Event Image';
                    img.style.maxWidth = '200px'; 
                    img.style.maxHeight = '200px'; 
                    img.style.marginTop = '10px';
                    img.style.borderRadius = '4px';
                    
                    imagePreviewDiv.appendChild(label);
                    imagePreviewDiv.appendChild(img);
                } else {
                    imagePreviewDiv.innerHTML = '<p style="color: #999;">No image currently set.</p>';
                }
                
                // Display the modal
                modal.style.display = "block";

            } else {
                alert('Could not fetch event details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while fetching event data.');
        });
}

document.addEventListener("DOMContentLoaded", function () {
  var eventModal = document.getElementById("addEventModal");
  var eventBtn =
    document.getElementById("addNewEventBtn") ||
    document.getElementById("dashboardAddEventBtn");
  var eventClose = eventModal ? eventModal.querySelector(".close") : null;

  // Category Modal Elements
  var categoryModal = document.getElementById("addCategoryModal");
  var categoryBtn = document.getElementById("addNewCategoryBtn");
  var categoryClose = categoryModal
    ? categoryModal.querySelector(".close")
    : null;

  var editModal = document.getElementById("editEventModal");
var editClose = editModal ? editModal.querySelector(".close") : null;
  var editLinks = document.querySelectorAll(".action-links .edit");

  // Event Modal Handlers
  if (eventBtn) {
    eventBtn.onclick = function () {
      eventModal.style.display = "block";
    };
  }

  if (eventClose) {
    eventClose.onclick = function () {
      eventModal.style.display = "none";
    };
  }

  if (categoryBtn) {
    categoryBtn.onclick = function () {
      categoryModal.style.display = "block";
    };
  }

  if (categoryClose) {
    categoryClose.onclick = function () {
      categoryModal.style.display = "none";
    };
  }

  if (editClose) {
    editClose.onclick = function () {
      editModal.style.display = "none";
    };
  }

  editLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const eventId = this.getAttribute("data-id");

      if (eventId) {



        fetchEventDetails(eventId, editModal);
      }
    });
  });

  window.onclick = function (event) {
    if (event.target == eventModal) {
      eventModal.style.display = "none";
    }
    if (categoryModal && event.target == categoryModal) {
      categoryModal.style.display = "none";
    }

    if (editModal && event.target == editModal) {
      editModal.style.display = "none";
    }
  };

  if (document.getElementById("existingVenueFields")) {
    toggleVenueFields("existing");
  }
});
