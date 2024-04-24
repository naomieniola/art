<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: indexuser.php");
    exit();
}

if (isset($_GET['redirect']) && $_GET['redirect'] === 'discussionsuser') {
    $errorMessage = "You need to be logged in to access the Discussions page.";
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
                        <button tyype="button" class="btn btn-outline-primary login">
                            <span class="default-icon">
                                <img src="/planetart/images/user.png" alt="User Icon" class="user-icon">
                            </span>
                            <span class="hover-icon">
                                <a href="login.php">
                                    <img src="/planetart/images/userhover.png" alt="Another Icon" class="user-icon">
                                </a>
                                </span>
                            Login
                        </button>
                    </div>
                    </div>
                </ul>
    </nav>

</header>

     <!-- Main Content Container -->
     <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form action="signup.php" method="post">
                <?php
                require_once "../../config.php"; 
                
                if (isset($_POST["submit"])) {
                    $email =  $_POST["email"];
                    $fullname =  $_POST["fullname"];
                    $username =  $_POST["username"];
                    $password =  $_POST["password"];

                    //password encryption
                    $passwordhash = password_hash($password, PASSWORD_DEFAULT);
                    
                    
                    $errors = array();

                    if (empty($email) OR empty($fullname) OR empty($username) OR empty($password)) {
                        array_push($errors, "All fields are required");
                    }

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        array_push($errors, "Email invalid");
                    }

                    if (strlen($username) < 3 || strlen($username) > 15) {
                        array_push($errors, "Username should be between 3 and 15 characters long");
                    }
                    
                    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                        array_push($errors, "Password must meet the following criteria:<br>- At least 8 characters long<br>- Contain both letters and numbers<br>- Contain at least one special character");
                    }
                    
                    $sql = "SELECT * FROM users WHERE email = '$email'";
                    $result = mysqli_query($conn, $sql);
                    $rowCount = mysqli_num_rows($result);

                    if ($rowCount > 0) {
                      array_push($errors, "Email already exists!");
                    }

                    $sqlusername = "SELECT * FROM users WHERE username = '$username'";
                    $result = mysqli_query($conn, $sqlusername);
                    $rowCount = mysqli_num_rows($result);

                    if ($rowCount > 0) {
                      array_push($errors, "Username already exists!");
                    }
                    

                    if (count($errors) == 0) {
                        //insert data into db
                        require_once "../../config.php";
                        $sql = "INSERT INTO users (email, full_name, username, password) VALUES ( ?, ?, ?, ? )";
                        $stmt = mysqli_stmt_init($conn);
                        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                        
                         // After successful registration, set the user's fullname in the session
                            // After successful registration
                            if ($prepareStmt) {
                                mysqli_stmt_bind_param($stmt, "ssss", $email, $fullname, $username, $passwordhash);
                                mysqli_stmt_execute($stmt);

                                // Set user details in the session
                                $_SESSION["user"] = $username;
                                $_SESSION["user_fullname"] = $fullname;
                                $_SESSION["user_id"] = mysqli_insert_id($conn);
                                
                                // Redirect to the user's homepage
                                header("Location: /planetart/pages/users/indexuser.php");
                                exit();
                            } else {
                                die("Something went wrong.");
                            }
                        }
                    }

                    /*if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt, "ssss", $email, $fullname, $username, $passwordhash);
                        mysqli_stmt_execute($stmt);

                        // Set user fullname in the session
                        $_SESSION['user_fullname'] = $fullname;

                        echo '<div class="container">';
                        echo '<div class="row justify-content-center">';
                        echo '<div class="col-12 col-md-4">';
                        echo "<div class='alert alert-success'>You are registered successfully.</div>";
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        die("Something went wrong.");
                    } */
                ?> 

                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid" style="position: relative; height: 100vh;">
  <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/planetart/images/mark-ps.png'); background-size: 100% auto; background-position: center; background-repeat: no-repeat; opacity: 1; z-index: -1;"></div>
  <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);">
                <div class="card-body">
                            <form action="signup.php" method="post">
                                <?php
                                if (isset($errors) && count($errors) > 0) {
                                    echo '<div class="alert alert-danger">';
                                    foreach ($errors as $error) {
                                        echo '<p>' . $error . '</p>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-outline-primary login w-100" name="submit">Sign-up</button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <p>Already have an account? <a href="login.php" class="signup-text">Login</a></p>
                                    </div>
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
        <!-- Copyright -->
        <div class="footer p-3">
            © 2024
            <a>Planet Art</a>
        </div>
        <!-- Copyright -->
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