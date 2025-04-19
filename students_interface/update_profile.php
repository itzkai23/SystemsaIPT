<?php
require '../connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Error: User not logged in.");
    }

    $username = $_POST['uname'] ?? null;
    $current_password = $_POST['current_password'] ?? null;
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Fetch existing credentials
    $stmt = $conn->prepare("SELECT uname, password FROM registration WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if (!$userData) {
        die("User not found.");
    }

    // Update username only if provided and different
    if (!empty($username) && $username !== $userData['uname']) {
        $updateUsername = $conn->prepare("UPDATE registration SET uname = ? WHERE id = ?");
        $updateUsername->bind_param("si", $username, $user_id);
        if ($updateUsername->execute()) {
            $_SESSION['user_name'] = $username; // ensure this matches your system usage
        } else {
            die("Error updating username.");
        }
    }

    // Update password only if new password fields are filled
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        // Validate current password
        if (empty($current_password) || !password_verify($current_password, $userData['password'])) {
            die("Current password is incorrect or not provided.");
        }

        // Validate new passwords
        if (empty($new_password) || empty($confirm_password)) {
            die("New password and confirm password fields are required.");
        }

        if ($new_password !== $confirm_password) {
            die("New password and confirmation do not match.");
        }

        // Hash and update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updatePassword = $conn->prepare("UPDATE registration SET password = ? WHERE id = ?");
        $updatePassword->bind_param("si", $hashed_password, $user_id);
        if (!$updatePassword->execute()) {
            die("Error updating password.");
        }
    }

    // Final redirection
    header("Location: upf.php?success=Credentials updated");
    exit();
}
?>
