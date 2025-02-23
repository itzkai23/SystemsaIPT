// Get the logout button and dropdown elements
const logoutButton = document.getElementById('logoutButton');
const logoutDropdown = document.getElementById('logoutDropdown');

// Toggle the dropdown when the logo is clicked
logoutButton.addEventListener('click', function() {
  // Toggle the visibility of the dropdown
  logoutDropdown.style.display = logoutDropdown.style.display === 'block' ? 'none' : 'block';
});

// Optionally, hide the dropdown if clicked outside
window.addEventListener('click', function(event) {
  if (!logoutButton.contains(event.target) && !logoutDropdown.contains(event.target)) {
    logoutDropdown.style.display = 'none';
  }
});