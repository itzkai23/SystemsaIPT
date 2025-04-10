<?php
require '../connect.php';
$query = "SELECT section FROM sections ORDER BY section";
$result = mysqli_query($conn, $query);
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
    <link rel="stylesheet" href="../css/cbot.css">
    <link rel="stylesheet" href="../css/silog.css">

</head>
<body>
<header>
    <img src="../images/head.png" alt="headerlogo" class="logo">
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
            <img src="../images/close.png" alt="close">
        </span>
        <img src="../images/logo.png" alt="City of Malabon University" class="image">
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
            <button type="submit" name="sub" class="form-login-btn">Login</button>
            <p>Don't have an account? <a href="#" class="register-link" >SignUp</a></p>
            
            <div>
                <label for="termsCheckbox" class="open-modal-btn">Terms and Conditions</label>
            </div>
        
        </form>
    </div>

    <div class="form-box register">
        <span class="close register" id="regi">
            <img src="../images/close.png" alt="close">
        </span>
        <img src="../images/logo.png" alt="City of Malabon University" class="image">
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
                <span class="icon"><i class="fas fa-users"></i></span>
                <select name="section" required>
                <option value="" disabled selected>Select Section</option>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?= $row['section']; ?>"><?= $row['section']; ?></option>
                <?php endwhile; ?>
                </select>
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

          <button type="submit" name="submit" class="form-register-btn">Register</button>
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
    <img src="../images/transbot2.png" alt="imgbot">
</button>  

<div class="chat-popup" id="chat-popup">
    <div class="botmage">
        <img src="../images/transbot.png" alt="imgbot">
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

<script src="../js/faeye.js"></script>
<script src="../js/cbot.js"></script>
<script src="../js/silog.js"></script>

</body>
</html>