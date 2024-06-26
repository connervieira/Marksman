<?php

$config_database_name = "./config.txt";

if (file_exists("../config.txt")) {
    $config_database_name = "../config.txt";
}

if (is_writable(".") == false) {
    echo "<p class=\"error\">The " . getcwd() . " directory is not writable to PHP.</p>";
    exit();
}

// Load and initialize the database.
if (file_exists($config_database_name) == false) { // Check to see if the database file doesn't exist.
    $configuration_database_file = fopen($config_database_name, "w") or die("Unable to create configuration database file."); // Create the file.

    $config["product_name"] = "Marksman";
    $config["interface_password"] = "";
    $config["heartbeat_threshold"] = 5; // This is the number of seconds old the last heartbeat has to be before the system is considered to be offline.
    $config["theme"] = "dark"; // This determines the supplmentary CSS file that will be used across the interface.
    $config["exec_user"] = "assassin"; // This is the user on the system that will be used to control executables.
    $config["log_output"] = false; // This determines whether or not Marksman will direct Assassin's console output to a log file.
    $config["instance_directory"] = "/home/assassin/Software/Assassin/instance"; // This defines where the Assassin directory can be found.
    $config["refresh_delay"] = 100; // This determines how many milliseconds the interface will wait between refreshes.
    $config["precision"]["coordinates"] = 4; // This determines how many decimal places coordinates will be shown to.

    fwrite($configuration_database_file, serialize($config)); // Set the contents of the database file to the placeholder configuration.
    fclose($configuration_database_file); // Close the database file.
}

if (file_exists($config_database_name) == true) { // Check to see if the item database file exists. The database should have been created in the previous step if it didn't already exists.
    $config = unserialize(file_get_contents($config_database_name)); // Load the database from the disk.
} else {
    echo "<p class=\"error\">The configuration database failed to load</p>"; // Inform the user that the database failed to load.
    exit(); // Terminate the script.
}


$instance_configuration_path = $config["instance_directory"] . "/config.json"; // This is the file path to the configuration file of the Assassin instance.
if (file_exists($instance_configuration_path)) { // Check to see if the instance configuration file exists.
    $instance_config = json_decode(file_get_contents($config["instance_directory"] . "/config.json"), true); // Load the instance configuration file.
    if ($instance_config["external"]["local"]["enabled"] == true) {
        $config["interface_directory"] = $instance_config["external"]["local"]["interface_directory"]; // Auto-fill the interface directory.
    } else {
        echo "<p class=\"error\">The local interface directory is disabled in Assassin's configuration. " . htmlspecialchars($config["product_name"]) . " requires this feature to be enabled to function.</p>"; // Inform the user that the database failed to load.
        exit(); // Terminate the script.
    }
} else {
    if (!isset($displayed_instance_config_warning)) { // Only show the instance configuration warning if it has not already been displayed.
        echo "<p class=\"error\">The interface directory could not be identified from the instance configuration file. It is possible the Assassin configuration file is corrupt, or the incorrect Assassin instance directory is set.</p>"; // Inform the user that the database failed to load.
    }
    $displayed_instance_config_warning = true; // Indicate that the instance configuration warning has already been displayed.

}

?>
