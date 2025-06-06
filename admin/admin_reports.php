<?php
include '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

// Handle "Mark as Reviewed" button submission for comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $updateQuery = $conn->prepare("UPDATE reports SET status = 'reviewed' WHERE id = ?");
    $updateQuery->bind_param("i", $report_id);
    $updateQuery->execute();
    $updateQuery->close();
    echo "<script>alert('Report marked as reviewed!'); window.location.href='admin_reports.php';</script>";
}

// Handle "Mark as Reviewed" button submission for professor reports
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prof_report_id'])) {
    $prof_report_id = intval($_POST['prof_report_id']);
    $updateProfQuery = $conn->prepare("UPDATE reports_prof SET status = 'reviewed' WHERE id = ?");
    $updateProfQuery->bind_param("i", $prof_report_id);
    $updateProfQuery->execute();
    $updateProfQuery->close();
    echo "<script>alert('Professor report marked as reviewed!'); window.location.href='admin_reports.php';</script>";
}

// Fetch reported comments
$reportsQuery = $conn->query("SELECT r.id AS report_id, c.comment AS reported_text, u.fname, u.lname, r.reported_at, r.status FROM reports r LEFT JOIN comments c ON r.comment_id = c.id JOIN registration u ON r.user_id = u.id ORDER BY r.reported_at DESC");

// Fetch reported professors
$profReportsQuery = $conn->query("SELECT rp.id AS prof_report_id, p.name AS professor_name, u.fname, u.lname, rp.reasons, rp.details, rp.report_date, rp.status FROM reports_prof rp JOIN professors p ON rp.professor_id = p.id JOIN registration u ON rp.user_id = u.id ORDER BY rp.report_date DESC");

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
    <title>Reported Comments & Professors</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/repcom.css">
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
     <li><a class="a-bar"href="register_prof.php"><i class="fas fa-user"></i><span>Faculty Registration</span></a></li>
       
</ul>

<div class="right-section">                              
<div class="mid-section">
         <!-- <a href="admin.php" class="home">Home</a>
         <a href="../students_interface/instructorsProfiles.php" class="pf">Faculty</a> -->
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

    <!-- <h3 class="styled-heading">Reported Comments & Evaluations</h3> -->
    <div class="repcomcon-btn">
    <a href="#" id="studentBtn" class="active" onclick="showReport('comm', this); return false;">Student Reports</a>
    <a href="#" id="professorBtn" onclick="showReport('prof', this); return false;">Professor Reports</a>
    </div>

    <div class="maincon-repcom">
    <div class="rec-com-container">
        <!-- Reported Comments Section -->
        <div id="report2" class="report-container">
            <h4 class="sticky-heading">Reported Comments</h4>
            <div class="report-grid"> <!-- ✅ NEW WRAPPER -->
            <?php while ($report = $reportsQuery->fetch_assoc()) : ?>
                <div class='report-box'>
                    <strong class="std-comment-report">Reported by <?php echo htmlspecialchars($report['fname'] . " " . $report['lname']); ?></strong>
                    <small class="date-report">Reported on: <?php echo htmlspecialchars($report['reported_at']); ?></small>
                    <div class="reported-text"> <?php echo htmlspecialchars($report['reported_text']); ?> </div>
                    
                    <?php if ($report['status'] === 'pending') : ?>
                        <form method="post">
                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                            <button type="submit">Mark as Reviewed</button>
                        </form>
                    <?php else : ?>
                        <span class="reviewed">Reviewed</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div> <!-- ✅ CLOSE WRAPPER -->
        </div>

        <!-- Reported Professors Section -->
        <div id="report1" class="report-container">
            <h4 class="sticky-heading">Reported Professors</h4>
            <div class="report-grid">
                <?php while ($profReport = $profReportsQuery->fetch_assoc()) : ?>
                    <div class='report-box1'>
                        <strong class="prof-reported">Prof. <?php echo htmlspecialchars($profReport['professor_name']); ?></strong>
                        <small class="date-report">Reported on: <?php echo htmlspecialchars($profReport['report_date']); ?></small> 
                        <div class="reported-con">Reasons: <strong class="reported-text"><?php echo htmlspecialchars($profReport['reasons']); ?></strong></div>
                        <div class="reported-con">
                            <?php if ($profReport['reasons'] === 'Others') : ?>
                                <strong>Details:</strong> <span class="reported-text"><?php echo htmlspecialchars($profReport['details']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($profReport['status'] === 'pending') : ?>
                            <form method="post">
                                <input type="hidden" name="prof_report_id" value="<?php echo $profReport['prof_report_id']; ?>">
                                <button type="submit" class="mark-btn">Mark as Reviewed</button>
                            </form>
                        <?php else : ?>
                            <span class="reviewed">Reviewed</span>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        
    </div>
    </div>
        <script src="../js/sidebar.js"></script>
        <script src="../js/logs.js"></script>
        <script src="../js/admin.js"></script>
</body>
</html>
