<?php
require_once("../config.php");
global $DB, $PAGE, $CFG, $USER, $OUTPUT, $INSTITUTION;
require_login();
$OUTPUT->loadjquery();
echo $OUTPUT->header();
echo phpinfo();
// echo "<pre>";
// print_r($LOCAL);
// echo "</pre>";
echo $OUTPUT->footer();