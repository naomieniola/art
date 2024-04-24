<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

// Check if the discussion is being viewed
$viewingDiscussionId = isset($_GET['view_discussion_id']) ? intval($_GET['view_discussion_id']) : 0;

if (isset($_SESSION["user"])) {
    if (basename($_SERVER['PHP_SELF']) == "login.php" || basename($_SERVER['PHP_SELF']) == "signup.php") {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
} else {
    if (basename($_SERVER['PHP_SELF']) != "login.php" && basename($_SERVER['PHP_SELF']) != "signup.php") {
        $_SESSION["previous_page"] = $_SERVER["REQUEST_URI"];
        header("Location: login.php");
        exit();
    }
}

session_regenerate_id(true);

// Add the discussion handling code here
require_once "../../config.php";

$discussionInput = '';
$thoughtsInput = '';

if (isset($_POST['startDiscussionButton'])) {
    $discussionInput = $_POST['discussionInput'];
    $thoughtsInput = $_POST['thoughtsInput'];

    
    // Store the discussion ID in a variable
    $discussionId = 0; // Initialize to a default value
    //$discussionId = mysqli_insert_id($conn); // Assuming your discussions table has an auto-incrementing primary key

    // Assuming you have a 'discussions' table with columns 'discussion_topic' and 'thoughts'
    $sqlDiscussion = "INSERT INTO discussions (user_fullname, discussion_topic, thoughts) VALUES (?, ?, ?)";
    $stmtDiscussion = mysqli_stmt_init($conn);

    if ($stmtDiscussion) {
        if (mysqli_stmt_prepare($stmtDiscussion, $sqlDiscussion)) {
            mysqli_stmt_bind_param($stmtDiscussion, "sss", $_SESSION['user_fullname'], $discussionInput, $thoughtsInput);
            mysqli_stmt_execute($stmtDiscussion);

            // Get the last inserted ID
            $discussionId = mysqli_insert_id($conn);

            // Redirect or display a success message as needed
        } else {
            // Handle error in preparing the discussion statement
            die("Error in preparing the discussion statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the discussion statement
        die("Error in initializing the discussion statement: " . mysqli_error($conn));
    }
}

// Add the comment handling code here
if (isset($_POST['addCommentButton'])) {
    $commentText = $_POST['commentInput'];
    $userFullname = $_SESSION['user_fullname'];
    $discussionId = $_POST['discussionId']; // Get the discussion ID from the form

    // Assuming you have a 'comments' table with columns 'discussion_id', 'user_fullname', and 'comment_text'
    $sqlComment = "INSERT INTO comments (discussion_id, user_fullname, comment_text) VALUES (?, ?, ?)";
    $stmtComment = mysqli_stmt_init($conn);

    if ($stmtComment) {
        if (mysqli_stmt_prepare($stmtComment, $sqlComment)) {
            mysqli_stmt_bind_param($stmtComment, "iss", $discussionId, $userFullname, $commentText);
            mysqli_stmt_execute($stmtComment);

            // Redirect or display a success message as needed
        } else {
            // Handle error in preparing the comment statement
            die("Error in preparing the comment statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the comment statement
        die("Error in initializing the comment statement: " . mysqli_error($conn));
    }
}
// Handle discussion deletion
if (isset($_GET['delete_discussion_id'])) {
    $deleteDiscussionId = intval($_GET['delete_discussion_id']);
    $sqlDeleteDiscussion = "DELETE FROM discussions WHERE id = ? AND user_fullname = ?";
    $stmtDeleteDiscussion = mysqli_stmt_init($conn);

    if ($stmtDeleteDiscussion) {
        if (mysqli_stmt_prepare($stmtDeleteDiscussion, $sqlDeleteDiscussion)) {
            mysqli_stmt_bind_param($stmtDeleteDiscussion, "is", $deleteDiscussionId, $_SESSION['user_fullname']);
            mysqli_stmt_execute($stmtDeleteDiscussion);

            // Redirect or display a success message as needed
            header("Location: discussionsuser.php");
            exit();
        } else {
            // Handle error in preparing the delete discussion statement
            die("Error in preparing the delete discussion statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the delete discussion statement
        die("Error in initializing the delete discussion statement: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Discussions</title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
</head>

<body>

    <!-- Header Container -->
    <header>
    <nav class="navbar">
        <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
        <span class="site-name">Planet Art</span>

        <div>
            <a class="btn btn-outline-secondary nav-links" href="indexuser.php">Home</a>
            <a class="btn btn-outline-secondary nav-links" href="artistsuser.php">Artists</a>
            <a class="btn btn-outline-secondary nav-links" href="mediumsuser.php">Mediums</a>
            <a class="btn btn-outline-secondary nav-links current-page" href="discussionsuser.php">Discussions</a>
        </div>

        <div class="ml-auto">
            <!-- Welcome message -->
            <?php if (isset($_SESSION['user_fullname'])) : ?>
                <p class="welcome-message">Welcome, <?php echo $_SESSION['user_fullname']; ?>!</p>
            <?php endif; ?>

            <!-- Account button with dropdown -->
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary login rounded-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Account
                </button>

                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?logout=1">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <?php
    // Check if the logout action is triggered
    if (isset($_GET['logout']) && $_GET['logout'] == 1) {
        session_start();
        session_destroy();
        header("Location: /planetart/pages/non-users/login.php");
        exit();
    }
    ?>
    </header>

<!-- Main Content Container -->
<div class="container">
    <div class="row justify-content-center">
        <!-- Container for starting discussions -->
        <div class="col-6">
            <?php if (isset($_GET['view_discussion_id'])) : ?>
                <!-- Display form with discussion topic and thoughts -->
                <div class="comment-container" id="commentContainer">
                    <?php
                    // Fetch and display discussion details
                    $viewingDiscussionId = intval($_GET['view_discussion_id']);
                    $sqlFetchDiscussion = "SELECT * FROM discussions WHERE id = ?";
                    $stmtFetchDiscussion = mysqli_stmt_init($conn);
                    ?>
                    <!-- Back button to return to discussions list -->
                    <a href="discussionsuser.php" class="btn btn-primary mb-3">Back</a>
                        <!-- Fetch and display existing comments for the discussion -->
                        <?php

                                
                        function getTimeElapsedString($dateTime) {
                            $timeAgo = strtotime($dateTime);
                            $currentTime = time();
                            $timeDifference = $currentTime - $timeAgo;
                            $seconds = $timeDifference;
                            $minutes = round($seconds / 60);           // value 60 is seconds
                            $hours = round($seconds / 3600);           // value 3600 is 60 minutes * 60 sec
                            $days = round($seconds / 86400);          // value 86400 is 24 hours * 60 minutes * 60 sec
                            $weeks = round($seconds / 604800);        // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
                            $months = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec

                            if ($seconds <= 60) {
                                return "Just Now";
                            } elseif ($minutes <= 60) {
                                return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
                            } elseif ($hours <= 24) {
                                return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
                            } elseif ($days <= 7) {
                                return ($days == 1) ? "1 day ago" : "$days days ago";
                            } elseif ($weeks <= 4.3) {  // 4.3 == 30/7
                                return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
                            } else {
                                return ($months == 1) ? "1 month ago" : "$months months ago";
                            }
                        }
                            if ($stmtFetchDiscussion) {
                        if (mysqli_stmt_prepare($stmtFetchDiscussion, $sqlFetchDiscussion)) {
                            mysqli_stmt_bind_param($stmtFetchDiscussion, "i", $viewingDiscussionId);
                            mysqli_stmt_execute($stmtFetchDiscussion);

                            $resultDiscussion = mysqli_stmt_get_result($stmtFetchDiscussion);
                            $discussionDetails = mysqli_fetch_assoc($resultDiscussion);
                             // Display the time elapsed
                            echo '<p><strong>Created ' . getTimeElapsedString($discussionDetails['created_at']) . '</strong></p>';
                             // Display discussion topic and thoughts

                            echo '<p><strong>Discussion Topic:</strong> ' . htmlspecialchars($discussionDetails['discussion_topic']) . '</p>';
                            echo '<p><strong>Thoughts: </strong>' . htmlspecialchars($discussionDetails['thoughts']) . '</p>';
                        
                            
                        } else {
                            // Handle error in preparing the fetch discussion statement
                            die("Error in preparing the fetch discussion statement: " . mysqli_error($conn));
                        }
                    } else {
                        // Handle error in initializing the fetch discussion statement
                        die("Error in initializing the fetch discussion statement: " . mysqli_error($conn));
                    }
                    $sqlFetchComments = "SELECT * FROM comments WHERE discussion_id = ? ORDER BY created_at DESC";
                    $stmtFetchComments = mysqli_stmt_init($conn);

                    if ($stmtFetchComments) {
                        if (mysqli_stmt_prepare($stmtFetchComments, $sqlFetchComments)) {
                            mysqli_stmt_bind_param($stmtFetchComments, "i", $viewingDiscussionId);
                            mysqli_stmt_execute($stmtFetchComments);

                            $resultComments = mysqli_stmt_get_result($stmtFetchComments);

                            while ($comment = mysqli_fetch_assoc($resultComments)) {
                                echo '<div class="mb-2">';
                                echo '<p>';
                                echo '<img src="/planetart/images/profile-user.png" alt="User Icon" class="user-forum-icon">';
                                echo '<strong>' . htmlspecialchars($comment['user_fullname']) . ':</strong> ' . htmlspecialchars($comment['comment_text']);
                                echo '</p>';
                                echo '</div>';


                            }
                        } else {
                            // Handle error in preparing the fetch comments statement
                            die("Error in preparing the fetch comments statement: " . mysqli_error($conn));
                        }
                    } else {
                        // Handle error in initializing the fetch comments statement
                        die("Error in initializing the fetch comments statement: " . mysqli_error($conn));
                    }
                    ?>

                    <!-- Add Comment Form -->
                    <form id="addCommentForm" method="post">
                        <input type="hidden" name="discussionId" value="<?php echo $viewingDiscussionId; ?>">
                        <textarea class="form-control mb-2" name="commentInput" placeholder="Type your comment here"></textarea>
                        <button type="submit" class="btn btn-primary btn-add-comment" name="addCommentButton">Add Comment</button>
                    </form>

                    <!-- Display user's comment -->
                    <?php if (isset($_POST['addCommentButton']) && $_POST['discussionId'] == $viewingDiscussionId) : ?>
                        <div class="mb-2">
                        <?php if (isset($_SESSION['user_fullname'])) : ?>
                    <?php endif; ?>    </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <!-- Display form to start discussion -->
                <div class="col-12discussion-body">
                <form id="startDiscussionForm" method="post">
                    <input type="text" class="form-control mb-2" name="discussionInput" placeholder="Type your discussion topic here">
                    <textarea class="form-control mb-2" name="thoughtsInput" placeholder="Type your thoughts here"></textarea>
                    <button type="submit" class="btn btn-primary" name="startDiscussionButton">Start Discussion</button>
                </form>
                </div>

                <?php
                // Fetch and display existing discussions
                $discussionsPerPage = 10;
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $offset = ($page - 1) * $discussionsPerPage;

                $sqlFetchDiscussions = "SELECT * FROM discussions ORDER BY created_at DESC LIMIT ?, ?";
                $stmtFetchDiscussions = mysqli_stmt_init($conn);

                if ($stmtFetchDiscussions) {
                    if (mysqli_stmt_prepare($stmtFetchDiscussions, $sqlFetchDiscussions)) {
                        mysqli_stmt_bind_param($stmtFetchDiscussions, "ii", $offset, $discussionsPerPage);
                        mysqli_stmt_execute($stmtFetchDiscussions);

                        $resultDiscussions = mysqli_stmt_get_result($stmtFetchDiscussions);

                        // Display all discussions
                        while ($discussion = mysqli_fetch_assoc($resultDiscussions)) {
                            echo '<div class="row justify-content-center mb-4">';
                            echo '<div class="col-12 discussion-body">';
                            echo '<p class="username-discussion">';
                            echo '<img src="/planetart/images/profile-user.png" alt="User Icon" class="user-forum-icon">';
                            echo htmlspecialchars($discussion['user_fullname']);
                            echo '</p>';
                            echo '<p id="discussionName" class="discussion-name">' . htmlspecialchars($discussion['discussion_topic']) . '</p>';
                            //echo '<div id="discussionThoughts" class="mb-2">' . htmlspecialchars($discussion['thoughts']) . '</div>';
                            echo '<div class="comment-container">';
                            
                            // Add "View Discussion" button with a link to the current page, passing the discussion ID
                            echo '<a href="?view_discussion_id=' . $discussion['id'] . '" class="btn btn-primary">View Discussion</a>';
                            
                            // Add "Delete" button visible only to the user who started the discussion
                            if (isset($_SESSION['user_fullname']) && $_SESSION['user_fullname'] == $discussion['user_fullname']) {
                                echo '<a href="?delete_discussion_id=' . $discussion['id'] . '" class="btn btn-danger">Delete</a>';
                            }
                            
                            echo '</div></div></div>';
                        }
                        
                        // Display pagination buttons if there are more than 10 discussions
                        $sqlCountDiscussions = "SELECT COUNT(*) AS count FROM discussions";
                        $resultCount = mysqli_query($conn, $sqlCountDiscussions);
                        $rowCount = mysqli_fetch_assoc($resultCount)['count'];
                        $totalPages = ceil($rowCount / $discussionsPerPage);

                        if ($totalPages > 1) {
                            echo '<div class="row justify-content-center">';
                            echo '<div class="col-6">';
                            if ($page > 1) {
                                echo '<a href="?page=' . ($page - 1) . '" class="btn btn-primary">Back</a>';
                            }
                            if ($page < $totalPages) {
                                echo '<a href="?page=' . ($page + 1) . '" class="btn btn-primary ml-auto">Next</a>';
                            }
                            echo '</div></div>';
                        }
                    } else {
                        // Handle error in preparing the fetch discussions statement
                        die("Error in preparing the fetch discussions statement: " . mysqli_error($conn));
                    }
                } else {
                    // Handle error in initializing the fetch discussions statement
                    die("Error in initializing the fetch discussions statement: " . mysqli_error($conn));
                }
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>


    <!-- Footer -->
    <footer class="text-center">
        <div class="footer p-3">
            © 2024
            <a>Planet Art</a>
        </div>
    </footer>

    <!-- Bootstrap js and popper.js -->
    <script src="../../js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"
        integrity="sha384-X48EVOIu1KKPHFG0f/RaN8GGVGa2NgqzqXf8+2Vb9sUmXsLGee4JSChHX3U8PVIe"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"
        integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
</body>
</html>