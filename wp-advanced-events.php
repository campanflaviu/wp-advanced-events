<?php
/*
   Plugin Name: Advanced Events
   Plugin URI: https://github.com/campanflaviu/wp-advanced-events
   Version: 0.1
   Author: Flaviu
   Description: Advanced Events
   Text Domain: wp-advanced-events
   License: GPLv3
  */

$plugin_name = 'wp-advanced-events';


add_action( 'admin_menu', 'wpae_add_admin_menu' );
register_activation_hook( __FILE__, 'wpae_install' );

function wpae_add_admin_menu(  ) {
    global $plugin_name;
    add_menu_page(
        'Advanced Events',          // page title
        'Advanced Events',          // menu title
        'manage_options',           // capability
        $plugin_name . '/main.php', // menu slug
        '',                         // callback,
        'dashicons-calendar-alt'    // icon
    );

    add_submenu_page(
        'wp-advanced-events-main',  // parent slug
        'Events',                   // page title
        'Events',                   // menu title
        'manage_options',           // capability
        $plugin_name . '/main.php'  // slug
    );

    add_submenu_page(
        $plugin_name . '/main.php', // parent slug
        'Events',                   // page title
        'Add',                      // menu title
        'manage_options',           // capability
        'wp-advanced-events/add.php'// slug
    );
}

function wpae_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'wpae_events';

    $charset_collate = $wpdb->get_charset_collate();

    $wpdb->query("CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;");
}