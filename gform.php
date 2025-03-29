<?php
require 'connect.php';
require 'functions.php'; // Include the functions file
session_start();

// Prevent browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if professor_id is set in either the URL (GET) or the form submission (POST)
if (isset($_GET['professor_id'])) {
    $professor_id = $_GET['professor_id'];
} elseif (isset($_POST['professor_id'])) {
    $professor_id = $_POST['professor_id'];
} else {
    die("No professor selected. <a href='instructorsEval.php'>Go back</a>");
}

// Keep your existing default image
$default_image = "images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to evaluate. <a href='login.php'>Login</a>");
}

$user_id = $_SESSION['user_id']; // Consistently use user_id

// ✅ Check if the user can evaluate *ONLY* on initial page load (GET request)
if ($_SERVER["REQUEST_METHOD"] === "GET" && !canEvaluate($conn, $user_id, $professor_id)) {
    die("❌ You have already evaluated this professor. Please wait 30 days before evaluating again. <a href='instructorsEval.php'>Go back</a>");
}

// Fetch professor name
$profQuery = $conn->prepare("SELECT name FROM professors WHERE id = ?");
$profQuery->bind_param("i", $professor_id);
$profQuery->execute();
$result = $profQuery->get_result();

if ($result->num_rows > 0) {
    $profData = $result->fetch_assoc();
    $professor_name = $profData['name'];
} else {
    die("Invalid professor selected. <a href='instructorsEval.php'>Go back</a>");
}

// Default profile image
$default_image = "images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['comments'])) {
        die("All fields are required. <a href='gform.php?professor_id=$professor_id'>Go back</a>");
    }

    $comments = $conn->real_escape_string($_POST['comments']);
    $submitted_at = date("Y-m-d H:i:s");

    // ✅ Ensure user_id is used in the database insert query
    $stmt = $conn->prepare("INSERT INTO instructor_evaluation (user_id, professor_id, comments, submitted_at) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("iiss", $user_id, $professor_id, $comments, $submitted_at);

    if ($stmt->execute()) {
        // ✅ Redirect after successful evaluation
        echo "<script>alert('✅ Evaluation submitted successfully!'); window.location.href = 'instructorsEval.php';</script>";
        exit;
    } else {
        die("❌ Error submitting evaluation. <a href='gform.php?professor_id=$professor_id'>Try again</a>");
    }
}

// Fetch professors from the database
$sql = "SELECT id, name FROM professors";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Eval</title>
    
    <style>


* {
    margin: 0;
    padding: 0;
  }
  
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
    top: 10px;
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
.user {
    color: white;
    font-family: "Roboto", sans-serif;
    font-weight: 500;
    font-size: 15px;
}

.form-container {
    background-color: white;
    max-width: 650px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    margin-top: 5%;
}

.form-title {
    font-size: 26px;
    font-weight: bold;
    color: #1a5276;
    margin-bottom: 10px;
    text-align: center;
}

.p-val {
    text-align: center;
    margin-bottom: 10px;
}

.description {
    font-size: 14px;
    color: #555;
    margin-bottom: 15px;
    text-align: center;
}

.rating-scale {
    background: #b3e0f2;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    color: #154360;
    text-align: center;
    margin-bottom: 20px;
}

.rating-scale p {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

table {
    width: 100%;
    margin-top: 15px;
    border-collapse: collapse;
}

td {
    padding: 12px;
    text-align: left;
}

label {
    font-size: 16px;
    color: #1a5276;
    display: block;
    margin-bottom: 6px;
}

.scrollable-table-container {
        max-height: 300px; /* Adjust the height to your needs */
        overflow-y: auto;
        margin-bottom: 20px;
    }

.question-table {
    width: 100%;
    border-collapse: collapse;
}

.question-table td {
    padding: 10px;
    vertical-align: middle;
}

.question-table label {
    font-size: 16px;
}

.question-table .label-column {
    width: 50%; /* Ensures label stays on the left */
    text-align: left;
    font-weight: bold;
}

.question-table .radio-group {
    text-align: right; /* Aligns radio buttons to the right */
    display: flex;
    
}

.radio-group label {
    margin-right: 10px; /* Adds spacing between radio options */
}

input[type="radio"] {
    margin: 0 5px;
    transform: scale(1.2);
    accent-color: #1a5276;
}

.feedback, #feedback {
    text-align: left;
}
textarea {
    width: 45%;
    padding: 10px;
    border: 1px solid #bbb;
    border-radius: 6px;
    font-size: 16px;
    height: 100px;
    resize: none;
    margin-bottom: 20px;
}

.btn-con {
    text-align: right;
}

button {
    background-color: #1a5276;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    width: 20%;
    transition: 0.3s;
    margin-top: 10px;
}

button:hover {
    background-color: #154360;
}

@media (max-width: 768px) {
        .form-container {
            width: 100%;
            padding: 20px;
        }

        button {
            width: 100%;
        }

        .radio-group {
            justify-content: center;
        }
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

   <div class="form-container">
        <h1 class="form-title">Faculty Evaluation</h1>
        <p class="p-val">Evaluating: <?php echo $professor_name; ?></p>
        <p class="description">Your feedback helps us improve our teaching standards. Please rate the instructor based on the following criteria.</p>
        
        <div class="rating-scale">
            <p><strong>5</strong>- Very Satisfactory <strong>4</strong>- Satisfactory <strong>3</strong>- Neutral <strong>2</strong>- Unsatisfactory <strong>1</strong>- Very Unsatisfactory</p>
        </div>
        
        <form action="form_act.php" method="post">
           <div class="scrollable-table-container">
            <table class="question-table">
            <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">
            <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
            <tr>
                    <td class="label-column">
                        <label>How would you rate the instructor's ability to explain concepts clearly?</label>
                    </td>

                    <td class="radio-group">
                        <label><input type="radio" name="q1" value="5" required> 5</label>
                        <label><input type="radio" name="q1" value="4"> 4</label>
                        <label><input type="radio" name="q1" value="3"> 3</label>
                        <label><input type="radio" name="q1" value="2"> 2</label>
                        <label><input type="radio" name="q1" value="1"> 1</label>
                    </td>
                </tr>
                
                <tr>
                    <td class="label-column">
                        <label>How engaging was the instructor during lectures?</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q2" value="5" required> 5</label>
                        <label><input type="radio" name="q2" value="4"> 4</label>
                        <label><input type="radio" name="q2" value="3"> 3</label>
                        <label><input type="radio" name="q2" value="2"> 2</label>
                        <label><input type="radio" name="q2" value="1"> 1</label>
                    </td>
                </tr>

                <tr>
                    <td class="label-column">
                        <label>How well did the instructor manage the class?</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q3" value="5" required> 5</label>
                        <label><input type="radio" name="q3" value="4"> 4</label>
                        <label><input type="radio" name="q3" value="3"> 3</label>
                        <label><input type="radio" name="q3" value="2"> 2</label>
                        <label><input type="radio" name="q3" value="1"> 1</label>
                    </td>
                </tr>

                <tr>
                    <td class="label-column">
                        <label>How relevant and useful was the course material?</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q4" value="5" required> 5</label>
                        <label><input type="radio" name="q4" value="4"> 4</label>
                        <label><input type="radio" name="q4" value="3"> 3</label>
                        <label><input type="radio" name="q4" value="2"> 2</label>
                        <label><input type="radio" name="q4" value="1"> 1</label>
                    </td>
                </tr>

                <tr>
                    <td class="label-column">
                        <label>How would you rate the instructor's overall effectiveness?</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q5" value="5" required> 5</label>
                        <label><input type="radio" name="q5" value="4"> 4</label>
                        <label><input type="radio" name="q5" value="3"> 3</label>
                        <label><input type="radio" name="q5" value="2"> 2</label>
                        <label><input type="radio" name="q5" value="1"> 1</label>
                    </td>
                </tr>
            </table>
            
            <label for="feedback">Optional Feedback</label>
            <textarea id="feedback" name="feedback"></textarea>
            
            <div class="btn-con">
            <button type="submit">Submit</button>
        </div>
        
        </div>
        </form>
    </div>
<script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
</body>
</html>