<?php
session_start();
include 'connect.php'; // Ensure this contains the connectDatabase() function

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Ensure user is logged in
    $content = trim($_POST['content']);
    $image_url = null;

    // Handle image upload
    if (!empty($_FILES['image_url']['name'])) { // Changed 'picture_url' to 'image_url'
        $target_dir = "upload_post/";
        $target_file = $target_dir . basename($_FILES["image_url"]["name"]); // Changed 'picture_url' to 'image_url'
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) { // Changed 'picture_url' to 'image_url'
                $image_url = $target_file;
            }
        } else {
            echo "Invalid file type!";
            exit();
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_url, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $content, $image_url);

    if ($stmt->execute()) {
        header("Location: freedomwall.php"); // Redirect after successful post
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
