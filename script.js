// script.js
window.addEventListener('DOMContentLoaded', function () {
    var resetBtn = document.getElementById('resetBtn');
    var form = document.getElementById('filterForm');

    if (resetBtn && form) {
        resetBtn.addEventListener('click', function () {
            // reset selects
            document.getElementById('category').selectedIndex = 0;
            document.getElementById('venue').selectedIndex = 0;
            form.submit(); // reload with no filters
        });
    }
});
