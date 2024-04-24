<?php
// Include the database connection file
require_once "../../config.php";

// Check if the addCommentButton is set
if (isset($_POST['addCommentButton'])) {
    // Check if the commentInput is set and not empty
    if (isset($_POST['commentInput']) && !empty($_POST['commentInput'])) {
        // Get the discussion ID, user's full name, and comment text from the POST data
        $discussionId = $_POST['discussionId'];
        $userFullname = $_SESSION['user_fullname'];
        $commentText = $_POST['commentInput'];

        // Prepare and execute the SQL query to insert the comment into the database
        $sqlComment = "INSERT INTO comments (discussion_id, user_fullname, comment_text) VALUES (?, ?, ?)";
        $stmtComment = mysqli_stmt_init($conn);

        if ($stmtComment) {
            if (mysqli_stmt_prepare($stmtComment, $sqlComment)) {
                mysqli_stmt_bind_param($stmtComment, "iss", $discussionId, $userFullname, $commentText);
                mysqli_stmt_execute($stmtComment);

                // Return a success message or handle the response as needed
                echo "Comment added successfully!";
            } else {
                // Handle error in preparing the comment statement
                echo "Error in preparing the comment statement: " . mysqli_error($conn);
            }
        } else {
            // Handle error in initializing the comment statement
            echo "Error in initializing the comment statement: " . mysqli_error($conn);
        }
    } else {
        // Handle the case where the commentInput is not set or empty
        echo "Comment cannot be empty!";
    }
} else {
    // Handle the case where addCommentButton is not set
    echo "Invalid request!";
}

// Close the database connection
mysqli_close($conn);
?>