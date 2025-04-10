<?php
session_start();
require '../connect.php';

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
    $professor_id = (int)$_POST['professor_id']; // Ensure integer

    // Retrieve all 20 integer-based questions from POST data
    $q1 = (int)$_POST['q1'];
    $q2 = (int)$_POST['q2'];
    $q3 = (int)$_POST['q3'];
    $q4 = (int)$_POST['q4'];
    $q5 = (int)$_POST['q5'];
    $q6 = (int)$_POST['q6'];
    $q7 = (int)$_POST['q7'];
    $q8 = (int)$_POST['q8'];
    $q9 = (int)$_POST['q9'];
    $q10 = (int)$_POST['q10'];
    $q11 = (int)$_POST['q11'];
    $q12 = (int)$_POST['q12'];
    $q13 = (int)$_POST['q13'];
    $q14 = (int)$_POST['q14'];
    $q15 = (int)$_POST['q15'];
    $q16 = (int)$_POST['q16'];
    $q17 = (int)$_POST['q17'];
    $q18 = (int)$_POST['q18'];
    $q19 = (int)$_POST['q19'];
    $q20 = (int)$_POST['q20'];

    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

    // Prepare SQL statement
    $sql = "INSERT INTO instructor_evaluation 
            (user_id, professor_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q18, q19, q20, feedback) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";  

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "iiiiiiiiiiiiiiiiiiiiiis", 
            $user_id, $professor_id, 
            $q1, $q2, $q3, $q4, $q5, 
            $q6, $q7, $q8, $q9, $q10, 
            $q11, $q12, $q13, $q14, $q15, 
            $q16, $q17, $q18, $q19, $q20, 
            $feedback
        );
        
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
