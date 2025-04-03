function toggleMenu(button) {
    var menu = button.nextElementSibling; // Get the corresponding menu
    menu.style.display = (menu.style.display === "block") ? "none" : "block";

    // Close other open menus
    document.querySelectorAll(".menu-options").forEach(function (otherMenu) {
        if (otherMenu !== menu) {
            otherMenu.style.display = "none";
        }
    });
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    if (!event.target.matches(".menu-btn")) {
        document.querySelectorAll(".menu-options").forEach(function (menu) {
            menu.style.display = "none";
        });
    }
});