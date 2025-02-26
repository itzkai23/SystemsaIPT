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
    margin: 0;
    padding: 0;
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
margin: 80px auto;
background: rgba(255, 255, 255, 0.1); 
backdrop-filter: blur(5px);
padding: 20px;
border-radius: 12px;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
z-index: 0;
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

/* User comments to instructor profile (Only Text) */
.comment-post {
    background: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    display: flex;
    gap: 15px;
    align-items: center;
    transition: box-shadow 0.3s;
}

.comment-post:hover {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
}

.comment-post label {
    border: 1px solid #e6e6e6;
    border-radius: 30px;
    /* width: 65%;
    height: 50%;
    padding: 10px 0px 10px 15px; */
    cursor: pointer;
    width: 100%;
    padding: 10px 15px;
    background: #f8f9fa;
    transition: background 0.3s;
}

.comment-post label:hover {
    background-color: #e6e6e6;
}

.comment-post img {
  width: 40px;
  border-radius: 20px;
}

/* POP-UP USER COMMENT */

/* Hidden Checkbox */
.modal-toggle_usercom {
    display: none;
}

/* Modal Styles */
.modal_usercom {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 14px;
}

/* Modal Content */
.modal-content_usercom {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    height: 100%;
    max-width: 600px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
    /* text-align: left;
    max-height: 70vh; 
    overflow-y: auto;  */
}


/* Close Button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    color: #aaa;
    cursor: pointer;
    text-decoration: none;
    padding: 7.5px 15px;
    border-radius: 20px;
}

.close:hover {
    color: black;
    background-color: rgb(196, 192, 192);
}

/* Show Modal When Checkbox is Checked */
.modal-toggle_usercom:checked + .modal_usercom {
    display: block;
}

.modal-content-usercom {
  text-align: center;
  background-color: white;
  margin: auto;
  margin-top: 9%;
  padding: 20px;
  border-radius: 10px;
  width: 50%;
  
  max-width: 500px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
}

/* .comment-label-section {
  width: 80%;
  padding: 15px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: inline-block;
  vertical-align: top;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-top: 50px;
} */

.modal-content-usercom div {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  align-items: center;
}

.modal-content-usercom img {
  width: 40px;
  border-radius: 20px;
}

.modal-content-usercom textarea {
    font-size: 15px;
    /* font-weight: 700;
    font-family: Roboto, sans-serif;
    color: rgb(122, 122, 122); */
    border-radius: 5px;
    width: 97%;
    height: 100px;
    /* cursor: text;
    display: block; */
    border: 1px solid #ccc;
    padding: 5px;
    outline: none;
    resize: none;
    /* overflow: auto;
    margin-bottom: 20px; */
}

/* Customize the scrollbar (For WebKit browsers like Chrome, Edge, Safari) */
textarea::-webkit-scrollbar {
width: 10px; /* Width of the scrollbar */
}

textarea::-webkit-scrollbar-track {
background: #f1f1f1; /* Background of the track */
border-radius: 5px;
}

textarea::-webkit-scrollbar-thumb {
background: #888; /* Color of the scroll thumb */
border-radius: 5px; /* Round edges */
}

textarea::-webkit-scrollbar-thumb:hover {
background: #555; /* Darker thumb on hover */
}


.modal-content-usercom button {
    width: 100%;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: not-allowed;
    font-size: 15px;
    font-weight: 700;
    font-family: Roboto, sans-serif;

}

textarea:not(:placeholder-shown) + button {
    background-color: rgb(57, 57, 255);
    color: white;
    cursor: pointer;
    border: none;
    transition: background-color 0.2s ease;
}

/* POP-UP ALL USERS COMMENTS */

/* Hidden Checkbox */
.modal-toggle {
  display: none;
}
/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 10;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 0px;
  border-radius: 13px;

  /* display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3); */
}
/* Modal Content */
.modal-content {
  background-color: white;
  margin: auto;
  margin-top: 80px;
  padding: 20px;
  border-radius: 10px;
  width: 80%;
  height: 60%;
  max-width: 600px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
}
/* Hide the modal by default */


/* Show modal when checkbox is checked */
#termsCheckbox:checked ~ .modal {
    visibility: visible;
    opacity: 1;
}

/* Smooth pop effect */
#termsCheckbox:checked ~ .modal .modal-content {
    transform: scale(1);
}

.modal-toggle:checked + .modal {
    display: block;
}

/* Close button */
.close {
    float: right;
    font-size: 24px;
    cursor: pointer;
}

.close:hover {
    color: black;
    background-color: rgb(196, 192, 192);
}

/* Show Modal When Checkbox is Checked */

/* Comment & Details Section */
.comdent {
display: flex;
justify-content: space-between;
flex-wrap: wrap;
gap: 15px;
}

/* .boxes{
  display: flex;
  flex-direction: row;
  gap:148px;
} */

/* Individual Cards */
.section {
width: 45%;
height: 170px;
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

.section2:hover {
transform: translateY(-5px);
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}


.con-rate {
display: flex;
justify-content: space-between;
align-items: center;
background: #f8f9fa;
padding: 15px;
border-radius: 10px;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
font-family: Arial, sans-serif;
max-width: 400px;
margin: 10px auto;
}

.rate-total, .rate-stars {
display: flex;
align-items: center;
gap: 2px;
font-size: 15px;
color: #333;
}

.rate-total strong {
color: #007bff;
font-size: 18px;
}

.stars {
font-size: 18px;
color: #f1c40f; /* Gold color for stars */
}

.user-participant {
background: #eef2f3;
padding: 10px 15px;
border-radius: 8px;
font-size: 16px;
color: #333;
display: flex;
align-items: center;
gap: 6px;
box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
max-width: 328px;

}

.user-participant strong {
color: #28a745; /* Green color to highlight the number */
font-size: 18px;
}

.label-section {
  width: 95%;
  height: 78%;
  padding: 15px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: inline-block;
  vertical-align: top;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-top: 0px;
  margin-left: 0px;
  overflow: scroll;
}

.label-section::-webkit-scrollbar {
  width: 6px;
}

.label-section::-webkit-scrollbar-thumb {
  background: #007bff;
  border-radius: 10px;
}

.label-section::-webkit-scrollbar-track {
  background: #f1f1f1
}

.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
    margin-left: 60px;
}
.rating input {
    display: none;
}
.rating label {
    font-size: 1.5rem;
    color: #ccc;
    cursor: pointer;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: gold;
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

.com-scroll {
  max-height: 80px; 
  overflow-y: scroll; 
  border: 1px solid #ccc; 
  padding: 10px; 
  width: 92%;
  cursor: pointer; 
}

.com-scroll::-webkit-scrollbar {
  width: 8px;
}

.com-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.com-scroll::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.com-scroll::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Comment box layout */
  .comment-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f9f9f9;
  padding: 10px;
  border-radius: 8px;
  margin-top: 10px;
  
  }


  .comment-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  }

  .comment-text {
  flex-grow: 1;
  }

  .comment-text strong {
  font-size: 14px;
  }

  .comment-text p {
  font-size: 15px;
  font-weight: 800;
  margin: 5px 0;
  }

  .comment-text small {
  font-size: 11px;
  color: gray;
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
        
        <section class="comment-post">
          <img src="<?php echo htmlspecialchars($current_image); ?>" alt="Profile pic">
          <label for="termsCheckbox_usercomment" class="rant-post" type="text">Comment your concerns</label>
        </section>

        <input type="checkbox" id="termsCheckbox_usercomment" class="modal-toggle_usercom">
        <div class="modal_usercom">
          <div class="modal-content-usercom">
            <label for="termsCheckbox_usercomment" class="close">&times;</label>
            
              <h3>Your comment/concerns</h3>
                    <div>
                    <img src="<?php echo htmlspecialchars($current_image); ?>" alt="picture">
                    <p>Name of user</p>
                    </div>                  
                    <textarea name="" id="" placeholder="What's your concern?"></textarea>
                    
                    <button>Comment</button>

          </div>  
        </div>

        
    <!-- Display Submitted Data -->
    <div class="comdent">
        <label for="termsCheckbox" class="section">
            <h3>Comments</h3>
            <div class="com-scroll">
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
        </label>

<!-- Rating Section -->
<div class="section">
  <h3>Rating</h3>
  <p></p>

  <div class="con-rate">
    <div class="rate-total">
        <span>Ratings:</span> <strong>120</strong>
    </div>
    <div class="rate-stars">
        <span>Stars Rating:</span> 
        <div class="stars">
            ⭐⭐⭐⭐☆
        </div>
    </div>
</div>
  
<div class="user-participant">
    <span>Users Participating:</span> <strong>150</strong>

    <div class="rating">
      <input type="radio" id="star5" name="rating" value="5">
      <label for="star5">★</label>
      
      <input type="radio" id="star4" name="rating" value="4">
      <label for="star4">★</label>
      
      <input type="radio" id="star3" name="rating" value="3">
      <label for="star3">★</label>
      
      <input type="radio" id="star2" name="rating" value="2">
      <label for="star2">★</label>
      
      <input type="radio" id="star1" name="rating" value="1">
      <label for="star1">★</label>
  </div>
</div>

  
</div>

    </div>


<!-- Hidden Checkbox to Trigger Modal -->
<input type="checkbox" id="termsCheckbox" class="modal-toggle">

<!-- Modal Structure -->
<div class="modal">
    <div class="modal-content">
        <h3>Comments</h3>
        <label for="termsCheckbox" class="close">&times;</label>
        <div class="label-section">
            <!-- <h3>Comments</h3> -->
            <div class="modal-scroll">
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
    </div>
</div>

    
</div>

    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>
    <script src="js/logs.js"></script>
</body>
</html>