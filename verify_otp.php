<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_user'])) {
    header("Location: silog.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp_array = $_POST['otp']; // This will be an array of digits
    $entered_otp = implode("", $entered_otp_array); // Convert array to string

    if ($entered_otp == $_SESSION['otp']) {
        // Save the user data to the database
        $user = $_SESSION['temp_user'];
        $query = "INSERT INTO registration (fname, lname, uname, contact, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $user['fname'], $user['lname'], $user['uname'], $user['contact'], $user['email'], $user['password']);

        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION['otp']);
            unset($_SESSION['temp_user']);
            echo "<script>
                    alert('Successfully created an account!');
                    window.location.href='silog.php';
                  </script>";
            exit();
        } else {
            echo 'Error inserting data.';
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.'); window.location.href='verify_otp.php';</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="css/verify.css">
</head>
<body>
    <div class="buo">
    <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Email Icon">
    <h2>Account Verification</h2>
    <p class="guide">Go to Gmail using the CMU account you register. If you can't find the otp on your inbox, check your spam messages and look for "University Registration", that's where you'll find your otp.</p>
    <p class="guide">This is the last step of your registration. It's a necessary step to verify that you're the real owner of the Gmail you are using to register in order to avoid unwanted risks such as identity theft. Thank you for understanding.</p>
    
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
