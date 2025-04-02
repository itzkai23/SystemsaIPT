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
    <link rel="stylesheet" href="css/verify.css">
</head>
<body>
<div class="buo">
    <h2>Enter OTP</h2>
    <form method="post">
        <!-- <input type="text" name="otp" placeholder="Enter OTP" required> -->
        <div class="otp-container">
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        <input type="text" name="otp[]" maxlength="1" oninput="moveToNext(this)" onkeydown="moveToPrev(event, this)" required>
        </div>
        <button class="verify-btn" type="submit">Verify</button>
        <a href="silog.php" class="resend">Back to Register</a>
    </form>
    
    </div>

    <script>
        function moveToNext(input) {
            if (input.value.length === 1) {
                let nextInput = input.nextElementSibling;
                if (nextInput) {
                    nextInput.focus();
                }
            }
        }

        function moveToPrev(event, input) {
            if (event.key === "Backspace" && input.value === "") {
                let prevInput = input.previousElementSibling;
                if (prevInput) {
                    prevInput.focus();
                }
            }
        }
    </script>
</body>
</html>
