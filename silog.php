<?php
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="css/silog.css"> -->
    <title>Silog</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/silog.css">
    <link rel="stylesheet" href="css/cbot.css">
    <style>
    
/* cmu logo sa form */
.image{
    display: block;
    margin-left: auto;
    margin-right: auto;
    height: 5em;
    width:6em;
}

/* login and register form css */
.form-box h2 {
    text-align: center;
    margin-bottom: 20px;
}

.form-box h5 {
    margin:5px;
    margin-left: 128px;
    font-weight: lighter;
}

.form-box p {
    text-align: center;
    font-size: 14px;
    margin-top: 10px;
    margin-bottom: 0px;
  }

.input-group {
position: relative;
width: 100%;
} 
  
.form-box input {
    width: 98%;
    padding: 10px 40px 10px 40px;
    margin: 10px 0;
    border: 1px solid #575454;
    border-radius: 5px;
    font-size: 16px;
    outline: none;
}

.form-box input:focus {
    border: solid 2px #190561 ;
}

.toggle-eye {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: rgb(16, 29, 90);
}

.icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: rgb(16, 29, 90);
    font-size: 18px;
}
  
.form-box button {  
    width: 100%;
    padding: 10px;
    background-color: #190561;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 16px;
    cursor: pointer;
}
  
.form-box button:hover {
    /* background-color: #fbff01; */
    background-color:rgb(255, 217, 0);
    color: #190561;
    transition: .3s;
    border: solid 1px #190561;
}
  
.form-box a {
    color: #190561;
    text-decoration: none;
}
  
.form-box a:hover {
    text-decoration: underline;
    color: #0056b3;
}

/* form class container */
.wrapper {
    position: relative;
    width: 300px;
    height: 400px;
    display: flex;
    justify-content: center;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform .5s ease, width .2s ease;
    transform: scale(0);
    overflow: hidden;
    text-align: center;
}

.wrapper.active-popup {
    transform: scale(1);
}

.wrapper.active {
    width: 310px;
}

.wrapper .form-box.login {
    transition: transform .18s ease;
    transform: translateX(0);
    margin-top: 20px;
    height: 300px;
}

.wrapper.active .form-box.login{
    transition: none;
    transform: translateX(-400px);
}

.wrapper .form-box.register{
    position: absolute;
    transition: none;
    transform: translateX(400px);
    padding: 5px 20px 10px 20px;
}

.wrapper.active .form-box.register{
    transition: transform .18s ease;
    transform: translateX(0);
}

/* close button ng login */
.close.login{
    position: absolute;
    top: -2.2em;
    right: -0.8em;
    cursor: pointer;
}

/* close button ng register */
.close.register{
    position: absolute;
    top: -1.2em;
    right: .6em;
    cursor: pointer;
}

/* x image */
.close img{
    height: 10px;
    width: 10px;
}

.close:hover{
    transform: scale(1.3);
    transition: 0.4s ease;
    border-radius: 100%;
}

/* form scroll input */
.register{
    #register{
        .register-input-wrapper{
            height: 180px;
            overflow-y: auto;
        }
        .register-input-wrapper::-webkit-scrollbar{
            width: 5px;
        }
        .register-input-wrapper::-webkit-scrollbar-track{
            background-color: transparent;
            border:solid 1px black;
            cursor: pointer;
        }
        .register-input-wrapper::-webkit-scrollbar-thumb{
            background-color:#162938;
            margin: 2px;
            cursor: pointer;
        }
    }
}



/* Button to Open Modal */
.open-modal-btn {
    cursor: pointer;
    font-size: 13px;
    color: #190561;
    margin-top: 5px;
    
}

.open-modal-btn:hover {
    /* background-color: #0056b3; */
    text-decoration: underline;
    color: #0056b3;
    transition: 0.5s ease;
}

/* Hidden Checkbox */
.modal-toggle {
    display: none;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

/* Modal Content */
.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    text-align: left;
    max-height: 70vh; /* Set max height to prevent overflowing */
    overflow-y: auto; /* Enable vertical scrolling */
}

/* WebKit Scrollbar Styling */
.modal-content::-webkit-scrollbar {
    width: 8px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #007BFF;
    border-radius: 5px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #0056b3;
}

/* Close Button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    color: #aaa;
    cursor: pointer;
    text-decoration: none;
}

.close:hover {
    color: black;
}

/* Show Modal When Checkbox is Checked */
.modal-toggle:checked + .modal {
    display: block;
}

h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

p {
    font-size: 1rem;
    margin-bottom: 15px;
}


/* for TEXT-CONTAINER */

/* Improved TEXT-CONTAINER */
.h-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 600px; /* Reduced width for better readability */
    width: 90%;
    padding: 30px;
    text-align: center;
    background: rgba(255, 255, 255, 0.95); /* Slightly more opaque */
    border-radius: 15px; /* Softer edges */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
    backdrop-filter: blur(12px); /* Stronger blur */
    transition: opacity 0.4s ease-in-out, visibility 0.4s ease-in-out, transform 0.3s ease-in-out;
}

/* Ensures smooth fade-in/out */
.h-text.hidden {
    opacity: 0;
    visibility: hidden;
    transform: translate(-50%, -55%);
}

/* Heading Styles */
.h-text h2 {
    font-size: 2rem;
    font-weight: bold;
    color: #012362;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
    animation: fadeIn 0.8s ease-in-out;
}

/* Subheading Styles */
.h-text h3 {
    font-size: 1.1rem;
    color: #162938;
    font-weight: 500;
    line-height: 1.6; /* Improved readability */
    margin-bottom: 0;
    animation: fadeIn 1s ease-in-out;
}

/* Animation for smooth entrance */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>

</head>
<body>
<header>
    <img src="images/head.png" alt="headerlogo" class="logo">
    <nav class="navigation">
       <a href="#">Regulations</a>
       <a href="#">Objective</a>
       <a href="profstatus.php">Faculties</a>
       <button class="btn-popup">Login</button>      
    </nav>
</header>

<div class="wrapper">
    <div class="form-box login">
        <span class="close login" id="logi" >
            <img src="images/close.png" alt="close">
        </span>
        <img src="images/logo.png" alt="City of Malabon University" class="image">
        <form id="login" action="log.php" method="post">
            <div class="input-group">
            <span class="icon"><i class="fas fa-user"></i></span>
            <input type="text" id="username" name="uname" placeholder="Username" required>
            </div>

            <div class="input-group">
            <span class="icon"><i class="fas fa-lock"></i></span>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span class="toggle-eye" onclick="togglePassword()"><i class="fas fa-eye" id="pass-eyecon"></i></span>
            </div>
            <h5><a href="forgotpass.php">Forgot password?</a></h5>
            <button type="submit" name="sub">Login</button>
            <p>Don't have an account? <a href="#" class="register-link" >SignUp</a></p>
            
            <div>
                <label for="termsCheckbox" class="open-modal-btn">Terms and Conditions</label>
            </div>
        
        </form>
    </div>

    <div class="form-box register">
        <span class="close register" id="regi">
            <img src="images/close.png" alt="close">
        </span>
        <img src="images/logo.png" alt="City of Malabon University" class="image">
        <form id="register" action="reg.php" method="post">
            <div class="register-input-wrapper">
                <div class="input-group">
                <span class="icon"><i class="fas fa-circle-user"></i></span>
                <input type="text" name="fname" placeholder="First Name" required >
                </div>
                <div class="input-group">
                <span class="icon"><i class="fas fa-signature"></i></span>
                <input type="text" name="lname" placeholder="Last Name" required>
                </div>
                <div class="input-group">
                <span class="icon"><i class="fas fa-user"></i></span>
                <input type="text" name="uname" placeholder="Username" id="user" required>
                </div>
                <div class="input-group">
                <span class="icon"><i class="fas fa-contact-book"></i></span>    
                <input type="number" name="contact" placeholder="ContactNo." required >
                </div>
                <div class="input-group">
                <span class="icon"><i class="fas fa-mail-bulk"></i></span>
                <input type="email" name="email" placeholder="CMU EMAIL" required>
                </div>
                <div class="input-group">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" placeholder="Password" id="pass" required>
                <span class="toggle-eye" onclick="togglePassign()"><i class="fas fa-eye"></i></span>
                </div> 
             </div>

          <button type="submit" name="submit">Register</button>
          <p>Already have an account? <a href="#" class="login-link">Login</a></p>
          
          <div>
          <label for="termsCheckbox" class="open-modal-btn">Terms and Conditions</label>
          </div>
        
        </form>
      </div>

</div>


<!-- Hidden Checkbox -->
<input type="checkbox" id="termsCheckbox" class="modal-toggle">

<!-- Modal (Pop-Up) -->
<div class="modal">
    <div class="modal-content">
        <label for="termsCheckbox" class="close">&times;</label>
        <h2>Website Terms and Conditions</h2>
        <p>Welcome to our website. These Terms and Conditions govern your use of this website...</p>
        <h2>Acceptance of Terms</h2>
        <p>By accessing or using the Faculty Evaluation Management System (FEMS), you agree to comply with these Terms and Conditions. If you do not agree with any part of these terms, you must refrain from using the system.</p>
        <h2>Intellectual Property</h2>
        <p>All content, including system functionalities, evaluation forms, reports, and related materials, is the property of School. Unauthorized reproduction, modification, or distribution of any part of the system is strictly prohibited.</p>
        <h2>User Responsibilities</h2>
        <p>Users are responsible for maintaining the confidentiality of their login credentials. Unauthorized access, misuse, or tampering with evaluation data is strictly prohibited and may result in disciplinary action.</p>
        
        <h2>Limitation of Liability</h2>
        <p>School is not responsible for any errors, inaccuracies, or system downtimes that may impact the evaluation process. While we strive to maintain data security, we do not guarantee that the system will always be free from disruptions or cyber threats.</p>
        <h2>Changes to Terms</h2>
        <p>We reserve the right to update these Terms and Conditions at any time. Any changes will be posted within the system, and continued use of FEMS after modifications constitutes acceptance of the revised terms.</p>
        <h2>Data Privacy and Security</h2>
        <p>All faculty evaluation data collected through the system is confidential and will only be used for academic and administrative purposes. The school follows strict data protection measures to prevent unauthorized access.</p>
        
        <h2>Governing Law</h2>
        <p>These Terms and Conditions are governed by the laws and policies of [Your Schools Jurisdiction]. Any disputes arising from the use of FEMS shall be resolved in accordance with institutional regulations.</p>
        <h2>Contact Information</h2>
        <p>For inquiries or concerns regarding the Faculty Evaluation Management System, please contact us at [Your Contact Email].</p>
    </div>
</div>

<div class="h-text">
    <h2>Welcome to the Faculty Evaluation Management System!</h2>
    <h3>Take a moment to share your thoughts on your professors and courses. Your input helps shape a better learning experience for you and future students!</h3>
    </div>

    <button class="chat-button" onclick="toggleChat()">
    <div class="notification-dot"></div>
    <img src="images/transbot2.png" alt="imgbot">
</button>  

<div class="chat-popup" id="chat-popup">
    <div class="botmage">
        <img src="images/transbot.png" alt="imgbot">
    </div>
    <div class="chat-header">UP-CHAT</div>
    <label for="chat-popup" class="close">&times;</label>
    <!-- <div class="chat-body" id="chat-body">
        <div class="chat-message bot-message">Hello! How can I help you?</div>
    </div> -->
    <iframe src="https://www.chatbase.co/chatbot-iframe/5cyDdC8pYxitCeWBRtzVm" frameborder="0" class="chat-body" id="chat-body">
        <!-- <div class="chat-message bot-message">Hello! How can I help you?</div> -->
    </iframe>
    <!-- <div class="chat-footer">
        <textarea id="user-input" placeholder="Type a message..."></textarea>
        <button onclick="sendMessage()">
            <img class="send-image" src="images/sends.png" alt="Send">
        </button>
    </div> -->
</div>

<script src="js/cbot.js"></script> <!-- Ensure correct path to cbot.js -->


<script src="js/faeye.js"></script>
<script src="js/cbot.js"></script>
<script>
const wrapper = document.querySelector('.wrapper');
const loginlink = document.querySelector('.login-link');
const registerlink = document.querySelector('.register-link');
const loginButton = document.querySelector('.btn-popup');
const closelog = document.getElementById('logi');
const closereg = document.getElementById('regi');
const loginForm = document.getElementById('login');
const registerForm = document.getElementById('register');
const hText = document.querySelector('.h-text'); // Selecting the .h-text element

// Function to hide .h-text
function hideHText() {
  hText.style.display = 'none';
}

// Function to show .h-text
function showHText() {
  hText.style.display = 'block';
}

// login button sa nav
loginButton.addEventListener('click', () => {
  wrapper.classList.add('active-popup');
  hideHText(); // Hide .h-text when login/register form opens
});

//papuntang signup
registerlink.addEventListener('click', () => {
  wrapper.classList.add('active');
  loginForm.reset();
});

// papuntang login
loginlink.addEventListener('click', () => {
  wrapper.classList.remove('active');
  registerForm.reset(); 
});

//close button sa login
closelog.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  loginForm.reset();
  showHText(); // Show .h-text when login/register form closes
});

//close button sa signup
closereg.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  registerForm.reset();
  showHText(); // Show .h-text when login/register form closes  
});

document.getElementById("registerForm").addEventListener("submit", function(event) {
    var email = document.getElementById("email").value;
    var allowedDomain = "@cityofmalabonuniversity.edu.ph";

    if (!email.endsWith(allowedDomain)) {
        alert("Invalid email! Use your @cityofmalabonuniversity.edu.ph gmail");
        event.preventDefault(); // Prevents form submission
    }
});
</script>

</body>
</html>