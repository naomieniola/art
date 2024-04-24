<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

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

    // Assuming you have a 'discussions' table with columns 'discussion_topic' and 'thoughts'
    $sql = "INSERT INTO discussions (discussion_topic, thoughts) VALUES (?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if ($stmt) {
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $discussionInput, $thoughtsInput);
            mysqli_stmt_execute($stmt);

            // Redirect or display a success message as needed
        } else {
            // Handle error in preparing the statement
            die("Error in preparing the statement: " . mysqli_error($conn));
        }
    } else {
        // Handle error in initializing the statement
        die("Error in initializing the statement: " . mysqli_error($conn));
    }
}

// Add the comment handling code here
if (isset($_POST['addCommentButton'])) {
    $commentText = $_POST['commentInput'];
    $userFullname = $_SESSION['user_fullname'];
    $discussionId = 1; // Replace with the actual discussion_id, you need to get it from the discussion you're commenting on

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
            <div class="col-6 discussion-body">
                <?php if (isset($_SESSION['user_fullname'])) : ?>
                    <p class="username-discussion"><?php echo $_SESSION['user_fullname']; ?></p>
                <?php endif; ?>

                <?php if (isset($_POST['startDiscussionButton'])) : ?>
                    <!-- Display form with discussion topic and thoughts -->
                    <p id="discussionName" class="discussion-name"><?php echo htmlspecialchars($discussionInput); ?></p>
                    <div id="discussionThoughts" class="mb-2"><?php echo htmlspecialchars($thoughtsInput); ?></div>

                    <!-- Discussion and Comment Section -->
                    <div class="comment-container" id="commentContainer">
                        <!-- Comments or discussion content will be appended here -->
                        <?php
                        // Fetch and display existing comments for the discussion
                        $sqlFetchComments = "SELECT * FROM comments WHERE discussion_id = ? ORDER BY created_at DESC";
                        $stmtFetchComments = mysqli_stmt_init($conn);

                        if ($stmtFetchComments) {
                            if (mysqli_stmt_prepare($stmtFetchComments, $sqlFetchComments)) {
                                mysqli_stmt_bind_param($stmtFetchComments, "i", $discussionId);
                                mysqli_stmt_execute($stmtFetchComments);

                                $resultComments = mysqli_stmt_get_result($stmtFetchComments);

                                while ($comment = mysqli_fetch_assoc($resultComments)) {
                                    echo '<p>' . htmlspecialchars($comment['user_fullname']) . ': ' . htmlspecialchars($comment['comment_text']) . '</p>';
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
                    </div>

                    <!-- Add Comment Form -->
                    <form id="addCommentForm" method="post">
                        <textarea class="form-control mb-2" name="commentInput" placeholder="Type your comment here"></textarea>
                        <button type="submit" class="btn btn-primary btn-add-comment" name="addCommentButton">Add Comment</button>
                    </form>

                    <!-- Display user's comment -->
                    <?php if (isset($_POST['addCommentButton'])) : ?>
                        <div class="mb-2">
                            <p><?php echo htmlspecialchars($_SESSION['user_fullname']) . ': ' . htmlspecialchars($_POST['commentInput']); ?></p>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- Display form to start discussion -->
                    <form id="startDiscussionForm" method="post">
                        <input type="text" class="form-control mb-2" name="discussionInput" placeholder="Type your discussion topic here">
                        <textarea class="form-control mb-2" name="thoughtsInput" placeholder="Type your thoughts here"></textarea>
                        <button type="submit" class="btn btn-primary" name="startDiscussionButton">Start Discussion</button>
                    </form>
                <?php endif; ?>

                <!-- Delete Discussion Button -->
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
   