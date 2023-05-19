<?php
include "./config.php";

if ($config["interface_password"] !== "") {
    session_start();
    if ($_SESSION['authid'] == "marksman") {
        $username = $_SESSION['username'];
    } else {
        if ($force_login_redirect == true) {
            if (file_exists("./login.php")) {
                header("Location: ./login.php");
            } else if (file_exists("../login.php")) {
                header("Location: ../login.php");
            } else {
                header("Location: ./login.php");
            }
            exit();
        }
    }
}

?>

