<?php
include "./config.php";
include "./utils.php";

$force_login_redirect = true;
include "./authentication.php";



// Verify the theme from the form input, and apply it now so that the newly selected theme is reflected by the theme that loads when the page is displayed. This process is repeated during the actual configuration validation process later.
if ($_POST["theme"] == "dark"  or $_POST["theme"] == "light") { // Make sure the theme input matches one of the expected options.
    $config["theme"] = $_POST["theme"]; // Update the configuration array.
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $config["product_name"]; ?> - Settings</title>
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
        <div class="buffer">
            <h2>Controller Settings</h2>
            <main>
                <?php
                $valid = true;
                if ($_POST["controller"] == "Submit") { // Check to see if the controller settings form was submitted.
                    if (preg_match("/^[A-Za-z0-9]*$/", $_POST["interface_password"])) { // Check to see if all of the characters in the submitted password are alphanumeric.
                        if (strlen($_POST["interface_password"]) <= 100) { // Check to make sure the submitted password is not an excessive length.
                            $config["interface_password"] = $_POST["interface_password"]; // Save the submitted interface password to the configuration array.
                        } else {
                            echo "<p class='error'>The interface password can only be 100 characters or less.</p>";
                            $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                        }
                    } else {
                        echo "<p class='error'>The interface password can only contain alpha-numeric characters.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }

                    if ($_POST["heartbeat_threshold"] >= 1 and $_POST["heartbeat_threshold"] <= 60) { // Make sure the heartbeat threshold input is within reasonably expected bounds.
                        $config["heartbeat_threshold"] = intval($_POST["heartbeat_threshold"]); // Save the submitted heartbeat threshold option to the configuration array.
                    } else {
                        echo "<p class='error'>The heartbeat threshold option is not within expected bounds.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }

                    if ($_POST["theme"] == "dark"  or $_POST["theme"] == "light") { // Make sure the theme input matches one of the expected options.
                        $config["theme"] = $_POST["theme"]; // Save the submitted theme option to the configuration array.
                    } else {
                        echo "<p class='error'>The theme option is not an expected option.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }




                    if (preg_match("/^[A-Za-z0-9]*$/", $_POST["exec_user"])) { // Check to see if all of the characters in the submitted execution user are alphanumeric.
                        if (strlen($_POST["exec_user"]) <= 100) { // Check to make sure the submitted execution user is not an excessive length.
                            $config["exec_user"] = $_POST["exec_user"]; // Save the submitted execution user to the configuration array.
                        } else {
                            echo "<p class='error'>The execution user can only be 100 characters or less.</p>";
                            $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                        }
                    } else {
                        echo "<p class='error'>The execution user can only contain alpha-numeric characters.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }
                    if ($_POST["log_output"] == "on") { // Check to see if output logging was checked in the form.
                        $config["log_output"] = true; // This determines whether or not Marksman will direct Assassin's console output to a log file.
                    } else {
                        $config["log_output"] = false; // This determines whether or not Marksman will direct Assassin's console output to a log file.
                    }

                    if (is_dir($_POST["instance_directory"])) { // Make sure the root directory input is actually a directory.
                        $config["instance_directory"] = $_POST["instance_directory"]; // Save the submitted root directory option to the configuration array.
                    } else {
                        echo "<p class='error'>The specified root directory does not exist.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }


                    if (floatval($_POST["refresh_delay"]) > 0 and floatval($_POST["refresh_delay"]) <= 5000) { // Make sure the refresh delay input is within the expected range.
                        $config["refresh_delay"] = floatval($_POST["refresh_delay"]); // Save the submitted refresh delay option to the configuration array.
                    } else {
                        echo "<p class='error'>The specified refresh delay is not within the expected range.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }

                    if (intval($_POST["precision_coordinates"]) >= 0 and intval($_POST["precision_coordinates"]) <= 10) { // Make sure the coordinate precision input is within the expected range.
                        $config["precision"]["coordinates"] = intval($_POST["precision_coordinates"]); // Save the submitted coordinate precision option to the configuration array.
                    } else {
                        echo "<p class='error'>The specified coordinate precision is not within the expected range.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }




                    if ($valid == true) { // Check to see if the entered configuration is completely valid.
                        if (is_writable($config_database_name)) { // Check to make sure the configuration file is writable.
                            file_put_contents($config_database_name, serialize($config)); // Save the modified configuration to disk.
                            echo "<p>Successfully updated configuration.</p>";
                        } else {
                            echo "<p class='error'>The configuration file is not writable.</p>";
                        }
                    } else {
                        echo "<p class='error'>The configuration was not updated.</p>";
                    }
                }
                ?>
                <form method="post">
                    <div class="buffer">
                        <h3>Controller Settings</h3>
                        <label for="interface_password">Password:</label> <input type="text" id="interface_password" name="interface_password" placeholder="password" pattern="[a-zA-Z0-9]{0,100}" value="<?php echo $config["interface_password"]; ?>"><br><br>
                        <label for="heartbeat_threshold">Heartbeat Threshold:</label> <input type="number" id="heartbeat_threshold" name="heartbeat_threshold" placeholder="5" min="1" max="20" value="<?php echo $config["heartbeat_threshold"]; ?>"> <span>seconds</span><br><br>
                        <label for="theme">Theme:</label>
                        <select id="theme" name="theme">
                            <option value="dark" <?php if ($config["theme"] == "dark") { echo "selected"; } ?>>Dark</option>
                            <option value="light" <?php if ($config["theme"] == "light") { echo "selected"; } ?>>Light</option>
                        </select><br><br>
                        <label for="precision_coordinates">Coordinate Precision:</label> <input type="number" id="precision_coordinates" name="precision_coordinates" placeholder="4" step="1" min="0" max="10" value="<?php echo $config["precision"]["coordinates"]; ?>"> <span>places</span><br><br>
                        <label for="refresh_delay">Refresh Interval:</label> <input type="number" id="refresh_delay" name="refresh_delay" placeholder="100" step="50" min="1" max="5000" value="<?php echo $config["refresh_delay"]; ?>"> <span>milliseconds</span><br><br>
                    </div>

                    <div class="buffer">
                        <h3>Connection Settings</h3>
                        <label for="exec_user">Execution User:</label> <input type="text" id="exec_user" name="exec_user" placeholder="Username" pattern="[a-zA-Z0-9]{1,100}" value="<?php echo $config["exec_user"]; ?>"><br><br>
                        <label for="log_output">Log Output:</label> <input type="checkbox" id="log_output" name="log_output" <?php if ($config["log_output"] == true) { echo "checked"; } ?>><br><br>
                        <label for="instance_directory">Instance Directory:</label> <input type="text" id="instance_directory" name="instance_directory" placeholder="/home/assassin/Assassin" value="<?php echo $config["instance_directory"]; ?>"><br><br>
                        <label for="interface_directory">Interface Directory:</label> <input type="text" id="interface_directory" name="instance_directory" placeholder="/home/assassin/Assassin" value="<?php echo $config["interface_directory"]; ?>" style='color:#aaaaaa;' disabled><br><br>

                        <br><br><input type="submit" class="button" value="Submit" name="controller">
                    </div>
                </form>
            </div>
            <hr>
            <div class="buffer">
                <h2>Instance Settings</h2>
                <form method="post">
                    <?php
                        $instance_configuration_path = $config["instance_directory"] . "/config.json"; // This is the file path to the configuration file of the Assassin instance.
                        $instance_config = json_decode(file_get_contents($config["instance_directory"] . "/config.json"), true);


                        if ($_POST["instance"] == "Submit") { // Check to see if the instance settings form was submitted.
                            // General configuration.
                            $instance_config["general"]["refresh_delay"] = floatval($_POST["general>refresh_delay"]);
                            $instance_config["general"]["gps"]["speed_source"] = strval($_POST["general>gps>speed_source"]);
                            $instance_config["general"]["gps"]["provider"] = strval($_POST["general>gps>provider"]);

                            if ($_POST["display>status_lighting>enabled"] == "on") { $instance_config["display"]["status_lighting"]["enabled"] = true; } else { $instance_config["display"]["status_lighting"]["enabled"] = false; }
                            $instance_config["display"]["status_lighting"]["delay"] = floatval($_POST["display>status_lighting>delay"]);
                            $instance_config["display"]["status_lighting"]["base_url"] = $_POST["display>status_lighting>base_url"];


                            // GPS alert configuration.
                            if ($_POST["general>gps>alerts>enabled"] == "on") { $instance_config["general"]["gps"]["alerts"]["enabled"] = true; } else { $instance_config["general"]["gps"]["alerts"]["enabled"] = false; }
                            $instance_config["general"]["gps"]["alerts"]["look_back"] = intval($_POST["general>gps>alerts>look_back"]);

                            if ($_POST["general>gps>alerts>overspeed>enabled"] == "on") { $instance_config["general"]["gps"]["alerts"]["overspeed"]["enabled"] = true; } else { $instance_config["general"]["gps"]["alerts"]["overspeed"]["enabled"] = false; }
                            $instance_config["general"]["gps"]["alerts"]["overspeed"]["max_speed"] = floatval($_POST["general>gps>alerts>overspeed>max_speed"]);
                            if ($_POST["general>gps>alerts>overspeed>prioritize_highest"] == "on") { $instance_config["general"]["gps"]["alerts"]["overspeed"]["prioritize_highest"] = true; } else { $instance_config["general"]["gps"]["alerts"]["overspeed"]["prioritize_highest"] = false; }

                            if ($_POST["general>gps>alerts>no_data>enabled"] == "on") { $instance_config["general"]["gps"]["alerts"]["no_data"]["enabled"] = true; } else { $instance_config["general"]["gps"]["alerts"]["no_data"]["enabled"] = false; }
                            $instance_config["general"]["gps"]["alerts"]["no_data"]["length"] = intval($_POST["general>gps>alerts>no_data>length"]);

                            if ($_POST["general>gps>alerts>frozen>enabled"] == "on") { $instance_config["general"]["gps"]["alerts"]["frozen"]["enabled"] = true; } else { $instance_config["general"]["gps"]["alerts"]["frozen"]["enabled"] = false; }
                            $instance_config["general"]["gps"]["alerts"]["frozen"]["length"] = intval($_POST["general>gps>alerts>frozen>length"]);

                            if ($_POST["general>gps>alerts>diagnostic>enabled"] == "on") { $instance_config["general"]["gps"]["alerts"]["diagnostic"]["enabled"] = true; } else { $instance_config["general"]["gps"]["alerts"]["diagnostic"]["enabled"] = false; }


                            // Attention monitoring configuration.
                            if ($_POST["general>attention_monitoring>enabled"] == "on") { $instance_config["general"]["attention_monitoring"]["enabled"] = true; } else { $instance_config["general"]["attention_monitoring"]["enabled"] = false; }
                            $instance_config["general"]["attention_monitoring"]["reset_time"] = floatval($_POST["general>attention_monitoring>reset_time"]);
                            $instance_config["general"]["attention_monitoring"]["reset_speed"] = floatval($_POST["general>attention_monitoring>reset_speed"]);
                            $instance_config["general"]["attention_monitoring"]["triggers"]["time"] = floatval($_POST["general>attention_monitoring>triggers>time"]);


                            // Traffic camera alert configuration.
                            if ($_POST["general>traffic_camera_alerts>enabled"] == "on") { $instance_config["general"]["traffic_camera_alerts"]["enabled"] = true; } else { $instance_config["general"]["traffic_camera_alerts"]["enabled"] = false; }
                            $instance_config["general"]["traffic_camera_alerts"]["loaded_radius"] = floatval($_POST["general>traffic_camera_alerts>loaded_radius"]);
                            if ($_POST["general>traffic_camera_alerts>speed_check"] == "on") { $instance_config["general"]["traffic_camera_alerts"]["speed_check"] = true; } else { $instance_config["general"]["traffic_camera_alerts"]["speed_check"] = false; }

                            $instance_config["general"]["traffic_camera_alerts"]["triggers"]["distance"] = floatval($_POST["general>traffic_camera_alerts>triggers>distance"]);
                            $instance_config["general"]["traffic_camera_alerts"]["triggers"]["speed"] = floatval($_POST["general>traffic_camera_alerts>triggers>speed"]);
                            $instance_config["general"]["traffic_camera_alerts"]["triggers"]["angle"] = floatval($_POST["general>traffic_camera_alerts>triggers>angle"]);
                            $instance_config["general"]["traffic_camera_alerts"]["triggers"]["direction"] = floatval($_POST["general>traffic_camera_alerts>triggers>direction"]);

                            if ($_POST["general>traffic_camera_alerts>enabled_types>speed"] == "on") { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["speed"] = true; } else { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["speed"] = false; }
                            if ($_POST["general>traffic_camera_alerts>enabled_types>redlight"] == "on") { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["redlight"] = true; } else { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["redlight"] = false; }
                            if ($_POST["general>traffic_camera_alerts>enabled_types>misc"] == "on") { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["misc"] = true; } else { $instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["misc"] = false; }


                            // ALPR alert configuration.
                            if ($_POST["general>alpr_alerts>enabled"] == "on") { $instance_config["general"]["alpr_alerts"]["enabled"] = true; } else { $instance_config["general"]["alpr_alerts"]["enabled"] = false; }
                            $instance_config["general"]["alpr_alerts"]["loaded_radius"] = floatval($_POST["general>alpr_alerts>loaded_radius"]);
                            $instance_config["general"]["alpr_alerts"]["alert_range"] = floatval($_POST["general>alpr_alerts>alert_range"]);
                            $instance_config["general"]["alpr_alerts"]["filters"]["angle_threshold"] = floatval($_POST["general>alpr_alerts>filters>angle_threshold"]);
                            $instance_config["general"]["alpr_alerts"]["filters"]["direction_threshold"] = floatval($_POST["general>alpr_alerts>filters>direction_threshold"]);


                            // Predator integration configuration.
                            if ($_POST["general>predator_integration>enabled"] == "on") { $instance_config["general"]["predator_integration"]["enabled"] = true; } else { $instance_config["general"]["predator_integration"]["enabled"] = false; }
                            $instance_config["general"]["predator_integration"]["latch_time"] = floatval($_POST["general>predator_integration>latch_time"]);


                            // ADS-B alert configuration.
                            if ($_POST["general>adsb_alerts>enabled"] == "on") { $instance_config["general"]["adsb_alerts"]["enabled"] = true; } else { $instance_config["general"]["adsb_alerts"]["enabled"] = false; }
                            $instance_config["general"]["adsb_alerts"]["minimum_vehicle_speed"] = floatval($_POST["general>adsb_alerts>minimum_vehicle_speed"]);
                            $instance_config["general"]["adsb_alerts"]["message_time_to_live"] = floatval($_POST["general>adsb_alerts>message_time_to_live"]);
                            $instance_config["general"]["adsb_alerts"]["threat_threshold"] = intval($_POST["general>adsb_alerts>threat_threshold"]);

                            $instance_config["general"]["adsb_alerts"]["criteria"]["speed"]["minimum"] = floatval($_POST["general>adsb_alerts>criteria>speed>minimum"]);
                            $instance_config["general"]["adsb_alerts"]["criteria"]["speed"]["maximum"] = floatval($_POST["general>adsb_alerts>criteria>speed>maximum"]);

                            $instance_config["general"]["adsb_alerts"]["criteria"]["altitude"]["minimum"] = floatval($_POST["general>adsb_alerts>criteria>altitude>minimum"]);
                            $instance_config["general"]["adsb_alerts"]["criteria"]["altitude"]["maximum"] = floatval($_POST["general>adsb_alerts>criteria>altitude>maximum"]);

                            $instance_config["general"]["adsb_alerts"]["criteria"]["distance"]["base_distance"] = floatval($_POST["general>adsb_alerts>criteria>distance>base_distance"]);
                            $instance_config["general"]["adsb_alerts"]["criteria"]["distance"]["base_altitude"] = floatval($_POST["general>adsb_alerts>criteria>distance>base_altitude"]);


                            // Weather alert configuration.
                            if ($_POST["general>weather_alerts>enabled"] == "on") { $instance_config["general"]["weather_alerts"]["enabled"] = true; } else { $instance_config["general"]["weather_alerts"]["enabled"] = false; }
                            $instance_config["general"]["weather_alerts"]["api_key"] = $_POST["general>weather_alerts>api_key"];
                            $instance_config["general"]["weather_alerts"]["refresh_interval"] = floatval($_POST["general>weather_alerts>refresh_interval"]);

                            $instance_config["general"]["weather_alerts"]["criteria"]["visibility"]["below"] = floatval($_POST["general>weather_alerts>criteria>visibility>below"]);
                            $instance_config["general"]["weather_alerts"]["criteria"]["visibility"]["above"] = floatval($_POST["general>weather_alerts>criteria>visibility>above"]);

                            $instance_config["general"]["weather_alerts"]["criteria"]["temperature"]["below"] = floatval($_POST["general>weather_alerts>criteria>temperature>below"]);
                            $instance_config["general"]["weather_alerts"]["criteria"]["temperature"]["above"] = floatval($_POST["general>weather_alerts>criteria>temperature>above"]);


                            // Drone detection configuration.
                            if ($_POST["general>drone_alerts>enabled"] == "on") { $instance_config["general"]["drone_alerts"]["enabled"] = true; } else { $instance_config["general"]["drone_alerts"]["enabled"] = false; }
                            $instance_config["general"]["drone_alerts"]["hazard_latch_time"] = floatval($_POST["general>drone_alerts>hazard_latch_time"]);


                            // Bluetooth monitoring configuration.
                            if ($_POST["general>bluetooth_monitoring>enabled"] == "on") { $instance_config["general"]["bluetooth_monitoring"]["enabled"] = true; } else { $instance_config["general"]["bluetooth_monitoring"]["enabled"] = false; }
                            $instance_config["general"]["bluetooth_monitoring"]["latch_time"] = floatval($_POST["general>bluetooth_monitoring>latch_time"]);
                            $instance_config["general"]["bluetooth_monitoring"]["scan_time"] = floatval($_POST["general>bluetooth_monitoring>scan_time"]);
                            $instance_config["general"]["bluetooth_monitoring"]["minimum_following_distance"] = floatval($_POST["general>bluetooth_monitoring>minimum_following_distance"]);



                            $encoded_instance_config = json_encode($instance_config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                            file_put_contents($instance_configuration_path, $encoded_instance_config);
                        }
                    ?>
                    <div class="buffer">
                        <h3>General</h3>
                        <label for="general>refresh_delay">Refresh Delay:</label> <input type="number" id="general>refresh_delay" name="general>refresh_delay" placeholder="0.5" step="0.1" min="0" max="60" value="<?php echo $instance_config["general"]["refresh_delay"]; ?>"> <span>seconds</span><br><br>
                        
                        <div class="buffer">
                            <h4>GPS</h4>
                            <label for="general>gps>provider">Provider:</label>
                            <select id="general>gps>provider" name="general>gps>provider">
                                <option value="gpsd" <?php if ($instance_config["general"]["gps"]["provider"] == "gpsd") { echo "selected"; } ?>>GPSD</option>
                                <option value="termux" <?php if ($instance_config["general"]["gps"]["provider"] == "termux") { echo "selected"; } ?>>Termux</option>
                                <option value="locateme" <?php if ($instance_config["general"]["gps"]["provider"] == "locateme") { echo "selected"; } ?>>LocateMe</option>
                            </select><br>
                            <label for="general>gps>speed_source">Speed Source:</label>
                            <select id="general>gps>speed_source" name="general>gps>speed_source">
                                <option value="gps" <?php if ($instance_config["general"]["gps"]["speed_source"] == "gps") { echo "selected"; } ?>>GPS</option>
                                <option value="obd" <?php if ($instance_config["general"]["gps"]["speed_source"] == "obd") { echo "selected"; } ?>>OBD</option>
                                <option value="calculated" <?php if ($instance_config["general"]["gps"]["speed_source"] == "calculated") { echo "selected"; } ?>>Calculated</option>
                            </select>
                        </div>
                        <div class="buffer">
                            <h4>Status Lighting</h4>
                            <label for="display>status_lighting>enabled">Enabled:</label> <input type="checkbox" id="display>status_lighting>enabled" name="display>status_lighting>enabled" <?php if ($instance_config["display"]["status_lighting"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="display>status_lighting>delay">Delay:</label> <input type="number" id="display>status_lighting>delay" name="display>status_lighting>delay" min="0" max="5" step="0.1" placeholder="0.5" value="<?php echo $instance_config["display"]["status_lighting"]["delay"]; ?>"> <span>seconds</span><br><br>
                            <div class="buffer">
                                <h5>URLs</h5>
                                <label for="display>status_lighting>base_url">Base URL:</label> <input type="text" id="display>status_lighting>base_url" name="display>status_lighting>base_url" placeholder="http://wled.local/win&A=255&FX=0" value="<?php echo $instance_config["display"]["status_lighting"]["base_url"]; ?>"><br><br>
                            </div>
                        </div>
                    </div>
                    <div class="buffer">
                        <h3>Alerts</h3>
                        <div class="buffer">
                            <h4>GPS</h4>
                            <label for="general>gps>alerts>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>enabled" name="general>gps>alerts>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>gps>alerts>look_back">Look Back:</label> <input type="number" id="general>gps>alerts>look_back" name="general>gps>alerts>look_back" min="0" max="100" step="1" placeholder="10" value="<?php echo $instance_config["general"]["gps"]["alerts"]["look_back"]; ?>"><br><br>
                            <div class="buffer">
                                <h5>Over Speed</h5>
                                <label for="general>gps>alerts>overspeed>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>overspeed>enabled" name="general>gps>alerts>overspeed>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["overspeed"]["enabled"]) { echo "checked"; } ?>><br><br>
                                <label for="general>gps>alerts>overspeed>max_speed">Max Speed:</label> <input type="number" id="general>gps>alerts>overspeed>max_speed" name="general>gps>alerts>overspeed>max_speed" min="0" max="1000" step="1" placeholder="400" value="<?php echo $instance_config["general"]["gps"]["alerts"]["overspeed"]["max_speed"]; ?>"> <span>mph</span><br><br>
                                <label for="general>gps>alerts>overspeed>prioritize_highest">Prioritize Highest:</label> <input type="checkbox" id="general>gps>alerts>overspeed>prioritize_highest" name="general>gps>alerts>overspeed>prioritize_highest" <?php if ($instance_config["general"]["gps"]["alerts"]["overspeed"]["prioritize_highest"]) { echo "checked"; } ?>><br><br>
                            </div>
                            <div class="buffer">
                                <h5>No Data</h5>
                                <label for="general>gps>alerts>no_data>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>no_data>enabled" name="general>gps>alerts>no_data>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["no_data"]["enabled"]) { echo "checked"; } ?>><br><br>
                                <label for="general>gps>alerts>no_data>length">Length:</label> <input type="number" id="general>gps>alerts>no_data>length" name="general>gps>alerts>no_data>length" min="0" max="100" step="1" placeholder="5" value="<?php echo $instance_config["general"]["gps"]["alerts"]["no_data"]["length"]; ?>"><br><br>
                            </div>
                            <div class="buffer">
                                <h5>Frozen</h5>
                                <label for="general>gps>alerts>frozen>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>frozen>enabled" name="general>gps>alerts>frozen>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["frozen"]["enabled"]) { echo "checked"; } ?>><br><br>
                                <label for="general>gps>alerts>frozen>length">Length:</label> <input type="number" id="general>gps>alerts>frozen>length" name="general>gps>alerts>frozen>length" min="0" max="100" step="1" placeholder="5" value="<?php echo $instance_config["general"]["gps"]["alerts"]["frozen"]["length"]; ?>"><br><br>
                            </div>
                            <div class="buffer">
                                <h5>Diagnostic</h5>
                                <label for="general>gps>alerts>diagnostic>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>diagnostic>enabled" name="general>gps>alerts>diagnostic>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["diagnostic"]["enabled"]) { echo "checked"; } ?>><br><br>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Attention Monitoring</h4>
                            <label for="general>attention_monintoring>enabled">Enabled:</label> <input type="checkbox" id="general>attention_monitoring>enabled" name="general>attention_monitoring>enabled" <?php if ($instance_config["general"]["attention_monitoring"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>attention_monintoring>reset_time">Reset Time:</label> <input type="number" id="general>attention_monitoring>reset_time" name="general>attention_monitoring>reset_time" step="0.01" placeholder="3" min="0" max="120" value="<?php echo $instance_config["general"]["attention_monitoring"]["reset_time"] ?>"> <span>minutes</span><br><br>
                            <label for="general>attention_monintoring>reset_speed">Reset Speed:</label> <input type="number" id="general>attention_monitoring>reset_speed" name="general>attention_monitoring>reset_speed" step="1" placeholder="3" min="0" max="120" value="<?php echo $instance_config["general"]["attention_monitoring"]["reset_speed"] ?>"> <span><?php echo $instance_config["display"]["displays"]["speed"]["unit"]; ?></span><br><br>
                            <div class="buffer">
                                <h5>Triggers</h5>
                                <label for="general>attention_monintoring>triggers>time">Time:</label> <input type="number" id="general>attention_monitoring>triggers>time" name="general>attention_monitoring>triggers>time" step="1" placeholder="180" min="0" max="600" value="<?php echo $instance_config["general"]["attention_monitoring"]["triggers"]["time"] ?>"> <span>minutes</span><br><br>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Traffic Enforcement Cameras</h4>
                            <label for="general>traffic_camera_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled" name="general>traffic_camera_alerts>enabled" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>traffic_camera_alerts>loaded_radius">Loaded Radius:</label> <input type="number" id="general>traffic_camera_alerts>loaded_radius" name="general>traffic_camera_alerts>loaded_radius" step="10" placeholder="500" min="0" max="5000" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["loaded_radius"]; ?>"> <span>miles</span><br><br>
                            <label for="general>traffic_camera_alerts>speed_check">Speed Check:</label> <input type="checkbox" id="general>traffic_camera_alerts>speed_check" name="general>traffic_camera_alerts>speed_check" <?php if ($instance_config["general"]["traffic_camera_alerts"]["speed_check"]) { echo "checked"; } ?>><br><br>
                            <div class="buffer">
                                <h5>Triggers</h5>
                                <label for="general>traffic_camera_alerts>triggers>distance">Distance:</label> <input type="number" id="general>traffic_camera_alerts>triggers>distance" name="general>traffic_camera_alerts>triggers>distance" step="0.1" placeholder="1" min="0" max="10" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["triggers"]["distance"]; ?>"> <span>miles</span><br><br>
                                <label for="general>traffic_camera_alerts>triggers>speed">Speed:</label> <input type="number" id="general>traffic_camera_alerts>triggers>speed" name="general>traffic_camera_alerts>triggers>speed" step="1" placeholder="0" min="-50" max="100" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["triggers"]["speed"]; ?>"> <span><?php echo $instance_config["display"]["displays"]["speed"]["unit"]; ?></span><br><br>
                                <label for="general>traffic_camera_alerts>triggers>distance">Angle:</label> <input type="number" id="general>traffic_camera_alerts>triggers>angle" name="general>traffic_camera_alerts>triggers>angle" step="5" placeholder="60" min="0" max="181" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["triggers"]["angle"]; ?>"> <span>degrees</span><br><br>
                                <label for="general>traffic_camera_alerts>triggers>distance">Direction:</label> <input type="number" id="general>traffic_camera_alerts>triggers>direction" name="general>traffic_camera_alerts>triggers>direction" step="5" placeholder="30" min="0" max="181" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["triggers"]["direction"]; ?>"> <span>degrees</span><br><br>
                            </div>
                            <div class="buffer">
                                <h5>Types</h5>
                                <label for="general>traffic_camera_alerts>enabled_types>speed">Speed:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>speed" name="general>traffic_camera_alerts>enabled_types>speed" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["speed"]) { echo "checked"; } ?>><br><br>
                                <label for="general>traffic_camera_alerts>enabled_types>redlight">Red Light:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>redlight" name="general>traffic_camera_alerts>enabled_types>redlight" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["redlight"]) { echo "checked"; } ?>><br><br>
                                <label for="general>traffic_camera_alerts>enabled_types>misc">Other:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>misc" name="general>traffic_camera_alerts>enabled_types>misc" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["misc"]) { echo "checked"; } ?>><br><br>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>License Plate Recognition Cameras</h4>
                            <label for="general>alpr_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>alpr_alerts>enabled" name="general>alpr_alerts>enabled" <?php if ($instance_config["general"]["alpr_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>alpr_alerts>loaded_radius">Loaded Radius:</label> <input type="number" id="general>alpr_alerts>loaded_radius" name="general>alpr_alerts>loaded_radius" step="10" placeholder="500" min="0" max="5000" value="<?php echo $instance_config["general"]["alpr_alerts"]["loaded_radius"]; ?>"> <span>miles</span><br><br>
                            <label for="general>alpr_alerts>alert_range">Alert Range:</label> <input type="number" id="general>alpr_alerts>alert_range" name="general>alpr_alerts>alert_range" step="0.1" placeholder="1" min="0" max="10" value="<?php echo $instance_config["general"]["alpr_alerts"]["alert_range"]; ?>"> <span>miles</span><br><br>
                            <div class="buffer">
                                <h5>Filters</h5>
                                <label for="general>alpr_alerts>filters>angle_threshold">Angle Threshold:</label> <input type="number" id="general>alpr_alerts>filters>angle_threshold" name="general>alpr_alerts>filters>angle_threshold" step="1" placeholder="50" min="0" max="180" value="<?php echo $instance_config["general"]["alpr_alerts"]["filters"]["angle_threshold"]; ?>"> <span>degrees</span><br><br>
                                <label for="general>alpr_alerts>filters>direction_threshold">Direction Threshold:</label> <input type="number" id="general>alpr_alerts>filters>direction_threshold" name="general>alpr_alerts>filters>direction_threshold" step="1" placeholder="20" min="0" max="180" value="<?php echo $instance_config["general"]["alpr_alerts"]["filters"]["direction_threshold"]; ?>"> <span>degrees</span><br><br>
                                <div class="buffer">
                                    <h6>Duplicate Merging</h6>
                                    <label for="general>alpr_alerts>filters>duplicate_filtering>enabled">Enabled:</label> <input type="checkbox" id="general>alpr_alerts>filters>duplicate_filtering>enabled" name="general>alpr_alerts>filters>duplicate_filtering>enabled" <?php if ($instance_config["general"]["alpr_alerts"]["filters"]["duplicate_filtering"]["enabled"]) { echo "checked"; } ?>><br><br>
                                    <label for="general>alpr_alerts>filters>duplicate_filtering>distance">Distance:</label> <input type="number" id="general>alpr_alerts>filters>duplicate_filtering>distance" name="general>alpr_alerts>filters>duplicate_filtering>distance" step="0.001" placeholder="0.01" min="0" max="0.2" value="<?php echo $instance_config["general"]["alpr_alerts"]["filters"]["duplicate_filtering"]["distance"]; ?>"> <span>miles</span><br><br>
                                    <label for="general>alpr_alerts>filters>duplicate_filtering>angle">Angle:</label> <input type="number" id="general>alpr_alerts>filters>duplicate_filtering>angle" name="general>alpr_alerts>filters>duplicate_filtering>angle" step="5" placeholder="20" min="0" max="180" value="<?php echo $instance_config["general"]["alpr_alerts"]["filters"]["duplicate_filtering"]["angle"]; ?>"> <span>degrees</span><br><br>
                                </div>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Predator Integration</h4>
                            <label for="general>predator_integration>enabled">Enabled:</label> <input type="checkbox" id="general>predator_integration>enabled" name="general>predator_integration>enabled" <?php if ($instance_config["general"]["predator_integration"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>predator_integration>latch_time">Alert Latch Time:</label> <input type="number" min="0" max="600" step="1" placeholder="10" id="general>predator_integration>latch_time" name="general>predator_integration>latch_time" value="<?php echo $instance_config["general"]["predator_integration"]["latch_time"]; ?>"> <span>seconds</span><br><br>
                        </div>
                        <div class="buffer">
                            <h4>ADS-B Aircraft Monitoring</h4>
                            <label for="general>adsb_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>adsb_alerts>enabled" name="general>adsb_alerts>enabled" <?php if ($instance_config["general"]["adsb_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>adsb_alerts>minimum_vehicle_speed">Minimum Vehicle Speed:</label> <input type="number" id="general>adsb_alerts>minimum_vehicle_speed" name="general>adsb_alerts>minimum_vehicle_speed" step="1" placeholder="20" min="0" max="200" value="<?php echo $instance_config["general"]["adsb_alerts"]["minimum_vehicle_speed"] ?>"> <span><?php echo $instance_config["display"]["displays"]["speed"]["unit"]; ?></span></span><br><br>
                            <label for="general>adsb_alerts>message_time_to_live">Message Time To Live:</label> <input type="number" id="general>adsb_alerts>message_time_to_live" name="general>adsb_alerts>message_time_to_live" step="1" placeholder="30" min="0" max="600" value="<?php echo $instance_config["general"]["adsb_alerts"]["message_time_to_live"] ?>"> <span>seconds</span><br><br>
                            <label for="general>adsb_alerts>threat_threshold">Threat Threshold:</label> <input type="number" id="general>adsb_alerts>threat_threshold" name="general>adsb_alerts>threat_threshold" step="1" placeholder="3" min="0" max="3" value="<?php echo $instance_config["general"]["adsb_alerts"]["threat_threshold"] ?>"><br><br>
                            <div class="buffer">
                                <h5>Criteria</h5>
                                <div class="buffer">
                                    <h5>Speed</h5>
                                    <label for="general>adsb_alerts>criteria>speed>minimum">Minimum Aircraft Speed:</label> <input type="number" id="general>adsb_alerts>criteria>speed>minimum" name="general>adsb_alerts>criteria>speed>minimum" step="1" placeholder="20" min="0" max="200" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["speed"]["minimum"] ?>"> <span>knots</span><br><br>
                                    <label for="general>adsb_alerts>criteria>speed>maximum">Maximum Aircraft Speed:</label> <input type="number" id="general>adsb_alerts>criteria>speed>maximum" name="general>adsb_alerts>criteria>speed>maximum" step="1" placeholder="200" min="0" max="1000" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["speed"]["maximum"] ?>"> <span>knots</span><br><br>
                                </div>
                                <div class="buffer">
                                    <h5>Altitude</h5>
                                    <label for="general>adsb_alerts>criteria>altitude>minimum">Minimum Aircraft Altitude:</label> <input type="number" id="general>adsb_alerts>criteria>altitude>minimum" name="general>adsb_alerts>criteria>altitude>minimum" step="100" placeholder="500" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["altitude"]["minimum"] ?>"> <span>feet</span><br><br>
                                    <label for="general>adsb_alerts>criteria>altitude>maximum">Maximum Aircraft Altitude:</label> <input type="number" id="general>adsb_alerts>criteria>altitude>maximum" name="general>adsb_alerts>criteria>altitude>maximum" step="1" placeholder="8000" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["altitude"]["maximum"] ?>"> <span>feet</span><br><br>
                                </div>
                                <div class="buffer">
                                    <h5>Distance</h5>
                                    <label for="general>adsb_alerts>criteria>distance>base_distance">Distance Threshold:</label> <input type="number" id="general>adsb_alerts>criteria>distance>base_distance" name="general>adsb_alerts>criteria>distance>base_distance" step="1" placeholder="5" min="0" max="100" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["distance"]["base_distance"] ?>"> <span>miles</span><br><br>
                                    <label for="general>adsb_alerts>criteria>distance>base_altitude">Base Altitude:</label> <input type="number" id="general>adsb_alerts>criteria>distance>base_altitude" name="general>adsb_alerts>criteria>distance>base_altitude" step="1" placeholder="8000" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["criteria"]["distance"]["base_altitude"] ?>"> <span>feet</span><br><br>
                                </div>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Weather</h4>
                            <label for="general>weather_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>weather_alerts>enabled" name="general>weather_alerts>enabled" <?php if ($instance_config["general"]["weather_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <div class="buffer">
                                <h4>Service</h4>
                                <label for="general>weather_alerts>api_key">API Key:</label> <input type="text" id="general>weather_alerts>api_key" name="general>weather_alerts>api_key" value="<?php echo $instance_config["general"]["weather_alerts"]["api_key"]; ?>"><br><br>
                                <label for="general>weather_alerts>refresh_interval">Refresh Interval:</label> <input type="number" id="general>weather_alerts>refresh_interval" name="general>weather_alerts>refresh_interval" step="1" placeholder="60" min="0" max="600" value="<?php echo $instance_config["general"]["weather_alerts"]["refresh_interval"] ?>"> <span>seconds</span><br><br>
                            </div>
                            <div class="buffer">
                                <h4>Criteria</h4>
                                <div class="buffer">
                                    <h5>Visibility</h5>
                                    <label for="general>weather_alerts>criteria>visibility>below">Below:</label> <input type="number" min="0" max="10000" step="100" placeholder="500" id="general>weather_alerts>criteria>visibility>below" name="general>weather_alerts>criteria>visibility>below" value="<?php echo $instance_config["general"]["weather_alerts"]["criteria"]["visibility"]["below"]; ?>"> <span>meters</span><br><br>
                                    <label for="general>weather_alerts>criteria>visibility>above">Above:</label> <input type="number" min="0" max="10000" step="100" placeholder="10000" id="general>weather_alerts>criteria>visibility>above" name="general>weather_alerts>criteria>visibility>above" value="<?php echo $instance_config["general"]["weather_alerts"]["criteria"]["visibility"]["above"]; ?>"> <span>meters</span><br><br>
                                </div>
                                <div class="buffer">
                                    <h5>Temperature</h5>
                                    <label for="general>weather_alerts>criteria>temperature>below">Below:</label> <input type="number" min="-100" max="100" step="1" placeholder="-10" id="general>weather_alerts>criteria>visibility>below" name="general>weather_alerts>criteria>temperature>below" value="<?php echo $instance_config["general"]["weather_alerts"]["criteria"]["temperature"]["below"]; ?>"><span>°C</span><br><br>
                                    <label for="general>weather_alerts>criteria>temperature>above">Above:</label> <input type="number" min="0" max="10000" step="1" placeholder="40" id="general>weather_alerts>criteria>temperature>above" name="general>weather_alerts>criteria>temperature>above" value="<?php echo $instance_config["general"]["weather_alerts"]["criteria"]["temperature"]["above"]; ?>"><span>°C</span><br><br>
                                </div>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Drone Detection</h4>
                            <label for="general>drone_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>drone_alerts>enabled" name="general>drone_alerts>enabled" <?php if ($instance_config["general"]["drone_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>drone_alerts>hazard_latch_time">Hazard Latch Time:</label> <input type="number" min="0" max="600" step="1" placeholder="10" id="general>drone_alerts>hazard_latch_time" name="general>drone_alerts>hazard_latch_time" value="<?php echo $instance_config["general"]["drone_alerts"]["hazard_latch_time"]; ?>"> <span>seconds</span><br><br>
                        </div>
                        <div class="buffer">
                            <h4>Bluetooth Monitoring</h4>
                            <label for="general>bluetooth_monitoring>enabled">Enabled:</label> <input type="checkbox" id="general>bluetooth_monitoring>enabled" name="general>bluetooth_monitoring>enabled" <?php if ($instance_config["general"]["bluetooth_monitoring"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>bluetooth_monitoring>latch_time">Latch Time:</label> <input type="number" min="0" max="600" step="1" placeholder="10" id="general>bluetooth_monitoring>latch_time" name="general>bluetooth_monitoring>latch_time" value="<?php echo $instance_config["general"]["bluetooth_monitoring"]["latch_time"]; ?>"> <span>seconds</span><br><br>
                            <label for="general>bluetooth_monitoring>scan_time">Scan Time:</label> <input type="number" min="0" max="15" step="1" placeholder="2" id="general>bluetooth_monitoring>scan_time" name="general>bluetooth_monitoring>scan_time" value="<?php echo $instance_config["general"]["bluetooth_monitoring"]["scan_time"]; ?>"> <span>seconds</span><br><br>
                            <label for="general>bluetooth_monitoring>minimum_following_distance">Following Distance:</label> <input type="number" min="0" max="30" step="0.1" placeholder="1" id="general>bluetooth_monitoring>minimum_following_distance" name="general>bluetooth_monitoring>minimum_following_distance" value="<?php echo $instance_config["general"]["bluetooth_monitoring"]["minimum_following_distance"]; ?>"> <span>miles</span><br><br>
                        </div>
                        <input type="submit" value="Submit" class="button" name="instance">
                    </div>
                </form>
            </main>
        </div>
    </body>
</html>
