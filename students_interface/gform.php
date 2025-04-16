<?php
require '../connect.php';
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

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to evaluate. <a href='login.php'>Login</a>");
}

// $user_id = $_SESSION['user_id']; // Consistently use user_id

// // ✅ Check if the user can evaluate *ONLY* on initial page load (GET request)
// if ($_SERVER["REQUEST_METHOD"] === "GET" && !canEvaluate($conn, $user_id, $professor_id)) {
//     // Show JavaScript alert and redirect
//     echo "<script>
//             alert('❌ You have already evaluated this professor. Please wait 30 days before evaluating again.');
//             window.location.href = 'instructorsEval.php'; 
//           </script>";
//     exit; // Make sure the script stops executing here
// }


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
$default_image = "../images/icon.jpg";

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
    <!-- Font Awesome CDN -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
    <link rel="stylesheet" href="../css/gform.css">
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
  <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></h4> 
</div>
         
</div>
</nav>

   <div class="form-container">
    
        <a href="instructorsEval.php" class="back-button">
                <img src="../images/backmage1.png" alt="Back" class="back-image">
        </a>

        <h1 class="form-title">Faculty Evaluation</h1>
        <p class="p-val">Evaluating: <?php echo $professor_name; ?></p>
        <p class="description">Your feedback helps us improve our teaching standards. Please rate the instructor based on the following criteria.</p>
        
        <form action="form_act.php" method="post">
           <div class="table-container">
            <h4>Questions</h4>
           <div class="rating-scale">
           <strong>(5)</strong><span>Outstanding</span>
           <strong>(4)</strong><span>Very Satisfactory</span>
           <strong>(3)</strong><span>Satisfactory</span>
           <strong>(2)</strong><span>Unsatisfactory</span>
           <strong>(1)</strong><span>Very Unsatisfactory</span>
           </div>

            <table class="question-table">
            <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">
            <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <tr>

                <tr class="label-radio">
                    <td><p>II. Teaching Performance</p></td>
                </tr>

                <tr class="label-radio">
                    <td><p>III. Course Content</p></td>
                </tr>
                    <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Relevance and currency of course materials</label>
                    </td>

                    <td class="radio-group">
                        <label><input type="radio" name="q1" value="5" required> 5</label>
                        <label><input type="radio" name="q1" value="4"> 4</label>
                        <label><input type="radio" name="q1" value="3"> 3</label>
                        <label><input type="radio" name="q1" value="2"> 2</label>
                        <label><input type="radio" name="q1" value="1"> 1</label>
                    </td>
                    </tr>
                    
                </tr>
                
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Alignment with course objectives and goals</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q2" value="5" required> 5</label>
                        <label><input type="radio" name="q2" value="4"> 4</label>
                        <label><input type="radio" name="q2" value="3"> 3</label>
                        <label><input type="radio" name="q2" value="2"> 2</label>
                        <label><input type="radio" name="q2" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Integration of diverse perspectives and real-world examples </label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q3" value="5" required> 5</label>
                        <label><input type="radio" name="q3" value="4"> 4</label>
                        <label><input type="radio" name="q3" value="3"> 3</label>
                        <label><input type="radio" name="q3" value="2"> 2</label>
                        <label><input type="radio" name="q3" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td><p>IV. Instructional Methods</p></td>
                </tr>
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Clarity and effectiveness of presentations</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q4" value="5" required> 5</label>
                        <label><input type="radio" name="q4" value="4"> 4</label>
                        <label><input type="radio" name="q4" value="3"> 3</label>
                        <label><input type="radio" name="q4" value="2"> 2</label>
                        <label><input type="radio" name="q4" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Use of engaging and active learning strategies</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q5" value="5" required> 5</label>
                        <label><input type="radio" name="q5" value="4"> 4</label>
                        <label><input type="radio" name="q5" value="3"> 3</label>
                        <label><input type="radio" name="q5" value="2"> 2</label>
                        <label><input type="radio" name="q5" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Responsiveness to diverse learning styles and needs</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q6" value="5" required> 5</label>
                        <label><input type="radio" name="q6" value="4"> 4</label>
                        <label><input type="radio" name="q6" value="3"> 3</label>
                        <label><input type="radio" name="q6" value="2"> 2</label>
                        <label><input type="radio" name="q6" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td><p>V. Assessment and Feedback</p></td>
                </tr>
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Use of varied assessment methods (e.g., exams,papers,presentations,group work):</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q7" value="5" required> 5</label>
                        <label><input type="radio" name="q7" value="4"> 4</label>
                        <label><input type="radio" name="q7" value="3"> 3</label>
                        <label><input type="radio" name="q7" value="2"> 2</label>
                        <label><input type="radio" name="q7" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Timeliness and quality of feedback on assignments</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q8" value="5" required> 5</label>
                        <label><input type="radio" name="q8" value="4"> 4</label>
                        <label><input type="radio" name="q8" value="3"> 3</label>
                        <label><input type="radio" name="q8" value="2"> 2</label>
                        <label><input type="radio" name="q8" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Fairness and consistency in grading</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q9" value="5" required> 5</label>
                        <label><input type="radio" name="q9" value="4"> 4</label>
                        <label><input type="radio" name="q9" value="3"> 3</label>
                        <label><input type="radio" name="q9" value="2"> 2</label>
                        <label><input type="radio" name="q9" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td><p>VI. Course Organization and Management</p></td>
                </tr>
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Clear and well-organized course structure (e.g., syllabus, schedule, materials)</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q10" value="5" required> 5</label>
                        <label><input type="radio" name="q10" value="4"> 4</label>
                        <label><input type="radio" name="q10" value="3"> 3</label>
                        <label><input type="radio" name="q10" value="2"> 2</label>
                        <label><input type="radio" name="q10" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Effective use of class time and course resources</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q11" value="5" required> 5</label>
                        <label><input type="radio" name="q11" value="4"> 4</label>
                        <label><input type="radio" name="q11" value="3"> 3</label>
                        <label><input type="radio" name="q11" value="2"> 2</label>
                        <label><input type="radio" name="q11" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Promptness and consistency in communications and announcements</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q12" value="5" required> 5</label>
                        <label><input type="radio" name="q12" value="4"> 4</label>
                        <label><input type="radio" name="q12" value="3"> 3</label>
                        <label><input type="radio" name="q12" value="2"> 2</label>
                        <label><input type="radio" name="q12" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td><p>VII. Faculty Accessibility and Support</p></td>
                </tr>
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Availability and responsiveness to student inquiries (e.g., office hours, email) </label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q13" value="5" required> 5</label>
                        <label><input type="radio" name="q13" value="4"> 4</label>
                        <label><input type="radio" name="q13" value="3"> 3</label>
                        <label><input type="radio" name="q13" value="2"> 2</label>
                        <label><input type="radio" name="q13" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Quality of guidance and support provided to students</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q14" value="5" required> 5</label>
                        <label><input type="radio" name="q14" value="4"> 4</label>
                        <label><input type="radio" name="q14" value="3"> 3</label>
                        <label><input type="radio" name="q14" value="2"> 2</label>
                        <label><input type="radio" name="q14" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Encouragement of student engagement and participation</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q15" value="5" required> 5</label>
                        <label><input type="radio" name="q15" value="4"> 4</label>
                        <label><input type="radio" name="q15" value="3"> 3</label>
                        <label><input type="radio" name="q15" value="2"> 2</label>
                        <label><input type="radio" name="q15" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td><p>VIII. Overall Assessment</p></td>
                </tr>
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>1.</strong><label>Overall teaching performance rating</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q16" value="5" required> 5</label>
                        <label><input type="radio" name="q16" value="4"> 4</label>
                        <label><input type="radio" name="q16" value="3"> 3</label>
                        <label><input type="radio" name="q16" value="2"> 2</label>
                        <label><input type="radio" name="q16" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>2.</strong><label>Overall course content rating</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q17" value="5" required> 5</label>
                        <label><input type="radio" name="q17" value="4"> 4</label>
                        <label><input type="radio" name="q17" value="3"> 3</label>
                        <label><input type="radio" name="q17" value="2"> 2</label>
                        <label><input type="radio" name="q17" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>3.</strong><label>Overall faculty effectiveness rating</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q18" value="5" required> 5</label>
                        <label><input type="radio" name="q18" value="4"> 4</label>
                        <label><input type="radio" name="q18" value="3"> 3</label>
                        <label><input type="radio" name="q18" value="2"> 2</label>
                        <label><input type="radio" name="q18" value="1"> 1</label>
                    </td>
                </tr>

                
                <tr class="label-radio">
                    <td class="label-column">
                    <strong>4.</strong><label>The instructor fostered a positive and respectful classroom environment.</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q19" value="5" required> 5</label>
                        <label><input type="radio" name="q19" value="4"> 4</label>
                        <label><input type="radio" name="q19" value="3"> 3</label>
                        <label><input type="radio" name="q19" value="2"> 2</label>
                        <label><input type="radio" name="q19" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                    <td class="label-column">
                    <strong>5.</strong><label>Overall, I would rate the instructor's teaching effectiveness as excellent.</label>
                    </td>
                
                    <td class="radio-group">
                        <label><input type="radio" name="q20" value="5" required> 5</label>
                        <label><input type="radio" name="q20" value="4"> 4</label>
                        <label><input type="radio" name="q20" value="3"> 3</label>
                        <label><input type="radio" name="q20" value="2"> 2</label>
                        <label><input type="radio" name="q20" value="1"> 1</label>
                    </td>
                </tr>

                <tr class="label-radio">
                  <td><label for="feedback" class="option-feedback">IX. Open-Ended Feedback</label></td>
                </tr>
                <tr class="label-con-feedback">
                  <td class="label-feedback">
                   <label>What aspects of the course or the faculty's teaching style did you find most effective or enjoyable?</label>
                  </td>
                  <td class="radio-group"><textarea id="feedback" name="feedback" placeholder="Optional"></textarea></td>
                </tr>

            </table>
            
            
            
            
            <div class="btn-con">
            <button type="submit"><img src="../images/sends.png" alt=""> Submit Evaluation</button>
        </div>
        
        </div>
        </form>
    </div>

<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
</body>
</html>