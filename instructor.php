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

// Fetch professor details
$profQuery = $conn->prepare("SELECT name, role, prof_img FROM professors WHERE id = ?");
$profQuery->bind_param("i", $professor_id);
$profQuery->execute();
$profResult = $profQuery->get_result();

if ($profResult->num_rows > 0) {
    $profData = $profResult->fetch_assoc();
    $professor_name = $profData['name'];
    $profrole = $profData['role'];
    $prof_img = $profData['prof_img'];
} else {
    die("Invalid professor selected. <a href='instructorsProfiles.php'>Go back</a>");
}

// Query to calculate average score and evaluation count
$avgQuery = "
SELECT 
    AVG((q1 + q2 + q3 + q4 + q5) / 5.0) AS professor_avg_score,
    COUNT(*) AS evaluation_count
FROM instructor_evaluation
WHERE professor_id = ?;
";

$avgStmt = $conn->prepare($avgQuery);
$avgStmt->bind_param("i", $professor_id);
$avgStmt->execute();
$avgResult = $avgStmt->get_result();
$avgData = $avgResult->fetch_assoc();

// Extract average score and evaluation count
$professor_avg_score = $avgData['professor_avg_score'];
$evaluation_count = $avgData['evaluation_count'];

// Prepare the main query to fetch feedback and comments
$query = "
SELECT 
    ie.feedback, 
    ie.submitted_at AS date_posted, 
    NULL AS comment, 
    NULL AS comment_id,  
    r.fname AS student_name, 
    r.lname, 
    r.picture AS student_image
FROM instructor_evaluation ie
JOIN registration r ON ie.user_id = r.id
WHERE ie.professor_id = ?

UNION ALL

SELECT 
    NULL AS feedback, 
    c.created_at AS date_posted,
    c.comment, 
    c.id AS comment_id,  
    r.fname AS student_name, 
    r.lname, 
    r.picture AS student_image
FROM comments c
JOIN registration r ON c.user_id = r.id
WHERE c.professor_id = ?

ORDER BY date_posted DESC;";  // Latest entries first

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $professor_id, $professor_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbackData = [];

while ($row = $result->fetch_assoc()) {
    $feedbackData[] = [
        'feedback' => !empty($row['feedback']) ? $row['feedback'] : null,
        'comment' => !empty($row['comment']) ? $row['comment'] : null,
        'comment_id' => isset($row['comment_id']) ? $row['comment_id'] : null,
        'date_posted' => $row['date_posted'],
        'student_name' => $row['student_name'],
        'lname' => $row['lname'],
        'student_image' => !empty($row['student_image']) ? $row['student_image'] : "images/icon.jpg"
    ];
}

$totalCount = count($feedbackData);

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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    
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
    margin-top: 3px;
    margin-right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
      }
      /* Container for the logo and dropdown */
.logout-container {
    position: relative;
    /* display: inline-block; */
    
      }

      /* Style for the logo/image */
.piclog {
width: 35px;
height: 35px;
border-radius: 17.5px;
object-fit: cover;
border: 1px solid goldenrod;
cursor: pointer;
  }
      
      /* Style for the dropdown (initially hidden) */
.logout-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 50px;  /* Adjust as per the size of your logo */
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-radius: 8px;
    min-width:  250px;
      }
      
.logoutbb {
    display: flex;
    align-items: center;
    gap: 23px;
    margin-left: 2px;
    margin-top: 7px;
      }
      
.logoutb2 {
    width: 25px;  /* Adjust the size of the logo */
    cursor: pointer;
      }
      
      /* Style for the logout link */
.logout-link {
    color: #f00;
    text-decoration: none;
    font-size: 17.5px;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    cursor: pointer;
      }
      
      /* Change color on hover */
.logout-link:hover {
    color: #c00;
      }
.logpf-con {
  display: flex;
  align-items: center;
  gap: 15px;
  width: 95%;
  border-radius: 5px;
  padding: 0px 0px 0px 10px;
  text-decoration: none;
  margin-bottom: 10px;
}
.a-pf:hover,.logpf-con:hover {
  background-color: rgb(236, 236, 236);
  transition: 0.5s ease;
  cursor: pointer;
}
.logpf-con img {
  width: 30px;
  height: 30px;
  border-radius: 15px;
  border: 1px solid blue;
}     
.logpf-con h4 {
  font-size: 18px;
  font-family: "Roboto", sans-serif;
  font-weight: 500;
  color: rgb(54, 54, 54);
}
.dlog-icon {
  display: flex;
  align-items: center;
  gap: 10px;
}
.dlog-icon img {
  width: 30px;
  border-radius: 15px;
}
.a-pf {
  font-size: 17px;
  font-weight: 400;
  font-family: "Roboto", sans-serif;
  text-decoration: none;
  color: rgb(19, 19, 19);
  display: block;
  margin-bottom: 2px;
  margin-left: 2px;
  width: 100%;
  padding: 8px;
  border-radius: 5px;
}

.user {
    color: white;
    margin-top: 15px;
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
/* background: linear-gradient(135deg, #1976d2, #64b5f6);  */
/* Blue gradient */
background-color: #FFA500;
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
display: block;
}

.prof-name{
font-size: 18px;
font-weight: bold;
color: black;
font-family: "Roboto", sans-serif;
font-weight: 600;
margin-bottom: 5px;
margin-top: auto;
}

.pro-role {
margin-top: 2px;
margin-bottom: 0px;
font-size: 16px;
font-family: "Roboto",sans-serif;
font-weight: 600;
opacity: 0.9;
}

.report-btn {
  position: absolute;
  top: 35px;
  right: 35px;
  display: inline-flex;
  align-items: center;
  gap: 8px; /* Space between icon & text */
  text-decoration: none;
  font-size: 16px;
  font-weight: bold;
  background: red; /* Flat red background */
  color: white;
  padding: 10px 15px;
  border-radius: 5px;
  transition: background 0.3s ease-in-out;
  font-family: 'Roboto', sans-serif;
}

.report-btn:hover {
  background: darkred;
}

/* User comments to instructor profile (Only Text) */
.comment-post {
    background: rgb(31, 31, 31);
    padding: 15px;
    margin-bottom: 20px;
    /* border: 1px solid #ddd; */
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
  width: 35px;
  height: 35px;
  border-radius: 17.5px;
  object-fit: cover;
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
  background-color: rgb(31, 31, 31);
  margin: auto;
  margin-top: 9%;
  padding: 20px;
  border-radius: 10px;
  width: 50%;
  
  max-width: 500px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
}

.modal-content-usercom div {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  align-items: center;
}

.your-com {
  width: 30px;
  height: 30px;
  border-radius: 15px;
  object-fit: cover;
}

.modal-content-usercom textarea {
    font-size: 15px;
    
    border-radius: 5px;
    width: 97%;
    height: 100px;
    border: 1px solid #ccc;
    padding: 5px;
    outline: none;
    resize: none;
    
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
    background-color: #1C1E21;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: not-allowed;
    font-size: 15px;
    font-weight: 700;
    font-family: Roboto, sans-serif;
    margin-top: 5px;
}

textarea:not(:placeholder-shown) + button {
    background-color: #333639;
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
}
/* Modal Content */
.modal-content {
  background-color: rgb(31, 31, 31);
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

.cname {
  color: white;
}

/* Show Modal When Checkbox is Checked */

/* Comment & Details Section */
.comdent {
display: flex;
justify-content: space-between;
flex-wrap: wrap;
gap: 15px;
}

/* Individual Cards */
.section {
width: 45%;
height: 170px;
padding: 15px;
background: rgb(26, 26, 26);
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


.user-participant {
background: #eef2f3;
padding: 10px 15px;
border-radius: 8px;
font-size: 16px;
color: #333;
display: flex;
align-items: center;
gap: 15px;
box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
max-width: 328px;
justify-content: center;
}

.user-participant strong {
color: #28a745; /* Green color to highlight the number */
font-size: 18px;
}

.label-section {
  width: 95%;
  height: 78%;
  padding: 15px;
  background:rgb(31, 31, 31);
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
  background: #888;
  border-radius: 10px;
}

/* .label-section::-webkit-scrollbar-track {
  background: #555;
} */

/* .rating {
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
} */

/* Comments and Average Container */
h3 {
font-size: 18px;
color: white;
margin-bottom: 10px;
border-bottom: 2px solid white;
padding-bottom: 5px;
}

h4 {
font-size: 18px;
color:rgb(255, 152, 33);
margin-bottom: 10px;
padding-bottom: 5px;
text-align: center;
}

/* Text */
p {
font-size: 16px;
color: #333;
word-wrap: break-word;
}

.com-scroll {
  max-height: 100px; 
  overflow-y: scroll; 
  border: 1px solid #ccc; 
  padding: 2px; 
  width: 100%;
  cursor: pointer; 
}

.com-scroll::-webkit-scrollbar {
  width: 8px;
}

/* .com-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
} */

.com-scroll::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.com-scroll::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Comment box layout 111111 */
.comment-box1 {
  display: flex;
  /* align-items: center; */
  align-items: flex-start; /* Align items to the top */
  gap: 10px;
  background:rgb(31, 31, 31);
  padding: 5px;
  border-radius: 8px;
  margin-top: 5px;
  }

  .comment-text1 {
  background-color:rgb(43, 43, 43);
  margin-bottom: 0px;
  padding: 10px;
  border-radius: 15px;
  max-width: 270px;
  max-height: none; /* Remove max-height if no limit is needed */
  word-wrap: break-word; /* Ensure words break properly */
  white-space: normal; /* Prevent the text from overflowing horizontally */
  }

  .comment-text1 strong {
  font-size: 14px;
  display: block; /* Ensures it appears above */
  color: white;
  }

  .comment-text1 p {
  font-size: 15px;
  font-weight: 300;
  margin-top: -15px;
  margin-bottom: 0px;
  color: white;
  }

  .comment-text1 small {
  font-size: 8px;
  color: gray;
  margin-top: 2px;
  display: block;
  color: white;
  }


/* Comment box layout*/
  .comment-box {
  display: flex;
  /* align-items: center; */
  align-items: flex-start; /* Align items to the top */
  gap: 10px;
  background:rgb(31, 31, 31);
  padding: 5px;
  border-radius: 8px;
  margin-top: 5px;
  }


  .comment-img {
  width: 30px;
  height: 30px;
  border-radius: 15px;
  object-fit: cover;
  flex-shrink: 0; /* Prevent shrinking */
  }

  .comment-text {
  max-width: 490px;
  background-color:rgb(43, 43, 43);
  margin-bottom: 0px;
  
  padding: 10px;
  border-radius: 15px;
  }

  .comment-text strong {
  font-size: 14px;
  display: block; /* Ensures it appears above */
  color: white;
  }

  .comment-text p {
  font-size: 15px;
  font-weight: 300;
  margin-top: -15px;
  margin-bottom: 0px;
  color: white;
  }

  .comment-text small {
  font-size: 8px;
  color: gray;
  margin-top: 2px;
  display: block;
  color: white;
  }



  .menu-container {
    position: relative;
    display:flexbox;
}

.menu-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    /* display: inline-block; */
    position: absolute;
    top: -35px;
    right: 10px;
}

.menu-options {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    min-width: 100px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

.menu-options form {
    margin: 0;
    padding: 5px;
}

.menu-options button {
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 5px 10px;
    cursor: pointer;
}

.menu-options button:hover {
    background: #f2f2f2;
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
      
     <li><a class="a-bar"href="#">Home</a></li>
     <li><a class="a-bar"href="instructorsProfiles.php">Faculty</a></li>
     <li><a class="a-bar"href="freedomwall.html">Newsfeed</a></li>
     <li><a class="a-bar"href="upf.php">Profile</a></li>
       
</ul>

<div class="right-section">                              
   
  <div class="logpos">
  
      <div class="logout-container"> 
        <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
        <div class="logout-dropdown" id="logoutDropdown">
                <a href="#" class="logpf-con">
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
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']);?></span></h4> 
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
         <h5 class="pro-role"><?php echo $profrole; ?></h5>
         </div>
         <!-- Add a Report Button -->
        <a href="report_prof.php?id=<?php echo urlencode($professor_id); ?>&name=<?php echo urlencode($professor_name); ?>&img=<?php echo urlencode($prof_img); ?>" 
        class="report-btn">Report</a>
        <!-- <i class="fas fa-flag"> -->
        </div>
        
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
                    <img src="<?php echo htmlspecialchars($current_image); ?>" class="your-com" alt="picture">
                    <p class="cname"><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></p>
                    </div>
                    <form action="comment.php" method="post">
                        <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">
                        <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
                        <textarea name="comment" id="" placeholder="What's your concern?"></textarea>            
                    <button type="submit">Comment</button>
                    </form>
          </div>  
        </div>

        
<!-- Display Submitted Data -->
<div class="comdent">
    <label for="termsCheckbox" class="section">
      <h3>Comments (<?php echo $totalCount; ?>)</h3> <!-- Display total count -->
        <div class="com-scroll">
                      <?php
              foreach ($feedbackData as $comm) {
                  // Display feedback if it exists
                  if (!empty($comm['feedback'])) {
                      echo '<div class="comment-box1">';
                      echo '<img src="' . htmlspecialchars($comm['student_image']) . '" alt="User" class="comment-img">';
                      echo '<div class="comment-text1">';
                      echo '<strong>' . htmlspecialchars($comm['student_name']) . " " . htmlspecialchars($comm['lname']) . '</strong><br>';
                      echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                      echo '<small>Evaluated: ' . htmlspecialchars($comm['date_posted']) . '</small>'; // Uses common date field
                      echo '</div>';
                      echo '</div>';
                  }

                  // Display comment if it exists
                  if (!empty($comm['comment'])) {
                      echo '<div class="comment-box1">';
                      echo '<img src="' . htmlspecialchars($comm['student_image']) . '" alt="User" class="comment-img">';
                      echo '<div class="comment-text1">';
                      echo '<strong>' . htmlspecialchars($comm['student_name']) . " " . htmlspecialchars($comm['lname']) . '</strong><br>';
                      echo '<p>' . htmlspecialchars($comm['comment']) . '</p>';
                      echo '<small>Commented on: ' . htmlspecialchars($comm['date_posted']) . '</small>'; // Uses common date field
                      echo '</div>';
                      echo '</div>';
                  }
              }
              ?>
        </div>
    </label>


<!-- Professors Average -->
<div class="section">
  <h3>Average Evaluation Points</h3>
  <h4>Current Status</h4>
  <div class="user-participant">
      <span>Number of Evaluations:</span> <strong><?php echo htmlspecialchars($evaluation_count); ?></strong>
      <span>Average:</span> <strong>
        <?php
        if ($evaluation_count > 0) {
            echo number_format($professor_avg_score, 2);
        } else {
            echo "No evaluations yet.";
        }
        ?>
      </strong>
  </div>
</div>


  
</div>

    </div>


<!-- Hidden Checkbox to Trigger Modal -->
<input type="checkbox" id="termsCheckbox" class="modal-toggle">

<!-- Modal Structure -->
<div class="modal">
    <div class="modal-content">
        <h3>Comments (<?php echo $totalCount; ?>)</h3> <!-- Display total count -->
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

                if (!empty($comm['feedback'])) {
                    // Display feedback without delete/report options
                    echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                    echo '<small>Evaluated: ' . htmlspecialchars($comm['date_posted']) . '</small>';
                } elseif (!empty($comm['comment'])) {
                    // Display comment WITH delete/report options
                    echo '<p>' . htmlspecialchars($comm['comment']) . '</p>';
                    echo '<small>Commented on: ' . htmlspecialchars($comm['date_posted']) . '</small>';

                    // Three-dot menu for comments ONLY
                    echo '<div class="menu-container">';
                    echo '<button class="menu-btn" onclick="toggleMenu(this)">&#x22EE;</button>';
                    echo '<div class="menu-options">';

                    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                        echo '<form action="delete_comment.php" method="post">
                                <input type="hidden" name="comment_id" value="' . htmlspecialchars($comm['comment_id']) . '">
                                <button type="submit" onclick="return confirm(\'Are you sure you want to delete this?\');">Delete</button>
                              </form>';
                    } else {
                        echo '<form action="report_comment.php" method="post">
                                <input type="hidden" name="comment_id" value="' . htmlspecialchars($comm['comment_id']) . '">
                                <button type="submit">Report</button>
                              </form>';
                    }

                    echo '</div>'; // Close .menu-options
                    echo '</div>'; // Close .menu-container
                }

                echo '</div>'; // Close .comment-text
                echo '</div>'; // Close .comment-box
            }
            ?>

            </div>
        </div>
    </div>
</div>

    
</div>

    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>

<script>
      function toggleMenu(button) {
    var menu = button.nextElementSibling; // Get the corresponding menu
    menu.style.display = (menu.style.display === "block") ? "none" : "block";

    // Close other open menus
    document.querySelectorAll(".menu-options").forEach(function (otherMenu) {
        if (otherMenu !== menu) {
            otherMenu.style.display = "none";
        }
    });
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    if (!event.target.matches(".menu-btn")) {
        document.querySelectorAll(".menu-options").forEach(function (menu) {
            menu.style.display = "none";
        });
    }
});
    </script>
</body>
</html>