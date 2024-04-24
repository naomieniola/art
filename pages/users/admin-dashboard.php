<?php
ob_start();
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();


/* Check if the user is an admin
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    $_SESSION["error_message"] = "You do not have permission to access this page.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
} */

// Check if the user is an admin
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    $_SESSION["error_message"] = "You do not have permission to access this page.";
    
    // Check if HTTP_REFERER is set, otherwise provide a fallback URL
    $redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "indexuser.php";
    header("Location: " . $redirect_url);
    exit();
}

// Check if the user is already logged in
if (isset($_SESSION["user"])) {
    // If the user is logged in and tries to access login.php or signup.php, redirect them to the previous page
    if (basename($_SERVER['PHP_SELF']) == "login.php" || basename($_SERVER['PHP_SELF']) == "signup.php") {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
} else {
    // If the user is not logged in, redirect them to login.php
    if (basename($_SERVER['PHP_SELF']) != "login.php" && basename($_SERVER['PHP_SELF']) != "signup.php") {
        $_SESSION["previous_page"] = $_SERVER["REQUEST_URI"];
        header("Location: login.php");
        exit();
    }
}


// Regenerate session ID to prevent session fixation
session_regenerate_id(true);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Admin Dashboard </title>
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
                <a class="btn btn-outline-secondary nav-links" href="indexuser.php">
                    Home
                </a>
                <a class="btn btn-outline-secondary nav-links" href="artistsuser.php">
                    Artists
                </a>
                <a class="btn btn-outline-secondary nav-links" href="discussionsuser.php">
                    Discussions
                </a>
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
            //
            session_start(); // Ensure session is started
            session_destroy();
            
            // Redirect the user to the login page after logging out successfully
            header("Location: /planetart/pages/non-users/login.php");
            exit();
        }
        ?>
    </header>

    <!-- Main Content Container -->
    <?php
        require_once "../../config.php";
    ?>

<div class="container-fluid admin-dashboard">
    <div class="background-image"></div>
    <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title">Admin Dashboard</h2>
            
            <!-- User Search and Admin Assignment -->
            <div class="mb-4">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchUser" placeholder="Search user by username to assign/unassign admin. Click 'Search' to view all admins.">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn btn-view-discussion" name="searchButton">Search</button>
                            <?php if (isset($_POST['searchButton'])) : ?>
                                <button type="submit" class="btn btn-secondary" name="closeButton">Close</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
                
                <?php
                if (isset($_POST['searchButton']) && !isset($_POST['closeButton'])) {
                    $searchUser = $_POST['searchUser'];
                    
                    // Search for the user in the users table
                    $sqlSearchUser = "SELECT * FROM users WHERE username LIKE ?";
                    $stmtSearchUser = mysqli_prepare($conn, $sqlSearchUser);
                    $searchTerm = "%" . $searchUser . "%";
                    mysqli_stmt_bind_param($stmtSearchUser, "s", $searchTerm);
                    mysqli_stmt_execute($stmtSearchUser);
                    $searchResult = mysqli_stmt_get_result($stmtSearchUser);
                    
                    if (mysqli_num_rows($searchResult) > 0) {
                        echo '<table class="table table-striped mt-3">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Username</th>';
                        echo '<th>First Name</th>';
                        echo '<th>Admin Status</th>';
                        echo '<th>Actions</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        
                        while ($user = mysqli_fetch_assoc($searchResult)) {
                            echo '<tr>';
                            echo '<td>' . $user['username'] . '</td>';
                            echo '<td>' . $user['full_name'] . '</td>';
                            echo '<td>' . ($user['admin'] == 1 ? 'Admin' : 'Regular User') . '</td>';
                            echo '<td>';
                            echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">';
                            echo '<input type="hidden" name="userId" value="' . $user['id'] . '">';
                            
                            if ($user['admin'] == 1) {
                                echo '<button type="submit" class="btn btn-warning btn-sm" name="unassignAdminButton">Unassign Admin</button>';
                            } else {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="assignAdminButton">Assign Admin</button>';
                            }
                            
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No user found.</p>';
                    }
                }
                ?>
            </div>
            
            <!-- Reported Comments Table -->
            <h4>Reported Comments</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Comment ID</th>
                        <th>Reported By</th>
                        <th>Reported User</th>
                        <th>Comment Text</th>
                        <th>Report Explanation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once "../../config.php";

                    // Fetch reported comments from the database
                    $sqlFetchReports = "SELECT * FROM reports";
                    $resultReports = mysqli_query($conn, $sqlFetchReports);

                    while ($report = mysqli_fetch_assoc($resultReports)) {
                        echo '<tr>';
                        echo '<td>' . $report['comment_id'] . '</td>';
                        echo '<td>' . $report['reported_by_user_fullname'] . '</td>';
                        echo '<td>' . $report['reported_user_fullname'] . '</td>';
                        echo '<td>' . $report['report_comment_text'] . '</td>';
                        echo '<td>' . $report['report_explanation'] . '</td>';
                        echo '<td>';
                        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">';
                        echo '<input type="hidden" name="deleteCommentId" value="' . $report['comment_id'] . '">';
                        echo '<button type="submit" class="btn btn-danger btn-sm" name="deleteCommentButton">Delete Comment</button>';
                        echo '</form>';
                        echo '&nbsp;';
                        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">';
                        echo '<input type="hidden" name="deleteUserFullname" value="' . $report['reported_user_fullname'] . '">';
                        echo '<button type="submit" class="btn btn-danger btn-sm" name="deleteUserButton">Delete User</button>';
                        echo '</form>';
                        echo '&nbsp;';
                        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">';
                        echo '<input type="hidden" name="deleteReportId" value="' . $report['id'] . '">';
                        echo '<button type="submit" class="btn btn-warning btn-sm" name="deleteReportButton">Delete Report</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    <?php
    // Admin assignment
    if (isset($_POST['assignAdminButton'])) {
        $userId = intval($_POST['userId']);
        
        // Update the user's admin status to 1 (admin)
        $sqlAssignAdmin = "UPDATE users SET admin = 1 WHERE id = ?";
        $stmtAssignAdmin = mysqli_prepare($conn, $sqlAssignAdmin);
        mysqli_stmt_bind_param($stmtAssignAdmin, "i", $userId);
        mysqli_stmt_execute($stmtAssignAdmin);
        
        // Refresh the page after assignment
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Admin unassignment
    if (isset($_POST['unassignAdminButton'])) {
        $userId = intval($_POST['userId']);
        
        // Update the user's admin status to 0 (regular user)
        $sqlUnassignAdmin = "UPDATE users SET admin = 0 WHERE id = ?";
        $stmtUnassignAdmin = mysqli_prepare($conn, $sqlUnassignAdmin);
        mysqli_stmt_bind_param($stmtUnassignAdmin, "i", $userId);
        mysqli_stmt_execute($stmtUnassignAdmin);
        
        // Refresh the page after unassignment
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Comment deletion
    if (isset($_POST['deleteCommentButton'])) {
        $deleteCommentId = intval($_POST['deleteCommentId']);

        // Display a confirmation alert using JavaScript
        echo '<script type="text/javascript">';
        echo 'if (confirm("Are you sure you want to delete this comment?")) {';
        
        // Delete the comment from the comments table
        $sqlDeleteComment = "DELETE FROM comments WHERE id = ?";
        $stmtDeleteComment = mysqli_prepare($conn, $sqlDeleteComment);
        mysqli_stmt_bind_param($stmtDeleteComment, "i", $deleteCommentId);
        mysqli_stmt_execute($stmtDeleteComment);
        
        // Delete the report from the reports table
        $sqlDeleteReport = "DELETE FROM reports WHERE comment_id = ?";
        $stmtDeleteReport = mysqli_prepare($conn, $sqlDeleteReport);
        mysqli_stmt_bind_param($stmtDeleteReport, "i", $deleteCommentId);
        mysqli_stmt_execute($stmtDeleteReport);
        
        // Refresh the page after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
  // User deletion
    if (isset($_POST['deleteUserButton'])) {
    $deleteUserFullname = $_POST['deleteUserFullname'];
    
    // Display a confirmation alert using JavaScript
    echo '<script type="text/javascript">';
    echo 'if (confirm("Are you sure you want to delete this user and all associated data?")) {';
    
    // Delete the user's liked items from the liked_items table
    $sqlDeleteUserLikedItems = "DELETE li FROM liked_items li
                                INNER JOIN users u ON li.user_id = u.id
                                WHERE u.full_name = ?";
    $stmtDeleteUserLikedItems = mysqli_prepare($conn, $sqlDeleteUserLikedItems);
    mysqli_stmt_bind_param($stmtDeleteUserLikedItems, "s", $deleteUserFullname);
    mysqli_stmt_execute($stmtDeleteUserLikedItems);
    
    // Delete the user from the users table
    $sqlDeleteUser = "DELETE FROM users WHERE full_name = ?";
    $stmtDeleteUser = mysqli_prepare($conn, $sqlDeleteUser);
    mysqli_stmt_bind_param($stmtDeleteUser, "s", $deleteUserFullname);
    mysqli_stmt_execute($stmtDeleteUser);
    
    // Delete the user's comments from the comments table
    $sqlDeleteUserComments = "DELETE FROM comments WHERE user_fullname = ?";
    $stmtDeleteUserComments = mysqli_prepare($conn, $sqlDeleteUserComments);
    mysqli_stmt_bind_param($stmtDeleteUserComments, "s", $deleteUserFullname);
    mysqli_stmt_execute($stmtDeleteUserComments);
    
    // Delete the user's discussions from the discussions table
    $sqlDeleteUserDiscussions = "DELETE FROM discussions WHERE user_fullname = ?";
    $stmtDeleteUserDiscussions = mysqli_prepare($conn, $sqlDeleteUserDiscussions);
    mysqli_stmt_bind_param($stmtDeleteUserDiscussions, "s", $deleteUserFullname);
    mysqli_stmt_execute($stmtDeleteUserDiscussions);
    
    // Delete the user's reports from the reports table
    $sqlDeleteUserReports = "DELETE FROM reports WHERE reported_user_fullname = ?";
    $stmtDeleteUserReports = mysqli_prepare($conn, $sqlDeleteUserReports);
    mysqli_stmt_bind_param($stmtDeleteUserReports, "s", $deleteUserFullname);
    mysqli_stmt_execute($stmtDeleteUserReports);
    
    echo 'window.location.href = "' . $_SERVER['PHP_SELF'] . '";';
    echo '} else {';
    echo 'window.location.href = "' . $_SERVER['PHP_SELF'] . '";';
    echo '}';
    echo '</script>';
    
    exit();
}

    // Handle report deletion
    if (isset($_POST['deleteReportButton'])) {
        $deleteReportId = intval($_POST['deleteReportId']);
        
        // Delete the report from the reports table
        $sqlDeleteReport = "DELETE FROM reports WHERE id = ?";
        $stmtDeleteReport = mysqli_prepare($conn, $sqlDeleteReport);
        mysqli_stmt_bind_param($stmtDeleteReport, "i", $deleteReportId);
        mysqli_stmt_execute($stmtDeleteReport);
        
        // Refresh the page after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>
    
   <!-- Footer -->
   <footer class="text-center">
        <div class="footer p-3">
            © 2024
            <a>Planet Art</a>
            <a href="privacy-policy.php" class="privacy-policy-link">Privacy Policy</a>
        </div>
    </footer>
    
    <!-- Bootstrap js and popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>