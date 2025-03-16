<?php
session_start();
include 'connect.php';

// Get professor data from URL
$professor_id = isset($_GET['id']) ? $_GET['id'] : '';
$professor_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown Professor';
$professor_img = isset($_GET['img']) ? htmlspecialchars($_GET['img']) : 'images/facultyb.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Professor</title>
    <link rel="stylesheet" href="css/report_prof.css">
</head>
<body>
    <div class="box">
        <!-- Display Professor's Image -->
        <img src="<?php echo $professor_img; ?>" alt="<?php echo $professor_name; ?>"> 
        <h2>Report a Professor</h2>    
        <h4><?php echo $professor_name; ?></h4>  

        <form action="submit_report.php" method="post">
            <input type="hidden" name="professor_id" value="<?php echo $professor_id; ?>">
           
            <div class="checkbox-container">
                <label><input type="checkbox" name="reason[]" value="Unfair Grading"> Unfair Grading</label>
                <label><input type="checkbox" name="reason[]" value="Lack of Professionalism"> Lack of Professionalism</label>
                <label><input type="checkbox" name="reason[]" value="Poor Communication"> Poor Communication</label>
                <label><input type="checkbox" name="reason[]" value="Frequent Absences or Tardiness"> Frequent Absences or Tardiness</label>
                <label><input type="checkbox" name="reason[]" value="Unorganized Teaching"> Unorganized Teaching</label>
                <label><input type="checkbox" name="reason[]" value="Bias or Favoritism"> Bias or Favoritism</label>
                <label><input type="checkbox" id="otherCheckbox" name="reason[]" value="Others"> Others:</label>
            </div>

            <textarea name="details" placeholder="Optional: Provide additional details..."></textarea>

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const otherCheckbox = document.getElementById("otherCheckbox");
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#otherCheckbox)');

            otherCheckbox.addEventListener("change", function () {
                if (this.checked) {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                    });
                } else {
                    checkboxes.forEach(checkbox => {
                        checkbox.disabled = false;
                    });
                }
            });
        });
    </script>
</body>
</html>
