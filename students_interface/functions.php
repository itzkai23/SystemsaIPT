<?php
require '../connect.php';

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

    $submitted_at = null;

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($submitted_at);
        $stmt->fetch();

        if (!empty($submitted_at)) {
            $last_evaluation_time = strtotime($submitted_at);
            $current_time = time();
            $days_since_last_evaluation = ($current_time - $last_evaluation_time) / (60 * 60 * 24);

            if ($days_since_last_evaluation < 84) {
                return false; // Within 12 weeks restriction
            }
        }
    }

    return true; // Either no record, or 12 weeks passed
}
?>
