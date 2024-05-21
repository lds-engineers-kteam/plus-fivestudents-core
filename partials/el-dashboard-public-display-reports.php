<?php
function plus_view_reports_page(){
	global $MOODLESESSION,$CFG;
	require_once($CFG->dirroot ."\partials\includes\moodlesession.php");
	$navbar_el = navbar();
	$settings_panel_el = settings_panel();
	$sidebar_el = sidebar();
	$footer_el = footer();
	
	return '<div class="container-scroller">
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
			  
			  '.'
			<div class="col-md-12 grid-margin stretch-card">
              	<div class="card" style="min-height: 60vh;">
                	<div class="card-body onlybtns">
                	'.(current_user_can('manage_plusscorecard')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/school-weekly-report/">'.plus_get_string("school_weekly_report", "site").'</a>':'').'  
            		'.(current_user_can('manage_plusscorecard')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/scorecard/">'.plus_get_string("scorecard", "site").'</a>':'').' 
            		'.(current_user_can('manage_plusstudentscorecard')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/student-score-card/">'.plus_get_string("studentrankingreport", "site").'</a>':'').' 
            		'.(current_user_can('manage_plusstudentscorecard')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/users-report/">'.plus_get_string("userreport", "site").'</a>':'').'
            		'.(current_user_can('manage_plusstudentprofile')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/student-profile-filter/">'.plus_get_string("studentprofile", "site").'</a>':'').'
            		'.(current_user_can('manage_plusclassprofile')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/class-profile-filter/">'.plus_get_string("classprofile", "site").'</a>':'').'
            		'.(current_user_can('manage_plusclassprofilecompetency')?'<a class="btn btn-primary mb-10" href='.$CFG->wwwroot.'"/class-profile-competency-filter/">Class Profile Competency</a>':'').'
                	</div>
            	</div>
        	</div>
			 	'.'
			  
			</div> 
			<!-- content-wrapper ends -->
			<!-- partial:footer.php -->
			'.$footer_el.'
			<!-- partial -->
		  </div>
		  <!-- main-panel ends -->
		</div>
		<!-- page-body-wrapper ends -->
	  </div>
	  <!-- container-scroller -->
	 ';
}

