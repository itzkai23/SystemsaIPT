<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

// Check if record_id is set
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Prepare delete query
    $query = "DELETE FROM registration WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: stundentlist.php?success=deleted");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "No record ID received.";
}
?>
