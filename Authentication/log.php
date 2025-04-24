<?php
session_start();

// If already logged in, redirect based on role
if (isset($_SESSION['user_name']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: ../admin/admin.php");
            break;
        case 'faculty':
            header("Location: ../students_interface/instructorsProfiles.php");
            break;
        default:
            header("Location: ../students_interface/home.php");
    }
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
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            switch ($row['role']) {
                case 'admin':
                    header("Location: ../admin/admin.php");
                    break;
                case 'faculty':
                    header("Location: ../students_interface/instructorsProfiles.php");
                    break;
                default:
                    header("Location: ../students_interface/home.php");
            }
            exit();
        } else {
            // Log invalid password attempt
            error_log("Invalid password attempt for username: $uname", 0);
            $_SESSION['login_error'] = 'Invalid username or password.';
            header("Location: silog.php");
            exit();
        }
    } else {
        // Log user not found
        error_log("User not found: $uname", 0);
        $_SESSION['login_error'] = 'User not found.';
        header("Location: silog.php");
        exit();
    }
} else {
    // Generic error logging for unexpected access
    error_log("Invalid access attempt.", 0);
    echo 'Invalid access.';
}
?>
