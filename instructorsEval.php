<?php
require 'connect.php';

// Fetch all professors from the database
$result = $conn->query("SELECT id, name, role, prof_img FROM professors");

if (!$result) {
    die("Error fetching professors: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <Title>Faculty Evaluation</Title>

     <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #e3f2fd; /* Light blue background */
    padding: 20px;
    text-align: center;
}

h1 {
    font-size: 28px;
    color: #1565c0;
    margin-bottom: 20px;
}

/* Container */
.group-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

/* Profile Card */
.card {
    background: linear-gradient(145deg, #ffffff, #bbdefb); /* Soft blue gradient */
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    padding: 20px;
    text-align: center;
    width: 250px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
}

/* Profile Image */
.card img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
    margin-bottom: 10px;
    border: 3px solid #1565c0; /* Blue border */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover img {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Instructor Name */
.card h2 {
    font-size: 20px;
    margin-bottom: 5px;
    color: #0d47a1;
}

/* Role */
.card .role {
    font-size: 16px;
    font-weight: bold;
    color: #1565c0;
    font-family: 'Times New Roman', Times, serif;
    background-color: #bbdefb;
    padding: 5px 10px;
    border-radius: 5px;
    display: inline-block;
}


/* Button Link */
.btn-link {
    display: inline-block;
    background: linear-gradient(145deg, #1e88e5, #1565c0); /* Blue gradient */
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 20px;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    margin-top: 10px;
    cursor: pointer;
}

/* Hover Effect */
.btn-link:hover {
    background: linear-gradient(145deg, #1565c0, #0d47a1);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
}


     </style>
    </head>  
<body>
        <h1>Faculty Evaluation</h1>
        <br>
        <div class="group-container">

    <?php if ($result->num_rows > 0) { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="card">
            <img src="<?php echo !empty($row['prof_img']) ? htmlspecialchars($row['prof_img']) : 'images/facultyb.png'; ?>" 
                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                 width="150" height="150">
            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
            <p class="role"><?php echo htmlspecialchars($row['role']); ?></p>
            <a class="btn-link" href="gform.php?professor_id=<?php echo $row['id']; ?>">Evaluate</a>
        </div>
        <?php } ?>
        <?php } else { ?>
        <p>No professors available for evaluation.</p>
    <?php } ?>

</div>
    
    </body>
</html>