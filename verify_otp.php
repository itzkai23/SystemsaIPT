<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_user'])) {
    header("Location: silog.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    if ($entered_otp == $_SESSION['otp']) {
        // Save the user data to the database
        $user = $_SESSION['temp_user'];
        $query = "INSERT INTO registration (fname, lname, uname, contact, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $user['fname'], $user['lname'], $user['uname'], $user['contact'], $user['email'], $user['password']);

        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION['otp']);
            unset($_SESSION['temp_user']);
            header("Location: silog.php"); // Redirect to login page
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
</head>
<body>
    <h2>Enter OTP</h2>
    <form method="post">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
