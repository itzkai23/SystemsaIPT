<?php
session_start();
include 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to report.");
}

if (isset($_POST['comment_id']) || isset($_POST['evaluation_id'])) {
    $user_id = $_SESSION['user_id'];

    if (!empty($_POST['comment_id'])) {
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
    } elseif (!empty($_POST['evaluation_id'])) {
        $evaluation_id = intval($_POST['evaluation_id']);

        // Prevent duplicate reports
        $check = $conn->prepare("SELECT id FROM reports WHERE evaluation_id = ? AND user_id = ?");
        $check->bind_param("ii", $evaluation_id, $user_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            die("<script>alert('You already reported this evaluation.'); window.history.back();</script>");
        }
        $check->close();

        // Insert report
        $stmt = $conn->prepare("INSERT INTO reports (evaluation_id, user_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $evaluation_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('Reported successfully!'); window.history.back();</script>";
}
?>
