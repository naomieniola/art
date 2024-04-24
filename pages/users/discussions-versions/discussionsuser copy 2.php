<?php
include("../../remember_me.php");

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
                    <p id="discussionName" class="discussion-name">Start Discussion</p>
                    <div id="discussionThoughts" class="mb-2"></div>
                    <p id="discussionText">This is your discussion area. Share your thoughts and connect with other art enthusiasts.</p>

                    <!-- Discussion and Comment Section -->
                    <div class="comment-container" id="commentContainer">
                        <!-- Comments or discussion content will be appended here -->
                    </div>

                    <!-- Start Discussion Form -->
                    <form id="startDiscussionForm">
                        <input type="text" class="form-control mb-2" id="discussionInput" placeholder="Type your discussion topic here">
                        <textarea class="form-control mb-2" id="thoughtsInput" placeholder="Type your thoughts here"></textarea>
                        <button type="button" class="btn btn-primary" onclick="startDiscussion()" id="startDiscussionButton">Start Discussion</button>
                    </form>

                    <!-- Add Comment Form -->
                    <form id="addCommentForm" style="display: none;">
                        <textarea class="form-control mb-2" id="commentInput" placeholder="Type your comment here"></textarea>
                        <button type="button" class="btn btn-primary btn-add-comment" onclick="addComment()" id="addCommentButton">Add Comment</button>
                    </form>

                    <!-- Edit Comment Form -->
                    <form id="editCommentForm" style="display: none;">
                        <textarea class="form-control mb-2" id="editCommentInput"></textarea>
                        <button type="button" class="btn btn-success" onclick="saveEditedComment()">Save Changes</button>
                    </form>

                    <!-- Delete Discussion Button -->
                </div>
            </div>
        </div>
    <script>
        
    var discussionStarted = false;
    var editingComment = null;

    function startDiscussion() {
        // Check if the discussion has already started
        if (discussionStarted) {
            return;
        }

        // Get the discussion input value
        var discussionTitle = document.getElementById('discussionInput').value;
        var thoughtsText = document.getElementById('thoughtsInput').value;

        // Check if the discussion title and thoughts are not empty
        if (discussionTitle.trim() !== '' && thoughtsText.trim() !== '') {
            // Set the discussion title dynamically
            document.getElementById('discussionName').innerText = discussionTitle;

            // Display the thoughts as a paragraph
            document.getElementById('discussionThoughts').innerHTML = '<p>' + thoughtsText + '</p>';

            // Replace thoughts textarea with plain text paragraph
            document.getElementById('thoughtsInput').style.display = 'none';

            // Enable the comment input field
            document.getElementById('commentInput').disabled = false;

            // Change the button text to 'Add Comment'
            document.getElementById('addCommentButton').innerText = 'Add Comment';

            // Show the Add Comment Form
            document.getElementById('addCommentForm').style.display = 'block';

            // Remove the Start Discussion button
            var startDiscussionButton = document.getElementById('startDiscussionButton');
            startDiscussionButton.parentNode.removeChild(startDiscussionButton);

            // Hide the discussion input
            document.getElementById('discussionInput').style.display = 'none';

            // Hide the placeholder text
            document.getElementById('discussionText').style.display = 'none';

            // Update the flag to indicate that a discussion has been started
            discussionStarted = true;
        }
    }

    function addComment() {
        // Check if the user is in the process of editing a comment
        if (editingComment !== null) {
            return;
        }

        // Get the comment input value
        var commentText = document.getElementById('commentInput').value;

        // Check if the comment is not empty
        if (commentText.trim() !== '') {
            // Create a new comment element
            var commentElement = document.createElement('div');
            commentElement.className = 'comment';

            // Display the user's full name dynamically
            <?php if (isset($_SESSION['user_fullname'])) : ?>
                commentElement.innerHTML = '<p class="discussion-comment"><strong><?php echo $_SESSION['user_fullname']; ?>:</strong> ' + commentText + ' <a href="#" onclick="editComment(this)">Edit</a> <a href="#" onclick="deleteComment(this)">Delete</a></p>';
            <?php endif; ?>

            // Append the new comment to the comment container
            document.getElementById('commentContainer').appendChild(commentElement);

            // Clear the comment input field
            document.getElementById('commentInput').value = '';
        }
    }

    function editComment(editLink) {
        // Check if the user is logged in
        <?php if (isset($_SESSION['user_fullname'])) : ?>
            // Check if the user is already editing a comment
            if (editingComment !== null) {
                return;
            }

            // Get the parent comment container
            var commentContainer = editLink.parentNode;

            // Get the comment text
            var commentText = commentContainer.innerText.replace('Edit', '').replace('<?php echo $_SESSION['user_fullname']; ?>:', '');

            // Create an editable textarea
            var editCommentInput = document.createElement('textarea');
            editCommentInput.className = 'form-control mb-2';
            editCommentInput.value = commentText.trim();

            // Create a "Save Changes" button
            var saveChangesButton = document.createElement('button');
            saveChangesButton.type = 'button';
            saveChangesButton.className = 'btn btn-success';
            saveChangesButton.innerText = 'Save Changes';
            saveChangesButton.onclick = function () { saveEditedComment(commentContainer, editLink, editCommentInput, saveChangesButton); };

            // Replace the original comment with the editable textarea and "Save Changes" button
            commentContainer.innerHTML = '';
            commentContainer.appendChild(editCommentInput);
            commentContainer.appendChild(saveChangesButton);

            // Hide the comment input field and "Add Comment" button during editing
            document.getElementById('commentInput').style.display = 'none';
            document.getElementById('addCommentButton').style.display = 'none';

            // Save a reference to the edited comment
            editingComment = commentContainer;
        <?php endif; ?>
    }

    function saveEditedComment(commentContainer, editLink, editCommentInput, saveChangesButton) {
        // Get the edited comment text
        var editedText = editCommentInput.value;

        // Update the edited comment's text
        commentContainer.innerHTML = '<p class="discussion-comment"><strong><?php echo $_SESSION['user_fullname']; ?>:</strong> ' + editedText + ' <a href="#" onclick="editComment(this)">Edit</a> <a href="#" onclick="deleteComment(this)">Delete</a></p>';

        // Show the comment input field and "Add Comment" button after saving changes
        document.getElementById('commentInput').style.display = 'block';
        document.getElementById('addCommentButton').style.display = 'block';

        // Reset the editingComment variable
        editingComment = null;
    }

        function deleteComment(deleteLink) {
        // Get the parent comment container
        var commentContainer = deleteLink.parentNode;

        // Remove the comment container from the DOM
        commentContainer.parentNode.removeChild(commentContainer);
    }

    
    </script>

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
