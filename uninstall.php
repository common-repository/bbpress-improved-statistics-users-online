<?php

/**
 * bbPress Advanced Statistics
 * Plugin Uninstaller
 * 
 * Once the user deletes the plugin we need to clean up our data. 
 * If the user has not chosen to retain the data, we remove the data.
 * 
 * Data deleted by this:
 * 
 * 1. bbpas table for each multisite
 * 2. All options added by the plugin
 * 3. Plugin version and widget settings
 */

// Check the constant to make sure we aren't directly accessing this file
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Firstly, check and deal with multi-site installations
if ( is_multisite() ) {
    global $wpdb;
    $original_blog_id = get_current_blog_id();

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        
        // Check to see if the blog has opted to keep the data
        if( get_option('bbpress-advanced-statistics-extra_keep_db') !== "on" ) {
            uninstall_procedure();
        }
    }

    switch_to_blog( $original_blog_id );
} else {

    // Check to see if the blog has opted to keep the data
    if( get_option('bbpress-advanced-statistics-extra_keep_db') !== "on" ) {
        uninstall_procedure();
    }

}

/**
 * This is where we deal with removing the data
 * @global type $wpdb
 */
function uninstall_procedure() {
    global $wpdb;
    $table_name = $wpdb->prefix . "bbpas";
    
    if( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) == $table_name ) {
        // If table is present, drop it
        $wpdb->query( "DROP TABLE $table_name" );
    }
    
    // Remove all options setup within the database
    $defaults = include( 'includes/core/defaults.php' );
    foreach ($defaults as $key => $value) {
        delete_option( "bbpress-advanced-statistics-" . $key );
    }
    
    // And now, the last few bits to cleanup.
    delete_option( "bbpress-advanced-statistics-version" );
    delete_option( "bbpress-advanced-statistics-dbversion" );
    delete_option( "widget_bbpress-advanced-statistics-widget" );
}