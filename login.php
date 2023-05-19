<?php
include "./config.php";

if ($config["interface_password"] == "") {
    header("Location: ./index.php");
}

$force_login_redirect = false;
include "./authentication.php";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Login</title>
        <link rel="stylesheet" href="./styles/main.css">
        <?php include "./loadtheme.php"; ?>
        <link rel="stylesheet" href="./fonts/lato/latofonts.css">

        <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="./assets/favicon/site.webmanifest">
    </head>
    <body class="truebody">
        <h1><?php echo $config["product_name"]; ?></h1>
        <h2>Login</h2>
        <?php
        $password = strval($_POST["password"]); // Get the entered password from the POST data.

        if ($password != "" and $password != null) { // Check to see if a password was entered.
            if ($config["interface_password"] == $password) { // Check to see if the password entered matches the password set in the configuration.
                $_SESSION['authid'] = "marksman";
                $_SESSION['username'] = "admin";

                echo "<p>Successfully logged in</p>";
                echo "<a href='./index.php'>Continue</a>";
                exit();
            } else {
                echo "<p class\"error\">Incorrect password</p>";
            }
        }
        ?>
        <main>
            <form method="post">
                <label for="password">Password: </label> <input type="password" placeholder="Password" name="password" id="password">
                <br><br>
                <input type="submit">
            </form>
        </main>
    </body>
</html>
