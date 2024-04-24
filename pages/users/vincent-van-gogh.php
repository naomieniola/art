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
    <title>Planet Art | Discussions</title>
    <link rel="stylesheet" href="/planetart/css/styles.css">
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
    <div class="full-width-container-van-gogh"></div>
    <div class="container explore-desc">
        <div class="row">
            <div class="col-12">
                <p class="artists-main-text">Vincent Van Gogh</p>
                <p class="artists-sub-text">Mar 30, 1853 - Jul 29, 1890</p>
                <p class="vincent-desc">Vincent van Gogh (1853-1890) was a Dutch post-impressionist painter who is widely regarded as one of the most influential figures in the history of Western art. He was born in Zundert, Netherlands, and struggled with mental health issues throughout his life. Despite facing numerous challenges, Van Gogh created a vast body of work that included over 2,000 paintings, drawings, and sketches.
                <br> <br>
                Van Gogh's art is characterized by bold colors, expressive brushwork, and a unique approach to capturing the emotional and spiritual aspects of his subjects. Some of his most famous works include "Starry Night," "Sunflowers," "Irises," and "The Bedroom."
                <br> <br>
                Tragically, Van Gogh's mental health deteriorated, and he ultimately died by suicide at the age of 37. Despite his struggles during his lifetime, his work gained recognition and appreciation posthumously, and today he is considered a master of modern art.
                </p>
            </div>
        </div>
    </div>

    <div class="col-12">
        <p class="artist-discover">Discover this Artist</p>
    </div>

    <div class="container mt-5">
        <!-- First Row -->
        <div class="row artist-row">
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/starry-night.jpg" data-image-title="Starry Night" data-image-description="The Starry Night is an oil-on-canvas painting by the Dutch Post-Impressionist painter Vincent van Gogh. Painted in June 1889, it depicts the view from the east-facing window of his asylum room at Saint-Rémy-de-Provence, just before sunrise, with the addition of an imaginary village.">
                        <img src="/planetart/images/starry-night.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/restaurant-van-gogh.jpg" data-image-title="Cafe Terrace at Night" data-image-description="Cafe Terrace at Night, also known as The Cafe Terrace on the Place du Forum, is an oil painting executed by the Dutch artist Vincent van Gogh in Arles, France, in September 1888. The painting is not signed, but described and mentioned by the artist in three letters.">
                        <img src="/planetart/images/restaurant-van-gogh.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/sunflowers.jpg" data-image-title="Sunflowers" data-image-description="The Sunflowers is a series of still life paintings by the Dutch painter Vincent van Gogh. The series depicts various bunches of sunflowers in a vase, with variations in the number of flowers, their arrangement, and the color scheme.">
                        <img src="/planetart/images/sunflowers.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/wheatfield.jpg" data-image-title="Wheatfield with Crows" data-image-description="Wheatfield with Crows is an oil on canvas painting by Dutch artist Vincent van Gogh, painted in 1890. It is believed to be one of his last works, painted shortly before his death. The painting depicts a dramatic, cloudy sky filled with crows over a wheat field.">
                        <img src="/planetart/images/wheatfield.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/butterflies-and-poppies.jpg" data-image-title="Butterflies and Poppies" data-image-description="Butterflies and Poppies is an oil painting by Vincent van Gogh. The painting features a lively and colorful composition of butterflies fluttering among vibrant red poppies. It showcases Van Gogh's fascination with nature and his expressive use of color.">
                        <img src="/planetart/images/butterflies-and-poppies.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
        </div>
        <!-- Second Row -->
        <div class="row artist-row">
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/gate-paris.jpg" data-image-title="The Gate of Paris" data-image-description="The Gate of Paris is an oil painting by Vincent van Gogh, painted in 1887. It depicts the entrance to the city of Paris, known as the Porte de la Villette. The painting showcases Van Gogh's distinctive style with bold brushstrokes and vibrant colors.">
                        <img src="/planetart/images/gate-paris.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/pollard-willow.jpg" data-image-title="Pollard Willow" data-image-description="Pollard Willow is an oil painting by Vincent van Gogh, created in 1889. The painting depicts a twisted and gnarled willow tree against a vivid blue sky. It exemplifies Van Gogh's expressive use of color and his fascination with the beauty of nature.">
                        <img src="/planetart/images/pollard-willow.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/the-sower.jpg" data-image-title="The Sower" data-image-description="The Sower is a painting by Vincent van Gogh, which he created in 1888. It depicts a sower in a field at sunset, with a large sun dominating the background. The painting is known for its vibrant colors and expressive brushwork, reflecting Van Gogh's emotional state and his connection to the agricultural landscape.">
                        <img src="/planetart/images/the-sower.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/yellow-house.jpg" data-image-title="The Yellow House" data-image-description="The Yellow House is an oil painting by Vincent van Gogh, created in 1888. It depicts the house he rented in Arles, France, which became known as the Yellow House. Van Gogh had hopes of establishing an artists' community there. The painting is notable for its bright, cheerful colors and the distinctive style that would come to define Van Gogh's work.">
                        <img src="/planetart/images/yellow-house.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/the-bedroom.jpg" data-image-title="The Bedroom" data-image-description="The Bedroom is a series of three similar paintings by Dutch artist Vincent van Gogh. The paintings depict Van Gogh's bedroom in Arles, France, with simple furniture and vivid colors. The works are notable for their sense of tranquility and the artist's experimentation with perspective and color.">
                        <img src="/planetart/images/the-bedroom.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
        </div>

        <!-- Third Row -->
        <div class="row artist-row">
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/almond-blossom.jpg" data-image-title="Almond Blossom" data-image-description="Almond Blossom is a group of several paintings made in 1888 and 1890 by Vincent van Gogh in Arles and Saint-Rémy, southern France of blossoming almond trees. The works reflect the influence of Japanese woodblock prints and Van Gogh's fascination with the beauty of nature.">
                        <img src="/planetart/images/almond-blossom.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/potatoe-eaters.jpg" data-image-title="The Potato Eaters" data-image-description="The Potato Eaters is an oil painting by Dutch artist Vincent van Gogh painted in April 1885 in Nuenen, Netherlands. It is considered one of Van Gogh's earliest masterpieces and depicts a group of poor peasants eating potatoes in a dark and gloomy setting, illuminated by a single lamp.">
                        <img src="/planetart/images/potatoe-eaters.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/wheatfield-reaper.jpg" data-image-title="Wheatfield with Reaper" data-image-description="Wheatfield with Reaper is an oil painting by Dutch artist Vincent van Gogh, painted in 1889. The painting depicts a reaper working in a field of wheat, with a vibrant blue sky and golden wheat dominating the composition. It showcases Van Gogh's distinctive style and his fascination with the beauty of the natural world.">
                        <img src="/planetart/images/wheatfield-reaper.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/wheatfield-thunderclouds.jpg" data-image-title="Wheatfield Under Thunderclouds" data-image-description="Wheatfield Under Thunderclouds is an oil painting by Dutch artist Vincent van Gogh, painted in 1890. The painting depicts a wheatfield under a turbulent sky with dark thunderclouds. It is considered one of Van Gogh's final works, completed shortly before his death, and reflects his emotional state during that time.">
                        <img src="/planetart/images/wheatfield-thunderclouds.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="/planetart/images/vincent-sunflowers.jpg" data-image-title="Sunflowers" data-image-description="Sunflowers is a series of still life oil paintings by Dutch painter Vincent van Gogh. The series depicts sunflowers in various stages of life, from full bloom to wilting. Van Gogh painted the series in Arles, France, in 1888 and 1889, and the paintings are now displayed in museums around the world.">
                        <img src="/planetart/images/vincent-sunflowers.jpg" class="img-fluid" style="width: 100%; height: 200px">
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <img src="/planetart/images/close.png" alt="Close" class="close-icon btn-close close-button" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <img id="modalImage" src="" class="img-fluid" alt="Modal Image">
                        <p id="modalDescription" class="mt-3"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-12">
            <p class="artists-main-quote">“I put my heart and my soul into my work, and have lost my mind in the process.”</p>
            <p class="artists-sub-quote">Vincent van Gogh</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            var imageModal = document.getElementById('imageModal');
            var modalImage = document.getElementById('modalImage');
            var modalTitle = document.getElementById('imageModalLabel');
            var modalDescription = document.getElementById('modalDescription');

            imageModal.addEventListener('show.bs.modal', function (event) {
                var link = event.relatedTarget;
                var imageSrc = link.getAttribute('data-image-src');
                var imageTitle = link.getAttribute('data-image-title');
                var imageDescription = link.getAttribute('data-image-description');

                modalImage.src = imageSrc;
                modalTitle.textContent = imageTitle;
                modalDescription.textContent = imageDescription;
            });
        });
    </script>
</body>
</html>