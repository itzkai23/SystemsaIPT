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