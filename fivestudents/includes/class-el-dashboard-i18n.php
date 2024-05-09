<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       web-hike.com
 * @since      1.0.0
 *
 * @package    El_Dashboard
 * @subpackage El_Dashboard/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    El_Dashboard
 * @subpackage El_Dashboard/includes
 * @author     Khem <khemrajsharmawh@gmail.com>
 */
class El_Dashboard_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		// Assuming your language files are located in a 'languages' directory relative to your current script
		$language_file_path = dirname(__FILE__) . '/languages/el-dashboard.pot';

		// Include the language file
		if (file_exists($language_file_path)) {
		    include_once $language_file_path;
		}


	}



}
