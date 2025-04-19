<?php
session_start();
require '../connect.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'], $_POST['comment_id'], $_SESSION['student_id'])) {
    $reply = trim($_POST['reply_text']);
    $comment_id = intval($_POST['comment_id']);
    $student_id = $_SESSION['student_id'];

    if (!empty($reply)) {
        $stmt = $conn->prepare("INSERT INTO comment_replies (comment_id, student_id, reply_text, date_posted) VALUES (?, ?, ?, NOW())");
        
        // Bind parameters and check for errors
        if ($stmt) {
            $stmt->bind_param("iis", $comment_id, $student_id, $reply);
            $stmt->execute();

            if ($stmt->error) {
                // If an error occurs, print it
                echo "Error executing query: " . $stmt->error;
            } else {
                echo "Reply successfully posted!";
            }
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
