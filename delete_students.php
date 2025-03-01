<?php
session_start();
require 'connect.php';

// Ensure only admins can delete
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

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
