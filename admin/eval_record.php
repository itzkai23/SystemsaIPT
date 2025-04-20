<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

// Search logic
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT
              ie.id,
              p.name AS professor_name,
              ie.q1, ie.q2, ie.q3, ie.q4, ie.q5, ie.q6, ie.q7, ie.q8, ie.q9, ie.q10, 
              ie.q11, ie.q12, ie.q13, ie.q14, ie.q15, ie.q16, ie.q17, ie.q18, ie.q19, ie.q20,
              ie.feedback,
              ie.submitted_at,
              (ie.q1 + ie.q2 + ie.q3 + ie.q4 + ie.q5 + ie.q6 + ie.q7 + ie.q8 + ie.q9 + ie.q10 + 
               ie.q11 + ie.q12 + ie.q13 + ie.q14 + ie.q15 + ie.q16 + ie.q17 + ie.q18 + ie.q19 + ie.q20 ) / 20 AS evaluation_avg_score,
              avg_scores.professor_avg_score
          FROM instructor_evaluation ie
          JOIN professors p ON ie.professor_id = p.id
          LEFT JOIN (
              SELECT
                  professor_id,
                  AVG((q1 + q2 + q3 + q4 + q5 + q6 + q7 + q8 + q9 + q10 + 
                       q11 + q12 + q13 + q14 + q15 + q16 + q17 + q18 + q19 + q20) / 20) AS professor_avg_score
              FROM instructor_evaluation
              GROUP BY professor_id
          ) AS avg_scores ON ie.professor_id = avg_scores.professor_id";

// If there's a search input, filter by professor's name only (student names removed)
if (!empty($search)) {
    $query .= " WHERE p.name LIKE '%$search%'";
}

$query .= " ORDER BY ie.submitted_at DESC";

$result = mysqli_query($conn, $query) or die("Query Failed: " . mysqli_error($conn));
$eval_count = ($result) ? mysqli_num_rows($result) : 0;

// Query to calculate averages for each question
$avg_query = "SELECT 
                AVG(q1) AS avg_q1, 
                AVG(q2) AS avg_q2, 
                AVG(q3) AS avg_q3, 
                AVG(q4) AS avg_q4, 
                AVG(q5) AS avg_q5,
                AVG(q6) AS avg_q6,
                AVG(q7) AS avg_q7,
                AVG(q8) AS avg_q8,
                AVG(q9) AS avg_q9,
                AVG(q10) AS avg_q10,
                AVG(q11) AS avg_q11,
                AVG(q12) AS avg_q12,
                AVG(q13) AS avg_q13,
                AVG(q14) AS avg_q14,
                AVG(q15) AS avg_q15,
                AVG(q16) AS avg_q16,
                AVG(q17) AS avg_q17,
                AVG(q18) AS avg_q18,
                AVG(q19) AS avg_q19,
                AVG(q20) AS avg_q20
              FROM instructor_evaluation";

if (!empty($search)) {
  $avg_query .= " WHERE professor_id IN (SELECT id FROM professors WHERE name LIKE '%$search%')";
}

$avg_result = mysqli_query($conn, $avg_query);
$averages = mysqli_fetch_assoc($avg_result);

$default_image = "../images/icon.jpg";
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;
$current_image .= "?t=" . time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Evaluation_Record</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="../css/eval_record.css"/>
  <link rel="stylesheet" href="../css/headmenu.css"/>
</head>
<body>

<nav class="home-header">
  <div class="ham-menu">
    <span></span><span></span><span></span>
  </div>
  <ul class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="../images/head2.png" alt="Sidebar Image" class="sidebar-image"/>
    </div>
    <li><a class="a-bar" href="../students_interface/home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
    <li><a class="a-bar" href="../students_interface/instructorsProfiles.php"><i class="fas fa-chalkboard-teacher"></i><span>Faculty</span></a></li>
    <li><a class="a-bar"href="register_prof.php"><i class="fas fa-user"></i><span>Faculty Registration</span></a></li>
  </ul>

  <div class="right-section">
  <div class="mid-section">
         <!-- <a href="admin.php" class="home">Home</a>
         <a href="../students_interface/instructorsProfiles.php" class="pf">Faculty</a> -->
</div>
    <div class="logpos">
      <div class="logout-container">
        <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton"/>
        <div class="logout-dropdown" id="logoutDropdown">
          <a href="upf.php" class="logpf-con">
            <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" alt="picture"/>
            <h4><?php echo htmlspecialchars($_SESSION['f_name'] . " " . $_SESSION['l_name']); ?></h4>
          </a>
          <div class="dlog-icon">
            <Img src="../images/offweb.png"><a class="a-pf" href="https://sgs.cityofmalabonuniversity.edu.ph/">Visit Official Website</a>
          </div>
          <div class="dlog-icon">
            <Img src="../images/announcement.png"><a class="a-pf" href="#">Announcement</a>
          </div>
          <div class="dlog-icon">
            <Img src="../images/facultyb.png"><a class="a-pf" href="../students_interface/instructorsProfiles.php">Faculty</a>
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

<div class="container">
  <h2>Evaluation Record</h2>
  <p class="student-count">Total Evaluations: <strong><?php echo $eval_count; ?></strong></p>
  <div class="search-container">
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Search professor name" value="<?php echo htmlspecialchars($search); ?>"/>
      <button type="submit">Search</button>
      <?php if (!empty($search)) : ?>
        <a href="eval_record.php"><button type="button">Clear</button></a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-container">
    <table id="studentTable">
      <thead>
        <tr>
          <th>Professor</th>
          <th>q1</th><th>q2</th><th>q3</th><th>q4</th><th>q5</th>
          <th>Feedback</th>
          <th>Submitted</th>
          <th>Evaluation Average</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <tr>
            <td><?php echo htmlspecialchars($row['professor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['q1']); ?></td>
            <td><?php echo htmlspecialchars($row['q2']); ?></td>
            <td><?php echo htmlspecialchars($row['q3']); ?></td>
            <td><?php echo htmlspecialchars($row['q4']); ?></td>
            <td><?php echo htmlspecialchars($row['q5']); ?></td>
            <td><?php echo htmlspecialchars($row['feedback']); ?></td>
            <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
            <td><?php echo number_format($row['evaluation_avg_score'], 2); ?></td>
            <td>
              <label class='view-btn' onclick='openModal(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>View</label>
              <form action='delete_record.php' method='POST' onsubmit='return confirm("Are you sure you want to delete this record?");'>
                <input type='hidden' name='record_id' value='<?php echo htmlspecialchars($row['id']); ?>'>
                <button type='submit'>Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Instructor Evaluation Records</h3>
    <div class="stud-prof">
      <h4>Professor: <strong id="modal-professor"></strong></h4>
      <h4>Evaluation Average: <strong id="modal-score"></strong></h4>
      <h4>Professor's Average Score: <strong id="modal-avg"></strong></h4>
    </div>

    <div id="delete-button-container">
      <form id="delete-record-form" action="delete_record.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
        <input type="hidden" name="record_id" id="delete-record-id">
        <button type="submit" id="delete-button" class="delete-btn">Delete</button>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <?php for ($i = 1; $i <= 10; $i++) echo "<th>q$i</th>"; ?>
        </tr>
      </thead>
      <tbody>
        <tr id="modal-questions-1-10"></tr>
      </tbody>
      <thead>
        <tr>
          <?php for ($i = 11; $i <= 20; $i++) echo "<th>q$i</th>"; ?>
        </tr>
      </thead>
      <tbody>
        <tr id="modal-questions-11-20"></tr>
      </tbody>
    </table>
  </div>
</div>


<script src="../js/eval_record.js"></script>
<script src="../js/sidebar.js"></script>
<script src="../js/logs.js"></script>
</body>
</html>
