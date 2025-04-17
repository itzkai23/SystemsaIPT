<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/forgotpass.css">
</head>
<body>
    <div class="container">
        <div class="forgot-image">
        <img src="../images/logo3.png" alt="" class="colored-img">
        <img src="../images/head2.png" alt="" class="colored-text-img">
        </div>
        <h2>Forgot Password?</h2>
        <p>Enter your CMU email and we'll send you an otp to your email on spam to reset your password</p>
        <form action="otppass.php" method="post">
            <input type="email" name="email" placeholder="Enter your CMU email" required>
            <button type="submit">Send</button>
        </form>
        <!-- <p class="message">Password reset will be sent to your email.</p> -->
        <a href="../Authentication/silog.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
