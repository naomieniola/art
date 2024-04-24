<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: indexuser.php");
    exit();

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
                    $email = $_POST["email"];
                    $fullname = $_POST["fullname"];
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    $dateofbirthDay = $_POST["dateofbirth_day"];
                    $dateofbirthMonth = $_POST["dateofbirth_month"];
                    $dateofbirthYear = $_POST["dateofbirth_year"];

                    $passwordhash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $errors = array();

                    if (empty($email) || empty($fullname) || empty($username) || empty($password)) {
                        array_push($errors, "All fields are required");
                    }

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        array_push($errors, "Email is invalid");
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

                  // Validate date of birth fields
                    if (empty($dateofbirthDay) || empty($dateofbirthMonth) || empty($dateofbirthYear)) {
                        array_push($errors, "Date of birth cannot be blank.");
                    } else {
                        // Validate day
                        if (!is_numeric($dateofbirthDay) || $dateofbirthDay < 1 || $dateofbirthDay > 31) {
                            array_push($errors, "Please enter a valid day for the date of birth.");
                        }

                        // Validate month
                        if (!is_numeric($dateofbirthMonth) || $dateofbirthMonth < 1 || $dateofbirthMonth > 12) {
                            array_push($errors, "Please enter a valid month for the date of birth.");
                        }

                        // Validate year
                        if (!is_numeric($dateofbirthYear) || $dateofbirthYear < 1900 || $dateofbirthYear > date("Y")) {
                            array_push($errors, "Please enter a valid year for the date of birth.");
                        }

                        // Check if the entered date is a valid date
                        if (!checkdate($dateofbirthMonth, $dateofbirthDay, $dateofbirthYear)) {
                            array_push($errors, "Please enter a valid date of birth.");
                        }

                        // Check if the user is at least 12 years old
                        $currentDate = new DateTime();
                        $dateofbirth = new DateTime("$dateofbirthYear-$dateofbirthMonth-$dateofbirthDay");
                        $age = $currentDate->diff($dateofbirth)->y;

                        if ($age < 12) {
                            array_push($errors, "You must be at least 12 years old to sign up.");
                        }
                    }

                    if (count($errors) == 0) {
                        $dateofbirth = $dateofbirthYear . '-' . $dateofbirthMonth . '-' . $dateofbirthDay;
                    
                        $sql = "INSERT INTO users (email, full_name, username, password, date_of_birth, profile_picture) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_stmt_init($conn);
                        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    
                        if ($prepareStmt) {
                            $defaultProfilePicture = 'default-user.png';
                            mysqli_stmt_bind_param($stmt, "ssssss", $email, $fullname, $username, $passwordhash, $dateofbirth, $defaultProfilePicture);
                            mysqli_stmt_execute($stmt);
                    
                            $_SESSION["user"] = $username;
                            $_SESSION["user_fullname"] = $fullname;
                            $_SESSION["user_id"] = mysqli_insert_id($conn);
                    
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
                                                <label for="email" class="form-label">Email <span class="required-asterisk">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="fullname" class="form-label">First Name<span class="required-asterisk">*</span></label>
                                                    <input type="text" class="form-control" id="fullname" name="fullname">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="username" class="form-label">Username <span class="required-asterisk">*</span></label>
                                                    <input type="text" class="form-control" id="username" name="username">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Password <span class="required-asterisk">*</span></label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label for="dateofbirth" class="form-label">Date of Birth <span class="required-asterisk">*</span></label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="dateofbirth_day" name="dateofbirth_day" placeholder="DD" min="1" max="31" required>
                                                        <select class="form-select" id="dateofbirth_month" name="dateofbirth_month" required>
                                                            <option value="">MMM</option>
                                                            <option value="1">Jan</option>
                                                            <option value="2">Feb</option>
                                                            <option value="3">Mar</option>
                                                            <option value="4">Apr</option>
                                                            <option value="5">May</option>
                                                            <option value="6">Jun</option>
                                                            <option value="7">Jul</option>
                                                            <option value="8">Aug</option>
                                                            <option value="9">Sep</option>
                                                            <option value="10">Oct</option>
                                                            <option value="11">Nov</option>
                                                            <option value="12">Dec</option>
                                                        </select>
                                                        <input type="number" class="form-control" id="dateofbirth_year" name="dateofbirth_year" placeholder="YYYY" min="1900" max="2100" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <p>By signing up, you agree to our <a href="privacy-policy.php" target="_blank" class="privacy-policy-link">Privacy Policy</a>. Please make sure to read it before proceeding.</p>
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
            <div class="footer p-3">
                © 2024
                <a>Planet Art</a>
                <a href="privacy-policy.php" class="privacy-policy-link">Privacy Policy</a>
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