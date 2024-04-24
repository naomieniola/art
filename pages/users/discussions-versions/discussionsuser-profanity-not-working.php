<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

require_once '../../vendor/autoload.php';

// Add this line to import the CensorWords class
use Snipe\BanBuilder\CensorWords;

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

    if ($seconds < 60) {
        return "Just Now";
    } elseif ($minutes < 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } elseif ($hours < 2) {
        return "Just Now";
    } elseif ($hours < 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } elseif ($days < 7) {
        return ($days == 1) ? "1 day ago" : "$days days ago";
    } elseif ($weeks < 4.3) {  // 4.3 == 30/7
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    }
}

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

// Report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reportCommentId']) && isset($_POST['reportCommentText'])) {
    // Extract data from the POST request
    $reportCommentId = intval($_POST['reportCommentId']);
    $reportCommentText = $_POST['reportCommentText'];
    $reportReason = $_POST['reportReason']; // Add this line to extract report reason
    $reportedUserFullname = $_POST['userFullname']; // Use the user's full name from the POST data

    // Connect to your database
    $conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL statement to insert data into the reports table
    $sql = "INSERT INTO reports (comment_id, reported_by_user_fullname, reported_user_fullname, report_comment_text, report_explanation) 
    VALUES ('$reportCommentId', '{$_SESSION['user_fullname']}', '$reportedUserFullname', SUBSTRING_INDEX('$reportCommentText', ':', -1), '$reportReason')";
    if (mysqli_query($conn, $sql)) {
        // Report inserted successfully, you can also print a success message
        echo "Report submitted successfully.";
    } else {
        // Error inserting report, you can print an error message
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);

    // Exit the script after handling the report submission
    exit;
}

$discussionInput = '';
$thoughtsInput = '';

// Function to check for inappropriate words
/*
function containsSwearWords($text) {
    $swearWords = array("fuck", "Fuck", "Bitch", "bitch", "Shit", "shit", "Bastard", "bastard"); // Add your list of inappropriate words

    foreach ($swearWords as $swearWord) {
        if (stripos($text, $swearWord) !== false) {
            return true;
        }
    }

    return false;
} */

// Create an instance of the CensorWords class
// Create an instance of the CensorWords class
$censorWords = new CensorWords();

if (isset($_POST['startDiscussionButton'])) {
    $discussionInput = trim($_POST['discussionInput']);
    $thoughtsInput = trim($_POST['thoughtsInput']);

    // Validate that the discussion topic and thoughts are not empty
    if (empty($discussionInput) || empty($thoughtsInput)) {
        echo '<div class="alert alert-danger" role="alert">Discussion topic and thoughts cannot be empty.</div>';
    } elseif ($censorWords->censorString($discussionInput) !== $discussionInput || $censorWords->censorString($thoughtsInput) !== $thoughtsInput) {
        echo '<div class="alert alert-danger" role="alert">Discussion topic or thoughts cannot contain inappropriate words.</div>';
    } else {
        // Censor the discussion topic and thoughts
        $censoredDiscussionInput = $censorWords->censorString($discussionInput);
        $censoredThoughtsInput = $censorWords->censorString($thoughtsInput);

        // Store the discussion ID in a variable
        $discussionId = 0; // Initialize to a default value

        // Assuming you have a 'discussions' table with columns 'discussion_topic' and 'thoughts'
        $sqlDiscussion = "INSERT INTO discussions (user_fullname, discussion_topic, thoughts) VALUES (?, ?, ?)";
        $stmtDiscussion = mysqli_stmt_init($conn);

        if ($stmtDiscussion) {
            if (mysqli_stmt_prepare($stmtDiscussion, $sqlDiscussion)) {
                mysqli_stmt_bind_param($stmtDiscussion, "sss", $_SESSION['user_fullname'], $censoredDiscussionInput, $censoredThoughtsInput);
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
}

/* if (isset($_POST['addCommentButton'])) {
    $commentText = trim($_POST['commentInput']);

    // Validate that the comment is not empty
    if (empty($commentText)) {
        echo '<div class="alert alert-danger" role="alert">Comment cannot be empty.</div>';
    } elseif (containsSwearWords($commentText)) {
        echo '<div class="alert alert-danger" role="alert">Comment contains inappropriate words.</div>';
    } else { */
        
        if (isset($_POST['addCommentButton'])) {
            $commentText = trim($_POST['commentInput']);
        
            // Validate that the comment is not empty
            if (empty($commentText)) {
                echo '<div class="alert alert-danger" role="alert">Comment cannot be empty.</div>';
            } elseif ($censorWords->censorString($commentText) !== $commentText) {
                echo '<div class="alert alert-danger" role="alert">Comment contains inappropriate words.</div>';
            } else {
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
        }

// Handle comment editing
$editCommentId = isset($_GET['edit_comment_id']) ? intval($_GET['edit_comment_id']) : 0;
$editCommentText = '';

if (isset($_POST['saveChangesButton'])) {
    $editedCommentText = trim($_POST['commentInput']);

    // Validate that the edited comment is not empty
    if (empty($editedCommentText)) {
        echo '<div class="alert alert-danger" role="alert">Comment cannot be empty.</div>';
    } elseif ($censorWords->censorString($editedCommentText) !== $editedCommentText) {
        echo '<div class="alert alert-danger" role="alert">Comment contains inappropriate words.</div>';
    } else {
        $editCommentId = intval($_POST['editCommentId']);
        $userFullname = $_SESSION['user_fullname'];
        $discussionId = intval($_POST['discussionId']);

        // Update the comment in the database
        $sqlUpdateComment = "UPDATE comments SET comment_text = ? WHERE id = ? AND user_fullname = ?";
        $stmtUpdateComment = mysqli_stmt_init($conn);

        if ($stmtUpdateComment) {
            if (mysqli_stmt_prepare($stmtUpdateComment, $sqlUpdateComment)) {
                mysqli_stmt_bind_param($stmtUpdateComment, "sis", $editedCommentText, $editCommentId, $userFullname);
                mysqli_stmt_execute($stmtUpdateComment);

                if (mysqli_stmt_affected_rows($stmtUpdateComment) > 0) {
                    // Comment edited successfully
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Comment edited successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                } else {
                    // Comment edit failed
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to edit the comment. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }

                // Redirect to the same discussion with the updated comment
                header("Location: ?view_discussion_id=" . $discussionId);
                exit();
            } else {
                // Handle error in preparing the update comment statement
                die("Error in preparing the update comment statement: " . mysqli_error($conn));
            }
        } else {
            // Handle error in initializing the update comment statement
            die("Error in initializing the update comment statement: " . mysqli_error($conn));
        }
    }
}

// Fetch the comment text if editing a comment
if ($editCommentId > 0) {
    $sqlFetchCommentText = "SELECT comment_text FROM comments WHERE id = ?";
    $stmtFetchCommentText = mysqli_stmt_init($conn);

    if ($stmtFetchCommentText) {
        if (mysqli_stmt_prepare($stmtFetchCommentText, $sqlFetchCommentText)) {
            mysqli_stmt_bind_param($stmtFetchCommentText, "i", $editCommentId);
            mysqli_stmt_execute($stmtFetchCommentText);

            $resultCommentText = mysqli_stmt_get_result($stmtFetchCommentText);

            if ($commentTextData = mysqli_fetch_assoc($resultCommentText)) {
                $editCommentText = $commentTextData['comment_text'];
            }
        } else {
            // Handle error in preparing the fetch comment text statement
            die("Error in preparing the fetch comment text statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the fetch comment text statement
        die("Error in initializing the fetch comment text statement: " . mysqli_error($conn));
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

// Handle comment deletion
if (isset($_POST['deleteCommentButton'])) {
    $deleteCommentId = intval($_POST['deleteCommentId']);
    
    // Delete the associated reports from the reports table
    $sqlDeleteReports = "DELETE FROM reports WHERE comment_id = ?";
    $stmtDeleteReports = mysqli_prepare($conn, $sqlDeleteReports);
    mysqli_stmt_bind_param($stmtDeleteReports, "i", $deleteCommentId);
    mysqli_stmt_execute($stmtDeleteReports);
    
    // Delete the comment from the comments table
    $sqlDeleteComment = "DELETE FROM comments WHERE id = ?";
    $stmtDeleteComment = mysqli_prepare($conn, $sqlDeleteComment);
    mysqli_stmt_bind_param($stmtDeleteComment, "i", $deleteCommentId);
    mysqli_stmt_execute($stmtDeleteComment);
    
    // Redirect to the same discussion page after deletion
    header("Location: ?view_discussion_id=" . $viewingDiscussionId);
    exit();
}

// Handle search input
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($searchKeyword)) {
    // Adjust the SQL query to filter discussions based on the search keyword
    $sqlFetchDiscussions = "SELECT * FROM discussions WHERE discussion_topic LIKE ? ORDER BY created_at DESC LIMIT ?, ?";
    $stmtFetchDiscussions = mysqli_stmt_init($conn);

    if ($stmtFetchDiscussions) {
        if (mysqli_stmt_prepare($stmtFetchDiscussions, $sqlFetchDiscussions)) {
            $searchParam = "%" . $searchKeyword . "%";
            mysqli_stmt_bind_param($stmtFetchDiscussions, "sii", $searchParam, $offset, $discussionsPerPage);
            mysqli_stmt_execute($stmtFetchDiscussions);
        } else {
            // Handle error in preparing the fetch discussions statement
            die("Error in preparing the fetch discussions statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the fetch discussions statement
        die("Error in initializing the fetch discussions statement: " . mysqli_error($conn));
    }
} else {
    // Use the existing SQL query to fetch discussions without filtering
    $sqlFetchDiscussions = "SELECT * FROM discussions ORDER BY created_at DESC LIMIT ?, ?";
    $stmtFetchDiscussions = mysqli_stmt_init($conn);

    if ($stmtFetchDiscussions) {
        if (mysqli_stmt_prepare($stmtFetchDiscussions, $sqlFetchDiscussions)) {
            mysqli_stmt_bind_param($stmtFetchDiscussions, "ii", $offset, $discussionsPerPage);
            mysqli_stmt_execute($stmtFetchDiscussions);
        } else {
            // Handle error in preparing the fetch discussions statement
            die("Error in preparing the fetch discussions statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the fetch discussions statement
        die("Error in initializing the fetch discussions statement: " . mysqli_error($conn));
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
            <a class="btn btn-outline-secondary nav-links current-page" href="discussionsuser.php">Discussions</a>
        </div>

        <div class="ml-auto">
                <!-- Welcome message -->
                <?php if (isset($_SESSION['user_fullname'])) : ?>
                    <p class="welcome-message">Welcome, <?php echo $_SESSION['user_fullname']; ?>!</p>
                <?php endif; ?>

            <!-- Account button with dropdown -->
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary login rounded-right" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Account
                    </button>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                            <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="likes.php">My Likes</a>
                                    <div class="dropdown-divider"></div>
                                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) : ?>
                                <a class="dropdown-item" href="admin-dashboard.php">Admin Dashboard</a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
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
<!-- Add this div for displaying the success message -->
<div id="reportSuccessMessage" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
    Report submitted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div id="reportErrorMessage" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
    Report submission was unsuccessful. Please try again later.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

        <?php if (basename($_SERVER['PHP_SELF']) == "discussionsuser.php" && (empty($_GET['view_discussion_id']) || !empty($_GET['view_discussion_id']))) : ?>
                    <div class="container explore-desc">
                        <div class="row">
                            <div class="col-12">
                                <?php if (empty($_GET['view_discussion_id'])) : ?>
                                    <p class="artists-main-text">Discussions</p>
                                    <p class="artists-sub-text">Lorem Ipsum</p>
                                <?php else : ?>
                                    <p class="artists-main-text">Discussions</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
        <?php endif; ?>


        <!-- Search Bar -->
        <?php if (basename($_SERVER['PHP_SELF']) == "discussionsuser.php" && empty($_GET['view_discussion_id'])) : ?>
            <div class="container col-6 justify-content-center">
                <form method="get" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search discussions" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                        <button type="submit" class="btn btn-delete-discussion" id="searchButton">Search</button>

                        <!-- Clear Button (visible when searchKeyword is not empty) -->
                        <?php if (!empty($searchKeyword)) : ?>
                            <a href="discussionsuser.php" class="btn btn-delete-discussion" id="clearButton">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>

<div class="container">
    <div class="row justify-content-center">
        <!-- Container for starting discussions -->
        <div class="col-6">
            <?php if (isset($_GET['view_discussion_id'])) : ?>
                <!-- Display form with discussion topic and thoughts -->
                <div class="comment-container" id="commentContainer">
                        <?php
                        // Fetch and display discussion details
                        
                            // Close the statement for fetching discussions
                            mysqli_stmt_close($stmtFetchDiscussions);

                            // Fetch and display discussion details
                            $viewingDiscussionId = intval($_GET['view_discussion_id']);
                            $sqlFetchDiscussion = "SELECT * FROM discussions WHERE id = ?";
                            $stmtFetchDiscussion = mysqli_stmt_init($conn);


                        if ($stmtFetchDiscussion) {
                            if (mysqli_stmt_prepare($stmtFetchDiscussion, $sqlFetchDiscussion)) {
                                mysqli_stmt_bind_param($stmtFetchDiscussion, "i", $viewingDiscussionId);
                                mysqli_stmt_execute($stmtFetchDiscussion);

                                $resultDiscussion = mysqli_stmt_get_result($stmtFetchDiscussion);

                                if ($discussionDetails = mysqli_fetch_assoc($resultDiscussion)) {
                                    // Display discussion details (topic and thoughts) inside a box
                                    echo '<div class="discussion-box">';
                                    echo '<h2 class="discussion-title">' . htmlspecialchars($discussionDetails['discussion_topic']) . '</h2>';
                                    echo '<p class="discussion-thoughts">' . htmlspecialchars($discussionDetails['thoughts']) . '</p>';
                                    echo '</div>';
                                }
                            } else {
                                // Handle error in preparing the fetch discussion statement
                                die("Error in preparing the fetch discussion statement: " . mysqli_error($conn));
                            }
                        } else {
                            // Handle error in initializing the fetch discussion statement
                            die("Error in initializing the fetch discussion statement: " . mysqli_error($conn));
                        }

                        // Fetch and display existing comments for the discussion
                        $sqlFetchComments = "SELECT c.*, u.profile_picture 
                        FROM comments c
                        JOIN users u ON c.user_fullname = u.full_name
                        WHERE c.discussion_id = ?
                        ORDER BY c.created_at DESC";                        
                        
                        $stmtFetchComments = mysqli_stmt_init($conn);

                        if ($stmtFetchComments) {
                            if (mysqli_stmt_prepare($stmtFetchComments, $sqlFetchComments)) {
                                mysqli_stmt_bind_param($stmtFetchComments, "i", $viewingDiscussionId);
                                mysqli_stmt_execute($stmtFetchComments);

                                $resultComments = mysqli_stmt_get_result($stmtFetchComments);

                                while ($comment = mysqli_fetch_assoc($resultComments)) {
                                    echo '<div class="comment-box mb-3">';
                                    echo '<div class="comment-content d-flex align-items-center">';
                                    echo '<p>';
                                    $userProfilePicture = $comment['profile_picture'];
                                    echo '<img src="/planetart/images/' . $userProfilePicture . '" alt="User Icon" class="user-forum-icon">';                                    echo '<strong>' . htmlspecialchars($comment['user_fullname']) . ':</strong> ';
                                
                                    {
                                        // Display the comment text normally
                                        echo htmlspecialchars($comment['comment_text']);
                                    }
                                
                                    echo '</p>';
                                
                                    // Dynamic dropdown for each comment
                                    // Report button
                                    
                                    echo '<div class="dropdown ml-auto">';
                                    
                                    if (isset($_SESSION['user_fullname']) && $_SESSION['user_fullname'] != $comment['user_fullname']) {
                                        echo '<button class="report-button" type="button" id="reportButton-' . $comment['id'] . '" onclick="reportComment(' . $comment['id'] . ', \'' . htmlspecialchars($comment['comment_text']) . '\', \'' . htmlspecialchars($comment['user_fullname']) . '\')">';
                                        echo '<img src="/planetart/images/report.png" alt="Report" class="report-icon">';
                                        echo '</button>'; 
                                    }
    
                                    
                                    echo '<button class="ellipsis-button" type="button" id="ellipsisDropdown' . $comment['id'] . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    echo '<img src="/planetart/images/ellipsis.png" alt="Ellipsis" class="ellipsis-icon">';
                                    echo '</button>';
                                
                                    echo '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ellipsisDropdown' . $comment['id'] . '">';
                                    echo '<li><a class="dropdown-item" href="#">Reply</a></li>';
                                
                                    // Check if the logged-in user is the creator of the comment
                                    if (isset($_SESSION['user_fullname']) && $_SESSION['user_fullname'] == $comment['user_fullname']) {
                                        // Add the "Edit Comment" link
                                        echo '<li><a class="dropdown-item" href="?view_discussion_id=' . $viewingDiscussionId . '&edit_comment_id=' . $comment['id'] . '">Edit Comment</a></li>';
                                
                                        // Add the delete comment form
                                        echo '<form method="post" class="delete-comment-form">';
                                        echo '<input type="hidden" name="deleteCommentId" value="' . $comment['id'] . '">';
                                        echo '<li><button type="submit" name="deleteCommentButton" class="dropdown-item" onclick="return confirm(\'Are you sure you want to delete this comment?\')">Delete</button></li>';
                                        echo '</form>';
                                    }
                                
                                    echo '</ul>';
                                
                                    echo '</div>'; // End of dropdown

                                    
                                
                                    echo '</div>';
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

    <!-- Add the comment form at the end -->
    <?php if ($editCommentId == 0) : ?>
        <form id="addCommentForm" method="post" class="d-flex align-items-center">
            <input type="hidden" name="discussionId" value="<?php echo $viewingDiscussionId; ?>">
            <textarea class="form-control mr-2" name="commentInput" placeholder="Type your comment here"></textarea>
            <button type="submit" class="btn btn-add-comment-with-image" name="addCommentButton">
                <img src="/planetart/images/send.png" alt="Send">
            </button>
        </form>
<?php else: ?>
    <!-- Display the form for editing the comment -->
    <form id="editCommentForm" method="post">
        <input type="hidden" name="editCommentId" value="<?php echo $editCommentId; ?>">
        <input type="hidden" name="discussionId" value="<?php echo $viewingDiscussionId; ?>">
        <textarea class="form-control mb-2" name="commentInput" placeholder="Type your edited comment here"><?php echo htmlspecialchars($editCommentText); ?></textarea>
        <button type="submit" class="btn btn-save-changes-with-image" name="saveChangesButton">
            <img src="/planetart/images/save-changes.png" alt="Save Changes">
        </button>
    </form>
<?php endif; ?>

    <!-- Display the new comment if it's successfully added -->
    <?php
    if (isset($_POST['addCommentButton']) && $_POST['discussionId'] == $viewingDiscussionId) {
        echo '<div class="col-6 mb-2">';
        /* if (isset($_SESSION['user_fullname'])) {
            // Display user's comment text
            echo '<p>' . 'hey' . htmlspecialchars($commentText) . '</p>';
            // Display the time elapsed
        } */
        echo '</div>';
    }
    ?>

</div>
            <?php else : ?>
                <!-- Display form to start discussion -->
                <div class="col-12 discussion-body">
                <form id="startDiscussionForm" method="post">
                    <input type="text" class="form-control mb-2" name="discussionInput" placeholder="Type your discussion topic here">
                    <textarea class="form-control mb-2" name="thoughtsInput" placeholder="Type your thoughts here"></textarea>
                    <button type="submit" class="btn btn-view-discussion" name="startDiscussionButton">Start Discussion</button>

                </form>
                </div>

                <?php
                // Fetch and display existing discussions
                $discussionsPerPage = 5;
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
                            // Add condition to show only discussions matching the search keyword
                            if (!empty($searchKeyword) && stripos($discussion['discussion_topic'], $searchKeyword) === false) {
                                continue; // Skip to the next iteration if the discussion does not match the search
                            }
                        
                            echo '<div class="row justify-content-center mb-4">';
                            echo '<div class="col-12 discussion-body">';
                            echo '<p class="username-discussion">';
                           // Display user's full name, image, and "Created X hours/days ago" on the same line
                            echo '<div class="user-info d-flex">';
                            echo '<img src="/planetart/images/default-user.png" alt="User Icon" class="user-forum-icon">';
                            echo '<p class="username">' . htmlspecialchars($discussion['user_fullname']) . '</p>';
                            echo '<p class="mx-1"> </p>'; // Add a single space between the full name and "Created X hours/days ago"
                            echo '<p class="created-time">' . getTimeElapsedString($discussion['created_at']) . '</p>';
                            echo '</div>';
                            echo '<p id="discussionName" class="discussion-name">' . htmlspecialchars($discussion['discussion_topic']) . '</p>';
                            //echo '<div id="discussionThoughts" class="mb-2">' . htmlspecialchars($discussion['thoughts']) . '</div>';
                            
                            // Add "View Discussion" button with a link to the current page, passing the discussion ID
                            echo '<a href="?view_discussion_id=' . $discussion['id'] . '" class="btn btn-view-discussion">View Discussion</a>';
                            
                            // Add "Delete" button visible only to the user who started the discussion
                            if (isset($_SESSION['user_fullname']) && $_SESSION['user_fullname'] == $discussion['user_fullname']) {
                                echo '<a href="?delete_discussion_id=' . $discussion['id'] . ' "class="btn btn-delete-discussion" onclick="return confirm(\'Are you sure you want to delete this discussion?\')">Delete</button></a>';

                            }
                            
                            echo '</div></div>';
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

 <!-- Report Confirmation Modal -->
 <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Report Confirmation</h5>
                    <img src="/planetart/images/close.png" alt="Close" class="close-icon btn-close close-button" data-bs-dismiss="modal" aria-label="Close">
                </div>
                <div class="modal-body">
                    <p>You are reporting this comment:</p>
                    <p id="reportCommentText"></p>
                    <textarea id="reportReasonTextarea" class="form-control" placeholder="Explain why you are reporting this comment"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-view-discussion" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-view-discussion" onclick="submitReport()">Submit Report</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    function reportComment(commentId, commentText, userFullname) {
    // Set the comment with user's name, making the user's full name bold
    $("#reportCommentText").html('<strong>' + userFullname + ':</strong> ' + commentText);
    $("#reportModal").data("commentId", commentId);
    $("#reportModal").data("userFullname", userFullname); // Store the user's full name in the modal data
    $("#reportModal").modal("show");
}

function submitReport() {
    var commentId = $("#reportModal").data("commentId");
    var commentText = $("#reportCommentText").text();
    var reportReason = $("#reportReasonTextarea").val();
    var userFullname = $("#reportModal").data("userFullname"); // Get the user's full name from the modal data
    var discussionId = <?php echo $viewingDiscussionId; ?>; // Assuming $viewingDiscussionId contains the ID of the discussion

    $.ajax({
        url: "<?php echo $_SERVER['PHP_SELF']; ?>", // Current page URL
        method: "POST",
        data: { reportCommentId: commentId, reportCommentText: commentText, reportReason: reportReason, userFullname: userFullname, discussionId: discussionId },
        success: function(response) {
            // Handle the server response
            console.log(response);
            // Show the success message
            $("#reportSuccessMessage").fadeIn();
            // Hide the modal after a short delay
            setTimeout(function() {
                $("#reportModal").modal("hide");
            }, 2000); // 2000 milliseconds = 2 seconds
        },
        error: function(xhr, status, error) {
            // Handle the error
            console.error(error);
            // You can show an error message
        }
    });
}
</script>

<script>
    $(document).ready(function() {
        // Function to handle real-time search
        $("#searchInput").on("input", function() {
            var searchKeyword = $(this).val();
            // Use AJAX to fetch and display discussions based on the search keyword
            $.ajax({
                url: "your_php_script.php", // Replace with the actual PHP script path
                method: "GET",
                data: { search: searchKeyword },
                success: function(result) {
                    // Replace the existing discussions container with the updated results
                    $("#discussionsContainer").html(result);
                }
            });
        });
    });
</script>
<script>
$(document).ready(function() {
    // Add click event listener to all report buttons
    $('.report-button').click(function() {
        var commentId = $(this).attr('id').split('-')[1];
        var commentText = $(this).closest('.comment-box').find('.comment-content p').text().trim();
        reportComment(commentId, commentText);
    });
});
</script>

    <!-- Footer -->
    <footer class="text-center">
        <div class="footer p-3">
            © 2024
            <a>Planet Art</a>
        </div>
    </footer>

    <!-- Bootstrap js and popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    </body>
</html>