<?php
session_start();
include "connect.php"; // Ensure this contains a valid MySQLi connection

if(isset($_POST["btnup"])){
    $user_id = $_POST["id"]; // Use "id" as per your database
    $name = $_FILES['picture_url']['name'];
    $tmp_name = $_FILES['picture_url']['tmp_name'];
    $upload_dir = "up_image/";

    // Validate if a file was uploaded
    if($name){
        $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION)); // Get file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($file_extension, $allowed_extensions)) {
            die("Error: Only JPG, JPEG, and PNG files are allowed.");
        }

        // Define unique filename with extension
        $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
        $picture = $upload_dir . $new_file_name;

        // Move the uploaded file
        if (move_uploaded_file($tmp_name, $picture)) {
            // Debugging: Check if move was successful
            if (!file_exists($picture)) {
                die("Error: File was not saved correctly.");
            }

            // Update database with new profile picture using MySQLi
            $sql_update_statement = "UPDATE registration SET picture = ? WHERE id = ?"; // Now using "id"
            $stmt = mysqli_prepare($conn, $sql_update_statement);
            mysqli_stmt_bind_param($stmt, "si", $picture, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Debugging: Check if the query executed correctly
                if (mysqli_affected_rows($conn) > 0) {
                    // Update session variable with new image path
                    $_SESSION['pic'] = $picture;

                    echo '<div id="message_success">Picture changed</div>';
                    echo '<script>document.location="upf.php";</script>';
                    exit();
                } else {
                    die("Error: Database update failed.");
                }
            } else {
                die("Error: Query execution failed.");
            }
        } else {
            die("Error uploading file.");
        }
    } else {
        die("No file uploaded.");
    }
}
?>
