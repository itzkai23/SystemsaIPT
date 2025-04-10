<?php
session_start();
include 'connect.php';

// Get professor data from URL
$professor_id = isset($_GET['id']) ? $_GET['id'] : '';
$professor_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown Professor';
$professor_img = isset($_GET['img']) ? htmlspecialchars($_GET['img']) : 'images/facultyb.png';

// Keep your existing default image
$default_image = "images/icon.jpg";

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
    <title>Report Professor</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="css/report_prof.css">
    <link rel="stylesheet" href="css/headmenu.css">
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
    <img src="images/head2.png" alt="Sidebar Image" class="sidebar-image">
   </div>
     <li><a class="a-bar"href="home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
     <li><a class="a-bar"href="instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
     <li><a class="a-bar"href="upf.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
          
   </ul>

   <div class="mid-section">
   <a href="home.php" class="home">Home</a>
   <a href="instructorsProfiles.php" class="pf">Faculty</a>
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

    <div class="box">
        <!-- Display Professor's Image -->
        <img src="<?php echo $professor_img; ?>" alt="<?php echo $professor_name; ?>"> 
        <h2>Report a Professor</h2>    
        <h4><?php echo $professor_name; ?></h4>  

        <form action="submit_report.php" method="post">
            <input type="hidden" name="professor_id" value="<?php echo $professor_id; ?>">
           
            <div class="checkbox-container">
                <label><input type="checkbox" name="reason[]" value="Unfair Grading"> Unfair Grading</label>
                <label><input type="checkbox" name="reason[]" value="Lack of Professionalism"> Lack of Professionalism</label>
                <label><input type="checkbox" name="reason[]" value="Poor Communication"> Poor Communication</label>
                <label><input type="checkbox" name="reason[]" value="Frequent Absences or Tardiness"> Frequent Absences or Tardiness</label>
                <label><input type="checkbox" name="reason[]" value="Unorganized Teaching"> Unorganized Teaching</label>
                <label><input type="checkbox" name="reason[]" value="Bias or Favoritism"> Bias or Favoritism</label>
                <label><input type="checkbox" id="otherCheckbox" name="reason[]" value="Others"> Others:</label>
            </div>

            <textarea name="details" placeholder="Optional: Provide additional details..."></textarea>

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>
    </div>

    <script src="js/report_prof.js"></script>
    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>
</body>
</html>
