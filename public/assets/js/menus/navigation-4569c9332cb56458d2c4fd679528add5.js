const dropdownButton = document.getElementById('dropdown-button');
const dropdownMenu = document.getElementById('dropdown-menu');
let isDropdownOpen = false; // Set to true to open the dropdown by default, false to close it by default

// Function to toggle the dropdown
function toggleDropdown() {
    isDropdownOpen = !isDropdownOpen;
    if (isDropdownOpen) {
        dropdownMenu.classList.remove('hidden');
    } else {
        dropdownMenu.classList.add('hidden');
    }
}

// Toggle the dropdown when the button is clicked
dropdownButton.addEventListener('click', toggleDropdown);

// Close the dropdown when clicking outside of it
window.addEventListener('click', (event) => {
    if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.add('hidden');
        isDropdownOpen = false;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Menu button event listener
    document.getElementById('menu-btn').addEventListener('click', function () {
        var menu = document.getElementById('mobile-menu');
        var overlay = document.getElementById('menu-overlay');
        var navbar = document.getElementById('dropdown-button');

        // Toggle the menu and overlay visibility
        menu.classList.toggle('translate-x-full');
        menu.classList.toggle('translate-x-0');
        menu.classList.toggle('hidden');
        overlay.classList.toggle('hidden');
        navbar.classList.toggle('blur-sm');
    });

    // Close button event listener
    document.getElementById('close-btn').addEventListener('click', closeMobileMenu);

    // Overlay click event to close the menu
    document.getElementById('menu-overlay').addEventListener('click', closeMobileMenu);
});

// Close the mobile menu function
function closeMobileMenu() {
    var menu = document.getElementById('mobile-menu');
    var overlay = document.getElementById('menu-overlay');
    var navbar = document.getElementById('dropdown-button');

    // Hide the menu and overlay
    menu.classList.add('translate-x-full');
    menu.classList.remove('translate-x-0');
    menu.classList.add('hidden');
    overlay.classList.add('hidden');
    navbar.classList.remove('blur-sm');
}
