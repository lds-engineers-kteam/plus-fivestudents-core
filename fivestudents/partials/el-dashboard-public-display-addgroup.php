<?php
function plus_view_addgroup_page(){
	global $MOODLESESSION;
	require_once($CFG->dirroot ."\partials\includes\moodlesession.php");
	require_once($CFG->dirroot ."\pages\plus-view-addgroup.php");
	$navbar_el = navbar();
	$settings_panel_el = settings_panel();
	$sidebar_el = sidebar();
	$main_panel_el = plus_add_group();
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
			  '.$main_panel_el.'
			  
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

function loggedin_check_plus_add_group(){

	if ( is_user_logged_in() && current_user_can('plus_addgroups')) {
		return plus_view_addgroup_page();
	}else{
		return plus_redirect(site_url().'/');
		 // '<script> location.href="'. site_url().'/"; </script>';
	}
}
