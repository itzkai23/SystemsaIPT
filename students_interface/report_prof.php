<?php
include '../connect.php';
require '../Authentication/restrict_to_student.php';
restrict_to_student();

// Get professor data from URL
$professor_id = isset($_GET['id']) ? $_GET['id'] : '';
$professor_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown Professor';
$professor_img = isset($_GET['img']) ? htmlspecialchars($_GET['img']) : '../images/facultyb.png';

$defimage = '../images/facultyb.png';

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
    <title>Report Professor</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/report_prof.css">
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
          <a href="home.php" class="home">Home</a>
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

    <div class="box">
        <!-- Display Professor's Image -->
        <img src="<?php  echo !empty($prof_img) ? htmlspecialchars($prof_img) : $defimage; ?>" alt="<?php echo $professor_name; ?>"> 
        <div class="report-f">
        <h2>Report:</h2>    
        <h4><?php echo $professor_name; ?></h4>
        </div>  

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

            <textarea name="details" placeholder="Others:"></textarea>

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>
    </div>

    <div class="modal-sched" id="scheduleModal">
  <div class="modal-content-sched">
    <div class="modal-header">
      <h2>Faculty Evaluation Schedule</h2>
      <button class="announcement-btn" onclick="showAnnouncement()">Notice</button>
      <button class="close-btn" onclick="closeModal()">&times;</button>
    </div>

    <?php 
// Define the start date of the evaluation period
$start_date = new DateTime('2025-04-29'); // Set your evaluation start date
$now = new DateTime();

// Check if the evaluation has started
$evaluation_not_started = $now < $start_date;

// Define announcement message based on the status
if ($evaluation_not_started) {
    $announcement_message = "The evaluation period hasn't started yet.";
} else {
    if ($can_evaluate) {
        $announcement_message = "You can now evaluate.";
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


    <script src="../js/report_prof.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/logs.js"></script>
    <script src="../js/sched.js"></script>
</body>
</html>
