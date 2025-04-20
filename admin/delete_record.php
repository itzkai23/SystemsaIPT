<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

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
