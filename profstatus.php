<?php
require 'connect.php';

// Initialize the $professors array
$professors = [];

// Fetch all professors (normal page load)
$profResult = $conn->query("SELECT id, name, role, prof_img FROM professors");
if ($profResult && $profResult->num_rows > 0) {
    while ($prof = $profResult->fetch_assoc()) {
        $avgQuery = "
        SELECT 
            AVG((q1 + q2 + q3 + q4 + q5) / 5.0) AS professor_avg_score,
            COUNT(*) AS evaluation_count
        FROM instructor_evaluation
        WHERE professor_id = ?;
        ";

        $avgStmt = $conn->prepare($avgQuery);
        $avgStmt->bind_param("i", $prof['id']);
        $avgStmt->execute();
        $avgResult = $avgStmt->get_result();
        $avgData = $avgResult->fetch_assoc();

        $professors[] = [
            'id' => $prof['id'],
            'name' => $prof['name'],
            'role' => $prof['role'],
            'prof_img' => !empty($prof['prof_img']) ? $prof['prof_img'] : "images/facultyb.png",
            'evaluation_count' => $avgData['evaluation_count'] ?? 0,
            'professor_avg_score' => $avgData['professor_avg_score'] ? number_format($avgData['professor_avg_score'], 2) : "No evaluations yet."
        ];
    }
}

if (isset($_GET['fetch_comments']) && isset($_GET['professor_id'])) {
    $professor_id = intval($_GET['professor_id']);

    $query = "
    SELECT 
        ie.feedback, 
        ie.submitted_at AS date_posted, 
        NULL AS comment, 
        NULL AS comment_id  
    FROM instructor_evaluation ie
    WHERE ie.professor_id = ?

    UNION ALL

    SELECT 
        NULL AS feedback, 
        c.created_at AS date_posted,
        c.comment, 
        c.id AS comment_id  
    FROM comments c
    WHERE c.professor_id = ?

    ORDER BY date_posted DESC;";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $professor_id, $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= '<div class="comment-box">';
            $output .= '<div class="comment-text">';
            if (!empty($row['feedback'])) {
                $output .= '<p>' . htmlspecialchars($row['feedback']) . '</p>';
            } elseif (!empty($row['comment'])) {
                $output .= '<p>' . htmlspecialchars($row['comment']) . '</p>';
            }
            $output .= '<small>' . htmlspecialchars($row['date_posted']) . '</small>';
            $output .= '</div></div>';
        }
        
    } else {
        $output = "<p>No comments or feedback available.</p>";
    }
    echo $output;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors Status</title>
    <link rel="stylesheet" href="css/profstatus.css">
</head>
<body>

<div class="page-container">
    <h1>Professors</h1>

    <!-- Display Professors List -->
    <div class="con-profs">
        <?php foreach ($professors as $prof): ?>
            <div class="rant-post" 
                data-id="<?php echo $prof['id']; ?>" 
                data-name="<?php echo htmlspecialchars($prof['name']); ?>"
                data-role="<?php echo htmlspecialchars($prof['role']); ?>"
                data-image="<?php echo htmlspecialchars($prof['prof_img']); ?>"
                data-evaluations="<?php echo htmlspecialchars($prof['evaluation_count']); ?>"
                data-average="<?php echo htmlspecialchars($prof['professor_avg_score']); ?>">
                <div class="con-prof">
                    <img src="<?php echo htmlspecialchars($prof['prof_img']); ?>" alt="Professor Image" width="150" height="150">
                    <div>
                        <h5><?php echo htmlspecialchars($prof['name']); ?></h5>
                        <h5><?php echo htmlspecialchars($prof['role']); ?></h5>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Back to Home Button -->
    <a href="silog.php">Back</a>

    <!-- Modal for Comments -->
    <div class="modal_usercom">
        <div class="modal-content-usercom">
            <span class="close">&times;</span>
            
            <div class="container">
                <div class="avnamecon">
                    <div class="avname">
                        <img id="profImg" src="" alt="Professor Image" width="150" height="150">
                        <div class="pc">
                            <h5 id="profName"></h5>
                            <h5 id="profRole"></h5>
                        </div>
                    </div>
                    <div class="rate">
                        <h3>Average Evaluation Points</h3>
                        <h4>Current Status</h4>
                        <div class="user-participant">
                            <span>Number of Evaluations:</span> <strong id="evaluationCount"></strong>
                            <span>Average:</span> <strong id="averageScore"></strong>
                        </div>
                    </div>
                </div>

                <form method="GET">
                  <input type="hidden" name="professor_id" value="">
                </form>

                <!-- Display Submitted Data -->
                <div class="comdent">
                <h3>Comments</h3> 
                    <div class="com-scroll" id="commentsContainer">
                        <p>No comments or feedback available.</p>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.querySelector(".modal_usercom");
        const closeModal = document.querySelector(".modal_usercom .close");
        const commentsContainer = document.getElementById("commentsContainer");

        document.querySelectorAll(".rant-post").forEach(item => {
            item.addEventListener("click", function () {
                const profId = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");
                const role = this.getAttribute("data-role");
                const image = this.getAttribute("data-image");
                const evaluationCount = this.getAttribute("data-evaluations");
                const averageScore = this.getAttribute("data-average");

                document.getElementById("profName").textContent = name;
                document.getElementById("profRole").textContent = role;
                document.getElementById("profImg").src = image;
                document.getElementById("evaluationCount").textContent = evaluationCount;
                document.getElementById("averageScore").textContent = averageScore;

                modal.style.display = "block";

                fetch(`profstatus.php?fetch_comments=1&professor_id=${profId}`)
                    .then(response => response.text()) 
                    .then(data => {
                        commentsContainer.innerHTML = data;
                    })
                    .catch(error => {
                        console.error("Error fetching comments:", error);
                        commentsContainer.innerHTML = "<p>Failed to load comments.</p>";
                    });
            });
        });

        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });
</script>

</body>
</html>
