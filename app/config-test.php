<?php

// Error reporting — show all errors in development.
// On production, set display_errors to 0 and log to a file instead.
error_reporting(E_ALL);
ini_set('display_errors', '1');

// MySQL
define('DB_HOST', 'HOST');
define('DB_NAME', 'DB');
define('DB_USERNAME', 'USER');
define('DB_PASSWORD', 'pass');

// Debug
define('APP_DEBUG', false);

// YouTube
define('YOUTUBE_KEY', 'KEY');
define('YOUTUBE_USER', 'CaseyNeistat');
define('START_DATE', '2015-03-26');
