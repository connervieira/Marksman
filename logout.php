<?php
include "./config.php";

$force_login_redirect = false;
include "./authentication.php";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Logout</title>
        <link rel="stylesheet" href="./styles/main.css">
        <?php include "./loadtheme.php"; ?>
        <link rel="stylesheet" href="./fonts/lato/latofonts.css">

        <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="./assets/favicon/site.webmanifest">
    </head>
    <body class="truebody">
        <div class="navbar">
            <a class="button" role="button" href="./login.php">Login</a>
        </div>
        <h1><?php echo $config["product_name"]; ?></h1>
        <h2>Logout</h2>
        <?php
        session_start(); // Get the session information.
        session_unset(); // Remove all session variables.
        session_destroy(); // Destroy the session.
        ?>
        <main>
            <p>You have successfully logged out.</p>
        </main>
    </body>
</html>
