const previewimage = () =>{
    let file = document.getElementById('fileinput');
    let picture = document.querySelector('.picture');
    let message = document.querySelector('.message');
    let btnup = document.querySelector('.btnup');

    picture.src = window.URL.createObjectURL(file.files[0]);

    let regex = new RegExp("[^.]+$");
    fileExtension = file.value.match(regex);
    if(fileExtension == "jpeg" || fileExtension == "jpg" || fileExtension == "png"){
        btnup.style.display="block";
        message.innerHTML="";
    }else{
        picture.src="images/error.jpg"
        btnup.style.display="none"
        message.innerHTML="<b>." +fileExtension+
            "</b> File is not allowed.<br/> Choose a .jpg or .png file only"
    }
}

document.addEventListener("DOMContentLoaded", function() {
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

    bdayInput.addEventListener("change", calculateAge);
    calculateAge(); // Run on page load
});
