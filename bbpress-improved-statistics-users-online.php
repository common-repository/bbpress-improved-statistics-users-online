<?php
/*
 * Plugin Name: bbPress Advanced Statistics
 * Version: 1.5
 * Plugin URI: http://www.thegeek.info
 * Description: Advanced Statistics Available for bbPress users, introducing a familiar looking online and statistics section to your forums!
 * Author: Jake Hall
 * Author URI: http://www.thegeek.info
 * Requires at least: 3.9
 * Tested up to: 4.8.1
 * Text Domain: bbpress-improved-statistics-users-online
 * Domain Path: /includes/lang
 *
 * @package WordPress
 * @author Jake Hall
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

DEFINE("REQUIRED_PHPV", "5.3.0");

/**
 * init
 * Include necessary files and create variables as needed
 * 
 * @since 1.5
 */

function init() {
       
    // Load admin side of the plugin if the user is in the admin area
    if( is_admin() ) {
        require_once( 'includes/admin/lib/admin.api.php' );
        require_once( 'includes/admin/class.settings.php' );
    }

    // Load plugin class files
    require_once( 'includes/core/class.utils.php'       );
    require_once( 'includes/core/class.statistics.php'  );
    require_once( 'includes/core/class.activity.php'    );
    require_once( 'includes/public/class.online.php'    );
    require_once( 'includes/public/class.extras.php'    );
    
    // Load the widget
    require_once( 'includes/core/class.widget.php' );
    
    
    // Add a hook for additional code/extensions
    do_action( 'bbpas_init' );

    // Define our core plugin variables. 
    DEFINE("BBPAS_VERS", "1.5");
    DEFINE("BBPAS_DBVERS", "1.5");
}

function createInstance() {
    
    $instance = bbPress_Advanced_Statistics::instance( __FILE__, BBPAS_VERS );
    
    // Create the settings instance if necessary
    if ( is_null( $instance->utils ) ) {
        $instance->utils = bbPress_Advanced_Statistics_Utilities::instance( $instance, BBPAS_VERS );
    }

    // Create the settings instance if necessary
    if ( is_null( $instance->settings ) && is_admin() ) {
        $instance->settings = bbPress_Advanced_Statistics_Settings::instance( $instance, BBPAS_VERS );
    }
    
     // Create the activity instance if necessary
    if ( is_null( $instance->activity ) ) {
        $instance->activity = bbPress_Advanced_Statistics_Activity::instance( $instance, BBPAS_VERS );
    }
    
    // Create the online instance if necessary
    if ( is_null( $instance->online ) ) {
        $instance->online = bbPress_Advanced_Statistics_Online::instance( $instance, BBPAS_VERS );
    }
    
    // Create the extras  instance if necessary
    if ( is_null( $instance->extras ) ) {
        $instance->extras = bbPress_Advanced_Statistics_Extras::instance( $instance, BBPAS_VERS );
    }
    
    // Add a hook for adding instances
    do_action( 'bbpas_post_instance' );
}

function bbPress_Advanced_Statistics() {    
    
        // Initialise the plugin, include files & create vars
        init();

        // Create instance for the plugin background
        createInstance();

        // Action for after the plugin has fully loaded
        do_action( 'bbpas_loaded' );
}

/**
 * check_requirements
 * We use this function to run through the plugin checks.
 * 
 * @return string of errors to display
 */

function check_requirements() {
    
    $failures = false;
    $return   = false;
    
    if ( version_compare( phpversion(), REQUIRED_PHPV, '<') ) {
        
        $failures[] = sprintf( __('Your PHP Version does not meet the minimum requirements. Minimum version required is %1$s. An upgrade is required', 'bbpress-improved-statistics-users-online'), REQUIRED_PHPV );
    }
    
    if( !class_exists("bbpress") )
    {                
        $failures[] = __('bbPress is required to use bbPress Advanced Statistics, please install and activate bbPress.', 'bbpress-improved-statistics-users-online');
    }
    
    if( $failures !== false ) {
        
        foreach( $failures as $value ) {
            $return .= "<li>" . $value . "</li>";
        }
    }
    
    return $return;
}

function plugin_compat() {
    $requirements = check_requirements();
    
    if( $requirements !== false ) {
        
        // Add admin notice with the failed requirements. Don't load bbPress Advanced Statistics
        add_action( 'admin_notices', 'admin_notices' );      
    } else {
        
        // Start the plugin
        bbPress_Advanced_Statistics();
    }
}

function admin_notices() {
    
    $failures   = check_requirements();
    $message    = __('Yikes! bbPress Advanced Statistics is not currently active on your WordPress website. The following dependencies have not been met: ', 'bbpress-improved-statistics-users-online');
    printf( '<div class="notice notice-error"><p><b>bbPress Advanced Statistics</b></p><p>%1$s</p>%2$s</div>', $message, $failures );        
}

// Using `after_setup_theme` as this allows us to hook into the plugin before launch
add_action('after_setup_theme', 'plugin_compat');