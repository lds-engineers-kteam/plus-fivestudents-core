<?php
function plus_view_groups_page(){
	global $MOODLESESSION;
	require_once(plugin_dir_path(__DIR__)."/partials/includes/moodlesession.php");
	require_once(plugin_dir_path(__DIR__)."/pages/plus-view-groups.php");
	$navbar_el = navbar();
	$settings_panel_el = settings_panel();
	$sidebar_el = sidebar();
	$main_panel_el = plus_view_groups();
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

function loggedin_check_plus_view_groups(){

	if ( is_user_logged_in() && current_user_can('plus_viewgroups')) {
		return plus_view_groups_page();
	}else{
		return plus_redirect(site_url().'/');
		// return '<script> location.href="'. site_url().'/"; </script>';
	}
}
 add_shortcode('plus-view-groups','loggedin_check_plus_view_groups');