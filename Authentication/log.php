<?php
session_start();

if (isset($_SESSION['user_name'])) {
    header("Location: ../students_interface/home.php");
    exit();
}

require '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub'])) {
    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);

    $select = "SELECT * FROM registration WHERE uname = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Session variables
            $_SESSION['user_name'] = $row['uname']; 
            $_SESSION['f_name'] = $row['fname'];
            $_SESSION['l_name'] = $row['lname'];
            $_SESSION['em'] = $row['email'];
            $_SESSION['con'] = $row['contact']; 
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['pic'] = !empty($row['picture']) ? $row['picture'] : "../images/icon.jpg";
            $_SESSION['Birthday'] = $row['Birthday'];
            $_SESSION['section'] = $row['section'];
            $_SESSION['is_admin'] = $row['is_admin'];

            // Redirect based on admin status
            $redirectUrl = $row['is_admin'] == 1 ? '../admin/admin.php' : '../students_interface/home.php';
            header("Location: $redirectUrl");
            exit();
        } else {
            // Log invalid password attempt
            error_log("Invalid password attempt for username: $uname", 0);
            // Redirect to error page with a specific message
            header("Location: silog.php?error=incorrect_password");
            exit();
        }
    } else {
        // Log user not found
        error_log("User not found: $uname", 0);
        // Redirect to error page with a specific message
        header("Location: silog.php?error=user_not_found");
        exit();
    }
} else {
    // Generic error logging for unexpected access
    error_log("Invalid access attempt.", 0);
    echo 'Invalid access.';
}
?>
