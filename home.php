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

if (isset($_SESSION['is_admin'])){
  header('location:admin.php');
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

<style>
* {
    margin: 0;
    padding: 0;
  }
  
body { 
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 97vh;
    background: 
        linear-gradient(rgb(76, 76, 209), rgba(125, 125, 233, 0.5)), /* Gradient overlay */
        url('images/malabon-1.jpg') no-repeat; /* Background image */
    background-size: cover;
    background-position: center;
    background-blend-mode: multiply; /* Blending the gradient with the image */
    }
  
.home-header {
      height: 55px;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: rgb(10, 0, 104);
      }
    
.left-section {
      width: 400px;
      display: flex;
      align-items: center;
      font-size: 21px;
      font-family: Racing Sans One;
      }

.mid-section {
display: flex;
align-items: center;
gap: 30px;
margin-left: 65%;
}

.mid-section a{
    position: relative;
    font-size: 1em;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-family: "Roboto", sans-serif;
}

.mid-section a:hover {
    color: rgb(250, 149, 54);
    transition: transform 0.7s ease;
}

.mid-section a::after {
    content: '';
    position: absolute;
    width: 100%; 
    height: 3px;
    background: rgb(255, 143, 39);
    border-radius: 5px;
    bottom: -5px;
    left: 0;
    transform:scaleX(0);
    transition: transform 0.7s ease; 
}

.mid-section a:hover::after {
    width: 100%; 
    transform: scaleX(1);
}
  
.right-section {
      width: 120px;
      margin-right: 25px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
      background-color: rgb(10, 0, 104);
      }

.user {
      color: white;
     } 
    
.ham-menu {
        height: 20px;
        width: 20px;
        margin: 15px 0px 0px 15px;
        position: relative;
        cursor: pointer;
      }
      
.ham-menu span {
        height: 2px;
        width: 15px;
        background-color: white;
        border-radius: 7.5px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        transition: .3s ease;
        z-index: 15;
      }
      
  .ham-menu span:nth-child(1) {
        top: 25%;
      }
      
  .ham-menu span:nth-child(3) {
        top: 75%;
      }
      
  .ham-menu.active span:nth-child(1) {
        top: 50%;
        transform: translate(-50%,-50%) rotate(45deg);
      }
      
  .ham-menu.active span:nth-child(2) {
        opacity: 0;
      }
      
  .ham-menu.active span:nth-child(3) {
        top: 50%;
        transform: translate(-50%,-50%) rotate(-45deg);
      }  
  
    
.sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        height: 100%;
        width: 250px;
        padding-top: 55px;
        padding-left: 0px;
        z-index: 10;
        background-color: rgb(11, 0, 114);
        backdrop-filter: blur(5px);
        box-shadow: -10px 0 10px rgb(0,0,0,0.1);
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        list-style: none;
        transition: left 0.3s ease;
  
      }
      
.sidebar.active {
        left: 0;
      }
      
.sidebar li {
        list-style: none;
        height: 54px;
        width: 100%;
      }
      
.sidebar a {
    text-decoration: none;
    font-size: 18px;
    font-family: Roboto, sans-serif;
    color: white;
    display: flex;
    padding: 15px 0px 15px 30px;
    transition: background-color 0.3s ease;
    transition: color 0.3s ease;
      }
      
.a-bar:hover {
    color: rgb(248, 171, 27);
      }

.logpos {
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
      }
      /* Container for the logo and dropdown */
.logout-container {
    position: relative;
    /* display: inline-block; */
    cursor: pointer;
      }
/* .logout-container img {
    width: 35px;
    height: 35px;
    border-radius: 17.5px;
    object-fit: cover;
    border: 1px solid goldenrod;
} */
.piclog {
    width: 35px;
    height: 35px;
    border-radius: 17.5px;
    object-fit: cover;
    border: 1px solid goldenrod; 
}      
      
      /* Style for the dropdown (initially hidden) */
.logout-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 50px;  /* Adjust as per the size of your logo */
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-radius: 8px;
    min-width:  250px;
      }
      
.logoutbb {
    display: flex;
    align-items: center;
    gap: 23px;
    margin-left: 2px;
    margin-top: 7px;
      }
      
.logoutb2 {
    width: 25px;  /* Adjust the size of the logo */
    cursor: pointer;
      }
      
      /* Style for the logout link */
.logout-link {
    color: #f00;
    text-decoration: none;
    font-size: 17.5px;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    cursor: pointer;
      }
      
      /* Change color on hover */
.logout-link:hover {
    color: #c00;
      }
 
.logpf-con {
  display: flex;
  align-items: center;
  gap: 15px;
  width: 95%;
  border-radius: 5px;
  padding: 5px 0px 5px 10px;
  text-decoration: none;
  margin-bottom: 10px;
}
.a-pf:hover,.logpf-con:hover {
  background-color: rgb(236, 236, 236);
  transition: 0.5s ease;
  cursor: pointer;
}
.logpf-con img {
  width: 30px;
  height: 30px;
  border-radius: 15px;
  border: 1px solid blue;
}     
.logpf-con h4 {
  font-size: 18px;
  font-family: "Roboto", sans-serif;
  font-weight: 500;
  color: rgb(54, 54, 54);
}
.dlog-icon {
  display: flex;
  align-items: center;
  gap: 10px;
}
.dlog-icon img {
  width: 30px;
  border-radius: 15px;
}
.a-pf {
  font-size: 17px;
  font-weight: 400;
  font-family: "Roboto", sans-serif;
  text-decoration: none;
  color: rgb(19, 19, 19);
  display: block;
  margin-bottom: 2px;
  margin-left: 2px;
  width: 100%;
  padding: 8px;
  border-radius: 5px;
}      

.h-text {
    text-align: center;
    width: 58%;
     background: rgba(255, 255, 255, 0.1); /*Semi-transparent background */
    padding: 30px;
    border-radius: 15px;
    margin-top: 50px;
    margin-left: 19%;
    transition: all 0.3s ease-in-out;
    z-index: auto;
}

.h-text h2 {
    font-weight: 600;
    font-size: 42px;
    text-align: center;
    color: white;
    display: block;
    margin-bottom: 20px;
    text-shadow: 3px 3px 7px rgba(0, 0, 0, 0.4);
    letter-spacing: 0.5px;
    transition: transform 0.3s ease-in-out;
    animation: fadeIn 0.8s ease-in-out;
}

.h-text h3 {
    font-size: 24px;
    font-weight: 500;
    color: white;
    margin-bottom: 25px;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
    transition: color 0.3s ease-in-out;
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

.link {
    color: white;
    font-size: 20px;
    font-weight: 600;
    border: none;
    background: linear-gradient(90deg, goldenrod, orange);
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.4s ease-in-out;
    box-shadow: 0 3px 6px rgba(255, 215, 0, 0.4);
    z-index: 1;
}

.link:hover {
    background: linear-gradient(90deg, orange, goldenrod);
    transform: scale(1.05);
    box-shadow: 0 5px 10px rgba(255, 215, 0, 0.6);
}

</style>
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
     <li><a class="a-bar"href="instructorsProfiles.php">Faculty</a></li>
     <li><a class="a-bar"href="freedomwall.html">Newsfeed</a></li>
     <li><a class="a-bar"href="upf.php">Profile</a></li>
          
   </ul>

   <div class="mid-section">
   <a href="home.php" class="home">Home</a>
   <a href="freedomwall.php" class="pf">Newsfeed</a>
   <a href="instructorsProfiles.php" class="pf">Faculty</a>
   </div>
   
   <div class="right-section">                              
      
     <div class="logpos">
         
         <div class="logout-container"> 
           <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
           <div class="logout-dropdown" id="logoutDropdown">
                <a href="#" class="logpf-con">
                  <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" alt="picture">
                  <h4><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></h4>
                </a>
              
               <div class="dlog-icon">
                <Img src="images/nfeed.png">
                <a class="a-pf" href="freedomwall.php">Newsfeed</a>
                </div>

               <div class="dlog-icon">
                 <Img src="images/offweb.png" alt="log">
                <a class="a-pf" href="https://sgs.cityofmalabonuniversity.edu.ph/">Visit Official Website</a>
                </div>

                <div class="dlog-icon">
                 <img src="images/announcement.png" alt="">
                <a class="a-pf" href="#">Announcement</a>
                </div>
                
                <div class="dlog-icon">
                 <img src="images/facultyb.png" alt="">
                <a class="a-pf" href="instructorsProfiles.php">Faculty</a>
                </div>

           <div class="logoutbb">
             <a href="logout.php"><img src="images/logoutb.png" class="logoutb2"></a>
             <a href="logout.php" class="logout-link">Logout</a>
           </div>
       
           </div>
         </div>
         <p class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']); ?></span></p> 
       </div>
            
   </div>
</nav>

<div class="nav-text">

<div class="h-text">
<h2>Your feedback helps evaluate faculty performance, shaping better teaching and an improved learning experience for all students.</h2>
<h3 class="">Your insight and feedbacks are all matters!</h3>
<a href="instructorsEval.php" class="link">Evaluate Now!</a>
</div> 
</div>   
<script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
    
    
  </body>
  
</html>