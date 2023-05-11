# Changelog

This document contains a list of all the changes for each version of Marksman.


## Version 0.9

### Initial Release

March 22nd, 2023

- Core functionality


## Version 1.0

### First Stable Release

*Release date to be determined*

- Added refresh delay configuration option.
- Add question mark icon to replace directional icons when there isn't enough information to display accurate data.
- Updated GPS alert handling.
    - GPS over-speed alerts are now rounded to 2 decimal places.
    - Added support for frozen GPS alerts.
    - Added support for GPS diagnostic alerts.
- The start.sh script placeholder is now created when the index pages loads, rather than the first time the "Start" button is pressed.
- Refined permissions verification process.
- Fixed a problem where Marksman would encounter a fatal error if the Assassin interface directory was missing.
- The connected Assassin instance configuration can now be modified directly from the Marksman settings page.
    - Commonly modified settings can be changed from the graphical interface.
- Made the interface more compact for better usability on smaller displays.
- Removed an improper reference to Predator in an input placeholder.
- Added auto-fill to the "Interface Directory" field when left blank.
    - If no interface directory is provided, Marksman will attempt to pull it from the instance configuration file.
- Added logo as the web-page favicon.
- Updated aircraft alert display.
    - Aircraft alerts now display the aircraft's identifier.
    - Aircraft alerts that are missing directional information are now more resilient.
- Added the ability to redirect Assassin's console output to a log file for debugging.
- Added file management tools.
- Marksman now considers the timestamps associated with status, warning, and error messages when calculating the last heartbeat time.
- Status, warning, and error messages from Assassin are now displayed in the main dashboard.
