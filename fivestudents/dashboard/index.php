<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/pages/main-panel.php');
require_once($CFG->dirroot . '/partials/includes/header.php');
require_once($CFG->dirroot . '/partials/includes/footer.php');
require_once($CFG->dirroot . '/pages/main-panel.php');
// echo $CFG->dirroot . '/pages/main-panel.php';
// echo "<br>";
// echo $CFG->dirroot . '/partials/includes/header.php';

// require_once($CFG->dirroot . '/partials/includes/navbar.php');
// require_once($CFG->dirroot . '/partials/includes/settings-panel.php');
// require_once($CFG->dirroot . '/partials/includes/sidebar.php');

main_header();
// $navbar_el = navbar();
// $settings_panel_el = settings_panel();
// $sidebar_el = sidebar();
// $main_panel_el = plus_view_noaccess();
main_panel();
main_footer();