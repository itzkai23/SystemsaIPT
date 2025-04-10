<?php
session_start();
require '../connect.php';

// Ensure only admins can delete
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

// Check if record_id is set
if (isset($_POST['record_id'])) {
    $record_id = intval($_POST['record_id']);

    // Prepare delete query
    $query = "DELETE FROM instructor_evaluation WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: eval_record.php?success=deleted");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "No record ID received.";
}
?>
