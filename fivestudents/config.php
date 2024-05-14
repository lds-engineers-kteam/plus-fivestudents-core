<?php
// Unset and initialize global variable $CFG
unset($CFG);
global $CFG;
$CFG = new stdClass();

// Set configuration properties
$CFG->wwwroot = "http://localhost/plus/fivestudents";
$CFG->apiroot = "https://portal.fivestudents.com";
$CFG->wproot = "https://plus.fivestudents.com";
$CFG->key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
$CFG->syncapicount = 5000;
$CFG->dirroot = $_SERVER['DOCUMENT_ROOT'] . '/plus/fivestudents'; 

// Suppress error reporting and set timezone
// error_reporting(1);
// error_reporting(E_ALL);
// ini_set('display_errors', 'Off');
// ini_set('max_input_vars', 10000);
date_default_timezone_set('Africa/Malabo');

// Check if wwwroot is configured
if (!isset($CFG->wwwroot)) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    }
    echo 'Fatal error: $CFG->wwwroot is not configured! Exiting.'."\n";
    exit(1);
}

session_start();
// Initialize global variables
global $DB, $USER, $PAGE, $OUTPUT, $API, $LOCAL, $CURRENTLANG, $CURRENTUSERSESSION, $MOODLESESSION;
$CURRENTLANG = "EN";
$errormessage = "";
require_once(__DIR__."/api/moodlecall.php");
require_once(__DIR__."/lib.php");
require_once(__DIR__."/partials/includes/languageselector.php");
base_init();

