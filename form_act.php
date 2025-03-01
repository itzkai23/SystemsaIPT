<?php
session_start();
require 'connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure session user_id exists
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }

    if (!isset($_POST['professor_id']) || empty($_POST['professor_id'])) {
        die("No professor was selected.");
    }

    $user_id = $_SESSION['user_id']; // Get user ID from session
    $professor_id = $_POST['professor_id']; // Keep as string if needed
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];
    $q4 = $_POST['q4'];
    $q5 = $_POST['q5'];
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

    // Prepare SQL statement
    $sql = "INSERT INTO instructor_evaluation (user_id, professor_id, q1, q2, q3, q4, q5, feedback) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";  

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iissssss", $user_id, $professor_id, $q1, $q2, $q3, $q4, $q5, $feedback);
        
        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['professor_id'] = null; // Clear professor session
            $stmt->close();
            $conn->close();
            // Redirect back to the same page
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit(); // Ensure script stops execution after redirect
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>
