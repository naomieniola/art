<?php
// add_comment.php

require_once "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $commentText = $_POST['commentText'];
    $userFullname = $_SESSION['user_fullname'];
    $discussionId = $_POST['discussionId']; // Make sure to send this value in your AJAX request

    $sqlComment = "INSERT INTO comments (discussion_id, user_fullname, comment_text) VALUES (?, ?, ?)";
    $stmtComment = mysqli_stmt_init($conn);

    if ($stmtComment) {
        if (mysqli_stmt_prepare($stmtComment, $sqlComment)) {
            mysqli_stmt_bind_param($stmtComment, "iss", $discussionId, $userFullname, $commentText);
            mysqli_stmt_execute($stmtComment);

            // Send a response to indicate success
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            // Handle error in preparing the comment statement
            echo json_encode(['status' => 'error', 'message' => 'Error in preparing the comment statement']);
            exit();
        }
    } else {
        // Handle error in initializing the comment statement
        echo json_encode(['status' => 'error', 'message' => 'Error in initializing the comment statement']);
        exit();
    }
} else {
    // Handle non-POST requests
    http_response_code(405); // Method Not Allowed
    exit();
}
?>