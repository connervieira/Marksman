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
    $config["interface_directory"] = "/home/assassin/Software/Assassin/interface"; // This defines where Assassin's interface directory can be found.
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



?>
