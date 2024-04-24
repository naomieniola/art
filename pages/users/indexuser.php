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

require_once "../../config.php";


// Regenerate session ID to prevent session fixation
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Home </title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap">
    <link rel="stylesheet" href="/planetart/css/index-user-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
</head>

<body>
<?php
    // Display error message if set
    if (isset($_SESSION["error_message"])) {
        echo '<div id="errorMessage" class="alert alert-danger">' . $_SESSION["error_message"] . '</div>';
        unset($_SESSION["error_message"]);
        
        echo '<script>
            setTimeout(function() {
                document.getElementById("errorMessage").style.display = "none";
            }, 10000);
        </script>';
    }
    ?>

    <!-- Header Container -->
    <header>
    <header>
    <nav class="navbar">
        <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
        <span class="site-name">Planet Art</span>

        <div>
            <a class="btn btn-outline-secondary nav-links current-page" href="indexuser.php">Home</a>
            <a class="btn btn-outline-secondary nav-links" href="artistsuser.php">Artists</a>
            <a class="btn btn-outline-secondary nav-links" href="discussionsuser.php">Discussions</a>
        </div>

            <div class="ml-auto d-flex align-items-center">
            <!-- Welcome message -->
            <?php if (isset($_SESSION['user_fullname'])) : ?>
                <p class="welcome-message mb-0 me-3">Welcome, <?php echo $_SESSION['user_fullname']; ?>!</p>
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
            // Perform logout actions here, e.g., destroy the session
            session_start(); // Ensure session is started
            session_destroy();
            
            // Clear the "Remember Me" cookie
            setcookie("remember_token", "", time() - 3600, "/");

            // Redirect the user to the login page
            header("Location: /planetart/pages/non-users/login.php");
            exit();
        }
        ?>
    </header>

    <!-- Main Content Container -->
    <div class="background-colour">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="explore-main-text" style="text-align: center;">What do you want to explore?</div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/fritz.jpg" alt="Artists" class="img-fluid">
                        <div class="card-content">
                            <h5>Artists</h5>
                            <p>Explore a vast collection of paintings, drawings and sketches from various artists and styles.</p>
                            <a href="artistsuser.php" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/beyonce.jpg" alt="Drawings" class="img-fluid">
                        <div class="card-content">
                            <h5>Music</h5>
                            <p>Immerse yourself in the captivating realm of music and compositions.</p>
                            <a href="musicians" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/poetry.jpg" alt="Music" class="img-fluid">
                        <div class="card-content">
                            <h5>Poetry</h5>
                            <p>Experience the beauty and depth of poetic expressions from renowned poets across the globe.</p>
                            <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/mediums.jpg" alt="Artists" class="img-fluid">
                        <div class="card-content">
                            <h5>Mediums</h5>
                            <p>Discover the diverse range of artistic mediums, from oil paints to digital art, and everything in between.</p>
                            <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/art-movements.jpg" alt="Artists" class="img-fluid">
                        <div class="card-content">
                            <h5>Art Movements</h5>
                            <p>Explore the influential art movements that shaped the art world throughout history</p>
                            <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="index-card">
                        <img src="/planetart/images/is-this-cake.jpg" alt="Artists" class="img-fluid">
                        <div class="card-content">
                            <h5>Unconventional art forms</h5>
                            <p>Discover the unexpected and thought-provoking world of unconventional art forms that challenge traditional definitions.</p>
                            <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid weekly-highlights">
            <div class="row">
                <div class="col-12 highlight-background">
                    <div class="highlight-content">
                        <h2 class="section-title">Weekly News</h2>
                        <a href="#" class="btn btn-outline-light btn-explore">Read</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container explore-street-view">
            <h2>Explore art from around the world</h2>
                <p>View art collections</p>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <img src="/planetart/images/Yemisi-Shyllon-Museum-of-Art.png" alt="Taj Mahal" class="card-img-top">
                                    <div class="card-body">
                                    <h5 class="card-title">Yemisi Shyllon Museum of Art</h5>
                                        <p class="card-text">Nigeria</p>
                                        <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <img src="/planetart/images/louvre.jpg" class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title">Louvre</h5>
                                            <p class="card-text">France</p>
                                            <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <img src="/planetart/images/van-gogh-museum.jpg" class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title">Van Gogh Museum</h5>
                                        <p class="card-text">Netherlands</p>
                                    <a href="#" class="btn btn-outline-light btn-explore">Explore</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <!-- Latest Discussions or Blog Posts -->
            <div class="container latest-posts">
                <h2 class="section-title" style="text-align: center;">Latest Discussions</h2>
                <div class="row">
                    <?php
                    // Fetch the latest discussions from the database
                    $sqlLatestDiscussions = "SELECT * FROM discussions ORDER BY created_at DESC LIMIT 3";
                    $resultLatestDiscussions = mysqli_query($conn, $sqlLatestDiscussions);

                    while ($discussion = mysqli_fetch_assoc($resultLatestDiscussions)) {
                        echo '<div class="col-md-4">';
                        echo '<div class="post-card">';
                        echo '<h4>' . htmlspecialchars($discussion['discussion_topic']) . '</h4>';
                        echo '<p>' . htmlspecialchars(substr($discussion['thoughts'], 0, 100)) . '...</p>';
                        echo '<a href="discussionsuser.php?view_discussion_id=' . $discussion['id'] . '" class="btn btn-outline-primary login">Read More</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                <div class="artform-card">
                    <div class="artform-header text-center">
                        <h2>Want your art to be featured on PlanetArt?</h2>
                    </div>
                        <div class="artform-body">
                            <p class="lead artform-lead text-center">Send us an email!</p>
                            <form>
                                <div class="form-group">
                                    <label for="artform-name" class="artform-label">Name</label>
                                    <input type="text" class="form-control artform-input" id="artform-name" placeholder="Enter your name">
                                </div>
                                <div class="form-group">
                                    <label for="artform-email" class="artform-label">Email</label>
                                    <input type="email" class="form-control artform-input" id="artform-email" placeholder="Enter your email">
                                </div>
                                <div class="form-group">
                                    <label for="artform-message" class="artform-label">Message</label>
                                <textarea class="form-control artform-textarea" id="artform-message" rows="3" placeholder="Tell us about your art"></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-outline-primary login" style="margin-top: 20px;">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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