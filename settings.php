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

                    if (is_dir($_POST["instance_directory"])) { // Make sure the root directory input is actually a directory.
                        $config["instance_directory"] = $_POST["instance_directory"]; // Save the submitted root directory option to the configuration array.
                    } else {
                        echo "<p class='error'>The specified root directory does not exist.</p>";
                        $valid = false; // Indicate that the configuration is not valid, and shouldn't be saved.
                    }

                    if (is_dir($_POST["interface_directory"])) { // Make sure the interface directory input is actually a directory.
                        $config["interface_directory"] = $_POST["interface_directory"]; // Save the submitted interface directory option to the configuration array.
                    } else {
                        echo "<p class='error'>The specified interface directory does not exist.</p>";
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
                        <h3>Interface Settings</h3>
                        <label for="interface_password">Password:</label> <input type="text" id="interface_password" name="interface_password" placeholder="password" pattern="[a-zA-Z0-9]{0,100}" value="<?php echo $config["interface_password"]; ?>"><br><br>
                        <label for="heartbeat_threshold">Heartbeat Threshold:</label> <input type="number" id="heartbeat_threshold" name="heartbeat_threshold" placeholder="5" min="1" max="20" value="<?php echo $config["heartbeat_threshold"]; ?>"> <span>seconds</span><br><br>
                        <label for="theme">Theme:</label>
                        <select id="theme" name="theme">
                            <option value="dark" <?php if ($config["theme"] == "dark") { echo "selected"; } ?>>Dark</option>
                            <option value="light" <?php if ($config["theme"] == "light") { echo "selected"; } ?>>Light</option>
                        </select><br><br>
                    </div>

                    <div class="buffer">
                        <h3>Connection Settings</h3>
                        <label for="exec_user">Execution User:</label> <input type="text" id="exec_user" name="exec_user" placeholder="Username" pattern="[a-zA-Z0-9]{1,100}" value="<?php echo $config["exec_user"]; ?>"><br><br>
                        <label for="instance_directory">Instance Directory:</label> <input type="text" id="instance_directory" name="instance_directory" placeholder="/home/assassin/Assassin" value="<?php echo $config["instance_directory"]; ?>"><br><br>
                        <label for="interface_directory">Interface Directory:</label> <input type="text" id="interface_directory" name="interface_directory" placeholder="/home/predator/Instance/" value="<?php echo $config["interface_directory"]; ?>"><br><br>
                        <label for="refresh_delay">Refresh Delay:</label> <input type="number" id="refresh_delay" name="refresh_delay" placeholder="100" step="1" min="1" max="5000" value="<?php echo $config["refresh_delay"]; ?>"> <span>milliseconds</span><br><br>
                        <label for="precision_coordinates">Coordinate Precision:</label> <input type="number" id="precision_coordinates" name="precision_coordinates" placeholder="4" step="1" min="0" max="10" value="<?php echo $config["precision"]["coordinates"]; ?>"> <span>places</span><br><br>

                        <br><br><input type="submit" class="button" value="Submit" name="controller">
                    </div>
                </form>
            </div>
            <hr>
            <div class="buffer">
                <h2>Instance Settings</h2>
                <form method="post">
                    <div class="buffer">
                        <h3>General</h3>
                        <label for="general>refresh_delay">Refresh Delay:</label> <input type="number" id="general>refresh_delay" name="general>refresh_delay" placeholder="0.5" step="0.1" min="0" max="60" value="<?php echo $instance_config["general"]["refresh_delay"]; ?>"> <span>seconds</span><br><br>
                        <br><br><input type="submit" class="button" value="Submit" name="instance">
                    </div>
                    <div class="buffer">
                        <h3>Alerts</h3>
                        <?php
                            $instance_configuration_path = $config["instance_directory"] . "/config.json"; // This is the file path to the configuration file of the Assassin instance.
                            $instance_config = json_decode(file_get_contents($config["instance_directory"] . "/config.json"), true);

                            # TODO: Add instance configuration saving.
                        ?>
                        <div class="buffer">
                            <h4>GPS</h4>
                            <label for="general>gps>alerts>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>enabled" name="general>gps>alerts>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>gps>alerts>look_back">Look Back:</label> <input type="number" id="general>gps>alerts>look_back" name="general>gps>alerts>look_back" min="0" max="100" step="1" placeholder="10" value="<?php echo $instance_config["general"]["gps"]["alerts"]["look_back"]; ?>"><br><br>
                            <div class="buffer">
                                <h5>Over Speed</h5>
                                <label for="general>gps>alerts>overspeed>enabled">Enabled:</label> <input type="checkbox" id="general>gps>alerts>overspeed>enabled" name="general>gps>alerts>overspeed>enabled" <?php if ($instance_config["general"]["gps"]["alerts"]["overspeed"]["enabled"]) { echo "checked"; } ?>><br><br>
                                <label for="general>gps>alerts>overspeed>maxspeed">Max Speed:</label> <input type="number" id="general>gps>alerts>overspeed>max_speed" name="general>gps>alerts>overspeed>max_speed" min="0" max="1000" step="1" placeholder="400" value="<?php echo $instance_config["general"]["gps"]["alerts"]["overspeed"]["max_speed"]; ?>"><br><br>
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
                            <label for="general>traffic_camera_alerts>loaded_radius">Loaded Radius:</label> <input type="number" id="general>traffic_camera_alerts>loaded_radius" name="general>traffic_camera_alerts>loaded_radius" step="10" placeholder="500" min="0" max="5000" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["loaded_radius"] ?>"> <span>miles</span><br><br>
                            <label for="general>traffic_camera_alerts>alert_range">Alert Range:</label> <input type="number" id="general>traffic_camera_alerts>alert_range" name="general>traffic_camera_alerts>alert_range" step="0.1" placeholder="1" min="0" max="10" value="<?php echo $instance_config["general"]["traffic_camera_alerts"]["alert_range"] ?>"> <span>miles</span><br><br>
                            <div class="buffer">
                                <h5>Types</h5>
                                <label for="general>traffic_camera_alerts>enabled_types>speed">Speed:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>speed" name="general>traffic_camera_alerts>enabled_types>speed" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["speed"]) { echo "checked"; } ?>><br><br>
                                <label for="general>traffic_camera_alerts>enabled_types>redlight">Red Light:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>redlight" name="general>traffic_camera_alerts>enabled_types>redlight" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["redlight"]) { echo "checked"; } ?>><br><br>
                                <label for="general>traffic_camera_alerts>enabled_types>misc">Other:</label> <input type="checkbox" id="general>traffic_camera_alerts>enabled_types>misc" name="general>traffic_camera_alerts>enabled_types>misc" <?php if ($instance_config["general"]["traffic_camera_alerts"]["enabled_types"]["misc"]) { echo "checked"; } ?>><br><br>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>License Plate Recognition Cameras</h4>
                            <label for="general>alpr_alerts>loaded_radius">Loaded Radius:</label> <input type="number" id="general>alpr_alerts>loaded_radius" name="general>alpr_alerts>loaded_radius" step="10" placeholder="500" min="0" max="5000" value="<?php echo $instance_config["general"]["alpr_alerts"]["loaded_radius"] ?>"> <span>miles</span><br><br>
                            <label for="general>alpr_alerts>alert_range">Alert Range:</label> <input type="number" id="general>alpr_alerts>alert_range" name="general>alpr_alerts>alert_range" step="0.1" placeholder="1" min="0" max="10" value="<?php echo $instance_config["general"]["alpr_alerts"]["alert_range"] ?>"> <span>miles</span><br><br>
                            <label for="general>alpr_alerts>angle_threshold">Angle Threshold:</label> <input type="number" id="general>alpr_alerts>angle_threshold" name="general>alpr_alerts>angle_threshold" step="1" placeholder="50" min="0" max="180" value="<?php echo $instance_config["general"]["alpr_alerts"]["angle_threshold"] ?>"> <span>degrees</span><br><br>
                            <label for="general>alpr_alerts>direction_threshold">Direction Threshold:</label> <input type="number" id="general>alpr_alerts>direction_threshold" name="general>alpr_alerts>direction_threshold" step="1" placeholder="20" min="0" max="180" value="<?php echo $instance_config["general"]["alpr_alerts"]["direction_threshold"] ?>"> <span>degrees</span><br><br>
                        </div>
                        <div class="buffer">
                            <h4>Predator Integration</h4>
                            <label for="general>predator_integration>enabled">Enabled:</label> <input type="checkbox" id="general>predator_integration>enabled" name="general>predator_integration>enabled" <?php if ($instance_config["general"]["predator_integration"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>predator_integration>latch_time">Alert Latch Time:</label> <input type="number" min="0" max="600" step="1" placeholder="10" id="general>predator_integration>latch_time" name="general>predator_integration>latch_time" value="<?php echo $instance_config["general"]["predator_integration"]["latch_time"]; ?>"> <span>seconds</span><br><br>
                        </div>
                        <div class="buffer">
                            <h4>ADS-B Aircraft Monitoring</h4>
                            <label for="general>adsb_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>adsb_alerts>enabled" name="general>adsb_alerts>enabled" <?php if ($instance_config["general"]["adsb_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <label for="general>adsb_alerts>minimum_vehicle_speed">Minimum Vehicle Speed:</label> <input type="number" id="general>adsb_alerts>minimum_vehicle_speed" name="general>adsb_alerts>minimum_vehicle_speed" step="1" placeholder="20" min="0" max="200" value="<?php echo $instance_config["general"]["adsb_alerts"]["minimum_vehicle_speed"] ?>"> <span>m/s</span><br><br>
                            <label for="general>adsb_alerts>message_time_to_live">Message Time To Live:</label> <input type="number" id="general>adsb_alerts>message_time_to_live" name="general>adsb_alerts>message_time_to_live" step="1" placeholder="30" min="0" max="600" value="<?php echo $instance_config["general"]["adsb_alerts"]["message_time_to_live"] ?>"> <span>seconds</span><br><br>
                            <label for="general>adsb_alerts>threat_threshold">Threat Threshold:</label> <input type="number" id="general>adsb_alerts>threat_threshold" name="general>adsb_alerts>threat_threshold" step="1" placeholder="3" min="0" max="3" value="<?php echo $instance_config["general"]["adsb_alerts"]["threat_threshold"] ?>"><br><br>
                            <div class="buffer">
                                <h5>Criteria</h5>
                                <div class="buffer">
                                    <h5>Speed</h5>
                                    <label for="general>adsb_alerts>minimum_aircraft_speed">Minimum Aircraft Speed:</label> <input type="number" id="general>adsb_alerts>minimum_aircraft_speed" name="general>adsb_alerts>minimum_aircraft_speed" step="1" placeholder="20" min="0" max="200" value="<?php echo $instance_config["general"]["adsb_alerts"]["minimum_aircraft_speed"] ?>"> <span>knots</span><br><br>
                                    <label for="general>adsb_alerts>maximum_aircraft_speed">Maximum Aircraft Speed:</label> <input type="number" id="general>adsb_alerts>maximum_aircraft_speed" name="general>adsb_alerts>maximum_aircraft_speed" step="1" placeholder="200" min="0" max="1000" value="<?php echo $instance_config["general"]["adsb_alerts"]["maximum_aircraft_speed"] ?>"> <span>knots</span><br><br>
                                </div>
                                <div class="buffer">
                                    <h5>Altitude</h5>
                                    <label for="general>adsb_alerts>minimum_aircraft_altitude">Minimum Aircraft Altitude:</label> <input type="number" id="general>adsb_alerts>minimum_aircraft_altitude" name="general>adsb_alerts>minimum_aircraft_altitude" step="100" placeholder="500" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["minimum_aircraft_altitude"] ?>"> <span>feet</span><br><br>
                                    <label for="general>adsb_alerts>maximum_aircraft_altitude">Maximum Aircraft Altitude:</label> <input type="number" id="general>adsb_alerts>maximum_aircraft_altitude" name="general>adsb_alerts>maximum_aircraft_altitude" step="1" placeholder="8000" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["maximum_aircraft_altitude"] ?>"> <span>feet</span><br><br>
                                </div>
                                <div class="buffer">
                                    <h5>Distance</h5>
                                    <label for="general>adsb_alerts>distance_threshold">Distance Threshold:</label> <input type="number" id="general>adsb_alerts>distance_threshold" name="general>adsb_alerts>distance_threshold" step="1" placeholder="5" min="0" max="100" value="<?php echo $instance_config["general"]["adsb_alerts"]["distance_threshold"] ?>"> <span>miles</span><br><br>
                                    <label for="general>adsb_alerts>base_altitude_threshold">Base Altitude:</label> <input type="number" id="general>adsb_alerts>base_altitude_threshold" name="general>adsb_alerts>base_altitude_threshold" step="1" placeholder="8000" min="0" max="50000" value="<?php echo $instance_config["general"]["adsb_alerts"]["base_altitude_threshold"] ?>"> <span>feet</span><br><br>
                                </div>
                            </div>
                        </div>
                        <div class="buffer">
                            <h4>Weather</h4>
                            <label for="general>weather_alerts>enabled">Enabled:</label> <input type="checkbox" id="general>weather_alerts>enabled" name="general>weather_alerts>enabled" <?php if ($instance_config["general"]["weather_alerts"]["enabled"]) { echo "checked"; } ?>><br><br>
                            <div class="buffer">
                                <h4>Service</h4>
                                <label for="general>weather_alerts>api_key">API Key:</label> <input type="text" id="general>weather_alerts>api_key" name="general>weather_alerts>api_key" value="<?php echo $instance_config["general"]["weather_alerts"]["api_key"]; ?>"><br><br>
                                <label for="general>weather_alerts>refresh_interval">Refresh Interval:</label> <input type="number" id="general>weather_alerts>refresh_interval" name="general>refresh_interval" step="1" placeholder="60" min="0" max="600" value="<?php echo $instance_config["general"]["weather_alerts"]["refresh_interval"] ?>"> <span>seconds</span><br><br>
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
                                    <label for="general>weather_alerts>criteria>temperature>above">Above:</label> <input type="number" min="0" max="10000" step="100" placeholder="10000" id="general>weather_alerts>criteria>temperature>above" name="general>weather_alerts>criteria>temperature>above" value="<?php echo $instance_config["general"]["weather_alerts"]["criteria"]["temperature"]["above"]; ?>"><span>°C</span><br><br>
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
                    </div>
                </form>
            </main>
        </div>
    </body>
</html>
