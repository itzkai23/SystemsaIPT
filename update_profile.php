<?php
require 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $f_name = $_POST['fname'];
    $l_name = $_POST['lname'];
    $bday = $_POST['Birthday'];
    $con = $_POST['contact'];
    $em = $_POST['email'];

    $updateQuery = "UPDATE registration SET fname=?, lname=?, Birthday=?, contact=?, email=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $f_name, $l_name, $bday, $con, $em, $user_id);

    if ($stmt->execute()) {
        $_SESSION['fname'] = $f_name;
        $_SESSION['lname'] = $l_name;
        $_SESSION['Birthday'] = $bday;
        $_SESSION['contact'] = $con;
        $_SESSION['email'] = $em;
        header("Location: upf.php?success=Profile updated");
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>
