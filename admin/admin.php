<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

// Keep your existing default image
$default_image = "../images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

// Get the existing schedule
$query = "SELECT id, start_date, end_date, status FROM evaluation_status LIMIT 1";
$result = $conn->query($query);
$current_schedule = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enable_evaluation'])) {
        // Enable evaluation and update status
        $status = 'On-going';

        // Ensure we only update the status without changing the dates here
        if ($stmt = $conn->prepare("UPDATE evaluation_status SET status = ? WHERE id = ?")) {
            $stmt->bind_param("si", $status, $current_schedule['id']);
            if ($stmt->execute()) {
                echo "Evaluation enabled successfully.";
                // Refresh the status
                $current_schedule['status'] = $status;
            } else {
                echo "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } elseif (isset($_POST['disable_evaluation'])) {
        // Disable evaluation
        $status = 'On-hold';

        if ($stmt = $conn->prepare("UPDATE evaluation_status SET status = ? WHERE id = ?")) {
            $stmt->bind_param("si", $status, $current_schedule['id']);
            if ($stmt->execute()) {
                echo "Evaluation disabled successfully.";
                // Refresh the status
                $current_schedule['status'] = $status;
            } else {
                echo "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } elseif (isset($_POST['update_schedule'])) {
        // Update the schedule with new dates
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if ($stmt = $conn->prepare("UPDATE evaluation_status SET start_date = ?, end_date = ? WHERE id = ?")) {
            $stmt->bind_param("ssi", $start_date, $end_date, $current_schedule['id']);
            if ($stmt->execute()) {
                echo "Evaluation schedule updated successfully.";
                // Refresh the schedule
                $current_schedule['start_date'] = $start_date;
                $current_schedule['end_date'] = $end_date;
            } else {
                echo "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/admin.css">
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
        <li><a class="a-bar" href="admin.php"><i class="fas fa-home"></i><span>Home</span></a></li>
        <li><a class="a-bar" href="../students_interface/instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
        <li><a class="a-bar" href="register_prof.php"><i class="fas fa-user"></i><span>Faculty Registration</span></a></li>
    </ul>

    <div class="right-section">
        <div class="mid-section"></div>
        <div class="logpos">
            <div class="logout-container"> 
                <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
                <div class="logout-dropdown" id="logoutDropdown">
                    <a href="admin.php" class="logpf-con">
                        <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" alt="picture">
                        <h4><?php echo htmlspecialchars($_SESSION['f_name']) . " " . ($_SESSION['l_name']); ?></h4>
                    </a>
                    <div class="dlog-icon">
                        <img src="../images/offweb.png" alt="log">
                        <a class="a-pf" href="https://sgs.cityofmalabonuniversity.edu.ph/">Visit Official Website</a>
                    </div>
                    <!-- <div class="dlog-icon">
                        <img src="../images/announcement.png" alt="">
                        <a class="a-pf" href="#">Announcement</a>
                    </div> -->
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
            <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']); ?></span></h4> 
        </div>
    </div>
</nav>

<div class="admin-sec">
    <p class="fp">Welcome Administrator!</p>
    <div class="fdiv">
        <div class="fdiv-header">
            <h5>Academic Year: 2024-2025</h5>
            <button id="openModalBtn" class="edit-btn">Edit Schedule</button>
        </div>
        <div class="eval-status-container">
        <p>Current Evaluation Status: 
        <span class="<?php echo strtolower(str_replace(' ', '-', $current_schedule['status'])); ?>">
            <?php echo htmlspecialchars($current_schedule['status']); ?>
        </span>
        </p>

            <p class="start-date">Start Date: <strong><?php echo htmlspecialchars($current_schedule['start_date']); ?></strong></p>
            <p class="end-date">End Date: <strong><?php echo htmlspecialchars($current_schedule['end_date']); ?></strong></p>
            <div class="eval-buttons">
                <!-- Separate form for enable and disable -->
                <form method="POST" action="">
                    <button type="submit" name="enable_evaluation" id="enableBtn">Open</button>
                </form>
                <form method="POST" action="">
                    <button type="submit" name="disable_evaluation" id="disableBtn">Hold</button>
                </form>
            </div>
        </div>

        
    </div>
</div>
    
    <!-- Enable/Disable and Update Schedule Form -->
     <!-- Modal Structure -->

<div id="scheduleModal-admin" class="modal-admin">
  <div class="modal-content-admin">
    <span class="close">&times;</span>
    <h2>Update Evaluation Schedule</h2>
    <form method="POST" action="">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" value="<?php echo htmlspecialchars($current_schedule['start_date']); ?>" required>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" value="<?php echo htmlspecialchars($current_schedule['end_date']); ?>" required>

        <!-- Enable and Disable buttons -->
        <div class="form-buttons">
        <!-- Update Schedule button -->
        <button type="submit" name="update_schedule" class="btn-update">Update Schedule</button>
        </div>

    </form>
    </div>
</div>

<div class="container">
    <div class="divider">
        <a href="../students_interface/instructorsProfiles.php" class="divider-link">Faculties Profiles</a>
        <img src="../images/facultyb.png" alt="icon">
    </div>
    
    <div class="divider">
        <a href="stundentlist.php" class="divider-link">Students List</a>
        <img src="../images/usersb.png" alt="icon">
    </div>
    
    <div class="divider">
        <a href="eval_record.php" class="divider-link">Evaluations Records</a>
        <img src="../images/feedb.png" alt="icon">
    </div>
    
    <div class="divider">
        <a href="admin_reports.php" class="divider-link">Report</a>
        <img src="../images/reportb.png" alt="icon">
    </div>
</div>

<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>

<script>
document.getElementById("openModalBtn").onclick = function() {
  document.getElementById("scheduleModal-admin").style.display = "block";
}

document.querySelector(".close").onclick = function() {
  document.getElementById("scheduleModal-admin").style.display = "none";
}

window.onclick = function(event) {
  const modal = document.getElementById("scheduleModal-admin");
  if (event.target === modal) {
    modal.style.display = "none";
  }
}

</script>
    
</body>
</html>
