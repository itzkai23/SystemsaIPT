<?php
session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $professor_id = $_POST['professor_id'];
    // $professor_name = $_POST['professor_name'];
    $user_id = $_SESSION['user_id']; // Assuming user is logged in
    $reasons = isset($_POST['reason']) ? implode(", ", $_POST['reason']) : 'No reason provided';
    $details = isset($_POST['details']) ? htmlspecialchars($_POST['details']) : '';

    // Insert report into the database
    $stmt = $conn->prepare("INSERT INTO reports_prof (professor_id, user_id, reasons, details, report_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $professor_id, $user_id, $reasons, $details);
    
    if ($stmt->execute()) {
        echo "<script>
        alert('Report submitted successfully!');
        window.location.href = 'instructorsProfiles.php';
        </script>";    
        exit(); // Ensure script stops execution after redirect
    } else {
        echo "<script>alert('Failed to submit report. Please try again.'); window.history.back();</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
