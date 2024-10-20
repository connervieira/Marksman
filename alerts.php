<?php
include "./config.php";
include "./utils.php";

$alerts_file_path = $config["interface_directory"] . "/alerts.json";
$status_file_path = $config["interface_directory"] . "/status.json";
if (is_dir($config["interface_directory"]) == true) { // Check to make sure the specified interface directory exists.
    if (file_exists($alerts_file_path) == true) { // Check to see if the alert file exists.
        $alert_log = json_decode(file_get_contents($alerts_file_path), true); // Load the alerts from the JSON data in the alert file.
    } else { // If the alert file doesn't exist, then load a blank placeholder instead.
        $alert_log = array(); // Set the alert log to an empty array.
    }

    if (file_exists($status_file_path) == true) { // Check to see if the status log file exists.
        $status_log = json_decode(file_get_contents($status_file_path), true); // Load the status messages from the JSON data in the status log file.
    } else { // If the status file doesn't exist, then load a blank placeholder instead.
        $status_log = array(); // Set the status log to an empty array.
    }
}


$status_log_status = array();
$status_log_warnings = array();
$status_log_errors = array();


foreach ($status_log as $time => $message) {
    if (intval($message[0]) == 1) {
        $status_log_status[$time] = [$time, $message[1]];
    } else if (intval($message[0]) == 2) {
        $status_log_warnings[$time] = $message[1];
    } else if (intval($message[0]) == 3) {
        $status_log_errors[$time] = $message[1];
    }
}

$last_alert = end($alert_log); // Get the last entry in the alert log.

$last_status_status = end($status_log_status); // Get the last status entry in the status log.


if (time() - floatval($last_status_status[0]) < 5) {
    echo "<table class=\"alert message_status\"><tr>";
    echo "    <th width=\"5%;\"><img src=\"img/message.svg\" height=\"50px\"></th>";
    echo "    <th width=\"35%;\"><h4>Status</h4></th>";
    echo "    <th width=\"60%;\"><p>" . $last_status_status[1] . "</p></th>";
    echo "</tr></table>";
}
foreach (array_reverse($status_log_warnings) as $time => $message) {
    if (time() - floatval($time) < 15) {
        echo "<table class=\"alert message_warning\"><tr>";
        echo "    <th width=\"5%;\"><img src=\"img/message.svg\" height=\"50px\"></th>";
        echo "    <th width=\"35%;\"><h4>Warning</h4></th>";
        echo "    <th width=\"60%;\"><p>" . $message . "</p></th>";
        echo "</tr></table>";
    }
}
foreach (array_reverse($status_log_errors) as $time => $message) {
    if (time() - floatval($time) < 300) {
        echo "<table class=\"alert message_error\"><tr>";
        echo "    <th width=\"5%;\"><img src=\"img/message.svg\" height=\"50px\"></th>";
        echo "    <th width=\"35%;\"><h4>Error</h4></th>";
        echo "    <th width=\"60%;\"><p>" . $message . "</p></th>";
        echo "</tr></table>";
    }
}




// Display attention alerts.
// Yellow
foreach ($last_alert["attention"] as $key => $alert) { // Iterate through each attention alert.
    if ($key == "time") {
        echo "<table class=\"alert yellow\"><tr>";
        echo "    <th width=\"5%;\"><img src=\"img/alerts/attention.svg\" height=\"50px\"></th>";
        echo "    <th width=\"50%;\"><h4>Attention Monitoring</h4></th>";
        echo "    <th width=\"45%;\"><p>Driving for " . floor(($alert["time"]%86400)/3600) . ":" . sprintf("%02d", floor(($alert["time"]%3600)/60)) . ":" . sprintf("%02d", $alert["time"]%60) . "</p></th>";
        echo "</tr></table>";
    }
}


// Display GPS alerts.
// Green
foreach ($last_alert["gps"] as $key => $alert) { // Iterate through each GPS alert.
    echo "<table class=\"alert green\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/gps.svg\" height=\"50px\"></th>";
    if ($key == "diagnostic") {
        echo "    <th width=\"35%\">";
        echo "        <h4>GPS Info</h4>";
        echo "        <p>Diagnostic</p>";
        echo "    </th>";
        echo "    <th width=\"35%\">";
        echo "        <p>" . decimal_precision($alert["lat"], $config) . ", " . decimal_precision($alert["lon"], $config) . "</p>";
        echo "        <p>" . round($alert["hdg"]) . "° at " . round(floatval($alert["alt"])) . " meters</p>";
        echo "    </th>";
        echo "    <th width=\"25%\">";
        echo "        <p>" . round(floatval($alert["spd"])*2.236936*100)/100 . " mph</p>";
        echo "        <p>" . $alert["sat"] . " satellites</p>";
        echo "    </th>";
    } else if ($key == "maxspeed") {
        echo "    <th width=\"45%\">";
        echo "        <h4>GPS Alert</h4>";
        echo "        <p>Over-Speed</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>The calculated GPS speed is <b>" . round($alert["speed"]*100)/100 . " mph</b>.</p>";
        echo "    </th>";
    } else if ($key == "nodata") {
        echo "    <th width=\"45%\">";
        echo "        <h4>GPS Alert</h4>";
        echo "        <p>No Data</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>The GPS is not returning data.</p>";
        echo "    </th>";
    } else if ($key == "frozen") {
        echo "    <th width=\"45%\">";
        echo "        <h4>GPS Alert</h4>";
        echo "        <p>Frozen</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>The GPS appear to be frozen.</p>";
        echo "    </th>";
    } else {
        echo "    <th width=\"45%\">";
        echo "        <h4>GPS Alert</h4>";
        echo "        <p>Unknown Type</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>An unknown GPS alert occurred.</p>";
        echo "    </th>";
    }
    echo "</tr></table>";
}



// Display OBD alerts.
// Olive
foreach ($last_alert["obd"] as $key => $alert) { // Iterate through each OBD alert.
    echo "<table class=\"alert olive\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/vehicle.svg\" height=\"50px\"></th>";
    if ($key == "speed") {
        echo "    <th width=\"45%\">";
        echo "        <h4>Vehicle Diagnostics</h4>";
        echo "        <p>Ground Speed</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p><b>" . round($alert["value"]) . " mph</b>";
        if ($alert["alert"] == "high") {
            echo " exceeds the maximum threshold";
        } else if ($alert["alert"] == "low") {
            echo " falls below the minimum threshold";
        }
        echo ".</p>";
        echo "    </th>";
    } else if ($key == "rpm") {
        echo "    <th width=\"45%\">";
        echo "        <h4>Vehicle Diagnostics</h4>";
        echo "        <p>Engine RPM</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p><b>" . round($alert["value"]) . " rpm</b>";
        if ($alert["alert"] == "high") {
            echo " exceeds the maximum threshold";
        } else if ($alert["alert"] == "low") {
            echo " falls below the minimum threshold";
        }
        echo ".</p>";
        echo "    </th>";
    } else if ($key == "fuel_level") {
        echo "    <th width=\"45%\">";
        echo "        <h4>Vehicle Diagnostics</h4>";
        echo "        <p>Fuel Level</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p><b>" . round($alert["value"]*100) . "% full</b>";
        if ($alert["alert"] == "high") {
            echo " exceeds the maximum threshold";
        } else if ($alert["alert"] == "low") {
            echo " falls below the minimum threshold";
        }
        echo ".</p>";
        echo "    </th>";
    } else if ($key == "airflow") {
        echo "    <th width=\"45%\">";
        echo "        <h4>Vehicle Diagnostics</h4>";
        echo "        <p>Airflow</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p><b>" . round($alert["value"]*100) . " grams per second</b>";
        if ($alert["alert"] == "high") {
            echo " exceeds the maximum threshold";
        } else if ($alert["alert"] == "low") {
            echo " falls below the minimum threshold";
        }
        echo ".</p>";
        echo "    </th>";
    } else {
        echo "    <th width=\"45%\">";
        echo "        <h4>Vehicle Diagnostics</h4>";
        echo "        <p>Unknown Alert</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>An unknown vehicle diagnostic alert occurred.</p>";
        echo "    </th>";
    }
    echo "</tr></table>";
}







// Display Predator alerts.
// Red
foreach ($last_alert["predator"] as $plate => $triggers) { // Iterate through each alert in the Predator integration alerts.
    foreach ($triggers as $rule => $info) {
        echo "<table class=\"alert red\"><tr>";
        echo "    <th width=\"5%\"><img src=\"img/alerts/predator.svg\" height=\"50px\"></th>";
        echo "    <th width=\"35%\">";
        echo "        <h4>Predator</h4>";
        echo "        <p>ALPR Alert</p>";
        echo "    </th>";
        echo "    <th width=\"25%\">";
        echo "        <p>" . $plate . "</p>";
        if (strlen($info["name"]) > 0) {
            echo "        <p>" . $info["name"] . "</p>";
        } else if (strlen($info["description"]) > 0) {
            echo "        <p>" . $info["description"] . "</p>";
        }
        echo "    </th>";
        echo "    <th width=\"35%\">";
        echo "        <p>" . $rule . "</p>";
        $vehicle_string = "";
        if (strlen($info["vehicle"]["color"]) > 0) { $vehicle_string .= strval($info["vehicle"]["color"]) . " "; }
        if (intval($info["vehicle"]["year"]) > 0) { $vehicle_string .= strval($info["vehicle"]["year"]) . " "; }
        if (strlen($info["vehicle"]["make"]) > 0) { $vehicle_string .= strval($info["vehicle"]["make"]) . " "; }
        if (strlen($info["vehicle"]["model"]) > 0) { $vehicle_string .= strval($info["vehicle"]["model"]) . " "; }
        if (strlen($vehicle_string) > 0) {
            $vehicle_string = substr($vehicle_string, 0, strlen($vehicle_string)-1); // Remove the last character, since it will always be a space.
            echo "        <p>" . $vehicle_string . "</p>";
        }
    }
    echo "    </th>";
    echo "</tr></table>";
}


// Display drone alerts.
// Magenta
foreach ($last_alert["drone"] as $alert) { // Iterate through each automonous threat alert.
    echo "<table class=\"alert magenta\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/drone.svg\" style=\"height:50px;transform:rotate(" . $alert["relativeheading"] . "deg);\"></th>";
    echo "    <th width=\"40%\">";
    echo "        <h4>Autonomous</h4>";
    echo "        <p>" . strtoupper($alert["threattype"][0]) . substr($alert["threattype"], 1, 100) . "</p>";
    echo "    </th>";
    echo "    <th width=\"25%\">";
    echo "        <p>" . $alert["strength"] . "% signal</p>";
    echo "        <p>" . strtotime($alert["lastseen"]) - strtotime($alert["firstseen"]) . " seconds</p>";
    echo "    </th>";
    echo "    <th width=\"25%\">";
    echo "        <p>" . $alert["company"] . "</p>";
    echo "        <p>" . $alert["name"] . "</p>";
    echo "    </th>";
    echo "</tr></table>";
}


// Display Bluetooth alerts.
// Pink
foreach ($last_alert["bluetooth"] as $alert) { // Iterate through each Bluetooth alert.
    echo "<table class=\"alert pink\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/bluetooth.svg\" height=\"50px\"></th>";
    echo "    <th width=\"35%\">";
    echo "        <h4>Bluetooth</h4>";
    if ($alert["blacklist"] == true) {
        echo "        <p>Blacklist</p>";
    } else {
        echo "        <p>Following</p>";
    }
    echo "    </th>";
    echo "    <th width=\"40%\">";
    echo "        <p>" . $alert["name"] . "</p>";
    echo "        <p>" . $alert["address"] . "</p>";
    echo "    </th>";
    echo "    <th width=\"20%\">";
    echo "        <p>" . $alert["distance_followed"] . " miles</p>";
    echo "        <p>" . $alert["lastseentime"] - $alert["firstseentime"] . " seconds</p>";
    echo "    </th>";
    echo "</tr></table>";
}



// Display traffic camera alerts.
// Blue
foreach ($last_alert["traffic_camera"] as $key => $alert) { // Iterate through each traffic camera alert.
    echo "<table class=\"alert blue\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/trafficamera.svg\" height=\"50px\"></th>";
    echo "    <th width=\"5%\"><img src=\"img/arrow.svg\" style=\"height:50px;transform: rotate(" . $alert["direction"] . "deg);\"></th>";
    echo "    <th width=\"35%\">";
    echo "        <h4>Traffic Camera</h4>";
    if ($alert["type"] == "speed") {
        echo "        <p>Speed Camera</p>";
        echo "    </th>";
        echo "    <th width=\"20%\">";
        echo "        <p>" . round($alert["dst"]*100)/100 . " miles</p>";
        if ($alert["spd"] !== null and $alert["spd"] > 0) { // Check to see if this camera has speed limit information.
            echo "        <p>" . round($alert["spd"]*0.621371192) . " mph</p>";
        } else {
            echo "        <p>Unknown threshold</p>";
        }
        echo "    </th>";
        echo "    <th width=\"35%\">";
        if ($alert["spd"] !== null and $alert["spd"] > 0) { // Check to see if this camera has street information.
            echo "        <p>" . $alert["str"] . "</p>";
        }
        echo "    </th>";
    } else if ($alert["type"] == "redlight") {
        echo "        <p>Red Light Camera</p>";
        echo "    </th>";
        echo "    <th width=\"55%\">";
        echo "        <p>" . round($alert["dst"]*100)/100 . " miles</p>";
        echo "        <p>" . $alert["str"] . "</p>";
    } else if ($alert["type"] == "misc") {
        echo "        <p>Miscellaneous Camera</p>";
        echo "    </th>";
        echo "    <th width=\"55%\">";
        echo "        <p>" . round($alert["dst"]*100)/100 . " miles</p>";
        echo "        <p>" . $alert["str"] . "</p>";
    } else {
        echo "        <p>Unknown Camera</p>";
        echo "    </th>";
        echo "    <th width=\"55%\">";
        echo "        <p>" . round($alert["dst"]*100)/100 . " miles</p>";
        echo "        <p>" . $alert["str"] . "</p>";
    }
    echo "</th></tr></table>";
}



// Display aircraft alerts.
// Cyan
foreach ($last_alert["aircraft"] as $key => $alert) { // Iterate through each time-based attention alert.
    echo "<table class=\"alert cyan\"><tr>";
    if ($alert["relativeheading"] == "?" or $alert["relativeheading"] == "?" or $alert["relativeheading"] == 0) {
        echo "    <th width=\"5%\"><img src=\"img/question.svg\" style=\"height:50px;\"></th>";
    } else {
        echo "    <th width=\"5%\"><img src=\"img/alerts/aircraft.svg\" style=\"height:50px;transform:rotate(" . ($alert["relativeheading"] - 90) . "deg);\"></th>";
    }
    if ($alert["latitude"] == 0 and $alert["longitude"] == 0) {
        echo "    <th width=\"5%\"><img src=\"img/question.svg\" style=\"height:50px;\"></th>";
    } else {
        echo "    <th width=\"5%\"><img src=\"img/arrow.svg\" style=\"height:50px;transform: rotate(" . $alert["direction"] . "deg);\"></th>";
    }
    echo "    <th width=\"40%\">";
    echo "        <h4>Aircraft</h4>";
    echo "        <p>" . $alert["id"] . "</p>";
    echo "    </th>";
    echo "    <th width=\"25%\">";
    if ($alert["latitude"] == 0 and $alert["longitude"] == 0) {
        echo "        <p>Distance unknown</p>";
    } else {
        echo "        <p>" . round($alert["distance"]*100)/100 . " miles</p>";
    }
    echo "        <p>" . $alert["altitude"] . " feet</p>";
    echo "    </th>";
    echo "    <th width=\"25%\">";
    echo "        <p>" . $alert["speed"] . " knots</p>";
    echo "        <p>" . $alert["threatlevel"] . "/3</p>";
    echo "    </th>";
    echo "</tr></table>";
}




// Display ALPR camera alerts.
// Purple
foreach ($last_alert["alpr"] as $key => $alert) { // Iterate through each time-based attention alert.
    echo "<table class=\"alert purple\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/alpr.svg\" style=\"height:50px;transform:rotate(" . $alert["relativefacing"] . "deg);\"></th>";
    echo "    <th width=\"5%\"><img src=\"img/arrow.svg\" style=\"height:50px;transform: rotate(" . $alert["direction"] . "deg);\"></th>";
    echo "    <th width=\"40%\">";
    echo "        <h4>ALPR Camera</h4>";
    echo "    </th>";
    echo "    <th width=\"50%\">";
    echo "        <p>" . round($alert["distance"]*100)/100 . " miles</p>";
    echo "        <p>" . $alert["road"] . "</p>";
    echo "    </th>";
    echo "</tr></table>";
}


// Display weather alerts.
// Orange
foreach ($last_alert["weather"] as $key => $alert) { // Iterate through each time-based attention alert.
    echo "<table class=\"alert orange\"><tr>";
    echo "    <th width=\"5%\"><img src=\"img/alerts/weather.svg\" style=\"height:50px;;\"></th>";
    if ($key == "visibility") {
        echo "    <th width=\"40%\">";
        echo "        <h4>Weather</h4>";
        echo "        <p>Visibility</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>Visibility is " . $alert[0] . " meters</p>";
        echo "    </th>";
    } else if ($key == "temperature") {
        echo "    <th width=\"40%\">";
        echo "        <h4>Weather</h4>";
        echo "        <p>Temperature</p>";
        echo "    </th>";
        echo "    <th width=\"50%\">";
        echo "        <p>Temperature is " . $alert[0] . " Celcius</p>";
        echo "    </th>";
    }
    echo "</tr></table>";
}


?>
