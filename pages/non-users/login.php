<?php
require_once "../../config.php";
require_once '../../vendor/autoload.php';
use Snipe\BanBuilder\CensorWords;
$censorWords = new CensorWords();


session_start();

// Check if the remember_token cookie is set
if (isset($_COOKIE['remember_token'])) {
    $rememberToken = $_COOKIE['remember_token'];

    // Find the user with the matching remember token
    $sql = "SELECT * FROM users WHERE remember_token = '$rememberToken'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Check if the remember token is still valid (not expired)
        $currentTime = time();
        if ($user['remember_expires'] > $currentTime) {
            // Set user details in the session
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_fullname"] = $user["full_name"];
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["admin"] = ($user["admin"] == 1) ? 1 : 0;

            // Redirect to the user's homepage
            header("Location: /planetart/pages/users/indexuser.php");
            exit();
        }
    }
}


if (isset($_GET['redirect']) && $_GET['redirect'] === 'discussionsuser') {
    if (!isset($_SESSION["user"])) {
        $errorMessage = "You need to be logged in to access the Discussions page.";
        header("refresh:7;url=login.php");
    } else {
        $discussionId = isset($_GET['view_discussion_id']) ? intval($_GET['view_discussion_id']) : 0;
        header("Location: discussionsuser.php?view_discussion_id=" . $discussionId);
        exit();
    }
}



if (isset($_POST["login"])) {
    $emailOrUsername = $_POST["email_or_username"];
    $password = $_POST["password"];
    $rememberMe = isset($_POST["rememberMe"]) ? true : false;

    if (empty($emailOrUsername) || empty($password)) {
        $errorMessage = "Cannot leave blank.";
    } else {
        // Query the database with either email or username
        $sql = "SELECT *, IFNULL(profile_picture, 'default-user.png') AS profile_picture FROM users WHERE email = '$emailOrUsername' OR username = '$emailOrUsername'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            // Set user details in the session
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_fullname"] = $user["full_name"];
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["admin"] = ($user["admin"] == 1) ? 1 : 0;
            $_SESSION["user_profile_picture"] = $user["profile_picture"];
        
            // "Remember Me" functionality
            if ($rememberMe) {
                $rememberToken = bin2hex(random_bytes(32));
                $expirationTime = time() + (86400 * 30); // 30 days in seconds
        
                // Update the database with the remember token and expiration time
                $updateSql = "UPDATE users SET remember_token = '$rememberToken', remember_expires = '$expirationTime' WHERE id = " . $user["id"];
                mysqli_query($conn, $updateSql);
        
                // Set the cookies with the remember token and email/username
                setcookie('remember_token', $rememberToken, $expirationTime, '/', null, null, true);
                setcookie('remember_email_or_username', $emailOrUsername, $expirationTime, '/', null, null, true);
            }
        
            // Redirect to the user's homepage
            header("Location: /planetart/pages/users/indexuser.php");
            exit();
        } else {
            $errorMessage = "Invalid login details. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Login </title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap">
    <link rel="stylesheet" href="/planetart/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
    <script src="/planetart/script/script.js"></script>
</head>

<body>

    
    <!-- Header Container -->
    <header>
    <nav class="navbar">
        <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
        <span class="site-name">Planet Art</span>

        <div>
            <a class="btn btn-outline-secondary nav-links" href="index.php">
                Home
            </a>
            <a class="btn btn-outline-secondary nav-links" href="artists.php">
                Artists
            </a>
            <a class="btn btn-outline-secondary nav-links" href="?redirect=discussionsuser">
                Discussions
            </a>
        </div>

        <ul class="navbar-nav ml-lg-auto">
            <div class="ml-lg-4">
            </div>
        </ul>
    </nav>

</header>

<div class="container-fluid" style="position: relative; height: 100vh;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/planetart/images/mark-ps.png'); background-size: 100% auto; background-position: center; background-repeat: no-repeat; opacity: 1; z-index: -1;"></div>
    <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);">
                    <div class="card-body">
                    <form action="login.php" method="post">
                        <?php if (isset($errorMessage)) : ?>
                            <div id="errorMessage" class="alert alert-danger" role="alert">
                                <?php echo $errorMessage; ?>
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('errorMessage').style.display = 'none';
                                }, 7000);
                            </script>
                        <?php endif; ?>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">                                
                                    <label for="email_or_username" class="form-label">Email or Username <span class="required-asterisk">*</span></label>
                                    <input type="text" class="form-control" id="email_or_username" name="email_or_username">
                                </div>

                                <div class="col-md-6 mb-3">                                
                                    <label for="password" class="form-label">Password <span class="required-asterisk">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Remember Me</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-outline-primary login w-100" name="login">Login</button>
                            </div>

                            <div class="text-center mt-3">
                                <p>Don't have an account? <a href="signup.php" class="signup-text">Signup</a></p>
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
        </div>
    </footer>

    <!-- Bootstrap js and popper.js -->
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