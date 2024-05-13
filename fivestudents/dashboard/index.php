<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/partials/includes/header.php');
require_once($CFG->dirroot . '/partials/includes/navbar.php');
require_once($CFG->dirroot . '/partials/includes/settings-panel.php');
require_once($CFG->dirroot . '/partials/includes/sidebar.php');
require_once($CFG->dirroot . '/partials/includes/footer.php');
require_once($CFG->dirroot . '/pages/main-panel.php');
require_once($CFG->dirroot . '/pages/plus-view-adduser.php');


// plus_getUsermeta();

main_header();
navbar();
// settings_panel();
// sidebar();
// plus_startMoodleSession();
main_panel();
main_footer();