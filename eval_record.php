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

$query = "SELECT
              ie.id,
              CONCAT(r.fname, ' ', r.lname) AS student_name,
              p.name AS professor_name,
              ie.q1, ie.q2, ie.q3, ie.q4, ie.q5, ie.q6, ie.q7, ie.q8, ie.q9, ie.q10, ie.q11, ie.q12, ie.q13, ie.q14, ie.q15, ie.q16, ie.q17, ie.q18, ie.q19, ie.q20,
              ie.feedback,
              ie.submitted_at,
              (ie.q1 + ie.q2 + ie.q3 + ie.q4 + ie.q5 + ie.q6 + ie.q7 + ie.q8 + ie.q9 + ie.q10 + ie.q11 + ie.q12 + ie.q13 + ie.q14 + ie.q15 + ie.q16 + ie.q17 + ie.q18 + ie.q19 + ie.q20 ) / 20 AS evaluation_avg_score,
              avg_scores.professor_avg_score
          FROM instructor_evaluation ie
          JOIN registration r ON ie.user_id = r.id
          JOIN professors p ON ie.professor_id = p.id
          LEFT JOIN (
              SELECT
                  professor_id,
                  AVG((q1 + q2 + q3 + q4 + q5 + q6 + q7 + q8 + q9 + q10 + q11 + q12 + q13 + q14 + q15 + q16 + q17 + q18 + q19 + q20) / 20) AS professor_avg_score
              FROM instructor_evaluation
              GROUP BY professor_id
          ) AS avg_scores ON ie.professor_id = avg_scores.professor_id";


// If there's a search input, filter by professor's or student's name
if (!empty($search)) {
    $query .= " WHERE p.name LIKE '%$search%' OR r.fname LIKE '%$search%' OR r.lname LIKE '%$search%'";
}

$query .= " ORDER BY ie.submitted_at DESC";

// $result = mysqli_query($conn, $query);
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


// If there's a search input, ensure the average calculation matches the filtered records
if (!empty($search)) {
  $avg_query .= " WHERE professor_id IN (SELECT id FROM professors WHERE name LIKE '%$search%')
                  OR user_id IN (SELECT id FROM registration WHERE fname LIKE '%$search%' OR lname LIKE '%$search%')";
}

$avg_result = mysqli_query($conn, $avg_query);
$averages = mysqli_fetch_assoc($avg_result);

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
    <title>Evaluation_Record</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
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

/* Table Styling */
/* table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
} */

/* Container for scrolling the table body */
.table-container {
    max-height: 320px;
    overflow: hidden;
    width: 100%;
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
    font-size: 12px;
    padding: 10px 3px 10px 3px;
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

/* Apply custom padding to td with the 'numeric' class */
td.numeric {
    padding: 8px;  /* Adjust padding for numeric data */
    text-align: right;  /* Right-align numbers */
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

tbody button {
  background-color: red; 
  color: white; 
  border: none; 
  padding: 5px 10px; 
  cursor: pointer;
  border-radius: 3px;
  margin-top: 10px;
}

tbody button:hover {
  background-color: #c00;
}

.view-btn {
  cursor: pointer;
  pointer-events: auto;
  background-color: #1565c0; 
  color: white; 
  border: none; 
  padding: 5px 13px; 
  cursor: pointer;
  border-radius: 3px;
  margin-bottom: 10px;
}

.view-btn:hover {
  background-color:rgb(10, 84, 168); 
}


/* POP-UP POST */
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(10px);
    background-color: rgba(0, 0, 0, 0.4); /* Dim background */
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 95%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
}

.modal-content h3 {
    font-family: "Roboto",sans-serif;
    color: #1565c0;
}

/* Target columns 3 to 6 for both th and td */
.modal-content th:nth-child(n+1):nth-child(-n+20),
.modal-content td:nth-child(n+1):nth-child(-n+20) {
  width: 20px;
  text-align: center; /* Center the content of these columns */
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    color: #aaa;
    cursor: pointer;
    padding: 7.5px 14px;
    border-radius: 20px;
}

.close:hover {
    color: black;
    background-color: rgb(196, 192, 192);
}

.stud-prof {
    display: flex;
    gap: 5%;
    justify-content: center;
}
.stud-prof h4 {
    font-weight: 600;
    font-family: Roboto, sans-serif;
    color: #1565c0;
}

.delete-btn {
    display: none;
    background-color: red; 
    color: white; 
    border: none; 
    padding: 5px 10px; 
    cursor: pointer;
    border-radius: 3px;
    position: absolute;
    top: 8em;
    right: 1.5em;
}

.delete-btn:hover {
  background-color: #c00;
}

/* Table Header */


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
    <h2>Evaluation Record</h2>
    <p class="student-count">Total Evaluations: <strong><?php echo $eval_count; ?></strong></p>
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search name" value="<?php echo htmlspecialchars($search); ?>">
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
                    <th>Student</th>
                    <th>Professor</th>
                    <th>q1</th>
                    <th>q2</th>
                    <th>q3</th>
                    <th>q4</th>
                    <th>q5</th>
                    <th>Feedback</th>
                    <th>Submitted</th>
                    <th>Evaluation Average</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
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
            <h4>Student: <strong id="modal-student"></strong></h4>
            <h4>Professor: <strong id="modal-professor"></strong></h4>
            <h4>Evaluation Average: <strong id="modal-score"></strong></h4>
            <h4>Professor's Average Score: <strong id="modal-avg"></strong></h4>
            </div>
            <!-- Place the delete button outside of the table -->
         <div id="delete-button-container">
            <form id="delete-record-form" action="delete_record.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                <input type="hidden" name="record_id" id="delete-record-id">
                <button type="submit" id="delete-button" class="delete-btn">Delete</button>
            </form>
        </div>
        

        <table>
            <thead>
                <tr>
                    <?php for ($i = 1; $i <= 20; $i++) { echo "<th>q$i</th>"; } ?>
                </tr>
            </thead>
            <tbody>
                <tr id="modal-questions"></tr>
            </tbody>
        </table>
        
    </div>
</div>

<script>
function openModal(data) {
    // Set student and professor names, and evaluation average
    document.getElementById("modal-student").textContent = data.student_name;
    document.getElementById("modal-professor").textContent = data.professor_name;
    document.getElementById("modal-score").textContent = parseFloat(data.evaluation_avg_score).toFixed(2);
    document.getElementById("modal-avg").textContent = parseFloat(data.professor_avg_score).toFixed(2);

    // Populate the 20 question responses
    let questionsRow = "";
    for (let i = 1; i <= 20; i++) {
        questionsRow += `<td>${data['q' + i] || 'N/A'}</td>`;
    }

    // Add delete button at the end of the row
    // questionsRow += `
    //     <td>
    //         <form action='delete_record.php' method='POST' onsubmit='return confirm("Are you sure you want to delete this record?");'>
    //             <input type='hidden' name='record_id' value='${data.id}'>
    //             <button type='submit'>Delete</button>
    //         </form>
    //     </td>`;

    document.getElementById("modal-questions").innerHTML = questionsRow;

    // Show the delete button
    document.getElementById("delete-record-id").value = data.id;  // Set the record id for deletion
    document.getElementById("delete-button").style.display = "block";  // Show delete button
    
    // Display the modal
    document.getElementById("modal").style.display = "block";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

</script>

    <script src="js/sidebar.js"></script>
    <script src="js/logs.js"></script>
</body>
</html>