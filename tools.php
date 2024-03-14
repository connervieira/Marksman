<?php
include "./config.php";
include "./utils.php";

$force_login_redirect = true;
include "./authentication.php";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Tools</title>
        <link rel="stylesheet" href="./styles/main.css">
        <?php include "./loadtheme.php"; ?>
        <link rel="stylesheet" href="./fonts/lato/latofonts.css">

        <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="./assets/favicon/site.webmanifest">
    </head>
    <body>
        <div class="navbar">
            <a class="button" role="button" href="./index.php">Back</a>
        </div>
        <h1><?php echo $config["product_name"]; ?></h1>
        <?php verify_permissions($config); ?>
        <main>
            <div class="buffer">
                <h2>Tools</h2>
                <p>The tools found here can be used to maintain Assassin and diagnose problems. However, they can also damage the instance if used incorrectly. Exercise caution when using these tools.</p>
                <div class="buffer">
                    <h3>Files</h3>
                    <a class="button" href="./tools/fileview.php">View</a>
                    <a class="button" href="./tools/filedownload.php">Download</a><br><br>
                </div>
                <div class="buffer">
                    <h3>Management</h3>
                    <a class="button" href="./service.php">Service</a><br><br>
                </div>
            </div>
        </main>
    </body>
</html>
