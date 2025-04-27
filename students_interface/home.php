<?php
require '../connect.php';
require '../Authentication/restrict_to_student.php';
restrict_to_student();
require 'evaluation_schedule.php';


// Prevent browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Keep your existing default image
$default_image = "../images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

// Example: get section from session
$user_section = isset($_SESSION['section']) ? $_SESSION['section'] : null;

// Call the function to get the evaluation schedule status
$evaluation_status = getEvaluationScheduleStatus($user_section, $conn);

// Check if the user can evaluate and display the corresponding message
$can_evaluate = $evaluation_status['allowed'];

?>

<!DOCTYPE html>
<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

 <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../css/home.css">
<link rel="stylesheet" href="../css/headmenu.css">
<link rel="stylesheet" href="../css/sched.css">
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
     <li><a class="a-bar"href="home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
     <li><a class="a-bar"href="instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
     <li><a class="a-bar"href="upf.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
          
   </ul>

   
   
   <div class="right-section">                              
   <div class="mid-section">
   <a href="home.php" class="active">Home</a>
   <a href="instructorsProfiles.php" class="pf">Faculty</a>
   <a href="upf.php" class="pf">Profile</a>
   </div>
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
                <!-- <a class="a-pf" href="#">Announcement</a> -->
                <a class="open-btn" onclick="openModal()">Schedule</a>
                </div>
                
                <div class="dlog-icon">
                 <img src="../images/facultyb.png" alt="">
                <a class="a-pf" href="instructorsProfiles.php">Faculty</a>
                </div>

           <div class="logoutbb">
             <a href="../Authentication/logout.php"><img src="../images/logoutb.png" class="logoutb2"></a>
             <a href="../Authentication/logout.php" class="logout-link">Logout</a>
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

<?php 
// Assuming $user_section and $conn are defined above
$status = getEvaluationScheduleStatus($user_section, $conn);
?>

<?php if ($status['allowed']): ?>
  <a href="instructorsEval.php" class="link">Evaluate Now!</a>
<?php else: ?>
  <div class="tooltip-wrapper">
    <a href="javascript:void(0);" class="link disabled-link">Evaluate Now!</a>
    <span class="tooltip-text">
      <i class="fas fa-info-circle"></i>
      <?php echo $status['message']; ?>
      <?php if ($status['schedule_date']): ?>
        <br><strong>Next available date: <?php echo $status['schedule_date']; ?></strong>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>



</div> 
</div> 
<div class="modal-sched" id="scheduleModal">
  <div class="modal-content-sched">
    <div class="modal-header">
      <h2>Faculty Evaluation Schedule</h2>
      <button class="announcement-btn" onclick="showAnnouncement()">Notice</button>
      <button class="close-btn" onclick="closeModal()">&times;</button>
    </div>

    <?php 
    // Assume evaluation_schedule.php is already required above
    // and $status is already available

    // Define announcement message based on the evaluation status
    if ($status['allowed']) {
        $announcement_message = "You can now evaluate.";
    } else {
        if ($status['schedule_date']) {
            $announcement_message = "The Faculty Evaluation is scheduled on " . date('F j, Y', strtotime($status['schedule_date'])) . ".";
        } else {
            $announcement_message = "You're not yet scheduled to evaluate.";
        }
    }
    ?>

    <!-- Announcement Pop-up -->
    <div class="announcement-popup" id="announcementPopup" style="display: none;">
      <button class="announcement-close" onclick="closeAnnouncement()">&times;</button>
      <h3>Schedule</h3>
      <p id="announcementText"><?php echo $announcement_message; ?></p>
    </div>

    <div class="modal-body">
      <table class="schedule-table">
        <thead>
          <tr>
            <th>Day</th>
            <th>Time</th>
            <th>Course/Section</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td rowspan="2">Monday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - A</td>
          </tr>
          <tr>
            <td>3:00 PM - 10:00 PM</td>
            <td>BSIT 3 - B</td>
          </tr>
          <tr>
            <td rowspan="2">Tuesday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - C</td>
          </tr>
          <tr>
            <td>3:00 PM - 10:00 PM</td>
            <td>BSIT 3 - D</td>
          </tr>
          <tr>
            <td rowspan="2">Wednesday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - E</td>
          </tr>
          <tr>
            <td>3:00 PM - 10:00 PM</td>
            <td>BSIT 3 - F</td>
          </tr>
          <tr>
            <td rowspan="2">Thursday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - G</td>
          </tr>
          <tr>
            <td>3:00 PM - 10:00 PM</td>
            <td>BSIT 3 - H</td>
          </tr>
          <tr>
            <td rowspan="2">Friday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - I</td>
          </tr>
          <tr>
            <td>3:00 PM - 10:00 PM</td>
            <td>BSIT 3 - J</td>
          </tr>
          <tr>
            <td>Saturday</td>
            <td>7:00 AM - 2:00 PM</td>
            <td>BSIT 3 - K</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
<script src="../js/sched.js"></script>
    
  </body>
  
</html>