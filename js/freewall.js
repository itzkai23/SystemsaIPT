function toggleMenu(menuId) {
    var menu = document.getElementById(menuId);
    menu.classList.toggle("show");
}

// Close menu when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.ellipsis-btn')) {
        var dropdowns = document.getElementsByClassName("menu-dropdown");
        for (var i = 0; i < dropdowns.length; i++) {
            dropdowns[i].classList.remove('show');
        }
    }
}

const textarea = document.getElementById("commentInput");
const textBox = document.querySelector(".text-box");

textarea.addEventListener("input", function () {
this.style.height = "20px"; // Reset height
this.style.height = this.scrollHeight + "px"; // Set new height based on content
textBox.style.height = this.scrollHeight + 30 + "px"; // Adjust .comment-box height
});


// Get the sidebar element
const sidebar = document.querySelector('.sidebar');

// Function to show scrollbar
const showScrollbar = () => {
sidebar.style.overflowY = 'auto'; // Show the scrollbar
};

// Function to hide scrollbar
const hideScrollbar = () => {
sidebar.style.overflowY = 'hidden'; // Hide the scrollbar
};

// Event listener to detect when the user hovers over the sidebar
sidebar.addEventListener('mouseenter', showScrollbar);

// Event listener to detect when the user leaves the sidebar
sidebar.addEventListener('mouseleave', hideScrollbar);