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

    <style>

body { 
    /* display: flex;
    justify-content: center;
    align-items: center; */
    font-family: Roboto, sans-serif;
    min-height: 97vh;
    background: 
        linear-gradient(rgb(76, 76, 209), rgba(125, 125, 233, 0.5)), /* Gradient overlay */
        url('images/malabon-1.jpg') no-repeat; /* Background image */
    background-size: cover;
    background-position: center;
    background-blend-mode: multiply; /* Blending the gradient with the image */
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
      }
    
.left-section {
      width: 400px;
      display: flex;
      align-items: center;
      font-size: 21px;
      font-family: Racing Sans One;
      }
  
.right-section {
      width: 120px;
      margin-right: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
      background-color: rgb(10, 0, 104);
      }

.user {
      color: white;
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
    top: 2px;
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
    background-color: orange;
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
.box{
    width: 100%;
    height: 80vh;
    padding-top: 20px;
    position: relative;
}

.box {
    height: 50%;
    width: 90%;
}        

.repcomcon {
    background-color: lightblue;
    margin: 20px;
    width: 40%;
    padding: 10px;
    display: inline-block;
}

/* Comments Section */
/* #comments-container, #report-container {
    padding-top: 20px;
}

.comment, .report{
    padding: 10px;
    background-color: #e9f7e9;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 5px;
}

#comments-list, #report-list{
    margin-top: 10px;
} */

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        width: 90%;
    }

    textarea {
        font-size: 14px;
    }

    button {
        font-size: 14px;
    }
}

#preview { max-width: 100%; display: none; margin-top: 10px; }     

.profile_section{
    position: relative;
    margin:auto;
}




/* General Styles */

/* Navigation */
.navigation {
    display: flex;
    justify-content: flex-end;
    background-color: transparent;
    padding: 10px 20px;
    width: 95%;
    text-align: center;
    margin-left: 10px;
}

.navigation a {
    margin-left: 30px;
    padding: 5px 10px;
    width: 6%;
    font-size: 18px;
    font-weight: 700;
    text-decoration: none;
    color: white;
    border: solid 1px white;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.navigation .pf:hover {
    background-color: goldenrod;
    color: rgb(11, 0, 114);
}

.navigation .home {
    background-color: goldenrod;
    color: rgb(11, 0, 114);
}

/* Profile Section */
.maincon-userpf {
    /* width: 100%;
    margin-top: 30px;
    text-align: center; */
    width: 100%;
    text-align: center;
    margin-top: 60px;
}

.pfcon {
    padding: 30px;
    background-color: rgb(10, 0, 104);
    width: 25%;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: inline-block;
    margin: 20px auto;
    margin-right: 20px;
    color: white;
}

.pfcon img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    margin-bottom: 10px;
}

.pfcon h4 {
    font-size: 22px;
    font-weight: bold;
    color: white;
}

.pfcon:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Comments & Reports Section */
.repcomcon {
    background-color: rgb(10, 0, 104);
    margin: 20px auto;
    width: 40%;
    padding: 10px;
    display: inline-block;
    border-radius: 10px;
}

#comments-container, #report-container {
    padding-top: 20px;
}

.comment, .report {
    padding: 10px;
    background-color: #e9f7e9;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 5px;
}

#comments-list, #report-list {
    margin-top: 10px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .pfcon {
        width: 40%;
    }

    .repcomcon {
        width: 60%;
    }
}

@media (max-width: 768px) {
    .navigation {
        flex-direction: column;
        align-items: center;
    }

    .navigation a {
        width: auto;
        padding: 8px 12px;
    }

    .pfcon {
        width: 70%;
    }

    .repcomcon {
        width: 80%;
    }
}

@media (max-width: 480px) {
    .home-header {
        flex-direction: column;
        height: auto;
        padding: 10px;
    }

    .navigation {
        width: 100%;
    }

    .pfcon {
        width: 90%;
    }

    .repcomcon {
        width: 90%;
    }

    .sidebar {
        width: 200px;
    }
}

.pfcom-con {
    margin-top: 30px;
}
#comments-list {
        max-height: 200px; /* Adjust height as needed */
        overflow-y: auto; /* Enables scrolling */
        border: 1px solid #ccc; /* Optional: adds a border */
        padding: 10px; /* Adds spacing */
        background-color: #f9f9f9; /* Optional: light background */
        border-radius: 5px; /* Optional: rounded corners */
    }

    .comment {
        margin-bottom: 10px; /* Adds spacing between comments */
        padding: 8px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .comment h4 {
        margin-bottom: 0px;
        text-align: left;
    }

    .comment h6 {
        margin-top: 5px;
        color: rgb(104, 104, 104);
    }

    .repcomcon h3 {
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
  <li><a class="a-bar"href="#">Objectives</a></li>
  <li><a class="a-bar"href="#">Announcement</a></li>
  <li><a class="a-bar"href="#">Rules and Regulation</a></li>
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
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']); ?></span></h4> 
    </div>
         
</div>
</nav>
    
    <div class="maincon-userpf">
        <div>
            <div class="navigation">
                <a href="home.php" class="pf">Home</a>
                <a href="upf.php" class="home">Profile</a>
                <a href="instructorsProfiles.php" class="pf">Faculty</a>
                </div>
        </div>
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
<!-- 
  <script>
        document.getElementById('upload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('preview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    img.src.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script> -->

</body>
</html>