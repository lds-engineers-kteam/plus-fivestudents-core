<?php
function main_dashbaord(){
	global $MOODLESESSION;
	
	require_once __DIR__ . "/partials/includes/moodlesession.php";
	require_once __DIR__ . "/pages/main-panel.php";

	$navbar_el = navbar();
	$settings_panel_el = settings_panel();
	$sidebar_el = sidebar();
	$main_panel_el = main_panel();
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

function loggedin_user_check(){

	if ( is_user_logged_in() && is_page('user-dashboard') ) {
		return main_dashbaord();
	}else{
		return plus_redirect(site_url().'/login');
		// return '<script> location.href="'. site_url().'/login"; </script>';
	}
}
 add_shortcode('dashboard','loggedin_user_check');