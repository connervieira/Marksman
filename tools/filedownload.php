<?php
include "../config.php";
include "../utils.php";

$force_login_redirect = true;
include "../authentication.php";



function download($file) { // Define the function to download a file without revealing the URL.
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}


if (isset($_GET["file"])) {
    $file = $config["instance_directory"] . '/' . $_GET["file"];
    if (file_exists($file) and is_dir($file) == false) {
        download($file);
        exit();
    }
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Download Files</title>
        <link rel="stylesheet" href="../styles/main.css">
        <?php include "../loadtheme.php"; ?>
        <link rel="stylesheet" href="../fonts/lato/latofonts.css">

        <link rel="apple-touch-icon" sizes="180x180" href="../assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="../assets/favicon/site.webmanifest">
    </head>
    <body>
        <div class="navbar">
            <a class="button" role="button" href="../tools.php">Back</a>
        </div>
        <h1><?php echo $config["product_name"]; ?></h1>
        <main>
            <h2>Download File</h2>
            <?php
                if (isset($_GET["file"])) {
                    echo "<h3>" . $_GET["file"] . "</h3>";
                    echo "<a class=\"button\" href=\"./filedownload.php\">Clear</a><br><br>";
                }
            ?>
            <div class="buffer" style="text-align:left;">
                <?php
                    if (isset($_GET["file"])) {
                        $file = $config["instance_directory"] . '/' . $_GET["file"];
                        if (is_dir($file)) {
                            $instance_files = scandir($file);
                            $instance_files = array_diff($instance_files, array(".", "..", ".git", "__pycache__", ".DS_Store"));
                        } else if (file_exists($file)) {
                            echo $file;
                            //download($file);
                        } else {
                            echo "<p>The selected file does not appear to exist.</p>";
                        }
                    } else {
                        $instance_files = scandir($config["instance_directory"]);
                        $instance_files = array_diff($instance_files, array(".", "..", ".git", "__pycache__", ".DS_Store"));
                    }

                    foreach ($instance_files as $file) {
                        if (isset($_GET["file"]) == true) {
                            $file = $_GET["file"] . "/" . $file;
                        }
                        $file = str_replace("//", "/", $file);
                        if (is_dir($config["instance_directory"] . "/" . $file)) {
                            echo "<p><a href=\"?file=" . $file . "/\">" . $file . "/</a></p>";
                        } else {
                            echo "<p><a href=\"?file=" . $file . "\">" . $file . "</a></p>";
                        }
                    }
                ?> 
            </div>
        </main>
    </body>
</html>
