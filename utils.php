<?php
if (file_exists("./config.php")) {
    include "./config.php";
} else if (file_exists("../config.php")) {
    include "../config.php";
} else if (file_exists("../../config.php")) {
    include "../../config.php";
}




// The `last_heartbeat` function gets the time since the last heartbeat from the instance.
function last_heartbeat($config) {
    $alerts_file_path = $config["interface_directory"] . "/alerts.json";
    $status_file_path = $config["interface_directory"] . "/status.json";
    if (is_dir($config["interface_directory"]) == true) { // Check to make sure the specified interface directory exists.
        if (file_exists($alerts_file_path)) { // Check to see if the alert file exists.
            $heartbeat_log = array_keys(json_decode(file_get_contents($alerts_file_path), true)); // Load the heartbeat log from the JSON data in the alerts file.
        } else { // If the heartbeat file doesn't exist, then load a blank placeholder instead.
            $heartbeat_log = array(); // Set the heartbeat log to an empty array.
        }
        if (file_exists($status_file_path)) { // Check to see if the status message log.
            $status_log = array_keys(json_decode(file_get_contents($status_file_path), true)); // Load the status message log from the JSON data in the alerts file.
        } else { // If the heartbeat file doesn't exist, then load a blank placeholder instead.
            $status_log = array(); // Set the status message log to an empty array.
        }
    } else {
        $heartbeat_log = array(); // Set the heartbeat log to an empty array.
    }

    $last_alert_heartbeat = microtime(true) - floatval(end($heartbeat_log)); // Calculate how many seconds ago the last heartbeat was, using the alert log.
    $last_status_heartbeat = microtime(true) - floatval(end($status_log)); // Calculate how many seconds ago the last heartbeat was, using the status log.

    if ($last_status_heartbeat < $last_alert_heartbeat) {
        $last_heartbeat = $last_status_heartbeat;
    } else {
        $last_heartbeat = $last_alert_heartbeat;
    }

    if ($last_heartbeat < -5) { // If the heartbeat happened more than 5 seconds in the future, then assume the clocks are desynced.
        $last_heartbeat = -1;
    } else if ($last_heartbeat < 0) { // If the heartbeat is only a few seconds in the future, then assume the time since the last heartbeat is 0 seconds.
        $last_heartbeat = 0;
    }
    return $last_heartbeat;
}


// The `is_alive` function checks to see if the linked instance is running, based on its heartbeat.
function is_alive($config) {
    $last_heartbeat = last_heartbeat($config);
    if ($last_heartbeat == -1) {
        return false;
    } else if ($last_heartbeat < $config["heartbeat_threshold"]) { // Only consider the system online if it's last heartbeat was within a certain number of seconds ago.
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

    if ($command_output !== "verify") { // Check to see if the command output differs from the expected output.
        echo "<p class=\"error\">PHP does not have the necessary permissions to manage this system as '" . $config["exec_user"] . "' using the '" . shell_exec("whoami") . "' user.</p>"; // Display an error briefly explaining the problem.
    }


    if (is_writable("./") == false) { // Check to se if the controller interface's root directory is writable.
        echo "<p class=\"error\">The " . $config["product_name"] . " directory is not writable. Please verify the permissions of the " . getcwd() . " directory.</p>";
    } else if (is_writable("./start.sh") == false and file_exists("./start.sh") == true) { // Check to see if the controller interface's start script is writable.
        echo "<p class=\"error\">The start.sh script in the " . getcwd() . " directory is not writable.</p>";
    }

    $instance_configuration_path = $config["instance_directory"] . "/config.json"; // This is the file path to the configuration file of the Assassin instance.
    if (is_dir($config["instance_directory"]) == false) { // Check to see if the root Assassin instance directory exists.
        echo "<p class=\"error\">The instance directory doesn't appear to exist at " . $config["instance_directory"] . ". Please adjust the controller configuration.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    } else if (file_exists($instance_configuration_path) == false) { // Check to see if the instance configuration file exists.
        echo "<p class=\"error\">The instance configuration file doesn't appear to exist at " . $instance_configuration_path . ". Please make sure the 'Instance Directory' points to a valid instance of Assassin.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    } else if (is_writable($instance_configuration_path) == false) { // Check to see if the instance configuration file is writable.
        echo "<p class=\"error\">The instance configuration file at " . $instance_configuration_path . " doesn't appear to be writable.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    }
    if (is_dir($config["interface_directory"]) == false) { // Check to see if the Assassin interface directory exists.
        echo "<p class=\"error\">The interface directory doesn't appear to exist at " . $config["interface_directory"] . ". Please adjust the controller configuration.</p>";
        echo "<a class=\"button\" href=\"./settings.php\">Settings</a>";
    }

}



// The `decimal_precision` function rounds off a given decimal number to a given number of decimal points.
function decimal_precision($number, $config) {
    return round($number * (10**$config["precision"]["coordinates"])) / (10**$config["precision"]["coordinates"]);
}
?>

