<?php
session_start();

function restrict_to_admin() {
    if (!isset($_SESSION['role'])) {
        // No role set, treat as unauthenticated
        header("Location: ../silog.php?error=unauthorized");
        exit();
    }

    switch ($_SESSION['role']) {
        case 'admin':
            // Allow access
            return;
        case 'faculty':
            header("Location: ../students_interface/instructorsProfiles.php");
            exit();
        case 'student':
            header("Location: ../students_interface/home.php");
            exit();
        default:
            // Catch any unexpected roles
            header("Location: ../silog.php?error=unauthorized");
            exit();
    }
}
