<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

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
    <title>Planet Art | Volker Hermes</title>
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
        <a class="btn btn-outline-secondary nav-links" href="mediumsuser.php">
            Mediums
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
            // Perform logout actions here, e.g., destroy the session
            session_start(); // Ensure session is started
            session_destroy();
            
            // Clear the "Remember Me" cookie
            setcookie("remember_token", "", time() - 3600, "/");

            // Redirect the user to the login page or any other desired page
            header("Location: /planetart/pages/non-users/login.php");
            exit();
        }
        ?>
    </header>

    <!-- Main Content Container -->
    <div class="full-width-container-volker-hermes"></div>
        <div class="container explore-desc">
                <div class="row">
                    <div class="col-12">
                        <p class="artists-main-text">Violker Hermes</p>
                        <p class="artists-sub-text">Born 1972</p>
                        <p class="vincent-desc">Volker Hermes, born in 1972, draws and paints powerfully and directly, with perseverance and a lot of humour. His pictures and drawings are always integrated into comprehensive projects, within which Hermes gets to the core of his subject in an almost scientific way. These projects include a multi-year preoccupation with the subject of portraiture and, more recently, with the subject of historical battle painting and seascapes.
                        <br> <br>
                        His works are always an expression of an intensive engagement with art. Thus his portraits do not serve the honor of their protagonists and the sea battles do not serve the glory of old seafaring. Hermes' paintings bring fame and glory to art itself. His figures exercise the possibilities of painterly representation with impressive virtuosity. And there are no limits to these. Hermes combines linear conception with painterly gestures and watercolor-like painting with hard Edding drawing and shows himself to be a master in both fields.
                        <br> <br>
                        <!-- Tragically, Van Gogh's mental health deteriorated, and he ultimately died by suicide at the age of 37. Despite his struggles during his lifetime, his work gained recognition and appreciation posthumously, and today he is considered a master of modern art. -->
                        </p>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
        <!-- First Row -->
        <div class="row">
            <div class="col medium-column-artists">
                <div class="position-relative medium-column-artists-inner">
                    <a href="volker-hermes.php">
                        <img src="/planetart/images/volker.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <p class="bottom-left-text"></p>
                </div>
            </div>
            <div class="col medium-column-artists" style="border-radius: 15px !important;">
                <div class="position-relative">
                    <a href="volker-hidden.php">
                        <img src="/planetart/images/volker1.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <p class="bottom-left-text"></p>
                </div>
            </div>
            <div class="col medium-column-artists">
                <div class="position-relative">
                    <a href="your_link_3">
                        <img src="volker.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <p class="bottom-left-text">Text 3</p>
                </div>
            </div>
            <div class="col medium-column-artists">
                <div class="position-relative">
                    <a href="your_link_4">
                        <img src="volker.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <p class="bottom-left-text">Text 4</p>
                </div>
            </div>
            <div class="col medium-column-artists">
                <div class="position-relative">
                    <a href="your_link_5">
                        <img src="volker.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <p class="bottom-left-text">Text 5</p>
                </div>
            </div>
        </div>


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