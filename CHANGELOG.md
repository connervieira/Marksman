# Changelog

This document contains a list of all the changes for each version of Marksman.


## Version 0.9

### Initial Release

March 22nd, 2023

- Core functionality


## Version 1.0

### First Stable Release

December 10th, 2023

- Added refresh delay configuration option.
- Add question mark icon to replace directional icons when there isn't enough information to display accurate data.
- Updated GPS alert handling.
    - GPS over-speed alerts are now rounded to 2 decimal places.
    - Added support for frozen GPS alerts.
    - Added support for GPS diagnostic alerts.
- Refined permissions verification process.
- The connected Assassin instance configuration can now be modified directly from the Marksman settings page.
    - Commonly modified settings can be changed from the graphical interface.
- Made the interface more compact for better usability on smaller displays.
- Removed an improper reference to Predator in an input placeholder.
- Added logo as the web-page favicon.
- Updated aircraft alert display.
    - Aircraft alerts now display the aircraft's identifier.
    - Aircraft alerts that are missing directional information are now more resilient.
- Added the ability to redirect Assassin's console output to a log file for debugging.
- Added file management tools.
- Marksman now considers the timestamps associated with status, warning, and error messages when calculating the last heartbeat time.
- Status, warning, and error messages from Assassin are now displayed in the main dashboard.
- Updated the start-stop system.
    - The "Stop" button is now always active, even when it appears disabled.
        - This allows frozen Assassin instances to be killed even when Marksman doesn't recognize them as being alive.
    - When running, the "Start" button becomes the "Restart" button, which quickly stops, then re-starts Assassin.
    - Moved the Assassin start system to a function to make the control script more organized.
    - The start.sh script placeholder is now created when the index pages loads, rather than the first time the "Start" button is pressed.
- Added the ability to switch between requiring and not requiring authentication.
    - By default, authentication is not required.
- Added vehicle on-board diagnostic alert handling.
- Improved the formatting of the file viewing tool output.
- Updated interface directory handling.
    - Fixed a problem where Marksman would encounter a fatal error if the Assassin interface directory was missing.
    - The interface is now detected automatically, and no longer needs to be set in the Marksman configuration.
- Added SystemD service management utility.
