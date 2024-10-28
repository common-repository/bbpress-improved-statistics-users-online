<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Activity {

    /**
	 * The single instance of bbPress_Advanced_Statistics_Online.
	 * @var 	object
	 * @access      private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
        
        /**
	 * The current user's ID
	 * @var         int
	 * @access      private
	 * @since       1.0.0
	 */
	private $_userID = 0;
        
        /**
	 * WordPress DB object
	 * @var         object
	 * @access      private
	 * @since       1.3.0
	 */
	private $_db;
        
        /**
	 * Plugin table name
	 * @var         object
	 * @access      private
	 * @since       1.3.0
	 */
	private $_table;
        
         /**
	 * SQL Time
	 * @var         object
	 * @access      private
	 * @since       1.3.0
	 */
	private $_sqlTime;
        
        /**
	 * Constructor function.
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
        
        /**
	 * Users IP Address
	 * @var         int
	 * @access      private
	 * @since       1.5
	 */
        private $_userIP;
        
        /**
	 * Users Status 
         * 
         * 0 = offline
         * 1 = active registered user
         * 2 = active guest
         * 
	 * @var         int
	 * @access      private
	 * @since       1.5
	 */
        private $_userStatus;

    
    public function __construct ( $parent ) {

        $this->parent = $parent;

        $this->_db = $this->parent->utils->db;
        $this->_table = $this->parent->utils->table;
        $this->_sqlTime = $this->parent->utils->sqlTime;
        
        // Hook into WordPress as needed
        $this->hook_actions();
    } // End __construct ()        

    /**
     * Main bbPress_Advanced_Statistics_Online Instance
     *
     * Ensures only one instance of bbPress_Advanced_Statistics_Online is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see bbPress_Advanced_Statistics()
     * @return Main bbPress_Advanced_Statistics_Online instance
     */
    public static function instance ( $file = '', $version ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }
        return self::$_instance;
    } // End instance ()
    
    
    /**
     * hook_actions
     * 
     * Fire off the hooks we need to do
     */
    public function hook_actions() {

        // Set the user data we need
        add_action('init', array( $this, 'setUserData' ), 10 );            

        // hook our status updates into the appropriate hooks
        add_action( 'template_redirect', array( $this, 'userActivity' ), 10 );

        add_action( 'clear_auth_cookie', array( $this, 'setUserOffline' ), 10 );
        add_action( 'set_auth_cookie',   array( $this, 'setGuestOffline'), 10 );
    }
    
   /**
    * update_lastactivity
    * 
    * Updates the DB value for the user id passed, uses WP's current_time
    * functionality. 
    * 
    * This is also how statuses are updated too (e.g logout)
    * 
    * @since 1.0.0
    * @param int $userID
    * @param int $status
    * 
    * @return void
    */

   public function update_lastactivity( $userID, $status ) {

       // C-A: The user ID should not be blank or equal to 0
       if( $userID && $userID !== 0 ) {

           // Update user's activity
           $this->_db->replace(
               $this->_table,
               array( 
                   'userid' => $userID,
                   'date_recorded' => current_time('mysql'),
                   'status' => $status
               )
           );

       }
   }

   /**
    * setUserOffline ( p.k.a userLoggedOut )
    * 
    * Hooked into clear_auth_cookie, wp_logout is too late as we need to
    * retain the ID to set the correct flag with the db. 
    * 
    * @since 1.5 (1.0.0)
    * 
    * @return void
    */

   public function setUserOffline()
   {
       // Set the user's status to 0
       $this->update_lastactivity( $this->_userID, 0 );
   }
   
   public function setGuestOffline() {
        $this->_db->delete( $this->_table, array( 'userid' => $this->_userID ) );
   }

   /**
    * userActivity
    * 
    * Hooked into template_redirect to be run each time the user
    * changes the page, simply runs the update_lastactivity function 
    * 
    * @since 1.0.0
    * 
    * @return void
    */
   public function userActivity()
   {
       
       // User is an offline guest by default
       $status = 2;

       if( is_user_logged_in() )
       {
           $status = 1;
       }
       
       $this->update_lastactivity( $this->_userID, $status );
   }

   /**
    * setUserData
    * 
    * Sets up the required user data for us, hooked into init. 
    * 
    * @since 1.0.0
    * 
    * @return void
    */
   public function setUserData()
   {              
       // Check if we're tracking Guests
       if( $this->parent->option['track_guests'] == "on" ) {

           // We are tracking guests                
           $id = ip2long( $_SERVER['REMOTE_ADDR'] );
           $this->_userStatus = 2;
       }

       // If the user is logged in, set the status and user ID
       if( is_user_logged_in() )
       {
           $id = wp_get_current_user()->ID;
           $this->_userStatus = 1;
       }

       // Make sure an id is set
       if( isset( $id ) ) {
           $this->_userID = $this->_userIP = $id;
       }
   }
}