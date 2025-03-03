<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

if (isset($_POST['comment_id']) || isset($_POST['evaluation_id'])) {
    $conn->begin_transaction(); // Ensure integrity

    try {
        if (!empty($_POST['comment_id'])) {
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
        } elseif (!empty($_POST['evaluation_id'])) {
            $evaluation_id = intval($_POST['evaluation_id']);

            // Delete related reports first (fallback if ON DELETE CASCADE fails)
            $stmt1 = $conn->prepare("DELETE FROM reports WHERE evaluation_id = ?");
            $stmt1->bind_param("i", $evaluation_id);
            $stmt1->execute();
            $stmt1->close();

            // Delete the evaluation itself
            $stmt2 = $conn->prepare("DELETE FROM instructor_evaluation WHERE id = ?");
            $stmt2->bind_param("i", $evaluation_id);
            $stmt2->execute();
            $stmt2->close();
        }

        $conn->commit();
        echo "<script>alert('Deleted successfully!'); window.history.back();</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error deleting: " . $e->getMessage();
    }
}
?>
