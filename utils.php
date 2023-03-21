<?php
include "./config.php";




// The `last_heartbeat` function gets the time since the last heartbeat from the instance.
function last_heartbeat($config) {
    $alerts_file_path = $config["interface_directory"] . "/alerts.json";
    if (is_dir($config["interface_directory"]) == true) { // Check to make sure the specified interface directory exists.
        if (file_exists($alerts_file_path)) { // Check to see if the alert file exists.
            $heartbeat_log = array_keys(json_decode(file_get_contents($alerts_file_path), true)); // Load the heartbeat log from the JSON data in the alerts file.
        } else { // If the heartbeat file doesn't exist, then load a blank placeholder instead.
            $heartbeat_log = array(); // Set the heartbeat log to an empty array.
        }
    }

    $last_heartbeat = microtime(true) - floatval(end($heartbeat_log)); // Calculate how many seconds ago the last heartbeat was.
    if ($last_heartbeat < -5) { // If the heartbeat happened more than 5 seconds in the future, then assume the clocks are desynced.
        $last_heartbeat = -1;
    } else if ($last_heartbeat < 0) { // If the heartbeat is only a few seconds in the future, then assume the time since the last heartbeat is 0 seconds.
        $last_heartbeat = 0;
    }
    return $last_heartbeat;
}


// The `is_alive` function checks to see if the linked instance is running, based on its heartbeat.
function is_alive($config) {
    if (last_heartbeat($config) < $config["heartbeat_threshold"]) { // Only consider the system online if it's last heartbeat was within a certain number of seconds ago.
        return true;
    } else { // If the last heartbeat exceeded the time to be considered online, display a message that the system is offline.
        return false;
    }
}



// The `verify_permissions` function checks to see if all permissions are set correctly, and that all files are in their expected locations.
function verify_permissions($config) {
    $verify_command = "sudo -u " . $config["exec_user"] . " echo verify"; // Prepare the command to verify permissions.
    $command_output = shell_exec($verify_command); // Execute the command, and record its output.
    $command_output = trim($command_output); // Remove whitespaces from the end and beginning of the command output.

    $instance_configuration_file = $config["instance_directory"] . "/config.json";

    if ($command_output !== "verify") { // Check to see if the command output differs from the expected output.
        echo "<p class=\"error\">PHP does not have the necessary permissions to manage this system as '" . $config["exec_user"] . "' using the '" . shell_exec("whoami") . "' user.</p>"; // Display an error briefly explaining the problem.
        exit(); // Terminate the script.
    }


    if (is_writable("./") == false) { // Check to se if the controller interface's root directory is writable.
        echo "<p class=\"error\">The " . $config["product_name"] . " directory is not writable. Please verify the permissions of the " . getcwd() . " directory.</p>";
    } else if (is_writable("./start.sh") == false) { // Check to see if the controller interface's start script is writable.
        echo "<p class=\"error\">The start.sh script in the " . getcwd() . " directory is not writable.</p>";
    }

    if (is_dir($config["instance_directory"]) == false) { // Check to see if the root Assassin instance directory exists.
        echo "<p class=\"error\">The instance directory doesn't appear to exist at " . $config["instance_directory"] . ". Please adjust the controller configuration.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    } else if (file_exists($instance_configuration_file) == false) { // Check to see if the instance configuration file exists.
        echo "<p class=\"error\">The instance configuration couldn't be located at " . $instance_configuration_file . ". Please verify that the interface configuration points to the correct instance root directory.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    } else if (is_writable($instance_configuration_file) == false) { // Check to see if the instance configuration file is writable.
        echo "<p class=\"error\">The instance configuration isn't writable. Please verify that the instance configuration file at " . $instance_configuration_file . " has the correct permissions to be modified by external programs.</p>";
    }

}

?>
