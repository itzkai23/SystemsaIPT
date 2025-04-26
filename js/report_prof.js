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

// Function to toggle the disabled state of the "Others" textarea
document.addEventListener('DOMContentLoaded', function() {
    const otherCheckbox = document.getElementById('otherCheckbox');
    const othersTextArea = document.querySelector('textarea[name="details"]');

    // Initially disable the textarea if "Others" is not checked
    if (!otherCheckbox.checked) {
        othersTextArea.disabled = true;
    }

    // Toggle textarea disabled state when the "Others" checkbox is clicked
    otherCheckbox.addEventListener('change', function() {
        if (otherCheckbox.checked) {
            othersTextArea.disabled = false;  // Enable the textarea
        } else {
            othersTextArea.disabled = true;   // Disable the textarea
            othersTextArea.value = '';        // Clear the textarea when unchecked
        }
    });
});