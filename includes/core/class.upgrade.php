<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Upgrade {
    
    /**
    * db version string
    * @var     string
    * @access  private
    * @since   1.3.0
    */
    
    private $_dbVersion;
    
    /**
    * wpdb object
    * @var     object
    * @access  private
    * @since   1.3.0
    */
    
    private $_db;
    
    /**
    * db table
    * @var     string
    * @access  private
    * @since   1.3.0
    */
    
    private $_table;
    
    /**
    * db charset
    * @var     string
    * @access  private
    * @since   1.3.0
    */
    
    private $_charset;
    
    /**
    * db versions object
    * @var     array
    * @access  private
    * @since   1.3.0
    */
    
    private $_dbVersions = array();
    
    public function __construct( $dbVersion ) {

        // Get wordpress db global and make available to upgrades
        global $wpdb;
        $this->_db = $wpdb;
        $this->_table = $this->_db->prefix . "bbpas";
        $this->_charset = $this->_db->get_charset_collate();

        // Get the database version
        $this->_dbVersion = $dbVersion;

        // Build the versions array
        $this->_dbVersions = array( 
            "1.0.2" => "db_upgrade_1_0_2",
            "1.0.3" => "db_upgrade_1_0_3",
            "1.1"   => "db_migration_1_1",
            "1.1.1" => "db_migration_1_1_1",
            "1.5"   => "db_migration_1_5"
        );
        
        // Run the relevant install procedure
        if ( is_multisite() ) {
            $this->multi_site_install();
        } else {
            $this->single_site_install();
        }
        
    } // End __construct () 
    
    /**
     * If we are working with a single installation, we run the function as normal
     * 
     * @since 1.4.1
     * @return void
     */
    
    public function single_site_install() {
        $this->check_for_db_upgrade();
    }
    
    /**
     * If we are working with a multi-site, we need to run these functions on all
     * blogs. Here we are switching through the blogs and running the upgrades
     * 
     * @since 1.4.1
     * @return void
     */
    
    public function multi_site_install() {
        global $wpdb;
        $original_blog_id = get_current_blog_id();

        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            
            // Ensure we have the correct table prefix by setting the value again
            $this->_table = $this->_db->prefix . "bbpas";
            $this->check_for_db_upgrade();
        }
        
        // Return back to the multi site we started with
        switch_to_blog( $original_blog_id );
    }

    /**
     * Here we check the current Database Version vs each of the upgrades 
     * available.
     * 
     * @since 1.3.0
     * @return void
     */

    public function check_for_db_upgrade() {

        foreach( $this->_dbVersions as $version => $function )
        {                
            if( version_compare( $this->_dbVersion, $version ) < 0 )
            {
                $this->upgrade_the_db( call_user_func( "bbPress_Advanced_Statistics_Upgrade::" . $function ) );
            }
        }
    }

    /**
     * If the database is out-of-date, we will use this
     * function to run the dbDelta option
     * 
     * Here we also check to see if a data migration is required, 
     * if it is, we will call the function attached to migrate the data
     * 
     * @since 1.3.0
     * @return void
     */

    private function upgrade_the_db( $upgrade ) {

        /**
         * Do we need to run some table operations?
         * 
         * If so, include the upgrade file and run dbDelta
         */
       
        if( isset( $upgrade['sql'] ) && $upgrade['sql'] !== false ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $upgrade['sql'] );
        }

        /**
         * Do we need to migrate?
         * 
         * If so, call the appropriate function
         */
        
        if( isset( $upgrade['migration'] ) && $upgrade['migration'] !== false ) {
            call_user_func( "bbPress_Advanced_Statistics_Upgrade::" . $upgrade['migration'] );            
        }
        
        // Kill the variable afterwards
        unset( $upgrade );
    }
    
    /**
     * If an error has occurred, we will use this function to
     * alert the user of the fact.
     * 
     * This is in its own function to allow us to add more debugging support
     * in the future, or, to potentially allow users to hook in their own
     * debugging functions.
     * 
     * @since 1.3.20
     * @return string
     */
    
    private function return_error( $function ) {
        wp_die( 
            sprintf(    
                /* Translators: 1: The database upgrade/migration that has failed */
                __('There was a problem whilst updating the database. (%1$s)', 'bbpress-improved-statistics-users-online'), 
                $this->_db->last_error 
            )
        );
    }

    #################################################
    ###
    ###             DATABASE UPGRADES
    ###
    #################################################


    /*
     * ==========================================================
     * ==================== DATABASE UPGRADE ====================
     * ========================= 1_0_2 ==========================
     * 
     * # Create new table for the plugin
     * # Migrate data from previous versions
     * 
     * @since 1.3.0
     * @return array
     */

    private function db_upgrade_1_0_2() {

        $sql = "CREATE TABLE $this->_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            userid bigint(20) NOT NULL,
            date_recorded datetime NOT NULL,
            status int(1) NOT NULL,
            UNIQUE KEY id (id),
            UNIQUE KEY userid (userid),
            INDEX date_recorded (date_recorded),
            INDEX status (status),
            PRIMARY KEY  (id)
        ) $this->_charset;";

        return array( 
            "sql" => $sql,
            "migration" => "migration_1_0_2"
        );
    }

    /*
     * ==========================================================
     * =================== DATABASE MIGRATION ===================
     * ========================= 1_0_2 ==========================
     * 
     * # Migrate data into newly created table
     * # Mark old data for deletion
     * 
     * @since 1.3.0
     */

    private function migration_1_0_2() {

        $data = $this->_db->get_results("SELECT * FROM `" .  $this->_db->prefix . "usermeta` WHERE `meta_key` LIKE '%bbpress-advanced-statistics%'", ARRAY_A);
        $migrate = array();

        // Manipulate the data in a way that we can import it. 
        foreach( $data as $key => $value )
        {
           $migrate[$value['user_id']][$value['meta_key']] = $value['meta_value'];            
        }

        foreach( $migrate as $key => $value ) 
        {
            // Only insert the values with valid status and activity status
            if( isset( $value['bbpress-advanced-statistics_lastactivity'] ) && isset( $value['bbpress-advanced-statistics_status'] ) )
            {
                $this->_db->replace( $this->_table, array("id" => NULL, "userid" => $key,
                                "date_recorded" => date('Y-m-d H:i:s', $value['bbpress-advanced-statistics_lastactivity'] ),
                                "status" => $value['bbpress-advanced-statistics_status']) );
            }

            if( $this->_db->last_error ) {
                $this->return_error(__FUNCTION__);
            }
        }
    }

    /*
     * ==========================================================
     * ==================== DATABASE UPGRADE ====================
     * ========================= 1_0_3 ==========================
     * 
     * # Fix unique key issue
     * # Remove of duplicate data
     * 
     * @since 1.3.1
     * @return array
     */

    private function db_upgrade_1_0_3() {

        // Delete any duplicates before we upgrade the db
        $this->_db->query("DELETE FROM " . $this->_table . "
                            WHERE id NOT IN (SELECT * 
                            FROM (SELECT MAX(n.id)
                                FROM " . $this->_table . " n
                                GROUP BY n.userid) x)
                         ");

        // There was an error when deleting the data, halt and alert the user.
        if( $this->_db->last_error ) {
            $this->return_error(__FUNCTION__);
        }

        $sql = "CREATE TABLE $this->_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            userid bigint(20) NOT NULL,
            date_recorded datetime NOT NULL,
            status int(1) NOT NULL,
            UNIQUE KEY id (id),
            UNIQUE KEY userid (userid),
            INDEX date_recorded (date_recorded),
            INDEX status (status),
            PRIMARY KEY  (id)
        ) $this->_charset;";

        return array( "sql" => $sql,
                      "migration" => false);
    }

    /*
     * ==========================================================
     * =================== DATABASE MIGRATION ===================
     * ========================== 1_1 ===========================
     * 
     * # Remove old data from usermeta table
     * 
     * @since 1.3.20
     */

    private function db_migration_1_1() {

        // Delete all of the old data related to the plugin in the User Table
        $sql = "DELETE FROM " . $this->_db->prefix . "usermeta WHERE `meta_key` LIKE '%bbpress-advanced-statistics%'";
        $this->_db->query($sql);

        // There was an error when deleting the data, halt and alert the user.
        if( $this->_db->last_error ) {
            $this->return_error(__FUNCTION__);
        }
    }
    
    /*
     * ==========================================================
     * =================== DATABASE MIGRATION ===================
     * ========================= 1_1_1 ==========================
     * 
     * # Migrate the delete_db option within the db
     * 
     * @since 1.4.1
     */

    private function db_migration_1_1_1() {

        if( get_option("bbpress-advanced-statistics-extra_delete_db") == "on" ) {
            update_option("bbpress-advanced-statistics-extra_keep_db", "off");
        }
        
        delete_option("bbpress-advanced-statistics-extra_delete_db");
    }
    
    /*
     * ==========================================================
     * =================== DATABASE MIGRATION ===================
     * ========================= 1_5 ==========================
     * 
     * # Delete removed options
     * 
     * @since 1.5
     */

    private function db_migration_1_5() {
        
        // Remove old settings
        delete_option("bbpress-advanced-statistics-before_forum_display");
        delete_option("bbpress-advanced-statistics-after_forum_display");
    }
    
}