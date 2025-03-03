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

// Fetch reported comments and evaluations
$reportsQuery = $conn->query("
    SELECT r.id AS report_id, 
           COALESCE(c.comment, ie.feedback) AS reported_text, 
           u.fname, u.lname, 
           r.reported_at, 
           r.status 
    FROM reports r 
    LEFT JOIN comments c ON r.comment_id = c.id 
    LEFT JOIN instructor_evaluation ie ON r.evaluation_id = ie.id 
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
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .report-box { 
            border: 1px solid #ccc; 
            background: white; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 8px; 
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); 
        }
        .reviewed { color: green; font-weight: bold; }
        .reported-text { 
            background: #f9f9f9; 
            padding: 10px; 
            border-radius: 5px; 
            white-space: pre-wrap; /* Keeps text formatting */
        }
        button { 
            background-color: blue; 
            color: white; 
            border: none; 
            padding: 8px 12px; 
            cursor: pointer; 
            border-radius: 5px; 
            margin-top: 5px; 
        }
        button:hover { background-color: darkblue; }
    </style>
</head>
<body>

    <h3>Reported Comments & Evaluations</h3>

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

</body>
</html>
