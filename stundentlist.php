<?php
session_start();
require 'connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Search logic
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT id, fname, lname, email, contact, picture FROM registration WHERE is_admin = 0";

if (!empty($search)) {
    $query .= " AND (fname LIKE '%$search%' OR lname LIKE '%$search%' OR email LIKE '%$search%')";
}


$result = mysqli_query($conn, $query);

// Get the total number of students
$student_count = ($result) ? mysqli_num_rows($result) : 0;

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
    <title>Student List</title>
    <style>
        /* General Body Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    text-align: center;
    padding: 20px;
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
  text-align: left;
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

/* Container Styling */
.container {
    max-width: 800px;
    max-height: 500px;
    margin: auto;
    margin-top: 60px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

  /* Search Bar */
  .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 10px;
            width: 60%;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

/* Heading */
h2 {
    color: #1565c0;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

  /* Styling for student count */
  .student-count {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

/* Table Styling */

/* Container for scrolling the table body */
.table-container {
    max-height: 300px;
    width: 100%;
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    /* max-height: 300px; */
    table-layout: fixed;
}

tbody {
  /* max-height: 300px;
    overflow-y: auto;
    width: 100%;
    table-layout: fixed; */
    display: block;  /* Make the tbody a block element to allow scrolling */
    max-height: 300px; /* Set the maximum height of the tbody */
    overflow-y: auto; /* Make the tbody scrollable */
    width: 100%;
}

/* Customize the scrollbar (For WebKit browsers like Chrome, Edge, Safari) */
tbody::-webkit-scrollbar {
width: 10px; /* Width of the scrollbar */
}

tbody::-webkit-scrollbar-track {
background: #f1f1f1; /* Background of the track */
border-radius: 5px;
}

tbody::-webkit-scrollbar-thumb {
background: #1e88e5; /* Color of the scroll thumb */
border-radius: 5px; /* Round edges */
}

tbody::-webkit-scrollbar-thumb:hover {
background: #555; /* Darker thumb on hover */
}

/* Table Header */
th {
    background: linear-gradient(145deg, #1e88e5, #1565c0);
    color: white;
    font-size: 16px;
    padding: 14px;
    text-align: left;
}

/* Table Rows */
td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    font-size: 15px;
    color: #333;
    word-wrap: break-word; /* Ensure long words are wrapped */
    word-break: break-word; /* Break long words when necessary */
}

tr {
    display: table; /* Keep rows as table elements */
    width: 100%;
    table-layout: fixed;
}

/* Alternating Row Colors */
tr:nth-child(even) {
    background-color: #f0f7ff;
}

/* Hover Effect */
tr:hover {
    background-color: #e3f2fd;
    transition: background 0.3s ease-in-out;
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
      
     <li><a class="a-bar"href="admin.php">Home</a></li>
     <li><a class="a-bar"href="instructorsProfiles.php">Faculty</a></li>
     <li><a class="a-bar"href="freedomwall.html">Newsfeed</a></li>
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

<div class="container">
        <h2>Student List</h2>
         <!-- Display Total Student Count -->
         <p class="student-count">Total Students: <strong><?php echo $student_count; ?></strong></p>
        <!-- Search Bar -->
        <div class="search-container">
              <form method="GET" action="">
                  <input type="text" name="search" placeholder="Search name" value="<?php echo htmlspecialchars($search); ?>">
                  <button type="submit">Search</button>
                  <?php if (!empty($search)) : ?>
                      <a href="stundentlist.php"><button type="button">Clear</button></a>
                  <?php endif; ?>
              </form>
          </div> 
        <div class="table-container">
        <table id="studentTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($student_count > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['fname'] . " " . $row['lname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                        echo "<td>
                                <form action='delete_students.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this account?\");'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                    <button type='submit' style='background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;'>Delete</button>
                                </form>
                                </td>";
                        echo "</tr>";
                        
                    }
                } else {
                    echo "<tr><td colspan='3'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </div>

    </div>
    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>
</body>
</html>
