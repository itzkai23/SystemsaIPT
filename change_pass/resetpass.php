<?php
session_start();
include '../connect.php'; // Ensure this file correctly connects to your database

// Check if the user has an email stored from OTP verification
if (!isset($_SESSION['reset_email'])) {
    header('Location: forgotpass.php'); // Redirect if no verified email
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE registration SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            session_destroy(); // Clear session
            header('Location: ../Authentication/silog.php?reset=success'); // Redirect to login
            exit();
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/resetpass.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Enter your new password</p>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post" action="">
            <input type="password" name="new-password" placeholder="New Password" required>
            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <a href="../Authentication/silog.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
