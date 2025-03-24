<?php
session_start();

// Ensure only admins can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #e3f2fd; /* Light blue background */
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
    margin-top: 3px;
    margin-right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
      }

/* Container for the logo and dropdown */
.logout-container {
    position: relative;
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

/* Admin Section */
.admin-sec {
    background: linear-gradient(135deg, #1976d2, #64b5f6); /* Gradient Blue */
    color: white;
    padding: 20px;
    margin: 80px 80px 10px 80px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.fp {
    font-size: 22px;
    font-weight: bold;
}

.fdiv {
    background-color: rgba(255, 255, 255, 0.2);
    padding: 15px;
    border-left: solid 3px white;
    border-radius: 8px;
    margin-top: 10px;
}

/* Grid Container */
.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
    gap: 20px;
    padding: 20px;
    margin: 20px 60px 20px 60px;
}

/* Divider (Box) */
.divider {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.divider:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Links */
.divider-link {
    text-decoration: none;
    font-size: 18px;
    color: #1976d2;
    font-weight: bold;
    display: block;
    margin-bottom: 10px;
    transition: color 0.3s ease;
}

.divider-link:hover {
    color: #0d47a1;
}

/* Icons */
.divider img {
    width: 60px;
    height: 60px;
    object-fit: cover; 
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
      <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']);?></span></h4> 
    </div>
         
</div>
</nav>
    <div class="admin-sec">
        <p class="fp">Welcome Administrator!</p>
        <div class="fdiv">
        <h5>Academic Year: 2024-2025</h5>
        <p>Evaluation Status: On-going</p>  
        </div>
    </div>
    
    <div class="container">
        <div class="divider">
            <a href="instructorsProfiles.php" class="divider-link">Faculties Profiles</a>
            <img src="images/facultyb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="stundentlist.php" class="divider-link">Students List</a>
            <img src="images/usersb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="eval_record.php" class="divider-link">Evaluations Records</a>
            <img src="images/feedb.png" alt="icon">
        </div>
        
        <div class="divider">
            <a href="admin_reports.php" class="divider-link">Report</a>
            <img src="images/reportb.png" alt="icon">
        </div>
    </div>
    
<script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
    
</body>
</html>