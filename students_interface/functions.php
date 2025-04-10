<?php
require '../connect.php'; // Ensure database connection is available

function canEvaluate($conn, $user_id, $professor_id) {
    $query = "SELECT submitted_at FROM instructor_evaluation 
              WHERE user_id = ? AND professor_id = ? 
              ORDER BY submitted_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $user_id, $professor_id);
    $stmt->execute();
    $stmt->store_result();

    // Initialize variable to avoid 'undefined variable' error
    $submitted_at = null;

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($submitted_at);
        if ($stmt->fetch() && !empty($submitted_at)) { // ✅ Ensure fetch() is successful
            $last_evaluation_time = strtotime($submitted_at);
            $current_time = time();
            $days_since_last_evaluation = ($current_time - $last_evaluation_time) / (60 * 60 * 24);

            if ($days_since_last_evaluation < 30) {
                return false; // ❌ Student must wait 30 days
            }
        }
    }

    return true; // ✅ Student is allowed to evaluate
}
?>
