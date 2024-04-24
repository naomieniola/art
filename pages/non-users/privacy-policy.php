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
                <button type="button" class="btn btn-outline-primary login">
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
        </ul>
    </nav>
</header>

   <!-- Main Content Container -->
   <div class="container-fluid" style="position: relative; min-height: 100vh;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('/planetart/images/mark-ps.png'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 1; z-index: -1;"></div>
            <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 100vh; padding-top: 50px; padding-bottom: 50px;">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.8); background-color: white;">
                            <div class="card-body" style="padding: 40px;">
                                <div class="privacy-policy-container">
                                    <h1>Privacy Policy</h1>
                                    <p>At Planet Art, we are committed to protecting your privacy and ensuring the security of your personal information. This privacy policy outlines how we collect, use, and safeguard your data in compliance with the Data Protection Act (DPA 2018).</p>
                                    
                                    <h2>Data Collection and Usage</h2>
                                    <p>When you sign up or log in to our website, we collect the following information:</p>
                                    <ul>
                                        <li>Email address: Used for account verification, login, and communication purposes.</li>
                                        <li>Full name: Used for personalization and to address you in communications.</li>
                                        <li>Username: Displayed on the discussion forum and used to identify you to other users.</li>
                                        <li>Password: Securely stored and used for authentication purposes.</li>
                                        <li>Date of Birth: Collected only to ensure that children under the age of 12 do not use our services.</li>

                                    </ul>
                                    <p>We do not collect or store any sensitive personal information beyond what is necessary for the functioning of our website and services.</p>
                                    
                                    <h2>Data Protection and Security</h2>
                                    <p>We employ industry-standard security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. This includes the use of encryption, secure servers, and access controls.</p>
                                    
                                    <h2>Data Sharing and Third Parties</h2>
                                    <p>We do not sell, trade, or otherwise transfer your personal information to outside parties. </p>
                                    <h2>Your Rights and Consent</h2>
                                    <p>By signing up or logging in to our website, you consent to the collection and use of your personal information as described in this privacy policy. You have the right to access, modify, or delete your personal data at any time by contacting us.</p>
                                    
                                    <h2>Updates to the Privacy Policy</h2>
                                    <p>We reserve the right to update or modify this privacy policy at any time. Any changes will be effective immediately upon posting the revised policy on our website. Your continued use of the website after any modifications constitutes your acceptance of the updated policy.</p>
                                    
                                    <p>If you have any questions or concerns about our privacy practices, please contact us at <a href="mailto:privacy@planetart.com" class="privacy-policy-link">privacy@planetart.com</a>.</p>
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