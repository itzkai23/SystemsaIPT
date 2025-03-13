<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

// Handle "Mark as Reviewed" button submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);

    $updateQuery = $conn->prepare("UPDATE reports SET status = 'reviewed' WHERE id = ?");
    $updateQuery->bind_param("i", $report_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('Report marked as reviewed!'); window.location.href='admin_reports.php';</script>";
    } else {
        echo "<script>alert('Error updating report.');</script>";
    }

    $updateQuery->close();
}

// Fetch reported comments
$reportsQuery = $conn->query("
    SELECT r.id AS report_id, 
           c.comment AS reported_text, 
           u.fname, u.lname, 
           r.reported_at, 
           r.status 
    FROM reports r 
    LEFT JOIN comments c ON r.comment_id = c.id 
    JOIN registration u ON r.user_id = u.id
    ORDER BY r.reported_at DESC
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Comments</title>
    <link rel="stylesheet" href="css/repcom.css">
</head>
<body>

    <h3>Reported Comments & Evaluations</h3>

    <!-- Buttons to reveal content -->
    <div class="repcomcon-btn">
            <!-- <a href="#report1">ComReport</a>
            <a href="#report2">Report</a> -->
            <a href="#report2">Student Reports</a>
            <a href="#report1">Professor Reports</a>
        </div>

    <div class="rec-com-container">

    <div id="report1" class="report-container">
    <?php while ($report = $reportsQuery->fetch_assoc()) : ?>
        <div class='report-box'>
            <p><strong><?php echo htmlspecialchars($report['fname'] . " " . $report['lname']); ?></strong> reported:</p>
            <div class="reported-text"><?php echo htmlspecialchars($report['reported_text']); ?></div>
            <small>Reported on: <?php echo htmlspecialchars($report['reported_at']); ?></small>
            
            <?php if ($report['status'] === 'pending') : ?>
                <form method="post">
                    <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                    <button type="submit">Mark as Reviewed</button>
                </form>
            <?php else : ?>
                <span class="reviewed">Reviewed</span>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

        <div class="report-box1">
            <p><strong>Jane Smith</strong> reported:</p>
            <div class="reported-text">Another reported comment here.</div>
            <small>Reported on: 2024-03-09</small>
            <span class="reviewed">Reviewed</span>
        </div>
    </div>

    </div>

</body>
</html>
