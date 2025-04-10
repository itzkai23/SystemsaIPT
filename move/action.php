<?php
session_start();
include 'connect.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : null;

    if ($action === 'delete_post') {
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            if ($post_id) { // Ensure post_id is valid
                $query = "DELETE FROM posts WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                echo "<script> alert('Post deleted successfully.');  window.location.href = 'freedomwall.php'; </script>";
            } else {
                echo "<script> alert('Invalid post ID.'); window.location.href = 'freedomwall.php'; </script>";
            }
        } else {
            echo "<script> alert('You do not have permission to delete posts.'); window.location.href = 'freedomwall.php'; </script>";
        }
    }

    if ($action === 'delete_comment') {
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            if ($comment_id) { // Ensure comment_id is valid
                $query = "DELETE FROM nf_comments WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $comment_id);
                $stmt->execute();
                echo "<script> alert('Comment deleted successfully.');  window.location.href = 'freedomwall.php'; </script>";
            } else {
                echo "<script> alert('Invalid comment ID.'); window.location.href = 'freedomwall.php'; </script>";
            }
        } else {
            echo "<script> alert('You do not have permission to delete comments.'); window.location.href = 'freedomwall.php'; </script>";
        }
    }

    if ($action === 'report_post') {
        if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            if ($post_id) { // Ensure post_id is valid
                $check_query = "SELECT id FROM newsfeed_report WHERE post_id = ? AND reported_by = ?";
                $stmt = $conn->prepare($check_query);
                $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    echo "<script> alert('You have already reported this post.'); window.location.href = 'freedomwall.php'; </script>";
                } else {
                    $query = "INSERT INTO newsfeed_report (post_id, reported_by) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
                    $stmt->execute();
                    echo "<script> alert('Post reported successfully.'); window.location.href = 'freedomwall.php'; </script>";
                }
            } else {
                echo "<script> alert('Invalid post ID.'); window.location.href = 'freedomwall.php'; </script>";
            }
        } else {
            echo "<script> alert('Admins cannot report posts.');  window.location.href = 'freedomwall.php'; </script>";
        }
    }

    if ($action === 'report_comment') {
        if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            if ($comment_id) { // Ensure comment_id is valid
                $check_query = "SELECT id FROM newsfeed_report WHERE comment_id = ? AND reported_by = ?";
                $stmt = $conn->prepare($check_query);
                $stmt->bind_param("ii", $comment_id, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    echo "<script> alert('You have already reported this comment.'); window.location.href = 'freedomwall.php'; </script>";
                } else {
                    $query = "INSERT INTO newsfeed_report (comment_id, reported_by) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $comment_id, $_SESSION['user_id']);
                    $stmt->execute();
                    echo "<script> alert('Comment reported successfully.'); window.location.href = 'freedomwall.php'; </script>";
                }
            } else {
                echo "<script> alert('Invalid comment ID.'); window.location.href = 'freedomwall.php'; </script>";
            }
        } else {
            echo "<script> alert('Admins cannot report comments.'); window.location.href = 'freedomwall.php'; </script>";
        }
    }    
}
?>
