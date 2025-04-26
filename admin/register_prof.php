<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin();

$default_image = "../images/icon.jpg";
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;
$current_image .= "?t=" . time(); // Prevent caching
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Register Faculty</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/headmenu.css">
    <link rel="stylesheet" href="../css/reg_prof.css">
</head>
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
     <li><a class="a-bar"href="register_prof.php"><i class="fas fa-user"></i><span>Faculty Registration</span></a></li>
       
</ul>



<div class="right-section">                              
<div class="mid-section">
         <!-- <a href="admin.php" class="home">Home</a>
         <a href="../students_interface/instructorsProfiles.php" class="pf">Faculty</a> -->
</div>
  <div class="logpos">
  
      <div class="logout-container"> 
        <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
        <div class="logout-dropdown" id="logoutDropdown">
                <a href="admin.php" class="logpf-con">
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

<div class="faculty-registration">
    <h3>Register New Faculty</h3>
    <form action="faculty_registration.php" method="POST">
        <label for="fname">First Name:</label>
        <input type="text" name="fname" id="fname" required>

        <label for="lname">Last Name:</label>
        <input type="text" name="lname" id="lname" required>

        <label for="uname">Username:</label>
        <input type="text" name="uname" id="uname" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" name="register_faculty" value="Register Faculty">
    </form>
</div>
<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
</body>
</html>
