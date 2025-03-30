<?php
session_start();
include 'connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST["otp"]);

    // Check if session OTP exists
    if (!isset($_SESSION['otp']) || !isset($_SESSION['email'])) {
        echo "<script>alert('Session expired. Please try again.'); window.location.href='forgotpass.php';</script>";
        exit();
    }

    $stored_otp = $_SESSION['otp'];
    $email = $_SESSION['email'];

    // Verify OTP
    if ($entered_otp == $stored_otp) {
        // OTP is correct, set session variable for password reset
        $_SESSION['reset_email'] = $email;  // Ensure resetpass.php can access the email
        unset($_SESSION['otp']); // Remove OTP after successful verification

        // Redirect to reset password page
        header("Location: resetpass.php");
        exit();
    } else {
        // Wrong OTP, redirect back with an error
        echo "<script>alert('Invalid OTP. Please try again.'); window.location.href='check_otp.php';</script>";
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <form action="" method="POST">
        <input type="number" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
