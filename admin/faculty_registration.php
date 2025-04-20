<?php
require '../connect.php';
require '../Authentication/restrict_to_admin.php';
restrict_to_admin(); // Redirects if not admin

// Handle Faculty Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_faculty'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);
    
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert faculty data into the registration table
        $insertFaculty = "INSERT INTO registration (fname, lname, uname, password, role) VALUES (?, ?, ?, ?, 'faculty')";
        $stmtInsert = mysqli_prepare($conn, $insertFaculty);
        mysqli_stmt_bind_param($stmtInsert, "ssss", $fname, $lname, $uname, $hashed_password);
        
        if (mysqli_stmt_execute($stmtInsert)) {
            echo "Faculty registered successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
?>
