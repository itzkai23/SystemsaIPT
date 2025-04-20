<?php
session_start();

function restrict_to_student() {
    if (!isset($_SESSION['role'])) {
        // Not logged in or no role set
        header("Location: ../silog.php?error=unauthorized");
        exit();
    }

    switch ($_SESSION['role']) {
        case 'student':
            // Allow access
            return;
        case 'admin':
            header("Location: ../admin/admin.php");
            exit();
        case 'faculty':
            header("Location: ../students_interface/instructorsProfiles.php");
            exit();
        default:
            header("Location: ../silog.php?error=unauthorized");
            exit();
    }
}
