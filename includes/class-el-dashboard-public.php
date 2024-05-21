<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       web-hike.com
 * @since      1.0.0
 *
 * @package    El_Dashboard
 * @subpackage El_Dashboard/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    El_Dashboard
 * @subpackage El_Dashboard/public
 * @author     Khem <khemrajsharmawh@gmail.com>
 */
class El_Dashboard_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in El_Dashboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The El_Dashboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/el-dashboard-public.css', array(), date("YmdHi"), 'all' );
		wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css', array(), '4.6.1', 'all');
		//wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), '5.0.2', 'all');
		wp_enqueue_style( $this->plugin_name.'feather', plugin_dir_url( __FILE__ ) . 'vendors/feather/feather.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'ti-icons', plugin_dir_url( __FILE__ ) . 'vendors/ti-icons/css/themify-icons.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'vendor-bundle', plugin_dir_url( __FILE__ ) . 'vendors/css/vendor.bundle.base.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'datatables', plugin_dir_url( __FILE__ ) . 'vendors/datatables.net-bs4/dataTables.bootstrap4.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'themify-icons', plugin_dir_url( __FILE__ ) . 'vendors/ti-icons/css/themify-icons.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'datatables-min', plugin_dir_url( __FILE__ ) . 'js/select.dataTables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'vertical-layout-light', plugin_dir_url( __FILE__ ) . 'css/vertical-layout-light/style.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'materialdesignicons', plugin_dir_url( __FILE__ ) . 'vendors/mdi/css/materialdesignicons.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'toast.min', plugin_dir_url( __FILE__ ) . 'vendors/jquery-toast-plugin/jquery.toast.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'tinyPlayer.min', plugin_dir_url( __FILE__ ) . 'vendors/tinyPlayer/tinyPlayer.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'plusplauer_stylised', plugin_dir_url( __FILE__ ) . 'vendors/plusplayer/css/stylised.css', array(), $this->version, 'all' );

	}
	
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in El_Dashboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The El_Dashboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/el-dashboard-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js', array( ), '4.6.1', true );
		//wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array( ), '5.0.2', true );	
		wp_enqueue_script( $this->plugin_name.'vendor-bundle-base', plugin_dir_url( __FILE__ ) . 'vendors/js/vendor.bundle.base.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'chart-min', plugin_dir_url( __FILE__ ) . 'vendors/chart.js/Chart.min.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'jquery-datatables', plugin_dir_url( __FILE__ ) . 'vendors/datatables.net/jquery.dataTables.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'jquery-datatables', plugin_dir_url( __FILE__ ) . 'vendors/datatables.net/jquery.dataTables.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'bootstrap4-datatables', plugin_dir_url( __FILE__ ) . 'vendors/datatables.net-bs4/dataTables.bootstrap4.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'datatables-select-min', plugin_dir_url( __FILE__ ) . 'js/dataTables.select.min.js', array( ), $this->version, false );		
		wp_enqueue_script( $this->plugin_name.'off-canvas', plugin_dir_url( __FILE__ ) . 'js/off-canvas.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'hoverable-collapse', plugin_dir_url( __FILE__ ) . 'js/hoverable-collapse.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'template-js', plugin_dir_url( __FILE__ ) . 'js/template.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'settings-js', plugin_dir_url( __FILE__ ) . 'js/settings.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'todolist', plugin_dir_url( __FILE__ ) . 'js/todolist.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'dashboard', plugin_dir_url( __FILE__ ) . 'js/dashboard.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'roundedBarCharts', plugin_dir_url( __FILE__ ) . 'js/Chart.roundedBarCharts.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'jquery.toast', plugin_dir_url( __FILE__ ) . 'vendors/jquery-toast-plugin/jquery.toast.min.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'howler.min', plugin_dir_url( __FILE__ ) . 'vendors/tinyPlayer/howler.min.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'tinymce', plugin_dir_url( __FILE__ ) . 'vendors/tinymce/tinymce.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'plusplayer', plugin_dir_url( __FILE__ ) . 'vendors/plusplayer/plusplayer.js', array( ), $this->version, false );
		// wp_enqueue_script( $this->plugin_name.'tinyPlayer.min', plugin_dir_url( __FILE__ ) . 'vendors/tinyPlayer/tinyPlayer.min.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'language', plugin_dir_url( __FILE__ ) . 'js/language.js', array( ), date("YmdHi"), false );
		wp_enqueue_script( $this->plugin_name.'plus', plugin_dir_url( __FILE__ ) . 'js/plus.js', array( ), date("YmdHi"), false );
	}

}

require_once("commonfunctions.php");
require_once("partials/includes/languageselector.php");
require_once("partials/includes/navbar.php");
require_once("partials/includes/sidebar.php");
require_once("partials/includes/settings-panel.php");
require_once("partials/includes/footer.php");

require_once ("partials/el-dashboard-public-display.php");
require_once ("partials/el-dashboard-public-display-dashboard.php");
require_once ("partials/el-dashboard-public-display-users.php");
require_once ("partials/el-dashboard-public-display-adduser.php");
require_once ("partials/el-dashboard-public-display-addteacher.php");
require_once ("partials/el-dashboard-public-display-addgroup.php");
require_once ("partials/el-dashboard-public-display-addhomework.php");
require_once ("partials/el-dashboard-public-display-teachers.php");
require_once ("partials/el-dashboard-public-display-groups.php");
require_once ("partials/el-dashboard-public-display-groupdetails.php");
require_once ("partials/el-dashboard-public-display-homework.php");
require_once ("partials/el-dashboard-public-display-homework-report.php");
require_once ("partials/el-dashboard-public-display-assessmentreport1.php");
require_once ("partials/el-dashboard-public-display-assessmentreport2.php");
require_once ("partials/el-dashboard-public-display-assessmentreport3.php");
require_once ("partials/el-dashboard-public-display-dashboardstatus-report.php");
require_once ("partials/el-dashboard-public-display-school-weekly-report.php");
require_once ("partials/el-dashboard-public-display-reports.php");
require_once ("partials/el-dashboard-public-display-scorecard.php");
require_once ("partials/el-dashboard-public-display-student-scorecard.php");
require_once ("partials/el-dashboard-public-display-addstudents.php");



require_once ("partials/el-dashboard-public-display-subscription_setting.php");
require_once ("partials/el-dashboard-public-display-studentsubscription.php");
require_once ("partials/el-dashboard-public-display-paymentTransection.php");
require_once ("partials/el-dashboard-public-display-printpassword.php");
require_once ("partials/el-dashboard-public-display-accountant.php");
require_once ("partials/el-dashboard-public-display-student-restriction.php");
require_once ("partials/el-dashboard-public-display-overridesubscription.php");
require_once ("partials/el-dashboard-public-display-student-profile-filter.php");
require_once ("partials/el-dashboard-public-display-student-profile.php");
require_once ("partials/el-dashboard-public-display-class-profile.php");
require_once ("partials/el-dashboard-public-display-class-profile-filter.php");
require_once ("partials/el-dashboard-public-display-studentdatesubscription.php");
require_once ("partials/el-dashboard-public-display-monthlyreport.php");
require_once ("partials/el-dashboard-public-common-page.php");
