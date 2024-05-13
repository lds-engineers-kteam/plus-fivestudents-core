<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/partials/includes/header.php');
require_once($CFG->dirroot . '/partials/includes/navbar.php');
require_once($CFG->dirroot . '/partials/includes/settings-panel.php');
require_once($CFG->dirroot . '/partials/includes/sidebar.php');
require_once($CFG->dirroot . '/partials/includes/footer.php');
require_once($CFG->dirroot . '/pages/plus-view-disablecourse.php');


// plus_startMoodleSession();

main_header();
// // navbar();
// // settings_panel();
// // sidebar();
// // plus_view_noaccess();
plus_view_disablecourse();
main_footer();


