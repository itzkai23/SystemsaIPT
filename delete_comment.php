<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

if (isset($_POST['comment_id'])) {
    $conn->begin_transaction(); // Ensure integrity

    try {
        $comment_id = intval($_POST['comment_id']);

        // Delete related reports first (fallback if ON DELETE CASCADE fails)
        $stmt1 = $conn->prepare("DELETE FROM reports WHERE comment_id = ?");
        $stmt1->bind_param("i", $comment_id);
        $stmt1->execute();
        $stmt1->close();

        // Delete the comment itself
        $stmt2 = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt2->bind_param("i", $comment_id);
        $stmt2->execute();
        $stmt2->close();

        $conn->commit();
        echo "<script>alert('Comment deleted successfully!'); window.history.back();</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error deleting comment: " . $e->getMessage();
    }
}
?>
