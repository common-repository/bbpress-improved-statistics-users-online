<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Extras {

	/**
	 * The single instance of bbPress_Advanced_Statistics_Extras.
	 * @var 	object
	 * @access      private
	 * @since 	1.2.0
	 */
	private static $_instance = null;
        
        /**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.2.0
	 */
	public $parent = null;
                
        /**
	 * Constructor function.
	 * @access      public
	 * @since       1.2.0
	 * @return      void
	 */
	public function __construct ( $parent ) {
            
            $this->parent = $parent;
            $this->activate_extras();
            
	} // End __construct ()
        
        
	/**
	 * Main bbPress_Advanced_Statistics_Extras Instance
	 *
	 * Ensures only one instance of bbPress_Advanced_Statistics_Extras is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @static
	 * @see bbPress_Advanced_Statistics()
	 * @return Main bbPress_Advanced_Statistics_Extras instance
	 */
	public static function instance ( $file = '', $version ) {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self( $file, $version );
            }
            return self::$_instance;
	} // End instance ()
                
        /**
         * Activate the extras that are enabled
         * 
         * @since 1.2
         * @return void
         */
        private function activate_extras() {
            
            // Check to see if the site has the whitelist enabled
            if( $this->parent->option['extra_enable_whitelist'] == "on" ) {
                $this->enable_whitelist();
            }
            
            // Check to see if the site has the shortcode enabled
            if( $this->parent->option['extra_enable_shortcode'] == "on" ) {
                $this->enable_shortcode();
            }
            
        }
        
        /**
         * Filter out shortcodes that are not allowed via the whitelist
         * 
         * @since 1.2
         * @global WP_Object $shortcode_tags
         * @param string $text
         * @return string
         */

        function shortcode_whitelist( $text ) {
            $whitelist = explode(',', $this->parent->option['extra_whitelist_fields_array']);

            global $shortcode_tags;

            // Store original copy of registered tags.
            $_shortcode_tags = $shortcode_tags;

            // Remove any tags not in whitelist.
            foreach ( $shortcode_tags as $tag => $function ) {
                
                if ( !in_array( $tag, $whitelist ) ) {
                    unset( $shortcode_tags[ $tag ] );
                }
            }

            // Apply shortcode.
            $text = shortcode_unautop( $text );
            $text = do_shortcode( $text );

            return $text;
        }
        
        /**
         * This will activate the shortcode whitelist in various filters
         * within bbPress
         * 
         * @since 1.2
         * @return void
         */
        private function enable_whitelist() {       
            add_filter( 'bbp_get_topic_content', array( $this, "shortcode_whitelist" ) );
            add_filter( 'bbp_get_reply_content', array( $this, "shortcode_whitelist" ) );
            add_filter( 'bbp_new_topic_pre_insert', array( $this, "shortcode_whitelist" ) );
        }        
        
        /**
         * Enable shortcodes within WordPress widgets
         * 
         * @since 1.2
         * @return void
         */    
        private function enable_shortcode() {
            add_shortcode("bbpas-activity", array( $this->parent->online, "build_html" ) );
            
            // Enable shortcodes within widgets
            add_filter('widget_text', 'do_shortcode');
        }
}