<?php
require 'connect.php';

// Initialize variables
$professor_id = null;
$professor_name = $profrole = $prof_img = null;
$feedbackData = [];

// Check if professor_id is set
if (isset($_GET['professor_id'])) {
    $professor_id = $_GET['professor_id'];
} elseif (isset($_POST['professor_id'])) {
    $professor_id = $_POST['professor_id'];
}

// Validate professor_id before proceeding
if ($professor_id) {
    // Fetch professor details
    $profQuery = $conn->prepare("SELECT name, role, prof_img FROM professors WHERE id = ?");
    $profQuery->bind_param("i", $professor_id);
    $profQuery->execute();
    $profResult = $profQuery->get_result();

    if ($profResult->num_rows > 0) {
        $profData = $profResult->fetch_assoc();
        $professor_name = $profData['name'];
        $profrole = $profData['role'];
        $prof_img = !empty($profData['prof_img']) ? $profData['prof_img'] : "images/default_prof.jpg";
    } else {
        $professor_name = "Unknown Professor";
        $profrole = "N/A";
        $prof_img = "images/default_prof.jpg";
    }

    $profQuery->close();

    // Fetch feedback and comments
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

    ORDER BY date_posted DESC;";  // Latest entries first

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $professor_id, $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $feedbackData[] = [
            'feedback' => $row['feedback'] ?: null,
            'comment' => $row['comment'] ?: null,
            'comment_id' => $row['comment_id'] ?: null,
            'date_posted' => $row['date_posted']
        ];
    }

    $stmt->close();
}

// Fetch all professors
$professors = [];
$profResult = $conn->query("SELECT id, name, role, prof_img FROM professors");
while ($prof = $profResult->fetch_assoc()) {
    $professors[] = [
        'id' => $prof['id'],
        'name' => $prof['name'],
        'role' => $prof['role'],
        'prof_img' => !empty($prof['prof_img']) ? $prof['prof_img'] : "images/default_prof.jpg"
    ];
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
                data-image="<?php echo htmlspecialchars($prof['prof_img']); ?>">
                <div class="con-prof">
                    <img src="<?php echo htmlspecialchars($prof['prof_img']); ?>" alt="Professor Image">
                    <div>
                        <h5><?php echo htmlspecialchars($prof['name']); ?></h5>
                        <h5><?php echo htmlspecialchars($prof['role']); ?></h5>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Back to Home Button -->
    <a href="silog.html">Back</a>

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
                </div>

                <form method="GET">
                  <input type="hidden" name="professor_id" value="<?php echo $professor_id; ?>">
                </form>
                <!-- Display Submitted Data -->
                <div class="comdent">
                    <h3>Comments</h3>
                    <div class="com-scroll" id="commentsContainer">
                        <?php
                        if (!empty($feedbackData)) {
                            foreach ($feedbackData as $comm) {
                                echo '<div class="comment-box">';
                                echo '<div class="comment-text">';
                                echo '<strong>Anonymous</strong><br>';
                                if (!empty($comm['feedback'])) {
                                    echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                                } elseif (!empty($comm['comment'])) {
                                    echo '<p>' . htmlspecialchars($comm['comment']) . '</p>';
                                }
                                echo '<small>' . htmlspecialchars($comm['date_posted']) . '</small>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo "<p>No comments or feedback available.</p>";
                        }
                        ?>
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
                const name = this.getAttribute("data-name");
                const role = this.getAttribute("data-role");
                const image = this.getAttribute("data-image");
                
                document.getElementById("profName").textContent = name;
                document.getElementById("profRole").textContent = role;
                document.getElementById("profImg").src = image;
                
                modal.style.display = "block";
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
