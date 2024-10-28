<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics {

    /**
     * The single instance of bbPress_Advanced_Statistics.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * Online class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $online = null;
    
    /**
     * Activity class object
     * @var     object
     * @access  public
     * @since   1.5
     */
    public $activity = null;

    /**
     * Extras class object
     * @var     object
     * @access  public
     * @since   1.2.0
     */
    public $extras = null;
    
    /**
     * Utils class object
     * @var     object
     * @access  public
     * @since   1.5
     */
    public $utils = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Options from the DB
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $options;
    
    /**
     * Options from the DB
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $option;

     /**
     * Loaded locale file location
     * @var     string
     * @access  public
     * @since   1.1.1
     */
    public $loaded_locale;

    /**
     * Default Options Array
     * @var     string
     * @access  public
     * @since   1.3.0
     */
    public $defaultOptionsArray;

    /**
     * Plugin Versions
     * @var     string
     * @access  public
     * @since   1.3.11
     */
    public $_dbVersion;


    /**
     * Constructor function.
     * TODO: This is a mess. Needs to be sorted and commented
     * @access  public
     * @since   1.0.0
     * @return  void
     */

    public function __construct ( $file = '', $version ) {

        // Define plugin version and token used throughout
        $this->_version = $version;    

        $this->_token = 'bbpress-advanced-statistics';

        $this->_dbVersion = array( 
            "plugin" => get_option( $this->_token . '-version' ),
            "table" => get_option( $this->_token . '-dbversion' ),
            "latest-db" => BBPAS_DBVERS,
            "latest-plugin" => BBPAS_VERS
        );

        // Load plugin environment variables
        $this->file = $file;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

        // Load API for generic admin functions
        if ( is_admin() ) {
            $this->admin = new bbPress_Advanced_Statistics_Admin_API();
        }

        // check version number. install if neccessary
        $this->install();

        // Load frontend CSS
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
                
        // Load our locale once the plugin has finished loading
        add_action('init', array($this, 'loadLocale'));   

    } // End __construct ()

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */

    public function enqueue_styles () {
        // Check to see if the user has disabled CSS within the plugin
        if( $this->option['disable_css'] !== "on" )
        {                 
            wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
            wp_enqueue_style( $this->_token . '-frontend' );
        }
    } // End enqueue_styles ()

    /**
     * Load localisation files (text-domain) from the relevant directories
     * Language packs loaded from /wp-content/languages/ override those in the 
     * official plugin folder, /includes/lang/
     * 
     * @access public
     * @since 1.1.1
     */

    public function loadLocale() {
        if( load_textdomain( 'bbpress-improved-statistics-users-online', WP_LANG_DIR . '/bbpress-advanced-statistics-' . get_locale() . '.mo' ) ) {
            $this->loaded_locale = WP_LANG_DIR . 'bbpress-advanced-statistics-' . get_locale() . '.mo';
        }
        else if( load_textdomain( 'bbpress-improved-statistics-users-online', WP_LANG_DIR . '/bbpress-advanced-statistics/bbpress-advanced-statistics-' . get_locale() . '.mo' ) ) {
            $this->loaded_locale = WP_LANG_DIR . '/bbpress-advanced-statistics/' . 'bbpress-advanced-statistics-' . get_locale() . '.mo';
        }
        else if( load_plugin_textdomain( 'bbpress-improved-statistics-users-online', false, dirname( plugin_basename( __FILE__ ) . '..' ) . '/lang/' )) {
            $this->loaded_locale = dirname( plugin_basename( __FILE__ ) . '..' ) . '/lang/' . 'bbpress-advanced-statistics-' . get_locale() . '.mo';
        } else {
            $this->loaded_locale = "Default Language";
        }
    }
    
    /**
     * Set options for the instance. Install options if necessary
     * @access public
     * @since 1.0.0
     * @param array $options
     * @param bool $install
     */

    public function setOptions( $options, $install )
    {            
        // Loop through all current options available
        foreach( $options as $key => $default )
        {
            // If we are installing, and the option doesn't exist... let's add it.
            if( $install == true && !get_option( $this->_token . '-' .  $key ) )
            {   
                update_option( $this->_token . '-' .  $key, $default );
            }
            
            // Set the option for use within the plugin
            $this->option[ $key ] = get_option( $this->_token . '-' .  $key );
        }
    }

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install() {
        
        $this->defaultOptionsArray = include( 'defaults.php' );

        // Load our options, set defaults if the plugin version has changed                
        if( $this->_dbVersion["plugin"] !== $this->_version )
        {                       
            $this->setOptions( $this->defaultOptionsArray, true );
            $this->_log_version_number($this->_version, 'plugin');
        } else {
            $this->setOptions( $this->defaultOptionsArray, false );
        }

        // Update the database if necessary
        if( $this->_dbVersion['latest-db'] !== $this->_dbVersion['table'] )
        {
            $this->create_table();
        }
    } // End install ()

    /**
     * Error Message handling for Plugin Installation
     * @param string $message
     * @return wp_die
     */
    public function install_error( $message ) {
        return wp_die(
            '<h2>' . __('bbPress Advanced Statistics Error', 'bbpress-improved-statistics-users-online') . '</h2>' .
            '<p>' . $message . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' .
            __( 'Return', 'bbpress-improved-statistics-users-online' ) . '</a>'
        );
    }

    /**
     * Create required plugin tables
     * 
     * @access private
     * @since 1.3.0
     */
    public function create_table() {

        // grab/init our upgrade class. A bit dirty atm, want to make this better
        require_once( 'class.upgrade.php' );
        $upgrade = new bbPress_Advanced_Statistics_Upgrade( $this->_dbVersion['table'] );

        $this->_log_version_number($this->_dbVersion['latest-db'], 'db');                
    }

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number ($version, $type) {
        if( $type == 'plugin') {
            $type = '-version';
        } else if( $type == 'db' ) {
            $type = '-dbversion';
        }

        update_option( $this->_token . $type, $version );
    } // End _log_version_number ()

    /**
     * Main bbPress_Advanced_Statistics Instance
     *
     * Ensures only one instance of bbPress_Advanced_Statistics is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see bbPress_Advanced_Statistics()
     * @return Main bbPress_Advanced_Statistics instance
     */
    public static function instance ( $file = '', $version ) {
        if ( is_null( self::$_instance ) ) {
                self::$_instance = new self( $file, $version );
        }
        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
    } // End __wakeup ()
}