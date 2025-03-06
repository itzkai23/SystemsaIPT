<?php
session_start();
include 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to report.");
}

if (isset($_POST['comment_id'])) {
    $user_id = $_SESSION['user_id'];
    $comment_id = intval($_POST['comment_id']);

    // Prevent duplicate reports
    $check = $conn->prepare("SELECT id FROM reports WHERE comment_id = ? AND user_id = ?");
    $check->bind_param("ii", $comment_id, $user_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        die("<script>alert('You already reported this comment.'); window.history.back();</script>");
    }
    
    $check->close();

    // Insert report
    $stmt = $conn->prepare("INSERT INTO reports (comment_id, user_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Reported successfully!'); window.history.back();</script>";
}
?>
