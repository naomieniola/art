<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

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

$discussionInput = '';
$thoughtsInput = '';

// Function to check for inappropriate words
function containsSwearWords($text) {
    $swearWords = array("fuck", "Fuck", "Bitch", "bitch", "Shit", "shit", "Bastard", "bastard"); // Add your list of inappropriate words

    foreach ($swearWords as $swearWord) {
        if (stripos($text, $swearWord) !== false) {
            return true;
        }
    }

    return false;
}

if (isset($_POST['startDiscussionButton'])) {
    $discussionInput = trim($_POST['discussionInput']);
    $thoughtsInput = trim($_POST['thoughtsInput']);

    // Validate that the discussion topic and thoughts are not empty
    if (empty($discussionInput) || empty($thoughtsInput)) {
        echo '<div class="alert alert-danger" role="alert">Discussion topic and thoughts cannot be empty.</div>';
    } else {
        // Store the discussion ID in a variable
        $discussionId = 0; // Initialize to a default value

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
}

if (isset($_POST['addCommentButton'])) {
    $commentText = trim($_POST['commentInput']);

    // Validate that the comment is not empty
    if (empty($commentText)) {
        echo '<div class="alert alert-danger" role="alert">Comment cannot be empty.</div>';
    } elseif (containsSwearWords($commentText)) {
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
if (isset($_GET['delete_comment_id'])) {
    $deleteCommentId = intval($_GET['delete_comment_id']);
    $sqlDeleteComment = "DELETE FROM comments WHERE id = ? AND user_fullname = ?";
    $stmtDeleteComment = mysqli_stmt_init($conn);

    if ($stmtDeleteComment) {
        if (mysqli_stmt_prepare($stmtDeleteComment, $sqlDeleteComment)) {
            mysqli_stmt_bind_param($stmtDeleteComment, "is", $deleteCommentId, $_SESSION['user_fullname']);
            mysqli_stmt_execute($stmtDeleteComment);

            // Redirect or display a success message as needed
            header("Location: your_page.php?view_discussion_id=" . $viewingDiscussionId);
            exit();
        } else {
            // Handle error in preparing the delete comment statement
            die("Error in preparing the delete comment statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the delete comment statement
        die("Error in initializing the delete comment statement: " . mysqli_error($conn));
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
<div class="container explore-desc">
        <div class="row">
            <div class="col-12">
                <p class="artists-main-text">Discover Discussions</p>
                <p class="artists-sub-text">Loerm Ipsum</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6">
                <?php if (isset($_GET['view_discussion_id'])) : ?>
                    <div class="comment-container" id="commentContainer">
                        <?php
                        $sqlDiscussion = "SELECT * FROM discussions WHERE id = ?";
                        $stmtDiscussion = mysqli_stmt_init($conn);

                        if ($stmtDiscussion) {
                            if (mysqli_stmt_prepare($stmtDiscussion, $sqlDiscussion)) {
                                mysqli_stmt_bind_param($stmtDiscussion, "i", $viewingDiscussionId);
                                mysqli_stmt_execute($stmtDiscussion);

                                $resultDiscussion = mysqli_stmt_get_result($stmtDiscussion);

                                if ($rowDiscussion = mysqli_fetch_assoc($resultDiscussion)) {
                                    echo '<div class="discussion-header">';
                                    echo '<h3>' . $rowDiscussion['discussion_topic'] . '</h3>';
                                    echo '<p class="user-date">' . $rowDiscussion['user_fullname'] . ', ' . getTimeElapsedString($rowDiscussion['timestamp']) . '</p>';
                                    echo '<p class="thoughts">' . $rowDiscussion['thoughts'] . '</p>';
                                    echo '</div>';

                                    echo '<div class="comments">';
                                    $sqlComments = "SELECT * FROM comments WHERE discussion_id = ?";
                                    $stmtComments = mysqli_stmt_init($conn);

                                    if ($stmtComments) {
                                        if (mysqli_stmt_prepare($stmtComments, $sqlComments)) {
                                            mysqli_stmt_bind_param($stmtComments, "i", $viewingDiscussionId);
                                            mysqli_stmt_execute($stmtComments);

                                            $resultComments = mysqli_stmt_get_result($stmtComments);

                                            while ($rowComment = mysqli_fetch_assoc($resultComments)) {
                                                echo '<div class="comment">';
                                                echo '<p class="user-date">' . $rowComment['user_fullname'] . ', ' . getTimeElapsedString($rowComment['timestamp']) . '</p>';
                                                echo '<p class="comment-text">' . $rowComment['comment_text'] . '</p>';
                                                echo '</div>';
                                            }
                                        } else {
                                            die("Error in preparing the comments statement: " . mysqli_error($conn));
                                        }
                                    } else {
                                        die("Error in initializing the comments statement: " . mysqli_error($conn));
                                    }

                                    echo '</div>';
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">Discussion not found.</div>';
                                }
                            } else {
                                die("Error in preparing the discussion statement: " . mysqli_error($conn));
                            }
                        } else {
                            die("Error in initializing the discussion statement: " . mysqli_error($conn));
                        }
                        ?>

                        <form id="addCommentForm" method="post">
                            <input type="hidden" name="discussionId" value="<?php echo $viewingDiscussionId; ?>">
                            <textarea class="form-control mb-2" name="commentInput" placeholder="Type your comment here"></textarea>
                            <button type="submit" class="btn btn-add-comment-with-image" name="addCommentButton">
                                <img src="/planetart/images/send.png" alt="Send">
                            </button>
                        </form>
                    </div>
                <?php else : ?>
                    <div class="col-12 discussion-body">
                        <form id="startDiscussionForm" method="post">
                            <input type="text" class="form-control mb-2" name="discussionInput"
                                placeholder="Type your discussion topic here" value="<?php echo $discussionInput; ?>">
                            <textarea class="form-control mb-2" name="thoughtsInput"
                                placeholder="Type your thoughts here"><?php echo $thoughtsInput; ?></textarea>
                            <button type="submit" class="btn btn-view-discussion" name="startDiscussionButton">Start Discussion</button>
                        </form>

                        <?php
                        $sqlDiscussions = "SELECT * FROM discussions ORDER BY timestamp DESC";
                        $stmtDiscussions = mysqli_stmt_init($conn);

                        if ($stmtDiscussions) {
                            if (mysqli_stmt_prepare($stmtDiscussions, $sqlDiscussions)) {
                                mysqli_stmt_execute($stmtDiscussions);

                                $resultDiscussions = mysqli_stmt_get_result($stmtDiscussions);

                                while ($rowDiscussions = mysqli_fetch_assoc($resultDiscussions)) {
                                    echo '<div class="discussion">';
                                    echo '<h4>' . $rowDiscussions['discussion_topic'] . '</h4>';
                                    echo '<p class="user-date">' . $rowDiscussions['user_fullname'] . ', ' . getTimeElapsedString($rowDiscussions['timestamp']) . '</p>';
                                    echo '<p class="thoughts">' . $rowDiscussions['thoughts'] . '</p>';
                                    echo '<a href="?view_discussion_id=' . $rowDiscussions['id'] . '">View Discussion</a>';

                                    if ($rowDiscussions['user_fullname'] == $_SESSION['user_fullname']) {
                                        echo ' | <a href="?delete_discussion_id=' . $rowDiscussions['id'] . '" onclick="return confirm(\'Are you sure you want to delete this discussion?\')">Delete Discussion</a>';
                                    }

                                    echo '</div>';
                                }
                            } else {
                                die("Error in preparing the discussions statement: " . mysqli_error($conn));
                            }
                        } else {
                            die("Error in initializing the discussions statement: " . mysqli_error($conn));
                        }
                        ?>
                    </div>
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