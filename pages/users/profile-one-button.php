<?php
session_start();
require_once "../../config.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Get the current user's information
$currentUser = $_SESSION["user"];
$sql = "SELECT *, IFNULL(profile_picture, 'default-user.png') AS profile_picture FROM users WHERE username = '$currentUser'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$successMessages = array();
$errorMessages = array();

if (isset($_POST["updateProfile"])) {
    $email = $_POST["email"];
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $profilePicture = $_POST["profilePicture"];

    // Update email
    if (!empty($email) && $email !== $user['email']) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessages[] = "Email is invalid";
        } else {
            // Check if the email already exists in the database
            $checkQuery = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $errorMessages[] = "Email already exists. Please choose a different email.";
            } else {
                // Update the email if it doesn't exist
                $updateQuery = "UPDATE users SET email = ? WHERE username = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmt, "ss", $email, $currentUser);
                mysqli_stmt_execute($stmt);
                $successMessages[] = "Email successfully updated.";
            }
        }
    } elseif (empty($email)) {
        $errorMessages[] = "Email cannot be empty";
    }

    // Update full name
    if (!empty($fullname) && $fullname !== $user['full_name']) {
        $updateQuery = "UPDATE users SET full_name = ? WHERE username = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ss", $fullname, $currentUser);
        mysqli_stmt_execute($stmt);
        $successMessages[] = "Full name successfully updated.";
    } elseif (empty($fullname)) {
        $errorMessages[] = "Full name cannot be empty";
    }

    // Update username
    if (!empty($username) && $username !== $user['username']) {
        if (strlen($username) < 3) {
            $errorMessages[] = "Username should be at least 3 characters long";
        } elseif (strlen($username) > 10) {
            $errorMessages[] = "Username should not be longer than 10 characters";
        } else {
            // Check if the username already exists in the database
            $checkQuery = "SELECT * FROM users WHERE username = ?";
            $stmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $errorMessages[] = "Username already exists. Please choose a different username.";
            } else {
                // Update the username if it doesn't exist
                $updateQuery = "UPDATE users SET username = ? WHERE username = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmt, "ss", $username, $currentUser);
                mysqli_stmt_execute($stmt);
                $_SESSION["user"] = $username;
                $successMessages[] = "Username successfully updated.";
            }
        }
    } elseif (empty($username)) {
        $errorMessages[] = "Username cannot be empty";
    }

    // Update password
    if (!empty($password)) {
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errorMessages[] = "Password must be at least 8 characters long and contain both letters and numbers";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $currentUser);
            mysqli_stmt_execute($stmt);
            $successMessages[] = "Password successfully updated.";
        }
    }

    // Update profile picture
    if (!empty($profilePicture) && $profilePicture !== $user['profile_picture']) {
        $updateQuery = "UPDATE users SET profile_picture = ? WHERE username = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ss", $profilePicture, $currentUser);
        mysqli_stmt_execute($stmt);
        $successMessages[] = "Profile picture successfully updated.";
    }

    if (empty($errorMessages)) {
        header("Location: profile.php");
        exit();
    }
}

if (isset($_POST["deleteAccount"])) {
    // Delete the user's account from the database
    $deleteQuery = "DELETE FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "s", $currentUser);
    mysqli_stmt_execute($stmt);

    // Destroy the session and redirect to the signup page
    session_destroy();
    header("Location: /planetart/pages/non-users/signup.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Profile</title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
</head>
<body>
    <header>
        <nav class="navbar">
            <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
            <span class="site-name">Planet Art</span>

            <div>
                <a class="btn btn-outline-secondary nav-links" href="indexuser.php">Home</a>
                <a class="btn btn-outline-secondary nav-links" href="artistsuser.php">Artists</a>
                <a class="btn btn-outline-secondary nav-links" href="discussionsuser.php">Discussions</a>
            </div>

            <div class="ml-auto">
                <?php if (isset($_SESSION['user_fullname'])) : ?>
                    <p class="welcome-message">Welcome, <?php echo $_SESSION['user_fullname']; ?>!</p>
                <?php endif; ?>

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
        if (isset($_GET['logout']) && $_GET['logout'] == 1) {
            session_start();
            session_destroy();
            header("Location: /planetart/pages/non-users/login.php");
            exit();
        }
        ?>
    </header>

    <div class="container-fluid" style="position: relative; min-height: 100vh;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/planetart/images/mark-ps.png'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 1; z-index: -1;"></div>
        <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 100vh; padding-top: 50px; padding-bottom: 50px;">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Update Profile</h2>

                                <?php foreach ($successMessages as $message) : ?>
                                    <div class="alert alert-success"><?php echo $message; ?></div>
                                <?php endforeach; ?>

                                <?php foreach ($errorMessages as $error) : ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endforeach; ?>

                                <form action="profile.php" method="post" onsubmit="return confirmAccountDeletion(event)">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $user['full_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <div class="form-text">Leave blank to keep the current password.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="profilePicture" class="form-label">Choose a Profile Picture</label>
                                        <div class="mb-3">
                                            <label class="me-3">
                                                <input type="radio" name="profilePicture" value="default-user.png" <?php if ($user['profile_picture'] == 'default-user.png') echo 'checked'; ?>>
                                                <img src="/planetart/images/default-user.png" alt="Default User" width="35" height="35">
                                            </label>
                                            <label class="me-3">
                                                <input type="radio" name="profilePicture" value="profile-user-pink.png" <?php if ($user['profile_picture'] == 'profile-user-pink.png') echo 'checked'; ?>>
                                                <img src="/planetart/images/profile-user-pink.png" alt="Pink User" width="35" height="35">
                                            </label>
                                            <label class="me-3">
                                                <input type="radio" name="profilePicture" value="profile-user-green.png" <?php if ($user['profile_picture'] == 'profile-user-green.png') echo 'checked'; ?>>
                                                <img src="/planetart/images/profile-user-green.png" alt="Green User" width="35" height="35">
                                            </label>
                                            <label class="me-3">
                                                <input type="radio" name="profilePicture" value="profile-user-blue.png" <?php if ($user['profile_picture'] == 'profile-user-blue.png') echo 'checked'; ?>>
                                                <img src="/planetart/images/profile-user-blue.png" alt="Blue User" width="35" height="35">
                                            </label>
                                            <label class="me-3">
                                                <input type="radio" name="profilePicture" value="profile-user-black.png" <?php if ($user['profile_picture'] == 'profile-user-black.png') echo 'checked'; ?>>
                                                <img src="/planetart/images/profile-user-black.png" alt="Black User" width="35" height="35">
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="updateProfile" class="btn btn-outline-primary login">Update</button>
                                </form>

                                <div class="mb-3">
                                    <h3>Delete Account</h3>
                                    <p>Deleting your account is permanent and cannot be undone.</p>
                                    <form action="profile.php" method="post" onsubmit="return confirmAccountDeletion(event)">
                                        <button type="submit" name="deleteAccount" class="btn btn-danger">Delete Account</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <div class="footer p-3">
            &copy; 2024
            <a>Planet Art</a>
        </div>
    </footer>

    <script>
        function confirmAccountDeletion(event) {
            if (event.submitter.name === "deleteAccount") {
                return confirm("Are you sure you want to delete your account? This action cannot be undone.");
            }
            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>