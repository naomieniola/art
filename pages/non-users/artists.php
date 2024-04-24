<?php
require_once "../../config.php";

session_start();

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
            <a class="btn btn-outline-secondary nav-links current-page" href="artists.php">
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
    <div class="background-colour">
        <div class="container explore-desc">
            <div class="row">
                <div class="col-12">
                    <p class="explore-main-text">Artists</p>
                    <p class="explore-sub-text"></p>
                </div>
            </div>
        </div>
        <!-- Search Buttons -->
        
        <div class="container mt-5">
        <!-- First Row -->
        <div class="row mt-3">
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="vincent-van-gogh.php">
                        <img src="/planetart/images/starry-night.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <div class="artist-info">
                        <p class="artist-name">Vincent Van Gogh</p>
                        <button class="like-button" data-id="3" data-name="Vincent Van Gogh">
                            <img src="/planetart/images/unlike.png" alt="Like">
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
                        <button class="like-button" data-id="4" data-name="Volker Hermes">
                            <img src="/planetart/images/unlike.png" alt="Like">
                        </button>
                    </div>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href="#.php">
                        <img src="/planetart/images/tylor1.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <div class="artist-info">
                        <p class="artist-name">Tylor Hurd</p>
                        <button class="like-button" data-id="5" data-name="Banksy">
                            <img src="/planetart/images/unlike.png" alt="Like">
                        </button>
                    </div>
                </div>
            </div>
            <div class="col medium-column">
                <div class="position-relative">
                    <a href=".php">
                        <img src="/planetart/images/mark.jpg" class="img-fluid" style="width: 100%; height: 222px">
                    </a>
                    <div class="artist-info">
                        <p class="artist-name">Mark Ryden</p>
                        <button class="like-button" data-id="6" data-name="Leonardo da Vinci">
                            <img src="/planetart/images/unlike.png" alt="Like">
                        </button>
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
                                <button class="like-button" data-id="7" data-name="Michelangelo">
                                    <img src="/planetart/images/unlike.png" alt="Like">
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
                                <button class="like-button" data-id="9" data-name="Raphael">
                                    <img src="/planetart/images/unlike.png" alt="Like">
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
                                <button class="like-button" data-id="10" data-name="Rembrandt">
                                    <img src="/planetart/images/unlike.png" alt="Like">
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
                                <button class="like-button" data-id="8" data-name="Pablo Picasso">
                                    <img src="/planetart/images/unlike.png" alt="Like">
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
                                    <img src="/planetart/images/helen-katherine.jpg" class="img-fluid" style="width: 100%; height: 222px">
                                </a>
                                <div class="artist-info">
                                    <p class="artist-name">Helen Katherine</p>
                                    <button class="like-button" data-id="13" data-name="Johannes Vermeer">
                                        <img src="/planetart/images/unlike.png" alt="Like">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col medium-column">
                            <div class="position-relative">
                                <a href="caravaggio.php">
                                    <img src="/planetart/images/gabe-weis.jpg" class="img-fluid" style="width: 100%; height: 222px">
                                </a>
                                <div class="artist-info">
                                    <p class="artist-name">Gabe Weis</p>
                                    <button class="like-button" data-id="14" data-name="Caravaggio">
                                        <img src="/planetart/images/unlike.png" alt="Like">
                                    </button>
                                </div>
                            </div>
                        </div>                    
                        <div class="col medium-column">
                            <div class="position-relative">
                                <a href="#">
                                    <img src="/planetart/images/soulo.jpg" class="img-fluid" style="width: 100%; height: 222px">
                                </a>
                                <div class="artist-info">
                                    <p class="artist-name">Soulo</p>
                                    <button class="like-button" data-id="11" data-name="Salvador Dali">
                                        <img src="/planetart/images/unlike.png" alt="Like">
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
                                <button class="like-button" data-id="12" data-name="Claude Monet">
                                    <img src="/planetart/images/unlike.png" alt="Like">
                                </button>
                            </div>
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
    
     <!-- Error Modal -->
     <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Error message will be displayed here -->
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap js and popper.js -->
    <script src="../../js/script.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"
        integrity="sha384-X48EVOIu1KKPHFG0f/RaN8GGVGa2NgqzqXf8+2Vb9sUmXsLGee4JSChHX3U8PVIe"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"
        integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>

        <script>
        $('.like-button').on('click', function(e) {
            e.preventDefault();
            var itemId = $(this).data('id');
            var itemName = $(this).data('name');
            var imageUrl = $(this).closest('.artist-name, .position-relative').find('img').attr('src');
            var likeButton = $(this);

            <?php if (!isset($_SESSION["user"])) : ?>
                // Show error message using modal
                var errorMessage = 'You need to be logged in to like artists. Redirecting to login page...';
                $('#errorModal .modal-body').text(errorMessage);
                $('#errorModal').modal('show');
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000);
                    <?php else : ?>
                        // Like functionality for logged-in users
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
                    <?php endif; ?>
                });
        </script>
    
</body>
</html>