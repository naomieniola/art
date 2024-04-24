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

require_once "../../config.php";
// Retrieve liked items from the database
$stmt = $conn->prepare("SELECT item_id, item_name, image_url FROM liked_items WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$likedItems = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Art | My Likes </title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="icon" href="/planetart/images/favicon_io/favicon-32x32.png" type="image/x-icon">
</head>

<body>
    

    <!-- Header Container -->
    <div id="success-message" class="alert alert-success d-none" role="alert"></div>

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
            // Logout actions
            session_start(); // Ensure session is started
            session_destroy();
            
            // Redirect the user to the login page when they logout
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
                    <p class="explore-main-text">My Likes</p>
                    <p class="explore-sub-text"></p>
                </div>
            </div>
        </div>
            
        <div class="container">
            <div class="row">
                <?php $counter = 0; ?>
                    <?php foreach ($likedItems as $item): ?>
                        <div class="col-md-3 mb-4">
                            <div class="artist-info-likes">
                                <div class="artist-name">
                                    <a href="#">
                                        <img src="<?php echo $item['image_url']; ?>" class="img-fluid" style="width: 100%; height: 222px">
                                    </a>
                                    <p class=""><?php echo $item['item_name']; ?></p>
                                    <button class="like-button liked" data-id="<?php echo $item['item_id']; ?>" data-name="<?php echo $item['item_name']; ?>">
                                        <img src="/planetart/images/like.png" alt="Like">
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php $counter++; ?>
                <?php if ($counter % 4 === 0): ?>
            </div>
            <div class="row">
                <?php endif; ?>
            <?php endforeach; ?>
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
            $(document).ready(function() {
                $(document).on('click', '.like-button', function() {
                    var itemId = $(this).data('id');
                    var itemName = $(this).data('name');
                    var imageUrl = $(this).closest('.artist-name').find('img').attr('src');

                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: { itemId: itemId, itemName: itemName, imageUrl: imageUrl, action: 'like_item' },
                        context: this,
                        success: function(response) {
                            if (response === 'liked') {
                                $(this).addClass('liked').text('Unlike');
                                // Show success message
                                var successMessage = 'Artist liked successfully!';
                                $('#success-message').text(successMessage).removeClass('d-none');
                                // Hide success message after 3 seconds
                                setTimeout(function() {
                                    $('#success-message').addClass('d-none');
                                }, 3000);
                            } else if (response === 'unliked') {
                                // Remove the liked item from the page
                                $(this).closest('.col-md-3').remove();
                                // Show success message
                                var successMessage = 'Artist unliked successfully!';
                                $('#success-message').text(successMessage).removeClass('d-none');
                                // Hide success message after 3 seconds
                                setTimeout(function() {
                                    $('#success-message').addClass('d-none');
                                }, 3000);
                            }
                        }
                    });
                });
            });
    </script>
</body>
</html>