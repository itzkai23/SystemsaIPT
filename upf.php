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

$user_id = $_SESSION['user_id']; // Get user ID from session

// Prepare SQL query to fetch feedback for the logged-in user
$query = "SELECT feedback, submitted_at FROM instructor_evaluation WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbackData = [];

while ($row = $result->fetch_assoc()) {
    $feedbackData[] = [
        'feedback' => $row['feedback'],
        'submitted_at' => $row['submitted_at']
    ];
}


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
    <title>Homepage</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/upf.css">

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
   <a href="freedomwall.html" class="pf">Newsfeed</a>
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
    
    <div class="maincon-userpf">

        <div class="maincon-userpf">
        
        <div class="pfcon">
        <section class="profile_section">
        <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
                <label for="fileinput">
                    <img src="<?php echo htmlspecialchars($current_image); ?>" class="picture" title="Click to upload image"/>
                </label>
                <input type="file" id="fileinput" name="picture_url" accept="image/*" onchange="previewimage();" style="display:none;"/>
                <button class="btnup" name="btnup" style="display:none;">Save</button>
            </form>
<!-- <div class="message"></div> -->
        
                </section>
                <h4><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></h4>
                <p class="course"><?php echo htmlspecialchars($_SESSION['em']);?></p>
                <p class="sec"><?php echo htmlspecialchars($_SESSION['con']);?></p>
            </div>
             <!-- Comments Display Section (Static, No Backend) -->
             <div class="repcomcon">    
            <div id="comments-container">
                <h3>Your previous comments</h3>
                
                <div id="comments-list">
                        <div class="comment">
                        <?php
                        foreach ($feedbackData as $entry) {
                            echo "<h4>" . htmlspecialchars($entry['feedback']) . "</h4>";
                            echo "<h6>" . htmlspecialchars($entry['submitted_at']) . "</h6>";
                        }
                        ?>

            
                        </div>         
                </div>
                    
            </div>
            
            
            <div id="report-container">
                <h3>Your previous report</h3>
                
                <div id="report-list">
                        <div class="report">
                            <p><strong>2025-01-27 12:30:00</strong> - This is a placeholder report from a student.</p>
                        </div>         
                </div>
            
    </div>
  </div>
</div>
  
</div>
 
  <script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
<script src="js/uploads.js"></script>

</body>
</html>