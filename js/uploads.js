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

const editBtn = document.getElementById('editProfileBtn');
const toggleStaticInfo = document.getElementById('toggleStaticInfo');
const saveBtn = document.getElementById('saveProfileBtn');
const staticInfo = document.getElementById('staticInfo');
const editFields = document.getElementById('editFields');

// Default active tab
toggleStaticInfo.classList.add('active');

// Reusable function to reset back to personal info
function resetToStaticInfo() {
  staticInfo.style.display = "grid";
  editFields.style.display = "none";
  saveBtn.style.display = "none";

  toggleStaticInfo.classList.add('active');
  editBtn.classList.remove('active');
}

// Show Username & Password section
editBtn.addEventListener('click', () => {
  if (!editBtn.classList.contains('active')) {
    staticInfo.style.display = "none";
    editFields.style.display = "grid";
    saveBtn.style.display = "inline-block";

    editBtn.classList.add('active');
    toggleStaticInfo.classList.remove('active');
  }
});

// Show Personal Info section
toggleStaticInfo.addEventListener('click', () => {
  if (!toggleStaticInfo.classList.contains('active')) {
    resetToStaticInfo();
  }
});

// Password validation on form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
  const newPassword = document.getElementById('new_password').value;
  const confirmPassword = document.getElementById('confirm_password').value;

  if (newPassword !== confirmPassword) {
    e.preventDefault();
    alert("New password and confirm password do not match.");
  }
});

// Prevent partial password update
document.getElementById("saveProfileBtn").addEventListener("click", function (e) {
  const current = document.getElementById("current_password");
  const newPass = document.getElementById("new_password");
  const confirm = document.getElementById("confirm_password");

  const oneFilled = current.value || newPass.value || confirm.value;

  if (oneFilled) {
    if (!current.value || !newPass.value || !confirm.value) {
      alert("To change your password, please fill in all password fields.");
      e.preventDefault();
    }
  }
});
