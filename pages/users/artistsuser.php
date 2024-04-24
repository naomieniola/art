<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

require_once "../../config.php";

if (isset($_POST['action']) && $_POST['action'] === 'like_item') {
    if (isset($_POST['itemId']) && isset($_POST['itemName']) && isset($_POST['imageUrl'])) {
        $itemId = $_POST['itemId'];
        $itemName = $_POST['itemName'];
        $imageUrl = $_POST['imageUrl'];
        $userId = $_SESSION['user_id'];

        // Check if the item is already liked by the user
        $stmt = $conn->prepare("SELECT COUNT(*) FROM liked_items WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Item is already liked, remove it from the table
            $stmt = $conn->prepare("DELETE FROM liked_items WHERE user_id = ? AND item_id = ?");
            $stmt->bind_param("ii", $userId, $itemId);
            $stmt->execute();
            $stmt->close();
            echo 'unliked';
        } else {
            // Item is not liked, insert it into the table
            $stmt = $conn->prepare("INSERT INTO liked_items (user_id, item_id, item_name, image_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $userId, $itemId, $itemName, $imageUrl);
            $stmt->execute();
            $stmt->close();
            echo 'liked';
        }
    }
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

// Retrieve the liked items for the current user
$stmt = $conn->prepare("SELECT item_id FROM liked_items WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$likedItems = [];
while ($row = $result->fetch_assoc()) {
    $likedItems[] = $row['item_id'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | Artists </title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
</head>

<body>
<div id="success-message" class="alert alert-success d-none" role="alert"></div>

    <!-- Header Container -->
    <header>
        <nav class="navbar">
            <img src="/planetart/images/planetartlogo.png" alt="planet art logo" width="45" height="30" class="logo-img">
            <span class="site-name">Planet Art</span>

            <div>
                <a class="btn btn-outline-secondary nav-links" href="indexuser.php">
                    Home
                </a>
                <a class="btn btn-outline-secondary nav-links current-page" href="artistsuser.php">
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
            // Clear the session and cookies
       // Clear the session and cookies
            session_unset();
            session_destroy();
            setcookie('remember_token', '', time() - 3600, '/', null, null, true);
            setcookie('remember_email_or_username', '', time() - 3600, '/', null, null, true);

            // Redirect to the login page
            header("Location: /planetart/pages/non-users/login.php");
            exit();
        }
        ?>
    </header>

    <!-- Main Content Container -->
    <div class="background-colour">
        <div class="container explore-desc">
                <div class="row">
                    <div class="col-12">
                        <p class="explore-main-text">Artists</p>
                        <p class="explore-sub-text"></p>
                    </div>
                </div>
            </div>
        
        <div class="container mt-5">
            <!-- First Row -->
            <div class="row">
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="vincent-van-gogh.php">
                            <img src="/planetart/images/butterflies-and-poppies.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Vincent Van Gogh</p>
                            <button class="like-button<?php echo in_array(3, $likedItems) ? ' liked' : ''; ?>" data-id="3" data-name="Vincent Van Gogh">
                                <img src="<?php echo in_array(3, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="volker-hermes.php">
                            <img src="/planetart/images/volker.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Volker Hermes</p>
                            <button class="like-button<?php echo in_array(4, $likedItems) ? ' liked' : ''; ?>" data-id="4" data-name="Volker Hermes">
                                <img src="<?php echo in_array(4, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/tylor1.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Tylor Hurd</p>
                            <button class="like-button<?php echo in_array(5, $likedItems) ? ' liked' : ''; ?>" data-id="5" data-name="Tylor Hurd">
                                <img src="<?php echo in_array(5, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/mark.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Mark Ryden</p>
                            <button class="like-button<?php echo in_array(6, $likedItems) ? ' liked' : ''; ?>" data-id="6" data-name="Leonardo da Vinci">
                                <img src="<?php echo in_array(6, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Second Row -->
            <div class="row mt-3">
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/chasbg.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Chas</p>
                            <button class="like-button<?php echo in_array(8, $likedItems) ? ' liked' : ''; ?>" data-id="8" data-name="Pablo Picasso">
                                <img src="<?php echo in_array(8, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/lisbeth.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Lisbeth</p>
                            <button class="like-button<?php echo in_array(9, $likedItems) ? ' liked' : ''; ?>" data-id="9" data-name="Lisbeth">
                                <img src="<?php echo in_array(9, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/landon.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Landon Khiry</p>
                            <button class="like-button<?php echo in_array(10, $likedItems) ? ' liked' : ''; ?>" data-id="10" data-name="Rembrandt">
                                <img src="<?php echo in_array(10, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/schatzi.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Schatzi</p>
                            <button class="like-button<?php echo in_array(11, $likedItems) ? ' liked' : ''; ?>" data-id="11" data-name="Salvador Dali">
                                <img src="<?php echo in_array(11, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Third Row -->
            <div class="row mt-3">
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/soulo.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Soulo</p>
                            <button class="like-button<?php echo in_array(13, $likedItems) ? ' liked' : ''; ?>" data-id="13" data-name="Johannes Vermeer">
                                <img src="<?php echo in_array(13, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/isaiah.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Isaiah</p>
                            <button class="like-button<?php echo in_array(14, $likedItems) ? ' liked' : ''; ?>" data-id="14" data-name="Caravaggio">
                                <img src="<?php echo in_array(14, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/helen-katherine.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Helen Katherine</p>
                            <button class="like-button<?php echo in_array(15, $likedItems) ? ' liked' : ''; ?>" data-id="15" data-name="Johannes Vermeer">
                                <img src="<?php echo in_array(15, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col medium-column">
                    <div class="position-relative">
                        <a href="#">
                            <img src="/planetart/images/gabe-weis.jpg" class="img-fluid" style="width: 100%; height: 222px">
                        </a>
                        <div class="artist-info">
                            <p class="artist-name">Gabe Weis</p>
                            <button class="like-button<?php echo in_array(16, $likedItems) ? ' liked' : ''; ?>" data-id="16" data-name="Michelangelo">
                                <img src="<?php echo in_array(16, $likedItems) ? '/planetart/images/like.png' : '/planetart/images/unlike.png'; ?>" alt="Like">
                            </button>
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

    <script>
    $('.like-button').on('click', function() {
    var itemId = $(this).data('id');
    var itemName = $(this).data('name');
    var imageUrl = $(this).closest('.artist-name, .position-relative').find('img').attr('src');
    var likeButton = $(this);

    $.ajax({
        url: '',
        method: 'POST',
        data: { itemId: itemId, itemName: itemName, imageUrl: imageUrl, action: 'like_item' },
        success: function(response) {
            if (response === 'liked') {
                likeButton.addClass('liked').find('img').attr('src', '/planetart/images/like.png');
                // Show success message with styled link
                var successMessage = 'Artist liked successfully! Click <a href="likes.php" class="success-message-link">here</a> to view your likes';
                $('#success-message').html(successMessage).removeClass('d-none');
                // Hide success message after 6 seconds
                setTimeout(function() {
                    $('#success-message').addClass('d-none');
                }, 6000);
            } else if (response === 'unliked') {
                likeButton.removeClass('liked').find('img').attr('src', '/planetart/images/unlike.png');
                // Show success message
                var successMessage = 'Artist unliked successfully!';
                $('#success-message').text(successMessage).removeClass('d-none');
                // Hide success message after 6 seconds
                setTimeout(function() {
                    $('#success-message').addClass('d-none');
                }, 6000);
            }
        }
    });
});
</script>

</body>
</html>