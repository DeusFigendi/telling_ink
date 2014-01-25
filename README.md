telling_ink
===========

Website generating all content direct from auphonic

Okay, this project is ment to generate a podcast-website without any backend.

## Requirements

An SQL-Database is needed and PHP (5 I think) libgd

## How it should work

Throw a production-file and any other file auphonic generates into the /TEMP/-dir and recall the website a few times. A new episode should apear.

## Files

### Filenaming

I used to use some prefixes for the files:

* co_ means "content" this file generates visible content for the website and writes it to $content
* db_ means "database" this file does something general with the database
* rd_ means "random" one of these files should be called on every page-call
* ut_ means "utility" this file dose something usefull and might be called if needed.

### Directories

* /audio/ contains the audiofiles
* /chapters/ contains chapterfiles (surprise)
* /fonts/ contains webfonts
* /images/ contains both: images for the website as waveforms and covers
* /json/ contains the productionfiles from auphonic
* /TEMP/ temporary contains all files auphonic generates
* /unknown_files/ stuff from TEMP this script couldn't decide what to do with this file

### Single files:

* api.php allows people to get information automaticly
* db_connect.php creates the database conection and checks if the user may view secret episodes (you must edit and rename this file)
* db_functions.php contains some functions that do calls on the database (eg. "find all contributers related to episodes in boox foo")
* deep_link.js allows podloves deeplinking to episodes: http://podlove.org/deep-link/
* episode_switch1.js generates a "table of content" for a book or something similar and allows the user to listen to all episodes (because it automaticly switches to the next episode)
* extend_player.js generates a beautyfull javascript-player instead of the native one from the browser.
* feed.php generates the RSS-feeds
* hidden_episodes.js generates a link at the bottom of the page where the user can type in the password to get secret episodes
* rd_checkflattr.php does nothing at the moment, will check how much flattering was done for this "thing"
* rd_checknew.php checks if there a new episodes in /TEMP/ and parses the productionfile shifts the files and fills the database
* rd_removespam removes some comments marked as spam from the database
* ut_generate_waveform.php generates a "pencil style" waveform from the original waveform by auphonic.
