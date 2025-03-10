<?php
session_start();
include 'connect.php'; // Database connection

// DELETE FUNCTIONALITY
if (isset($_POST['delete_post_id'])) {
    $post_id = intval($_POST['delete_post_id']);
    $deleteQuery = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        header("Location: freedom_wall.php?success=Post deleted successfully");
        exit();
    } else {
        header("Location: freedom_wall.php?error=Failed to delete post");
        exit();
    }
}

if (isset($_POST['delete_comment_id'])) {
    $comment_id = intval($_POST['delete_comment_id']);
    $deleteQuery = "DELETE FROM nf_comments WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $comment_id);

    if ($stmt->execute()) {
        header("Location: freedom_wall.php?success=Comment deleted successfully");
        exit();
    } else {
        header("Location: freedom_wall.php?error=Failed to delete comment");
        exit();
    }
}

// REPORT FUNCTIONALITY
if (isset($_POST['report_post_id'])) {
    $post_id = intval($_POST['report_post_id']);
    $reason = htmlspecialchars($_POST['report_reason']);

    $reportQuery = "INSERT INTO reports (content_id, type, reason) VALUES (?, 'post', ?)";
    $stmt = $conn->prepare($reportQuery);
    $stmt->bind_param("is", $post_id, $reason);

    if ($stmt->execute()) {
        header("Location: freedom_wall.php?success=Post reported successfully");
        exit();
    } else {
        header("Location: freedom_wall.php?error=Failed to report post");
        exit();
    }
}

if (isset($_POST['report_comment_id'])) {
    $comment_id = intval($_POST['report_comment_id']);
    $reason = htmlspecialchars($_POST['report_reason']);

    $reportQuery = "INSERT INTO reports (content_id, type, reason) VALUES (?, 'comment', ?)";
    $stmt = $conn->prepare($reportQuery);
    $stmt->bind_param("is", $comment_id, $reason);

    if ($stmt->execute()) {
        header("Location: freedom_wall.php?success=Comment reported successfully");
        exit();
    } else {
        header("Locationwou: freedom_wall.php?error=Failed to report comment");
        exit();
    }
}

header("Location: freedom_wall.php");
