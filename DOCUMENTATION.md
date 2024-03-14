# Documentation

This document exlains how to install, setup, and use Marksman.


## Introduction

### Terminology

Marksman and Assassin form a somewhat complex link, and it's important to understand a few terms before trying to connect them.

- The **instance** refers to the instance of Assassin that is being controlled by Marksman.
- The **controller** refers to Marksman, controlling a Assassin instance.

- The **interface directory** is a directory used by Assassin to feed information that will be read by Marksman. Think of this directory as the bridge Assassin uses to actively share information with external programs as it operates.
- The **instance directory** is the main Assassin directory, containing all of the scripts and support files used by the back-end.
- The **controller directory** is the main Marksman directory, containing all of the scripts and support files used by the front-end controller interface.

### Security

Marksman is primarily intended to be installed on a system dedicated to the usage of Assassin. As such, the following instructions often involve granting permissions without regard for the security of other applications. If you plan to install Marksman on a system running multiple services, use caution when granting very relaxed permissions.


## Installing

### Dependencies

There are a few dependencies that need to be installed for Marksman to function.

1. Install Apache, or another web-server host.
    - Example: `sudo apt-get install apache2`
2. Install and enable PHP for your web-server.
    - Example: `sudo apt-get install php; sudo a2enmod php*`
3. Restart your web-server host.
    - Example: `sudo apache2ctl restart`
4. Install Assassin.
    - Marksman requires Assassin version 2.0 or higher.
    - Assassin needs to be configured to communicate with external programs.

### Installation

After the dependencies are installed, copy the Marksman directory from the source you received it from, to the root of the root of your web-server directory.

For example: `cp ~/Downloads/Marksman /var/www/html/marksman`


## Set Up

### Permissions

For Marksman to function properly, Apache and PHP must be granted administrative rights. Without these, the controller won't be able to start and stop processes.

1. Open the sudo configuration file with the command `visudo`
2. Add the line `www-data ALL=(ALL) NOPASSWD: ALL`
3. Save the document and exit.
4. Make sure that the `marksman` directory is writable.
    - Example: `chmod 777 /var/www/html/marksman`
5. Ensure the Assassin directory (particularly the configuration file) is writable to Apache
    - Example: `chmod 777 /home/user/Software/Assassin/*`


### Connecting

After the basic set-up process is complete, you should be able to view the Marksman interface in a web browser.

1. Open a web browser of your choice.
2. Enter the URL for your Marksman installation.
    - Example: `http://192.168.0.76/marksman/`
3. At this point, you should see the main Marksman dashboard.
    - If you are shown a login page, enter the default password, `assassin`.

It should be noted that you're likely to see several errors at this point, given that Marksman hasn't been fully configured yet.


### Configuring

Once you've verified that Marksman is working as expected, you should configure it.

1. Click the "Settings" button on the main Marksman dashboard.
2. Adjust settings as necessary or desired.

The "Controller Settings" section contains settings relating to the Marksman controller interface.

- The "Password" setting specifies the password used to protect the web interface.
    - This password is not encrypted, nor is it intended to protect the security of the physical device running Marksman.
    - When this is left as a blank string, authentication is disabled, and anyone with network access to Marksman will be able to control and modify it.
- The "Heartbeat Threshold" setting determines how many seconds the instance needs to stop responding for before Marksman considers it to be inactive.
    - On slower devices, this value should be raised to prevent long processing times from causing Marksman to mistakenly believe the instance isn't running.
    - On faster devices, this value can be lowered to make the control interface more responsive.
    - It's better to err on the side of too high, since values that are too low can lead to unexpected behavior, like multiple instances running at once.
- The "Theme" setting determines the aesthetic theme that the web interface uses.
    - This setting is strictly visual, and doesn't influence functionality in any significant way.
- The "Coordinate Precision" setting determines how many decimal places Marksman will round off coordinates to.
- The "Refresh Interval" setting determines the delay (in milliseconds) between refreshes of the interface.
    - Faster values will improve responsiveness, but increase network usage.

The "Connection Settings" section contains settings relating to the connection between Marksman and the Assassin instance.

- The "Execution User" determines which user on the system Marksman will run the Assassin instance is.
    - This user does not need administrative privilege.
    - In most situations, this should be your username on the system.
        - You can determine your current user by running the `whoami` command.
- The "Instance Directory" setting should be used to specify the absolute directory path of the Assassin instance directory.


## Usage

At this point, Marksman should be fully configured, and there shouldn't be any errors on the main dashboard.

Provided Assassin is already installed and configured, you can use the "Start" and "Stop" buttons in the main interface to control it.

When Assassin is running, the heartbeat timer should reset with each processing cycle. If the heartbeat timer continuously climbs without resetting, then Assassin is not running properly.

Once Assassin is running, any alerts will be display in the bottom portion of the main Marksman dashboard.
