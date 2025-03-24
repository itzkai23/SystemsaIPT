<?php
session_start();
include 'connect.php'; // Ensure this contains the connectDatabase() function

$default_image = "images/icon.jpg";
$current_image = isset($_SESSION["pic"]) && !empty($_SESSION["pic"]) ? $_SESSION["pic"] : $default_image;
$current_image .= "?t=" . time(); // Prevent caching issues

// Fetch all students (excluding admins)
$studentsQuery = "SELECT fname, lname, picture FROM registration WHERE is_admin = 0 ORDER BY fname ASC";
$studentsResult = $conn->query($studentsQuery);

// Fetch all posts
$query = "SELECT p.id, p.content, p.image_url, p.created_at, r.fname, r.lname, r.picture 
          FROM posts p 
          JOIN registration r ON p.user_id = r.id 
          ORDER BY p.created_at DESC";
$result = $conn->query($query);

// Prepare the comments query (to be executed inside the loop)
$commentsQuery = "SELECT c.comment, c.created_at, r.fname, r.lname, r.picture 
                  FROM nf_comments c 
                  JOIN registration r ON c.user_id = r.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at ASC";
$stmt = $conn->prepare($commentsQuery); // Prepare once
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
                    <img src="images/jpcs.jpg" alt="Logo">
                </a>
                <h1>Freedom Wall</h1>
            </div>
            <input type="checkbox" id="menu-toggle" class="menu-checkbox">
            <label for="menu-toggle" class="menu-icon">&#9776;</label>
            <ul class="nav-links">
                <li><a href="home.php" class="tooltip"><img src="images/homer1.png" alt="Home"><span class="tooltip-text">Home</span></a></li>
                <li><a href="freedomwall.php" class="tooltip"><img src="images/newsfeed1.png" alt="Newsfeed"><span class="tooltip-text">Newsfeed</span></a></li>
                <li><a href="instructorsProfiles.php" class="tooltip"><img src="images/fapro1.png" alt="Faculty Profiles"><span class="tooltip-text">Faculty Profiles</span></a></li>
                <li><a href="#" class="tooltip"><img src="images/annce.png" alt="Announcement"><span class="tooltip-text">Announcement</span></a></li>
            </ul>
        </div>

        <div class="right-section">                              
   
   <div class="logpos">
   
       <div class="logout-container"> 
         <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" id="logoutButton">
         <div class="logout-dropdown" id="logoutDropdown">
                 <a href="home.php" class="logpf-con">
                   <img src="<?php echo htmlspecialchars($current_image); ?>" class="piclog" alt="picture">
                   <h4><?php echo htmlspecialchars($_SESSION['f_name']) ." ".($_SESSION['l_name']);?></h4>
                 </a>
               
                <div class="dlog-icon">
                 <Img src="images/nfeed.png">
                 <a class="a-pf" href="freedomwall.php">Newsfeed</a>
                 </div>
 
                <div class="dlog-icon">
                  <Img src="images/offweb.png" alt="log">
                 <a class="a-pf" href="https://sgs.cityofmalabonuniversity.edu.ph/">Visit Official Website</a>
                 </div>
 
                 <div class="dlog-icon">
                  <img src="images/announcement.png" alt="">
                 <a class="a-pf" href="#">Announcement</a>
                 </div>
                 
                 <div class="dlog-icon">
                  <img src="images/facultyb.png" alt="">
                 <a class="a-pf" href="instructorsProfiles.php">Faculty</a>
                 </div>
 
            <div class="logoutbb">
              <a href="logout.php"><img src="images/logoutb.png" class="logoutb2"></a>
              <a href="logout.php" class="logout-link">Logout</a>
            </div>
     
         </div>
        
       </div>
       <h4 class="user"><span><?php echo htmlspecialchars($_SESSION['f_name']);?></span></h4> 
     </div>
          
 </div>

    </nav>

    <div class="container">

        <!-- Main Content Section -->
        <main>
            <!-- Posting New Article (Only Text) -->
            <form action="create_post.php" method="POST" enctype="multipart/form-data">
                <section class="freewall-post">
                    <img src="<?php echo htmlspecialchars($current_image); ?>" class="fp-pic" title="Click to upload image"/>
                    <label for="termsCheckbox" class="rant-post">What's on your mind?</label>
                </section>
                <input type="checkbox" id="termsCheckbox" class="modal-toggle">
                <div class="modal">
                    <div class="modal-content">
                        <label for="termsCheckbox" class="close">&times;</label>
                        <h3 class="cpost">Create Post</h3>
                        <div class="pfn">
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
                        <button type="submit" name="submit" class="btnpost">Post</button>
                    </div>
                </div>
            </form>

<!-- Display Posts from Database -->
<?php while ($row = $result->fetch_assoc()) { ?>
    <article class="news-item">
        <div class="users-posted">
            <img src="<?php echo htmlspecialchars($row['picture'] ? $row['picture'] : 'images/icon.jpg'); ?>" alt="user-photo">
            <div>
                <h3><?php echo htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']); ?></h3>
                <small>Posted on: <?php echo htmlspecialchars($row['created_at']); ?></small>
            </div>

            <!-- Three-dot Menu for Posts -->
            <div class="ellipsis-menu">
                <button class="ellipsis-btn" onclick="toggleMenu('menu-post-<?php echo $row['id']; ?>')">&#8942;</button>
                <div class="menu-dropdown" id="menu-post-<?php echo $row['id']; ?>">
                    <form action="action.php" method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                    <?php if ($_SESSION['is_admin']) { ?>
                        <button type="submit" name="action" value="delete_post">Delete Post</button>
                    <?php } ?>
                    <?php if (!$_SESSION['is_admin']) { ?>
                        <button type="submit" name="action" value="report_post">Report Post</button>
                    <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        <?php if (!empty($row['image_url'])) { ?>
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Post Image" class="post-image">
        <?php } ?>
        
 <!-- Comments Section -->
 <div class="comments">
            <h3>Comments</h3>
            <div class="comment-list">
                <?php
                $post_id = $row['id'];
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                $commentsResult = $stmt->get_result();

                if ($commentsResult->num_rows > 0) {
                    while ($commentRow = $commentsResult->fetch_assoc()) {
                        $comment_id = $commentRow['id'] ?? null; // Ensures 'id' is defined
                        $commenterPic = !empty($commentRow['picture']) ? $commentRow['picture'] : 'images/icon.jpg';

                        echo "<div class='comment'>";
                        echo "<div class='imgcom'>";
                        echo "<img src='" . htmlspecialchars($commenterPic) . "' alt='User Profile' class='comment-profile'>";
                        echo "<div class='incom1'>";
                        echo "<div class='incom2'>";
                        echo "<strong>" . htmlspecialchars($commentRow['fname'] . " " . $commentRow['lname']) . "</strong> " . "<br>" . "<p class='currentcom'>" . nl2br(htmlspecialchars($commentRow['comment'])) . "</p>";
                        echo "</div>";
                        echo "<small class='ditcom'>" . htmlspecialchars($commentRow['created_at']) . "</small>";
                        echo "</div>";
                        echo "</div>";

                        // Three-dot Menu for Comments
                        if ($comment_id) {
                        echo "<div class='ellipsis-menu'>";
                        echo "<button class='ellipsis-btn' onclick=\"toggleMenu('menu-comm-{$commentRow['id']}')\">&#8942;</button>";
                        echo "<div class='menu-dropdown' id='menu-comm-{$commentRow['id']}'>";
                        echo "<form action='action.php' method='POST'>";
                        echo "<input type='hidden' name='comment_id' value='{$commentRow['id']}'>";
                        if ($_SESSION['is_admin']) {
                            echo "<button type='submit' name='action' value='delete_comment'>Delete Comment</button>";
                        } elseif (!$_SESSION['is_admin']) {
                            echo "<button type='submit' name='action' value='report_comment'>Report Comment</button>";
                         }

                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>No comments yet.</p>";
                }
            ?>

            
            <!-- Comment Form -->
            <form action="add_comment.php" method="POST" class="comment-form">
                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                <textarea name="comment" placeholder="Write a comment..." required></textarea>
                <button class="butt" type="submit">
                    <img src="images/sends.png" alt="">
                </button>
            </form>
        </div>
    </article><br>
<?php } ?>

        </main>

         <!-- Sidebar for Categories -->
         <aside class="sidebar">
            <h3>Students</h3>          
                <?php while ($row = $studentsResult->fetch_assoc()) { ?>
                    <div class='sidebar-pfp'>
                        <img class="pfsec" src='<?php echo htmlspecialchars($row['picture'] ?: 'images/icon.jpg'); ?>' alt='Profile pic'>
                        <p><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></p>
                    </div>
                <?php } ?>
         </aside>

    </div>

<script>
    function toggleMenu(menuId) {
        var menu = document.getElementById(menuId);
        menu.classList.toggle("show");
    }

    // Close menu when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.ellipsis-btn')) {
            var dropdowns = document.getElementsByClassName("menu-dropdown");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
        }
    }
</script>
<script src="js/logs.js"></script>

</body>
</html>
