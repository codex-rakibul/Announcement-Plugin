<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://boomdevs.com
 * @since             1.0.0
 * @package           Boomdevs_Plugins_Announcement
 *
 * @wordpress-plugin
 * Plugin Name:       BoomDevs Plugins Announcement
 * Plugin URI:        https://boomdevs.com
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            BoomDevs
 * Author URI:        https://boomdevs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       boomdevs plugins announcement
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOOMDEVS_PLUGINS_ANNOUNCEMENT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-boomdevs plugins announcement-activator.php
 */
function activate_boomdevs_plugins_announcement() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boomdevs plugins announcement-activator.php';
	Boomdevs_Plugins_Announcement_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-boomdevs plugins announcement-deactivator.php
 */
function deactivate_boomdevs_plugins_announcement() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boomdevs plugins announcement-deactivator.php';
	Boomdevs_Plugins_Announcement_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_boomdevs_plugins_announcement' );
register_deactivation_hook( __FILE__, 'deactivate_boomdevs_plugins_announcement' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-boomdevs plugins announcement.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_boomdevs_plugins_announcement(){

	$plugin = new Boomdevs_Plugins_Announcement();
	$plugin->run();

}
run_boomdevs_plugins_announcement();

// -----------REST API--------------
// Fetch all announcement posts data
function get_all_announcement_posts_data(){
    $all_posts = get_posts(array(
        'post_type'      => 'announcement',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    $formatted_posts = array();

    foreach($all_posts as $post){
        // Fetching the plugin logo ID from the custom field 'plugin_logo'
        $plugin_logo_id = get_post_meta($post->ID, 'plugin_logo', true);

        // Getting the image URL using the attachment ID
        $plugin_logo_src = wp_get_attachment_image_src($plugin_logo_id, 'full');
        $plugin_logo_url = isset($plugin_logo_src[0]) ? $plugin_logo_src[0] : '';

        // HTML attributes
        $button_html = '<a href="[[link]]" style="color:[[button-color]]; font-size:[[button-text]]px;">[[button]]</a>';
        $token_html = '<span style="color:[[token-color]]; font-size:[[token-font-size]]px;">[[token_code]]</span>';
        
        // Calling prepare_notice_data function to replace placeholders
        $announcement_notice = announcement_notice_data(
            $post->announcement_notice,
            $post->coupon_code,
            $post->button_text,
            $post->button_url,
            $button_html,
            $token_html,
            $post->coupon_code_font_size,
            $post->coupon_code_color,
            $post->button_text_size,
            $post->button_text_color
        );

        // Building the post data array
        $post_data = array(
            'ID'                     => $post->ID,
            'post_title'             => $post->post_title,
            'post_plugin_logo'       => $plugin_logo_url,
            'available_offering'     => $post->available_offering,
            'coupon_code'            => $post->coupon_code,  
            'button_text'            => $post->button_text,
            'button_url'             => $post->button_url,
            'cancel_button_text'     => $post->cancel_button_text,
            'show_logo'              => $post->show_logo,
            'show_notice_title'      => $post->show_notice_title,
            'show_notice_content'    => $post->show_notice_content,
            'show_primary_button'    => $post->show_primary_button,
            'show_cancel_button'     => $post->show_cancel_button,
            'announcement_notice'    => wp_kses_post($announcement_notice),
            'post_meta'              => get_post_meta($post->ID),
        );

        $formatted_posts[] = $post_data;
    }

    return $formatted_posts;
}

// Register REST API endpoint
function register_custom_rest_announcement(){
    register_rest_route(
        'custom-announcement-api/v1',
        '/all_announcement_post',
        array(
            'methods'             => 'GET',
            'callback'            => 'get_all_announcement_posts_data',
            'permission_callback' => '__return_true',
        )
    );
}
add_action('rest_api_init', 'register_custom_rest_announcement');

function announcement_notice_data(
    $notice_data, $token = null, 
    $button_text = null, 
    $button_link = null, 
    $button_html = null, 
    $token_html = null, 
    $coupon_code_font_size = null,
    $coupon_code_color = null, 
    $button_text_size = null,
    $button_text_color = null
){
    if($token != null){
        $token_html = str_replace('[[token-color]]', $coupon_code_color, $token_html);
        $token_html = str_replace('[[token-font-size]]', $coupon_code_font_size, $token_html);
        $token_html = str_replace('[[token_code]]', $token, $token_html);
        $notice_data = str_replace('[[token]]', $token_html, $notice_data);
    }
    if($button_html != null){
        $button_html = str_replace('[[button-color]]', $button_text_color, $button_html);
        $button_html = str_replace('[[button-text]]', $button_text_size, $button_html);
        $button_html = str_replace('[[link]]', $button_link, $button_html);
        $button_html = str_replace('[[button]]', $button_text, $button_html);
        $notice_data = str_replace('[[button_html]]', $button_html, $notice_data);
    }
    return $notice_data;
}
