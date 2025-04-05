<?php
require 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Error: User not logged in.");
    }

    // Retrieve submitted values (optional fields)
    $f_name = $_POST['fname'] ?? '';
    $l_name = $_POST['lname'] ?? '';
    $bday = $_POST['Birthday'] ?? '';
    $con = $_POST['contact'] ?? '';
    $em = $_POST['email'] ?? '';

    // Fetch current user data for fallback on empty fields
    $selectQuery = "SELECT fname, lname, Birthday, contact, email FROM registration WHERE id=?";
    $stmt = $conn->prepare($selectQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentData = $result->fetch_assoc();

    if ($currentData) {
        // Use current values if new values are empty
        $f_name = $f_name ?: $currentData['fname'];
        $l_name = $l_name ?: $currentData['lname'];
        $bday   = $bday   ?: $currentData['Birthday'];
        $con    = $con    ?: $currentData['contact'];
        $em     = $em     ?: $currentData['email'];

        // Update the record
        $updateQuery = "UPDATE registration SET fname=?, lname=?, Birthday=?, contact=?, email=? WHERE id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssssi", $f_name, $l_name, $bday, $con, $em, $user_id);

        if ($stmt->execute()) {
            // Update session values
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
    } else {
        echo "User not found.";
    }
}
?>
