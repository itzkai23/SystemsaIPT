<?php

session_start();
require 'connect.php';

// Check if professor_id is set in either the URL (GET) or the form submission (POST)
if (isset($_GET['professor_id'])) {
  $professor_id = $_GET['professor_id'];
} elseif (isset($_POST['professor_id'])) {
  $professor_id = $_POST['professor_id'];
} else {
  die("No professor selected. <a href='instructorsProfiles.php'>Go back</a>");
}

// Fetch professor name
$profQuery = $conn->query("SELECT name, role, prof_img FROM professors WHERE id = '$professor_id'");
if ($profQuery->num_rows > 0) {
    $profData = $profQuery->fetch_assoc();
    $professor_name = $profData['name'];
    $profrole = $profData['role'];
    $prof_img = $profData['prof_img'];
} else {
    die("Invalid professor selected. <a href='instructorsProfiles.php'>Go back</a>");
}

// Prepare SQL query to fetch feedback for the logged-in user
$query = "SELECT ie.feedback, ie.submitted_at, r.fname AS student_name, lname, r.picture AS student_image
          FROM instructor_evaluation ie
          JOIN registration r ON ie.user_id = r.id
          WHERE ie.professor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbackData = [];

while ($row = $result->fetch_assoc()) {
    $feedbackData[] = [
        'feedback' => $row['feedback'],
        'submitted_at' => $row['submitted_at'],
        'student_name' => $row['student_name'],
        'lname' => $row['lname'],
        'student_image' => !empty($row['student_image']) ? $row['student_image'] : "images/default_user.jpg" // Default profile image
    ];
}

$defimage = 'images/facultyb.png';

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
    <title>Instructor</title>
    <style>
    body {
        display: flex;
    justify-content: center;
    align-items: center;
    min-height: 95vh;
    background: 
        linear-gradient(rgb(76, 76, 209), rgba(125, 125, 233, 0.5)),
        url('images/malabon-1.jpg') no-repeat;
    background-size: cover;
    background-position: center;
    background-blend-mode: multiply; 
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
      z-index: 99;
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
        margin-top: 0px;
  
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
    position: absolute;
    top: 4px;
    right: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
      }
      /* Container for the logo and dropdown */
.logout-container {
    position: relative;
    display: inline-block;
      }
      
      /* Style for the logo/image */
      .logout-logo {
    width: 40px;  /* Adjust the size of the logo */
    cursor: pointer;
    border-radius: 20px;
      }
      
      /* Style for the dropdown (initially hidden) */
.logout-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;  /* Adjust as per the size of your logo */
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-radius: 8px;
    min-width: 100px;
      }
      
.logoutbb {
    display: flex;
    align-items: center;
    gap: 5px;
      }
      
.logoutb2 {
    width: 25px;  /* Adjust the size of the logo */
    cursor: pointer;
      }
      
      /* Style for the logout link */
.logout-link {
    color: #f00;
    text-decoration: none;
    font-size: 16px;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    cursor: pointer;
      }
      
      /* Change color on hover */
.logout-link:hover {
    color: #c00;
      }


/* Main Container */
.container {
width: 90%;
max-width: 800px;
margin: 20px auto;
background: white;
padding: 20px;
border-radius: 12px;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
margin: 80px auto;
}

/* Avatar & Name Section */
.avnamecon {
background: linear-gradient(135deg, #1976d2, #64b5f6); /* Blue gradient */
padding: 15px;
margin-bottom: 20px;
border-radius: 10px;
color: white;
display: flex;
flex-direction: column;
}

.avname {
display: flex;
align-items: center;
}

.avname img {
width: 100px;
height: 100px;
border-radius: 50%;
background: white;
padding: 3px;
margin-right: 10px;
box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.pc {
display: flex;
gap: 10px;
}

.prof-name{
font-size: 18px;
font-weight: bold;
}

.subject {
margin-top: 10px;
font-size: 16px;
margin-left: 23px;
opacity: 0.9;
}

/* Comment & Details Section */
.comdent {
display: flex;
justify-content: space-between;
flex-wrap: wrap;
gap: 15px;
}

/* Individual Cards */
.section {
width: 30%;
height: 150px;
padding: 15px;
background: #ffffff;
border-radius: 10px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
display: inline-block;
vertical-align: top;
transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.section:hover {
transform: translateY(-5px);
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Headings */
h3 {
font-size: 18px;
color: #1976d2;
margin-bottom: 10px;
border-bottom: 2px solid #64b5f6;
padding-bottom: 5px;
}

/* Text */
p {
font-size: 16px;
color: #333;
word-wrap: break-word;
}

.comment-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.comment-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.comment-text {
    flex: 1;
}

.comment-text strong {
    font-size: 14px;
    color: #1976d2;
}

.comment-text p {
    margin: 5px 0;
    font-size: 14px;
    color: #333;
}

.comment-text small {
    font-size: 12px;
    color: #666;
}

.user {
    color: white;
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
      
  <li><a class="a-bar"href="home.php">Home</a></li>
  <li><a class="a-bar"href="HTML/gallery.html">Objectives</a></li>
  <li><a class="a-bar"href="#">Announcement</a></li>
  <li><a class="a-bar"href="HTML/profile.html">Rules and Regulation</a></li>
  <li><a class="a-bar"href="upf.php">Profile</a></li>
       
</ul>

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
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></h4> 
    </div>
         
</div>
</nav>

    <div class="container">
        
        <div class="avnamecon">
        <div class="avname">
            
        <img src="<?php echo !empty($prof_img) ? htmlspecialchars($prof_img) : $defimage; ?>" 
                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                 width="150" height="150">
         <div class="pc">
         <h5 class="prof-name"> <?php echo $professor_name; ?></h5>
         </div>
        </div>
        <h5 class="subject"><?php echo $profrole; ?></h5>
        </div>
        
        
        <!-- Display Submitted Data -->
        <div class="comdent">
        <div class="section">
            <h3>Student's</h3>
            <p><?php echo $name; ?></p>
        </div>

          <div class="section">
            <h3>Comments</h3>
            <div style="max-height: 80px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; width: 92%; min-width: 100px;">
                <?php
                foreach ($feedbackData as $comm) {
                    echo '<div class="comment-box">';
                    echo '<img src="' . htmlspecialchars($comm['student_image']) . '" alt="User" class="comment-img">';
                    echo '<div class="comment-text">';
                    echo '<strong>' . htmlspecialchars($comm['student_name']) . " " . htmlspecialchars($comm['lname']) . '</strong><br>';
                    echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                    echo '<small>' . htmlspecialchars($comm['submitted_at']) . '</small>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="section">
            <h3>Rating</h3>
            <p><?php echo $rating; ?></p>
        </div>
        
        </div>
 

    </div>
    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>
    <script src="js/logs.js"></script>
</body>
</html>