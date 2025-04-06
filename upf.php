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

function calculateAge($bday) {
    $birthDate = new DateTime($bday);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}

$default_image = "images/icon.jpg";
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;
$current_image .= "?t=" . time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/upf.css">
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
           <li><a class="a-bar"href="instructorsProfiles.php">Faculty</a></li>
           <li><a class="a-bar"href="freedomwall.php">Newsfeed</a></li>
           <li><a class="a-bar"href="upf.php">Profile</a></li>
      </ul>

      <div class="mid-section">
         <a href="home.php" class="home">Home</a>
         <a href="freedomwall.html" class="pf">Newsfeed</a>
         <a href="instructorsProfiles.php" class="pf">Faculty</a>
      </div>

      <div class="right-section">
        <div class="logpos">
            <div class="logout-container"> 
                <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
                <div class="logout-dropdown" id="logoutDropdown">
                    <a href="upf.php" class="logpf-con">
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
            <p class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']); ?></span></p> 
        </div>
      </div>
   </nav>

   <div class="main-profile">
    <div class="profile-card">
      <section class="profile-left">
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
            <label for="fileinput" class="profile-picture-label">
                <img src="<?php echo htmlspecialchars($current_image); ?>" class="picture" title="Click to upload image"/>
                <div class="profile-edit-overlay">Change</div>
            </label>
            <input type="file" id="fileinput" name="picture_url" accept="image/*" onchange="previewimage();" style="display:none;"/>
            <button class="btnup" name="btnup" style="display:none;">Save</button>
        </form>
        <h3><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></h3>
      </section>

      <div class="profile-right">
        <h3>Profile Details</h3>
        <form action="update_profile.php" method="post" id="profileForm">
            <div class="profile-grid">
                <!-- First name and Last name display -->
                <div class="input-group" id="fname" style="display:none;">
                    <label><strong>First Name:</strong></label>
                    <input type="text" name="fname" value="<?php echo htmlspecialchars($_SESSION['f_name'] ?? ''); ?>" class="edit-input" style="display:none;">
                </div>
                <div class="input-group"id="lname" style="display:none;" >
                    <label><strong>Last Name:</strong></label>
                    <input type="text" name="lname" value="<?php echo htmlspecialchars($_SESSION['l_name'] ?? ''); ?>" class="edit-input" style="display:none;">
                </div>
                
                <!-- Cellphone No and Email (default visible) -->
                <div class="input-group">
                    <label><strong>Cellphone No.:</strong></label>
                    <span class="profile-text"><?php echo htmlspecialchars($_SESSION['con']); ?></span>
                    <input type="text" name="contact" value="<?php echo htmlspecialchars($_SESSION['con'] ?? ''); ?>" class="edit-input" style="display:none;">
                </div>
                <div class="input-group">
                    <label><strong>Email:</strong></label>
                    <span class="profile-text"><?php echo htmlspecialchars($_SESSION['em']); ?></span>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['em'] ?? ''); ?>" class="edit-input" style="display:none;">
                </div>
                
                <!-- Birthday, hidden initially and shown only when editing -->
                <p>You might also want to add these details:</p><br>
                <div class="input-group" id="birthdayGroup">
                    <label><strong>Birthday:</strong></label>
                    <span class="profile-text">
                        <?php echo htmlspecialchars($_SESSION['Birthday']); ?>
                    </span>
                    <input hidden type="date" id="Birthday" name="Birthday" value="<?php echo htmlspecialchars($_SESSION['Birthday'] ?? ''); ?>" class="edit-input">
                </div>
                
                <!-- Age calculated based on the birthday -->
                <div class="input-group"  >
                    <label><strong>Age:</strong></label>
                    <span class="profile-text" id="ageText">
                        <?php echo isset($_SESSION['Birthday']) ? calculateAge($_SESSION['Birthday']) : ''; ?>
                    </span>
                    <input type="text" id="age" readonly class="edit-input" style="display:none;">
                </div>
            </div>
            <br>
            <div class="profile-buttons">
                <button type="button" id="editProfileBtn">Edit Profile</button>
                <button type="submit" id="saveProfileBtn" style="display:none;">Save Changes</button>
                <button type="button" id="cancelEditBtn" style="display:none;">Cancel</button>
            </div>
        </form>
    </div>
    </div>
   </div>

<script src="js/sidebar.js"></script>
<script src="js/logs.js"></script>
<script src="js/uploads.js"></script>
</body>
</html>