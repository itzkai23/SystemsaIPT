<?php
require 'connect.php';
session_start();

// Prevent browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page if session is not set
if (!isset($_SESSION['user_name'])) {
    header('location:silog.php');
    exit();
}

// Keep your existing default image
$default_image = "images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>
    Bogh Art
  </title>
   
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/home.css">  

</head>
  
<body>

<nav class="home-header">

   <div class="ham-menu">
     <span></span>
     <span></span>
     <span></span>
   </div>

   <ul class="sidebar" id="sidebar">
         
     <li><a class="a-bar"href="#">Home</a></li>
     <li><a class="a-bar"href="HTML/gallery.html">Objectives</a></li>
     <li><a class="a-bar"href="#">Announcement</a></li>
     <li><a class="a-bar"href="HTML/profile.html">Rules and Regulation</a></li>
     <li><a class="a-bar"href="upf.php">Profile</a></li>
          
   </ul>

   <div class="mid-section">
   <a href="#home" class="home">Home</a>
   <a href="upf.php" class="pf">Profile</a>
   <a href="instructorsProfiles.php" class="pf">Faculty</a>
   </div>
   
   <div class="right-section">                              
      
     <div class="logpos">
         
         <div class="logout-container"> 
           <img src="<?php echo htmlspecialchars($current_image); ?>" class="logout-logo" id="logoutButton">
           <div class="logout-dropdown" id="logoutDropdown">
               <div class="logoutbb">
             <a href="logout.php"><img src="images/logoutb.png" class="logoutb2"></a>
             <a href="logout.php" class="logout-link">Logout</a>
           </div>
       
           </div>
         </div>
         <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']); ?></span></h4> 
       </div>
            
   </div>
</nav>

<div class="nav-text">

<div class="h-text">
<h2>Your feedback helps evaluate faculty performance, shaping better teaching and an improved learning experience for all students.</h2>
<h3 class="">Your insight and feedbacks are all matters!</h3>
<a href="instructorsEval.php">Evaluate Now!</a>
</div> 
</div>   
<script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
    
    
  </body>
  
</html>