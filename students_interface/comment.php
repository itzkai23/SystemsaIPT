<?php
session_start();
require '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        die("User not logged in.");
    }

    if (!isset($_POST['professor_id']) || !is_numeric($_POST['professor_id'])) {
        die("Invalid professor selected.");
    }

    $user_id = $_SESSION['user_id'];
    $professor_id = (int) $_POST['professor_id']; // Ensure it's an integer
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

    // Prepare SQL statement
    $sql = "INSERT INTO comments (user_id, professor_id, comment) 
            VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $user_id, $professor_id, $comment);

        if ($stmt->execute()) {
            $stmt->close();
               // Redirect back to the same page
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit(); // Ensure script stops execution after redirect
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

if ($conn) {
    $conn->close();
}
?>
