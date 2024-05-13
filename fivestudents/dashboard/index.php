<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/partials/includes/header.php');
require_once($CFG->dirroot . '/partials/includes/navbar.php');
require_once($CFG->dirroot . '/partials/includes/settings-panel.php');
require_once($CFG->dirroot . '/partials/includes/sidebar.php');
require_once($CFG->dirroot . '/partials/includes/footer.php');
require_once($CFG->dirroot . '/pages/main-panel.php');
require_once($CFG->dirroot . '/partials/includes/moodlesession.php');

$header_el = main_header();
$navbar_el = navbar();
$settings_panel_el = settings_panel();
$sidebar_el = sidebar();
$main_panel_el = main_panel();
$footer_el = main_footer();

echo '
	   '.$header_el.'
	 	 <div class="container-scroller">
			'.$navbar_el.'   
				<!-- Navbar partial end -->
					<div class="container-fluid page-body-wrapper">
						<!-- partial:settings-panel.php -->
						'.$settings_panel_el.'
						<!-- partial -->
						<!-- partial:sidebar.php -->
						'.$sidebar_el.'
						<!-- partial -->
						<div class="main-panel">
						<div class="content-wrapper">
						'.plus_checkerror().'
						'.$main_panel_el.'
						</div> 
						<!-- content-wrapper ends -->
						<!-- partial:footer.php -->
						<div class="pageloading"><img src="'.$CFG->wwwroot.'/images/ajax-loader-white.gif"></div>
				        <footer class="footer">
				          <div class="d-sm-flex justify-content-center justify-content-sm-between">
				            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> © '.date("Y").'.  <a href="https://plus.fivestudents.com/" target="_blank">fivestudents.com</a></span>
				          </div>
				        </footer>
						<!-- partial -->
					</div>
					<!-- main-panel ends -->
			</div>
		<!-- page-body-wrapper ends -->
	  </div>
	 <!-- container-scroller -->
	'.$footer_el.'
';
