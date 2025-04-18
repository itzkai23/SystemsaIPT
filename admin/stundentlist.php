<?php
session_start();
require '../connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../Authentication/home.php");
    exit();
}

// Search logic
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT id, fname, lname, email, contact, section FROM registration WHERE is_admin = 0";

if (!empty($search)) {
    $query .= " AND (fname LIKE '%$search%' OR lname LIKE '%$search%' OR email LIKE '%$search%')";
}


$result = mysqli_query($conn, $query);

// Get the total number of students
$student_count = ($result) ? mysqli_num_rows($result) : 0;

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
    <title>Student List</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/studentlist.css">
    <link rel="stylesheet" href="../css/headmenu.css">
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
     <li><a class="a-bar"href="../students_interface/home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
     <li><a class="a-bar"href="../students_interface/instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
     <li><a class="a-bar"href="../students_interface/upf.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
       
</ul>

<div class="right-section">                              
<div class="mid-section">
         <a href="../students_interface/home.php" class="home">Home</a>
         <a href="../students_interface/instructorsProfiles.php" class="pf">Faculty</a>
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
                <a class="a-pf" href="#">Announcement</a>
                </div>
                
                <div class="dlog-icon">
                 <img src="../images/facultyb.png" alt="">
                <a class="a-pf" href="../students_interface/instructorsProfiles.php">Faculty</a>
                </div>

           <div class="logoutbb">
             <a href="../Authentication/logout.php"><img src="../images/logoutb.png" class="logoutb2"></a>
             <a href="../Authentication/logout.php" class="logout-link">Logout</a>
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
                    <th>Section</th>
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
                        echo "<td>" . htmlspecialchars($row['section']) . "</td>";
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
    <script src="../js/sidebar.js"></script>
    <script src="../js/logs.js"></script>
</body>
</html>
