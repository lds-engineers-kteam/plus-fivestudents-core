<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/partials/includes/header.php');
require_once($CFG->dirroot . '/partials/includes/navbar.php');
require_once($CFG->dirroot . '/partials/includes/settings-panel.php');
require_once($CFG->dirroot . '/partials/includes/sidebar.php');
require_once($CFG->dirroot . '/partials/includes/footer.php');
require_once($CFG->dirroot . '/partials/includes/moodlesession.php');

echo '
	   '.main_header().'
	 	 <div class="container-scroller">
			'.navbar().'   
				<!-- Navbar partial end -->
					<div class="container-fluid page-body-wrapper">
						<!-- partial:settings-panel.php -->
						'.settings_panel().'
						<!-- partial -->
						<!-- partial:sidebar.php -->
						'.sidebar().'
						<!-- partial -->
						<div class="main-panel">
						<div class="content-wrapper">
						  '.plus_checkerror().'
						  
						  '.'
						<div class="col-md-12 grid-margin stretch-card">
			              	<div class="card" style="min-height: 60vh;">
			                	<div class="card-body onlybtns">
			                	'.(current_user_can('manage_plusscorecard')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/school-weekly-report/">'.plus_get_string("school_weekly_report", "site").'</a>':'').'  
			            		'.(current_user_can('manage_plusscorecard')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/scorecard/">'.plus_get_string("scorecard", "site").'</a>':'').' 
			            		'.(current_user_can('manage_plusstudentscorecard')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/student-score-card/">'.plus_get_string("studentrankingreport", "site").'</a>':'').' 
			            		'.(current_user_can('manage_plusstudentscorecard')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/users-report/">'.plus_get_string("userreport", "site").'</a>':'').'
			            		'.(current_user_can('manage_plusstudentprofile')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/student-profile-filter/">'.plus_get_string("studentprofile", "site").'</a>':'').'
			            		'.(current_user_can('manage_plusclassprofile')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/class-profile-filter/">'.plus_get_string("classprofile", "site").'</a>':'').'
			            		'.(current_user_can('manage_plusclassprofilecompetency')?'<a class="btn btn-primary mb-10" href="'.$CFG->wwwroot.'/class-profile-competency-filter/">Class Profile Competency</a>':'').'
			                	</div>
			            	</div>
			        	</div>
						 	'.'
						  
						</div>  
						<!-- content-wrapper ends -->
						<!-- partial:footer.php -->
						<div class="pageloading"><img src="'.$CFG->wwwroot.'/images/ajax-loader-white.gif"></div>
				        <footer class="footer">
				          <div class="d-sm-flex justify-content-center justify-content-sm-between">
				            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> © '.date("Y").'.  <a href="'.$CFG->wwwroot.'" target="_blank">fivestudents.com</a></span>
				          </div>
				        </footer>
						<!-- partial -->
					</div>
					<!-- main-panel ends -->
			</div>
		<!-- page-body-wrapper ends -->
	  </div>
	  <!-- container-scroller -->
	'.main_footer().'
';
