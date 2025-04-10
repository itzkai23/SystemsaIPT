<?php
session_start();
require '../connect.php';

// Ensure only admins can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../students_interface/home.php");
    exit();
}

// Keep your existing default image
$default_image = "../images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/headmenu.css">

<body>

<nav class="home-header">

<div class="ham-menu">
  <span></span>
  <span></span>
  <span></span>
</div>

<ul class="sidebar" id="sidebar">
      
   <div class="sidebar-header">
    <img src="../images/head2.png" alt="Sidebar Image" class="sidebar-image">
   </div>
     <li><a class="a-bar"href="admin.php"><i class="fas fa-home"></i><span>Home</span></a></li>
     <li><a class="a-bar"href="../students_interface/instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
     <li><a class="a-bar"href="upf.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
       
</ul>

<div class="mid-section">
         <a href="admin.php" class="home">Home</a>
         <a href="../students_interface/instructorsProfiles.php" class="pf">Faculty</a>
</div>

<div class="right-section">                              
   
  <div class="logpos">
  
      <div class="logout-container"> 
        <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
        <div class="logout-dropdown" id="logoutDropdown">
                <a href="upf.php" class="logpf-con">
                  <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" alt="picture">
                  <h4><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></h4>
                </a>
                
               <div class="dlog-icon">
                 <Img src="../images/offweb.png" alt="log">
                <a class="a-pf" href="https://sgs.cityofmalabonuniversity.edu.ph/">Visit Official Website</a>
                </div>

                <div class="dlog-icon">
                 <img src="../images/announcement.png" alt="">
                <a class="a-pf" href="#">Announcement</a>
                </div>
                
                <div class="dlog-icon">
                 <img src="../images/facultyb.png" alt="">
                <a class="a-pf" href="../students_interface/instructorsProfiles.php">Faculty</a>
                </div>

           <div class="logoutbb">
             <a href="../Authentication/logout.php"><img src="../images/logoutb.png" class="logoutb2"></a>
             <a href="../Authentication/logout.php" class="logout-link">Logout</a>
           </div>
    
        </div>
       
      </div>
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']);?></span></h4> 
    </div>
         
</div>
</nav>
    <div class="admin-sec">
        <p class="fp">Welcome Administrator!</p>
        <div class="fdiv">
        <h5>Academic Year: 2024-2025</h5>
        <p>Evaluation Status: On-going</p>  
        </div>
    </div>
    
    <div class="container">
        <div class="divider">
            <a href="../students_interface/instructorsProfiles.php" class="divider-link">Faculties Profiles</a>
            <img src="../images/facultyb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="stundentlist.php" class="divider-link">Students List</a>
            <img src="../images/usersb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="eval_record.php" class="divider-link">Evaluations Records</a>
            <img src="../images/feedb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="admin_reports.php" class="divider-link">Report</a>
            <img src="../images/reportb.png" alt="icon">
        </div>
    </div>
    
<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
    
</body>
</html>