<?php
require 'connect.php';

if(isset($_POST['submit'])){
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $uname = trim($_POST["uname"]);
    $contact = trim($_POST["contact"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $select = "SELECT * FROM registration WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        echo 'The email has already been used.';
        exit();
    } else {
        // Insert new user into the database with hashed password
        $query = "INSERT INTO registration (fname, lname, uname, contact, email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        // Use "ssssss" to match data types (contact should be string if storing as varchar)
        mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $uname, $contact, $email, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: silog.php"); // Redirect to login page
            exit();
        } else {
            echo 'Error inserting data.';
        }
    }
}
?>
