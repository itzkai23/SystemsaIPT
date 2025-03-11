<!-- File: views/edit.ejs -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors Status</title>
    <link rel="stylesheet" href="css/profstatus.css">

    
</head>

<body>
    
    

    <div class="page-container">
        <h1>Professors</h1>
    
        
        <div class="con-profs">
            
            
                <label for="termsCheckbox_usercomment" class="rant-post" type="text">
                    <div class="con-prof">
                        <img src="images/facultyb.png" alt="Prof1">
                        <div>
                            <h5>profname</h5>
                            <h5>Course Code</h5>
                        </div>
                        <h2>SUBJECT</h2>
                    </div>
                </label>
              
                <label for="termsCheckbox_usercomment" class="rant-post" type="text">
                    <div class="con-prof">
                        <img src="images/facultyb.png" alt="Prof2">
                        <div>
                            <h5>profname</h5>
                            <h5>Course Code</h5>
                        </div>
                        <h2>SUBJECT</h2>
                    </div>
                </label> 

           
                <label for="termsCheckbox_usercomment" class="rant-post" type="text">
                    <div class="con-prof">
                         <img src="images/facultyb.png" alt="Prof3">
                         <div>
                           <h5>profname</h5>
                           <h5>Course Code</h5>
                         </div>
                         <h2>SUBJECT</h2>
                    </div>
                </label>    
           
        </div>
    
        <!-- Back to Home Button -->
        <a href="silog.php">Back</a>
  
          <input type="checkbox" id="termsCheckbox_usercomment" class="modal-toggle_usercom">
          <div class="modal_usercom">
            <div class="modal-content-usercom">
              <label for="termsCheckbox_usercomment" class="close">&times;</label>
              
              <div class="container">
        
                <div class="avnamecon">
                <div class="avname">
                    
                <img src="<?php echo !empty($prof_img) ? htmlspecialchars($prof_img) : $defimage; ?>" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>" 
                         width="150" height="150">
                 <div class="pc">
                 <h5 class="prof-name"> <?php echo $professor_name; ?></h5>
                 </div>
                </div>
                <h5 class="subject"><?php echo $profrole; ?></h5>
                </div>
        
                
                <!-- Display Submitted Data -->
        
                  <div class="comdent">
                  <label for="termsCheckbox" class="section">
                    <h3>Comments</h3>
                    <div class="com-scroll">
                        <?php
                        foreach ($feedbackData as $comm) {
                            echo '<div class="comment-box">';
                            echo '<img src="' . htmlspecialchars($comm['student_image']) . '" alt="User" class="comment-img">';
                            echo '<div class="comment-text">';
                            echo '<strong>' . htmlspecialchars($comm['student_name']) . " " . htmlspecialchars($comm['lname']) . '</strong><br>';
                            echo '<p>' . htmlspecialchars($comm['feedback']) . '</p>';
                            echo '<small>' . htmlspecialchars($comm['submitted_at']) . '</small>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                      </label>
        
        
                      <div class="section">
                        <h3>Rating</h3>
                      
                      </div>
                      
        
                  </div>
        
        
        </div>
  
            
            </div>  
          </div>

    </div>
    


</body>
</html>
