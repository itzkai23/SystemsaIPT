document.addEventListener("DOMContentLoaded", function () {
    const otherCheckbox = document.getElementById("otherCheckbox");
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#otherCheckbox)');

    otherCheckbox.addEventListener("change", function () {
        if (this.checked) {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = true;
            });
        } else {
            checkboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
        }
    });
});