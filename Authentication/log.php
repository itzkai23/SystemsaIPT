<?php
session_start();

if (isset($_SESSION['user_name'])) {
    header("Location: home.php");
    exit();
}

require './connect.php';

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
            // Store user data in session
            $_SESSION['user_name'] = $row['uname']; 
            $_SESSION['f_name'] = $row['fname'];
            $_SESSION['l_name'] = $row['lname'];
            $_SESSION['em'] = $row['email'];
            $_SESSION['con'] = $row['contact']; 
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['pic'] = !empty($row['picture']) ? $row['picture'] : "../images/icon.jpg";
            $_SESSION['Birthday'] = $row['Birthday'];
            $_SESSION['is_admin'] = $row['is_admin']; // Store admin status

            // Redirect based on user type
            if ($row['is_admin'] == 1) {
                header('Location: admin.php'); // Redirect admin
            } else {
                header('Location: home.php'); // Redirect regular user
            }
            exit();
        } else {
            echo "<script> 
            alert('Incorrect Password!');
            window.location.href = 'silog.php';
             </script>";
        }
    } else {
        echo 'User not found!';
    }
} else {
    echo 'Invalid access.';
}
?>
