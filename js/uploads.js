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

const editBtn = document.getElementById("editProfileBtn");
const saveBtn = document.getElementById("saveProfileBtn");
const cancelBtn = document.getElementById("cancelEditBtn");
const profileTexts = document.querySelectorAll(".profile-text");
const editInputs = document.querySelectorAll(".edit-input");
const bdayInput = document.getElementById("Birthday");
const fname = document.getElementById("fname");
const lname = document.getElementById("lname");


editBtn.addEventListener("click", () => {
    profileTexts.forEach(el => el.style.display = "none");
    editInputs.forEach(el => el.style.display = "inline-block");
    fname.style.display = "inline-block";
    lname.style.display = "inline-block"; 
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
});

cancelBtn.addEventListener("click", () => {
    profileTexts.forEach(el => el.style.display = "inline-block");
    editInputs.forEach(el => el.style.display = "none");
    fname.style.display = "none"; 
    lname.style.display = "none"; 
    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";
});


