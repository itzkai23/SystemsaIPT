<?php
require 'connect.php';
require 'vendor/autoload.php'; // Load PHPMailer if using Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['submit'])){
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $uname = trim($_POST["uname"]);
    $contact = trim($_POST["contact"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $section = trim($_POST["section"]);


    // Define the allowed email domain
    $allowed_domain = "@cityofmalabonuniversity.edu.ph";

    // Check if the email ends with the allowed domain
    if (substr($email, -strlen($allowed_domain)) !== $allowed_domain) {
        echo "<script>alert('Invalid email! Only emails with @cityofmalabonuniversity.edu.ph are allowed.'); window.location.href='silog.php';</script>";
        exit();
    }

    // Validate password strength (Minimum 8 characters, at least one letter and one number)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include at least one letter and one number.'); window.location.href='silog.php';</script>";
        exit();
    }

    // Check if the email already exists
    $select = "SELECT * FROM registration WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        echo "<script>alert('The email has already been used.'); window.location.href='silog.php';</script>";
        exit();
    } else {
        // Generate OTP
        $otp = rand(100000, 999999);
        session_start();
        $_SESSION['otp'] = $otp;
        $_SESSION['temp_user'] = [
            'fname' => $fname,
            'lname' => $lname,
            'uname' => $uname,
            'contact' => $contact,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'section' => $section
        ];        

        // Send OTP to email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'imnotkairri@gmail.com';
            $mail->Password = 'ivgu sjgc bznw ssxr'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'University Registration');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is: $otp. Please enter this code to complete your registration.";

            $mail->send();
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>
