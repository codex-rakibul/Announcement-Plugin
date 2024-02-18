<?php

/**
 * Fired during plugin activation
 *
 * @link       https://boomdevs.com
 * @since      1.0.0
 *
 * @package    Boomdevs_Plugins_Announcement
 * @subpackage Boomdevs_Plugins_Announcement/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Boomdevs_Plugins_Announcement
 * @subpackage Boomdevs_Plugins_Announcement/includes
 * @author     BoomDevs <admin@boomdevs.com>
 */
class Boomdevs_Plugins_Announcement_Custom_Post {

    /**
     * Register the custom post type.
     *
     * @since 1.0.0
     */
    public static function register_custom_post_type() {
        $labels = array(
            'name'               => _x('Announcements', 'post type general name', 'boomdevs-plugins-announcement'),
            'singular_name'      => _x('Announcement', 'post type singular name', 'boomdevs-plugins-announcement'),
            'menu_name'          => _x('Announcements', 'admin menu', 'boomdevs-plugins-announcement'),
            'name_admin_bar'     => _x('Announcement', 'add new on admin bar', 'boomdevs-plugins-announcement'),
            'add_new'            => _x('Add New', 'announcement', 'boomdevs-plugins-announcement'),
            'add_new_item'       => __('Add New Announcement', 'boomdevs-plugins-announcement'),
            'new_item'           => __('New Announcement', 'boomdevs-plugins-announcement'),
            'edit_item'          => __('Edit Announcement', 'boomdevs-plugins-announcement'),
            'view_item'          => __('View Announcement', 'boomdevs-plugins-announcement'),
            'all_items'          => __('All Announcements', 'boomdevs-plugins-announcement'),
            'search_items'       => __('Search Announcements', 'boomdevs-plugins-announcement'),
            'not_found'          => __('No announcements found', 'boomdevs-plugins-announcement'),
            'not_found_in_trash' => __('No announcements found in Trash', 'boomdevs-plugins-announcement'),
        );
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'announcement'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
            'menu_icon'          => 'dashicons-megaphone',
        );
        register_post_type('announcement', $args);
    }

}

