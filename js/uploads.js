const previewimage = () => {
    let file = document.getElementById('fileinput');
    let picture = document.querySelector('.picture');
    let message = document.querySelector('.message');
    let btnup = document.querySelector('.btnup');

    picture.src = window.URL.createObjectURL(file.files[0]);

    let regex = new RegExp("[^.]+$");
    let fileExtension = file.value.match(regex);
    
    if (fileExtension && (fileExtension[0].toLowerCase() === "jpeg" || fileExtension[0].toLowerCase() === "jpg" || fileExtension[0].toLowerCase() === "png")) {
        btnup.style.display = "block";
        message.innerHTML = "";
    } else {
        picture.src = "images/error.jpg";
        btnup.style.display = "none";
        message.innerHTML = `<b>.${fileExtension}</b> File is not allowed.<br/> Choose a .jpg or .png file only`;
    }
};

document.addEventListener("DOMContentLoaded", function () {
    let bdayInput = document.getElementById("Birthday");
    let ageInput = document.getElementById("age");

    function calculateAge() {
        let bdayValue = bdayInput.value;
        if (!bdayValue) return;

        let bday = new Date(bdayValue);
        let today = new Date();
        let age = today.getFullYear() - bday.getFullYear();
        let monthDiff = today.getMonth() - bday.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < bday.getDate())) {
            age--;
        }

        ageInput.value = age;
    }

    if (bdayInput.value) calculateAge();
    bdayInput.addEventListener("change", calculateAge);

    // Edit Profile Script
    let editBtn = document.getElementById("editProfileBtn");
    let saveBtn = document.getElementById("saveProfileBtn");
    let cancelBtn = document.getElementById("cancelEditBtn");
    let form = document.getElementById("profileForm");

    let inputs = form.querySelectorAll("input:not(#age)");
    let originalValues = {}; 
    let hiddenFields = document.querySelectorAll(".hidden-profile"); // Select hidden fields

    // When "Edit Profile" is clicked
    editBtn.addEventListener("click", function () {
        hiddenFields.forEach(field => field.style.display = "block"); // Show fname & lname

        inputs.forEach(input => {
            originalValues[input.name] = input.value;
            input.removeAttribute("readonly"); // Allow editing
        });

        editBtn.style.display = "none";
        saveBtn.style.display = "inline-block";
        cancelBtn.style.display = "inline-block";
    });

    // When "Cancel" is clicked
    cancelBtn.addEventListener("click", function () {
        hiddenFields.forEach(field => field.style.display = "none"); // Hide fname & lname again

        inputs.forEach(input => {
            input.value = originalValues[input.name];
            input.setAttribute("readonly", true); // Lock fields again
        });

        editBtn.style.display = "inline-block";
        saveBtn.style.display = "none";
        cancelBtn.style.display = "none";
    });
});
