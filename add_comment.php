<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"]) && isset($_POST["comment"])) {
    if (!isset($_SESSION["user_id"])) {
        echo "Error: You must be logged in to comment.";
        exit();
    }

    $post_id = intval($_POST["post_id"]);
    $user_id = $_SESSION["user_id"];
    $comment = trim($_POST["comment"]);

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO nf_comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);

        if ($stmt->execute()) {
            header("Location: freedomwall.php"); // Refresh page after posting
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Comment cannot be empty!";
    }
}
$conn->close();
?>
