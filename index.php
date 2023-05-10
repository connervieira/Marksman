<?php
include "./config.php";

$force_login_redirect = true;
include "./authentication.php";

include "./utils.php";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Dashboard</title>
        <link rel="stylesheet" href="./styles/main.css">
        <?php include "./loadtheme.php"; ?>
        <link rel="stylesheet" href="./fonts/lato/latofonts.css">

        <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="./assets/favicon/site.webmanifest">
    </head>
    <body>
        <div class="navbar" role="navigation">
            <a class="button" role="button" href="./logout.php">Logout</a>
            <a class="button" role="button" href="./settings.php">Settings</a><br>
        </div>
        <noscript><p class="error">Your browser does not appear to have JavaScript enabled. <?php echo $config["product_name"]  ?> requires JavaScript to function.</p></noscript>
        <?php
        verify_permissions($config); // Verify that PHP has all of the appropriate permissions.

        $action = $_GET["action"];
        if (is_writable(".")) { // Check to see if the controller directory is writable.
            if (!file_exists("./start.sh")) { // Check to see if the script hasn't been created yet.
                file_put_contents("./start.sh", ""); // Create the start script.
            }
        }
        if ($action == "start") {
            if (is_writable("./start.sh")) {
                if ($config["log_output"] == true) { // Check to see if Marksman is configured to log the output of Assassin.
                    file_put_contents("./start.sh", "cd " . $config["instance_directory"] . "; python3 " . $config["instance_directory"] . "/main.py > marksmanoutput" . round(time()) . ".txt"); // Update the start script.
                } else { // Marksman is not configured to log the output of Assassin.
                    file_put_contents("./start.sh", "cd " . $config["instance_directory"] . "; python3 " . $config["instance_directory"] . "/main.py &"); // Update the start script.
                }
            } else {
                echo "<p class=\"error\">The start.sh script is not writable.</p>";
                exit();
            }
            if (file_exists("./start.sh")) { // Verify that the start script exists.
                if (is_alive($config) == false) {
                    $start_command = "sudo -u " . $config["exec_user"] . " sh ./start.sh"; // Prepare the command to start an instance.
                    shell_exec($start_command . ' > /dev/null 2>&1 &'); // Start an instance.
                    header("Location: ."); // Reload the page to remove any arguments from the URL.
                } else {
                    echo "<p class=\"error\">There seems to already be an instance active.</p>";
                    echo "<p class=\"error\">Please stop any existing instances before launching another.</p>";
                }
            } else {
                echo "<p class=\"error\">The start script doesn't appear to exist.</p>";
                echo "<p class=\"error\">The program could not be started.</p>";
            }
        } else if ($action == "stop") {
            shell_exec("sudo killall python3"); // Kill all Python executables.
            header("Location: ."); // Reload the page to remove any arguments from the URL.
        }
        ?>
        <main>
            <hr>
            <div>
                <?php
                if (is_alive($config) == true) {
                    $start_button = '<a class="button" role="button" id="startbutton" style="color:#aaaaaa" role="button" href="#">Start</a>';
                    $stop_button = '<a class="button" role="button" id="stopbutton" style="color:#ffffff" role="button" href="?action=stop">Stop</a>';
                } else {
                    $start_button = '<a class="button" role="button" id="startbutton" style="color:#ffffff" role="button" href="?action=start">Start</a>';
                    $stop_button = '<a class="button" role="button" id="stopbutton" style="color:#aaaaaa" role="button" href="#">Stop</a>';
                }

                echo $start_button;
                echo $stop_button;
                ?>
                <br>
                <p>The last instance heartbeat was <b id="lastheartbeatdisplay">X</b> seconds ago.</p>
            </div>
            <div id="alertsframe"></div>
        </main>
    </body>
    <script>
        const fetch_info = async () => {
            console.log("Fetching instance status");
            const status_response = await fetch('./jsrelay.php'); // Fetch the status information using the JavaScript relay page.
            const status_result = await status_response.json(); // Parse the JSON data from the response.

            // Update the control buttons based on the instance status.
            if (status_result.is_alive) {
                document.getElementById("startbutton").style.color = "#aaaaaa";
                document.getElementById("startbutton").href = "#";
                document.getElementById("stopbutton").style.color = "#ffffff";
                document.getElementById("stopbutton").href = "?action=stop";
            } else {
                document.getElementById("startbutton").style.color = "#ffffff";
                document.getElementById("startbutton").href = "?action=start";
                document.getElementById("stopbutton").style.color = "#aaaaaa";
                document.getElementById("stopbutton").href = "#";
            }
            document.getElementById("lastheartbeatdisplay").innerHTML = await (Math.round(status_result.last_heartbeat*100)/100).toFixed(2);

            const alerts_response = await fetch('./alerts.php'); // Fetch the status information using the JavaScript relay page.
            if (status_result.is_alive) {
                document.getElementById("alertsframe").innerHTML = await alerts_response.text();
            } else {
                document.getElementById("alertsframe").innerHTML = "<p><i>The instance is offline.</i></p>";
            }
        }

        setInterval(() => { fetch_info(); }, <?php echo floatval($config["refresh_delay"]); ?>); // Execute the instance fetch script at a regular timed interval.
    </script>
</html>
