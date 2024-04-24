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
    <title>Planet Art | Home </title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
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
    <nav class="navbar">
    <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
    <span class="site-name">Planet Art</span>

    <div>
        <a class="btn btn-outline-secondary nav-links current-page" href="indexuser.php">
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
    

    <div class="container">
        <div class="row">
            <!-- First column taking half the space -->
            <!-- <div class="col-md-6">
                <div class="bg-primary p-3 h-100">
                    Content for the first column -->
                    <!-- <h3> WHAT IS PLANET ART?</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt auctor elit, ut molestie diam bibendum vel. Donec suscipit ac nibh vel hendrerit. Proin nec leo a orci accumsan lacinia vitae non metus. Sed eu neque feugiat, vulputate turpis sollicitudin, fringilla lorem. Curabitur consequat consequat justo. Donec ac semper enim, quis tempus nisi. Aenean dictum malesuada libero id bibendum. Integer sit amet blandit ligula. Suspendisse velit metus, sollicitudin.</p>
                    
                    <p>The central questions that this website seeks to answer are
                    "what is art?" and “what does it mean to different people”? When
                    people think of art, most think about famous painters, and rarely
                    music and various types of instruments or how those instruments came
                    about. Neither do most think about culture, environment, language and
                    much more. The goal is to provide a comprehensive platform that helps
                    you to explore these questions and gain a deeper understanding of the
                    multifaceted nature of art.</p> -->
                </div> 
            </div>

            
        </div>
    </div>

    <div class="full-width-container"></div>

    <div class="container explore-desc">
        <div class="row">
            <div class="col-12">
                <p class="explore-main-text">Keep exploring...</p>
                <p class="explore-sub-text">Dive into culture from around the world</p>
            </div>
        </div>
    </div>

    <!-- Explore containers -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
            <div class="card custom-card">
                <img src="/planetart/images/chas1.jpg" alt="Image 1">
                <div class="card-content">
                <h5>Title 1</h5>
                <p>Description 1</p>
                <button class="btn btn-outline-primary btn-explore">Explore</button>
                </div>
            </div>
            </div>

            <div class="col-md-3">
            <div class="card custom-card">
                <img src="/planetart/images/chas1.jpg" alt="Image 2">
                <div class="card-content">
                <h5>Title 2</h5>
                <p>Description 2</p>
                <button class="btn btn-outline-primary btn-explore">Explore</button>
                </div>
            </div>
            </div>

    <div class="col-md-3">
      <div class="card custom-card">
        <img src="/planetart/images/poetry.jpg" alt="Image 3">
        <div class="card-content">
          <h5>Title 3</h5>
          <p>Description 3</p>
          <button class="btn btn-outline-primary btn-explore">Explore</button>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card custom-card">
        <img src="/planetart/images/artuk_egypt.jpg" alt="Image 4">
        <div class="card-content">
          <h5>Title 4</h5>
          <p>Description 4</p>
          <button class="btn btn-outline-primary btn-explore">Explore</button>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- Footer -->
    <footer class="text-center">
        <!-- Copyright -->
        <div class="footer p-3">
            © 2024
            <a>Planet Art</a>
        </div>
        <!-- Copyright -->
    </footer>

    <!-- Bootstrap js and popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>