<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Utilities {

    /**
     * The single instance of bbPress_Advanced_Statistics.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;
    
    public $db;   

    /**
     * Constructor function.
     * TODO: This is a mess. Needs to be sorted and commented
     * @access  public
     * @since   1.0.0
     * @return  void
     */

    public function __construct ( $file = '', $version ) {
        
        $this->init();

    } // End __construct ()
    
    public function init() {
        
        // Set the db
        $this->db();
    }
    
    /**
     * db
     * Creates the database variables
     * 
     * @global type $wpdb
     * @return type
     */
    public function db() {
        
        // Set some of the variables necessary        
        global $wpdb;
        $this->db = $wpdb;
        
        $this->table = $this->db->prefix . "bbpas";
        $this->sqlTime = current_time('mysql');
    }

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