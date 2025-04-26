<?php
require '../connect.php';
require '../Authentication/restrict_to_student.php';
restrict_to_student();

// Redirect to login page if session is not set
if (!isset($_SESSION['user_name'])) {
    header('location:silog.php');
    exit();
}

$query = "SELECT semester, school_year FROM section_professors LIMIT 1"; 
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Store in session
    $_SESSION['semester'] = $row['semester'];
    $_SESSION['school_year'] = $row['school_year'];
} else {
    // Default values or handle missing data
    $_SESSION['semester'] = "N/A";
    $_SESSION['school_year'] = "N/A";
}

$default_image = "../images/icon.jpg";
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;
$current_image .= "?t=" . time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/upf.css">
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
         <a href="upf.php" class="active">Profile</a>
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

   <div class="main-profile">
  <div class="profile-card">
    <!-- LEFT: Profile Image and Name -->
    <section class="profile-left">
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <input disabled type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
        <label for="fileinput" class="profile-picture-label">
          <img src="<?php echo htmlspecialchars($current_image); ?>" class="picture" title="Click to upload image" />
          <div class="profile-edit-overlay">Change</div>
        </label>
        <input disabled type="file" id="fileinput" name="picture_url" accept="image/*" onchange="previewimage();" style="display:none;" />
        <button class="btnup" name="btnup" style="display:none;">Save</button>
      </form>
      <h3><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']); ?></h3>
    </section>

    <!-- RIGHT: Personal Info -->
    <div class="profile-right">
      <div class="profile-buttons">
          <h5 id="toggleStaticInfo" class="toggle-tab">Personal Information</h5>
          <h5 id="editProfileBtn" class="toggle-tab">Username and Password</h5>
        </div>
      <form action="update_profile.php" method="post" id="profileForm">
        <div class="profile-grid" id="staticInfo">
          <!-- Email (read-only) -->
          <div class="user-input">
            <label>Email:</label><br>
            <input disabled type="email" value="<?php echo htmlspecialchars($_SESSION['em']); ?>" readonly>
          </div>

          <!-- Contact Number (read-only) -->
          <div class="user-input">
            <label>Phone No.:</label><br>
            <input disabled type="text" value="<?php echo htmlspecialchars($_SESSION['con']); ?>" readonly>
          </div>

          <!-- Section (read-only) -->
          <div class="user-input">
            <label>Course/Section:</label><br>
            <input disabled type="text" value="<?php echo htmlspecialchars($_SESSION['section']); ?>" readonly>
          </div>

          <!-- Semester (read-only) -->
          <div class="user-input">
            <label>Semester:</label><br>
            <input disabled type="text" value="<?php echo htmlspecialchars($row['semester']); ?>" readonly>
          </div>

          <!-- School Year (read-only) -->
          <div class="user-input">
            <label>School Year:</label><br>
            <input disabled type="text" value="<?php echo htmlspecialchars($row['school_year']); ?>" readonly>
          </div>
        </div>

    <!-- Editable Fields (Username & Password Only) -->
      <div class="profile-grid" id="editFields" style="display: none;">
        <div class="user-input">
          <label>Username:</label><br>
          <input type="text" name="uname" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
        </div>

        <div class="user-input">
          <label>Current Password:</label><br>
          <input type="password" name="current_password" id="current_password" placeholder="Enter current password">
        </div>

        <div class="user-input">
          <label>New Password:</label><br>
          <input type="password" name="new_password" id="new_password" placeholder="Enter new password">
        </div>

        <div class="user-input">
          <label>Confirm New Password:</label><br>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
        </div>
      </div>

        
        <button type="submit" id="saveProfileBtn" style="display:none;" class="save-btn">Save</button>
        <!-- <button type="button" id="cancelEditBtn" style="display:none;" class="cancel-btn">Cancel</button> -->
      </form>
    </div>
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


<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
<script src="../js/uploads.js"></script>
<script src="../js/sched.js"></script>
</body>
</html>