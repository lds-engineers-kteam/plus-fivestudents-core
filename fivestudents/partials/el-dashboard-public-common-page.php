<?php
function plus_view_common_page( $atts, $content = null ){
	global $MOODLESESSION;
	require_once(plugin_dir_path(__DIR__)."/partials/includes/moodlesession.php");
	$navbar_el = navbar();
	$settings_panel_el = settings_panel();
	$sidebar_el = sidebar();
	$footer_el .= footer();
	$main_panel_el = plus_view_noaccess();
	$page = (isset($atts['page'])?$atts['page']:'');
	switch ($page) {
		case 'training':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-trainings.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-trainings.php");
				$main_panel_el = plus_view_trainings();
			}
			break;
		case 'resources':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-resources.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-resources.php");
				$main_panel_el = plus_view_resources();
			}
			break;
		case 'resourcedetails':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-resourcedetails.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-resourcedetails.php");
				$main_panel_el = plus_view_resourcedetails();
			}
			break;
		case 'verificationcode':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-verificationcode.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-verificationcode.php");
				$main_panel_el = plus_view_verificationcode();
			}
			break;
		case 'importuser':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-importuser.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-importuser.php");
				$main_panel_el = plus_view_importuser();
			}
			break;
		case 'calendar':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-calendar.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-calendar.php");
				$main_panel_el = plus_view_calendar();
			}
			break;
		case 'addevent':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-addevent.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-addevent.php");
				$main_panel_el = plus_view_addevent();
			}
			break;
		case 'globalusers':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-globalusers.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-globalusers.php");
				$main_panel_el = plus_view_globalusers();
			}
			break;
		case 'addglobaluser':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-addglobaluser.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-addglobaluser.php");
				$main_panel_el = plus_view_addglobaluser();
			}
			break;
		case 'globaluserteacher':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-globaluserteacher.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-globaluserteacher.php");
				$main_panel_el = plus_view_globaluserteacher();
			}
			break;
		case 'surveys':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-surveys.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-surveys.php");
				$main_panel_el = plus_view_surveys();
			}
			break;
		case 'addsurvey':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-addsurvey.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-addsurvey.php");
				$main_panel_el = plus_view_addsurvey();
			}
			break;
		case 'viewsurvey':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-viewsurvey.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-viewsurvey.php");
				$main_panel_el = plus_view_viewsurvey();
			}
			break;
		case 'events':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-events.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-events.php");
				$main_panel_el = plus_view_events();
			}
			break;
		case 'claims':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-claims.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-claims.php");
				$main_panel_el = plus_view_claims();
			}
			break;
		case 'eventmanagement':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-eventmanagement.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-eventmanagement.php");
				$main_panel_el = plus_view_eventmanagement();
			}
			break;
		case 'notifications':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-notifications.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-notifications.php");
				$main_panel_el = plus_view_notifications();
			}
			break;
		case 'sushiltest':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-sushiltest.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-sushiltest.php");
				$main_panel_el = plus_view_sushiltest();
			}
			break;
		case 'disablecourse':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-disablecourse.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-disablecourse.php");
				$main_panel_el = plus_view_disablecourse();
			}
			break;
		case 'enablecourse':
			if(file_exists(plugin_dir_path(__DIR__)."/pages/plus-view-enablecourse.php")){
				require_once(plugin_dir_path(__DIR__)."/pages/plus-view-enablecourse.php");
				$main_panel_el = plus_view_enablecourse();
			}
			break;
	}
	$html = '<div class="container-scroller">
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
	 return $html;
}
add_shortcode('plus-view-common-page','plus_view_common_page');