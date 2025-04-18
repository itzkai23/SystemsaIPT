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
    AVG((q1 + q2 + q3 + q4 + q5 + q6 + q7 + q8 + q9 + q10 + q11 + q12 + q13 + q14 + q15 + q16 + q17 + q18 + q19 + q20) / 20.0) AS professor_avg_score,
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
    
    <link rel="stylesheet" href="css/instructor.css">
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
        <a href="instructorsProfiles.php" class="back-button">
                <img src="images/backmage1.png" alt="Back" class="back-image">
        </a>

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
    <script src="js/logs.js"></script>
    <script src="js/instructor.js"></script>
</body>
</html>