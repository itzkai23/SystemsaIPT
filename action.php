<?php
session_start();
include 'connect.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $post_id = $_POST['post_id'] ?? null;
    $comment_id = $_POST['comment_id'] ?? null;

    if ($action === 'delete_post') {
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $query = "DELETE FROM posts WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            echo "Post deleted successfully.";
        } else {
            echo "You do not have permission to delete posts.";
        }
    }

    if ($action === 'delete_comment') {
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $query = "DELETE FROM nf_comments WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            echo "Comment deleted successfully.";
        } else {
            echo "You do not have permission to delete comments.";
        }
    }

    if ($action === 'report_post') {
        if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $query = "INSERT INTO newsfeed_report (post_id, reported_by) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
            $stmt->execute();
            echo "Post reported successfully.";
        } else {
            echo "Admins cannot report posts.";
        }
    }

    if ($action === 'report_comment') {
        if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $query = "INSERT INTO newsfeed_report (comment_id, reported_by) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $comment_id, $_SESSION['user_id']);
            $stmt->execute();
            echo "Comment reported successfully.";
        } else {
            echo "Admins cannot report comments.";
        }
    }
}
