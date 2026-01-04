// script.js
document.addEventListener('DOMContentLoaded', function () {
    // Reset button functionality
    var resetBtn = document.getElementById('resetBtn');
    var form = document.getElementById('filterForm');

    if (resetBtn && form) {
        resetBtn.addEventListener('click', function () {
            // Clear search input
            var searchInput = document.getElementById('q');
            if (searchInput) {
                searchInput.value = '';
            }
            form.submit(); // reload with no filters
        });
    }

    // Dropdown toggle functionality
    var dropdowns = [
        { toggle: 'eventsToggle', menu: 'eventsMenu' },
        { toggle: 'sportsToggle', menu: 'sportsMenu' },
        { toggle: 'theatreToggle', menu: 'theatreMenu' }
    ];

    dropdowns.forEach(function (dropdown) {
        var toggle = document.getElementById(dropdown.toggle);
        var menu = document.getElementById(dropdown.menu);

        if (toggle && menu) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();

                // Close other dropdowns
                dropdowns.forEach(function (otherDropdown) {
                    if (otherDropdown.toggle !== dropdown.toggle) {
                        var otherMenu = document.getElementById(otherDropdown.menu);
                        var otherToggle = document.getElementById(otherDropdown.toggle);
                        if (otherMenu) otherMenu.classList.remove('show');
                        if (otherToggle) otherToggle.classList.remove('active');
                    }
                });

                // Toggle current dropdown
                menu.classList.toggle('show');
                toggle.classList.toggle('active');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(function (dropdown) {
                var menu = document.getElementById(dropdown.menu);
                var toggle = document.getElementById(dropdown.toggle);
                if (menu) menu.classList.remove('show');
                if (toggle) toggle.classList.remove('active');
            });
        }
    });
});
