<?php
session_start();
include 'connect.php'; // Ensure this contains the connectDatabase() function

// Keep your existing default image
$default_image = "images/icon.jpg";

// Use session to get the latest profile picture
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;

// Force-refresh the image to prevent caching issues
$current_image .= "?t=" . time();

// Fetch all users from the registration table
$studentsQuery = "SELECT fname, lname, picture FROM registration WHERE is_admin = 0 ORDER BY fname ASC";
$studentsResult = $conn->query($studentsQuery);

// Fetch posts from the database
$query = "SELECT p.id, p.content, p.image_url, p.created_at, r.fname, r.lname, r.picture 
          FROM posts p 
          JOIN registration r ON p.user_id = r.id 
          ORDER BY p.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freedom Wall Feed</title>
    <link rel="stylesheet" href="css/freewall.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Rowdies:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="#">
                    <img src="images/logo.png" alt="Logo">
                </a>
            </div>
            <input type="checkbox" id="menu-toggle" class="menu-checkbox">
            <label for="menu-toggle" class="menu-icon">&#9776;</label>
            <ul class="nav-links">
                <li><a href="#" class="tooltip"><img src="images/home-icon.png" alt="Home"><span class="tooltip-text">Home</span></a></li>
                <li><a href="#" class="tooltip"><img src="images/posts-icon.png" alt="Posts"><span class="tooltip-text">Posts</span></a></li>
                <li><a href="#" class="tooltip"><img src="images/profile-icon.png" alt="Profile"><span class="tooltip-text">Profile</span></a></li>
                <li><a href="#" class="tooltip"><img src="images/settings-icon.png" alt="Settings"><span class="tooltip-text">Settings</span></a></li>
            </ul>
        </div>
    </nav>
 
    <header>
        <h1>Freedom Wall</h1>
    </header>
    <div class="container">

        <!-- Main Content Section -->
        <main>
            <!-- Posting New Article (Only Text) -->
            <form action="create_post.php" method="POST" enctype="multipart/form-data">
                <section class="freewall-post">
                    <img src="<?php echo htmlspecialchars($current_image); ?>" class="picture" title="Click to upload image"/>
                    <label for="termsCheckbox" class="rant-post">What's on your mind?</label>
                </section>
                <input type="checkbox" id="termsCheckbox" class="modal-toggle">
                <div class="modal">
                    <div class="modal-content">
                        <label for="termsCheckbox" class="close">&times;</label>
                        <h3>Create Post</h3>
                        <div>
                            <img src="<?php echo htmlspecialchars($current_image); ?>" class="picture" title="Click to upload image"/>
                            <h3><?php echo htmlspecialchars($_SESSION['f_name']) . " " . htmlspecialchars($_SESSION['l_name']); ?></h3>
                        </div>
                        <div class="post-file">
                            <p>Add to your post</p>
                            <label for="fileinput">
                                <img src="images/photo-icon.jpg" alt="images">
                            </label>
                            <input type="file" id="fileinput" name="image_url" accept="image/*" style="display:none;"/>
                        </div>
                        <textarea name="content" placeholder="What's on your mind?" required></textarea>
                        <button type="submit" name="submit">Post</button>
                    </div>
                </div>
            </form>

            <!-- Display Posts from Database -->
            <?php while ($row = $result->fetch_assoc()) { ?>
                <article class="news-item">
                    <div class="users-posted">
                        <img src="<?php echo htmlspecialchars($row['picture'] ? $row['picture'] : 'images/icon.jpg'); ?>" alt="user-photo">
                        <p><?php echo htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']); ?></p>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <?php if (!empty($row['image_url'])) { ?>
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Post Image" class="post-image">
                    <?php } ?>
                    <small>Posted on: <?php echo htmlspecialchars($row['created_at']); ?></small>

                    <!-- Comments Section -->
                    <div class="comments">
                        <h3>Comments</h3>
                        <div class="textarea-btn-con">
                            <textarea type="text" placeholder="comment..."></textarea>
                            <button><img src="images/sends.png" alt=""></button>
                        </div>
                    </div>
                </article><br>
            <?php } ?>
        </main>

         <!-- Sidebar for Categories -->
         <aside class="sidebar">
            <h3>Students</h3>          
                <?php
                if ($studentsResult->num_rows > 0) {
                    while ($row = $studentsResult->fetch_assoc()) {
                        // Use the stored profile picture or default if empty
                        $profilePic = !empty($row['picture']) ? $row['picture'] : 'images/icon.jpg';

                        echo "<div class='sidebar-pfp'>";
                        echo "<img src='" . htmlspecialchars($profilePic) . "' alt='Profile pic'>";
                        echo "<p>" . htmlspecialchars($row['fname'] . " " . $row['lname']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No students found.</p>";
                }
                ?>
            </aside>

    </div>

</body>
</html>