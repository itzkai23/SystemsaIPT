<?php

session_start();
require '../connect.php';

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
        'student_image' => !empty($row['student_image']) ? $row['student_image'] : "../images/icon.jpg"
    ];
}

$totalCount = count($feedbackData);

$defimage = '../images/facultyb.png';

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
    <title>Instructor</title>
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/instructor.css">
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
     <li><a class="a-bar"href="home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
     <li><a class="a-bar"href="instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
     <li><a class="a-bar"href="upf.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
       
</ul>

<div class="right-section">                              
  <div class="mid-section">
    <a href="home.php" class="home">Home</a>
    <a href="instructorsProfiles.php" class="pf">Faculty</a>
    <a href="upf.php" class="pf">Profile</a>
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
                <a class="a-pf" href="instructorsProfiles.php">Faculty</a>
                </div>

           <div class="logoutbb">
             <a href="../Authentication/logout.php"><img src="../images/logoutb.png" class="logoutb2"></a>
             <a href="../Authentication/logout.php" class="logout-link">Logout</a>
           </div>
    
        </div>
       
      </div>
      <p class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']);?></span></p> 
    </div>
         
</div>
</nav>

    <div class="container">
        

        <div class="avnamecon">
        <a href="instructorsProfiles.php" class="back-button">
                <img src="../images/backmage1.png" alt="Back" class="back-image">
        </a>

        <div class="avname">
            
        <img src="<?php echo !empty($prof_img) ? htmlspecialchars($prof_img) : $defimage; ?>" 
                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                 width="150" height="150">
         <div class="pc">
         <div class="prof-rep-div">
         <h5 class="prof-name"> <?php echo $professor_name; ?></h5>
         <!-- Add a Report Button -->
         <a href="report_prof.php?id=<?php echo urlencode($professor_id); ?>&name=<?php echo urlencode($professor_name); ?>&img=<?php echo urlencode($prof_img); ?>" 
        class="report-btn">Report</a>
         </div>
         <h5 class="pro-role"><?php echo $profrole; ?></h5>
         </div>
        
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
                      echo '<img src="../images/icon.jpg " alt="User" class="comment-img">';
                      echo '<div class="comment-text1">';
                      echo '<strong> Student </strong><br>';
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
  <div class="user-participant" id="triggerPopup">
      <span>Number of Evaluations:</span> <strong><?php echo htmlspecialchars($evaluation_count); ?></strong>
      <span>Average:</span> <strong id="averageDisplay">
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

<!-- Modal structure -->
<div class="modal-overlay" id="popupModal">
  <div class="modal-rate">
    <button class="close-btn" id="closeModal">&times;</button>
    <div class="circle-wrap">
      <svg width="150" height="150">
        <circle class="circle-bg" cx="75" cy="75" r="70"></circle>
        <circle class="circle" id="progressCircle" cx="75" cy="75" r="70"></circle>
      </svg>
    </div>
    <div class="score-text" id="scoreDisplay">4.5 / 5.0</div>
    <div class="percentage" id="percentageDisplay">90%</div>
    <div class="feedback" id="feedbackText">Excellent performance – well above expectations.</div>
  </div>
</div>


  
</div>

    </div>


<!-- Hidden Checkbox to Trigger Modal -->
<input type="checkbox" id="termsCheckbox" class="modal-toggle">

<div class="modal">
    <div class="modal-content">
        <h3>Comments (<?php echo $totalCount; ?>)</h3> <!-- Display total count -->
        <label for="termsCheckbox" class="close-modal">&times;</label>
        <div class="label-section">
            <div class="modal-scroll">
            <?php
            foreach ($feedbackData as $comm) {
                echo '<div class="comment-box">';

                if (!empty($comm['feedback'])) {
                    // Anonymous feedback (evaluation)
                    echo '<img src="../images/icon.jpg" alt="Anonymous" class="comment-img">'; // use a generic image
                    echo '<div class="comment-text">';
                    echo '<strong>Student</strong><br>';
                    echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                    echo '<small>Evaluated: ' . htmlspecialchars($comm['date_posted']) . '</small>';
                } elseif (!empty($comm['comment'])) {
                    // User comment
                    echo '<img src="' . htmlspecialchars($comm['student_image']) . '" alt="User" class="comment-img">';
                    echo '<div class="comment-text">';
                    echo '<strong>' . htmlspecialchars($comm['student_name']) . " " . htmlspecialchars($comm['lname']) . '</strong><br>';
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

                    echo '</div>'; // .menu-options
                    echo '</div>'; // .menu-container
                }

                echo '</div>'; // .comment-text
                echo '</div>'; // .comment-box
            }
            ?>
            </div>
        </div>
    </div>
</div>


    
</div>

    <script src="../js/sidebar.js"></script>
    <script src="../js/logs.js"></script>
    <script src="../js/logs.js"></script>
    <script src="../js/instructor.js"></script>

    <script>
      // Score setup
  const scoreOutOf5 = 4.62;
  const maxScore = 5.0;
  const percentage = (scoreOutOf5 / maxScore) * 100;

  const circle = document.getElementById('progressCircle');
  const offset = 440 - (440 * percentage) / 100;
  circle.style.strokeDashoffset = offset;

  // Dynamic color
  if (percentage >= 90) {
    circle.style.stroke = '#4ade80';
  } else if (percentage >= 75) {
    circle.style.stroke = '#facc15';
  } else if (percentage >= 60) {
    circle.style.stroke = '#f97316';
  } else {
    circle.style.stroke = '#ef4444';
  }

  // Feedback logic
  const feedbackText = document.getElementById("feedbackText");
  if (percentage >= 90) {
    feedbackText.textContent = "Excellent performance – well above expectations.";
  } else if (percentage >= 75) {
    feedbackText.textContent = "Good performance – meets expectations.";
  } else if (percentage >= 60) {
    feedbackText.textContent = "Average – some improvement needed.";
  } else {
    feedbackText.textContent = "Below average – improvement required.";
  }

  // Set score/percentage text
  document.getElementById("scoreDisplay").textContent = `${scoreOutOf5.toFixed(2)} / ${maxScore.toFixed(2)}`;
  document.getElementById("percentageDisplay").textContent = `${Math.round(percentage)}%`;
  document.getElementById("averageDisplay").textContent = `${scoreOutOf5.toFixed(2)}`;



  // Modal logic
  const trigger = document.getElementById("triggerPopup");
  const modal = document.getElementById("popupModal");
  const closeBtn = document.getElementById("closeModal");

  trigger.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
    </script>
</body>
</html>