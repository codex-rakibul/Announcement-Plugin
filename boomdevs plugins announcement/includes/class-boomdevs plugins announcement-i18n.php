<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://boomdevs.com
 * @since      1.0.0
 *
 * @package    Boomdevs_Plugins_Announcement
 * @subpackage Boomdevs_Plugins_Announcement/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Boomdevs_Plugins_Announcement
 * @subpackage Boomdevs_Plugins_Announcement/includes
 * @author     BoomDevs <admin@boomdevs.com>
 */
class Boomdevs_Plugins_Announcement_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'boomdevs plugins announcement',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
