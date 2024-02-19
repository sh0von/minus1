<?php
session_start();
include('db.php');

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    // Redirect the user to another page
    header("Location: profile.php");
    exit(); // Ensure that no further code is executed after redirection
}
?>
<?php include 'header.php'; ?>

<section class="hero is-info is-fullheight-with-navbar">
    <div class="hero-body">
        <div class="container has-text-centered">
            <h1 class="title">
                Welcome to Minus One!
            </h1>
            <h2 class="subtitle">
                The ultimate platform for sharing and discovering code.
            </h2>

            <div class="buttons is-centered"> <!-- Added is-centered class here -->
                <?php
                // Check if the user is logged in
                if(isset($_SESSION['username'])) {
                    // Redirect the user to another page
                    header("Location: profile.php");
                    exit(); // Ensure that no further code is executed after redirection
                } else {
                    // If not logged in, display login and register buttons
                    echo '<p><a class="button is-primary is-medium" href="login.php">Login</a>  
                          <a class="button is-primary is-medium" href="register.php">Register</a></p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
