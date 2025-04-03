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

// Fetch all professors from the database
$result = $conn->query("SELECT id, name, role, prof_img FROM professors");

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
        <Title>Instructor's Profiles</Title>

    <link rel="stylesheet" href="css/instructorsProfiles.css">

    </head>
    
    <body>

    <nav class="home-header">

<div class="ham-menu">
  <span></span>
  <span></span>
  <span></span>
</div>

<ul class="sidebar" id="sidebar">
      
     <li><a class="a-bar"href="home.php">Home</a></li>
     <li><a class="a-bar"href="instructorsProfiles.php">Faculty</a></li>
     <li><a class="a-bar"href="freedomwall.php">Newsfeed</a></li>
     <li><a class="a-bar"href="upf.php">Profile</a></li>
       
</ul>

<div class="right-section">                              
   
  <div class="logpos">
  
      <div class="logout-container"> 
      <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
        <div class="logout-dropdown" id="logoutDropdown">
                <a href="home.php" class="logpf-con">
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
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></h4> 
    </div>
         
</div>
</nav>

<div class="whole">

        <a href="home.php" class="back-button">
                <img src="images/backmage1.png" alt="Back" class="back-image">
        </a>

        <h1>Instructor's Profiles</h1>
        <div class="group-container"> 
                <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                <img src="<?php echo !empty($row['prof_img']) ? htmlspecialchars($row['prof_img']) : 'images/facultyb.png'; ?>" 
                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                 width="150" height="150">                    
                 <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                    <a href="instructor.php?professor_id=<?php echo $row['id']; ?>"><p class="role"><?php echo htmlspecialchars($row['role']); ?></p></a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No professors.</p>
        <?php } ?>
        </div>
        </div>
        <script src="js/sidebar.js"></script>
        <script src="js/logs.js"></script>
    </body>
</html>