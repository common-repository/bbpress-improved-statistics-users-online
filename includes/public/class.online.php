<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Online {

	/**
	 * The single instance of bbPress_Advanced_Statistics_Online.
	 * @var 	object
	 * @access      private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
        
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
         * Currently active users
         * 
         * @var     array 
         * @access  private
         * @since   1.5
         */
        private $_activeUsers;
        
        /**
        * Formatted Stats 
        * 
        * @var         array
        * @access      private
        * @since       1.5
        */
        public $stats = array();
        
        /**
        * Allowed tags 
        * 
        * @var         array
        * @access      public
        * @since       1.5
        */
        private $_tags = array();
        
        /**
	 * Constructor function.
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
                
	public function __construct ( $parent ) {
            
            $this->parent = $parent;
            
            $this->set_vars();
                        
            // Hook into bbPress if the user has set the plugin to do so within settings
            $this->bbpress_hook_display();
            
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
         * set_vars
         * Used to setup important variables, called within the constructor
         * 
         * @global type $wpdb
         */
        private function set_vars() {
            
            // Create the wpdb db object and some helpful variables
            
            $this->_table = $this->parent->utils->table;
            $this->_sqlTime = $this->parent->utils->sqlTime;
        }
        
        /**
        * Fetches the currently active users
        * @since 1.0.0
        * 
        * @return WP_Query object
        */
        private function get_ActiveUsers()
        {            
           $query = $this->parent->utils->db->get_results(
                    "SELECT p.date_recorded as date_recorded, 
                    p.id, p.userid, p.status, 
                    w.user_nicename, w.user_login, w.display_name, w.id as wp_id
                    FROM " . $this->_table . " AS p 
                    LEFT OUTER JOIN " . $this->parent->utils->db->prefix . "users AS w ON w.id = p.userid 
                    WHERE date_recorded >= NOW() - INTERVAL " . $this->parent->option['user_activity_time'] . " HOUR 
                    AND (p.userid, p.date_recorded) IN (SELECT userid, MAX(date_recorded) FROM " . $this->_table . "
                            WHERE date_recorded >= NOW() - INTERVAL " . $this->parent->option['user_activity_time'] . " HOUR 
                            GROUP BY userid )
                    ORDER BY date_recorded DESC", OBJECT
                    );
                
           return $query;     
        }
        
        /**
         * sort_users
         * 
         * Here we are sorting through the active users and creating the arrays needed
         * 
         * @since 1.5       
         * @return array
         */
        private function sort_users() {            
            
            // Set the active users
            $this->_activeUsers = $this->get_ActiveUsers();
            
            // Work out the longest time possible for a user to be classed as active
            $active_timeframe = strtotime( $this->_sqlTime ) - ( $this->parent->option['user_inactivity_time'] * 60 );
            
            // Create our activity variable
            $activity = array( "count" => 
                            array( "active" => 0, 
                                   "guests" => 0,
                                   "guestsa" => 0,
                                   "inactive" => count( $this->_activeUsers ) 
                                )
                            );
            
            foreach( $this->_activeUsers as $user ) {
                
                // Presume user is inactive to begin
                $active = "inactive";
                
                // Check to see if the user is active
                if( ( strtotime( $user->date_recorded ) >= $active_timeframe ) ) {                    
                                        
                    // Guest user is active!
                    if( $user->wp_id === NULL ) {
                        $activity["count"]["guestsa"] = $activity["count"]["guestsa"] + 1;
                    } else {
                        // Finally, make sure the user hasn't logged out since
                        if( $user->status == 1 ) {
                            $active = "active";
                            $activity["count"]["active"] = $activity["count"]["active"] + 1;
                        }
                    }
                }
                
                // Check to see if the user is a guest/not logged in. 
                if( $user->wp_id === NULL ) {
                    $activity["count"]["guests"] = $activity["count"]["guests"] + 1;
                    // We don't want guests returned, so skip this loop
                    continue;
                }

                $role = $this->get_user_role( $user->userid, $user->user_nicename );

                $activity["raw"][$active][] = array(
                    "username"      => $user->user_nicename,
                    "user_role"     => $role,
                    "last_activity" => $user->date_recorded
                );
                
                // Save record of the user, we check active here as we need to add to both active and inactive
                if( $active == "active" ) {
                    $activity["html"]["active"][] = '<span class="bbp-topic-freshness-author '. $role . '">' . $this->get_user_link( $user ) . '</span>';
                }
                
                $activity["html"]['inactive'][] = '<span class="bbp-topic-freshness-author '. $role . '">' . $this->get_user_link( $user ) . '</span>';
            }
            
            // Create the statistics variable
            $this->stats = $this->stats_extra( $activity );
            
            // Build up the allowed tags array
            $this->allowed_tags();
        }
        
        /**
         * allowed_tags 
         * This builds up our tags for use within the customisation options
         * 
         * Filter: bbpas-replacement-tags
         * @since 1.5
         */
        private function allowed_tags() {
            // Store all of the strings currently replace
            $this->_tags = array(   
                        "%MINS%"                => $this->parent->option['user_inactivity_time'],
                        "%COUNT_ACTIVE_USERS%"  => $this->stats["count"]["active"],
                        "%HOURS%"               => $this->parent->option['user_activity_time'],
                        "%COUNT_ALL_USERS%"     => $this->stats["count"]["inactive"] - $this->stats["count"]["guests"],
                        "%COUNT_ALL_GUSERS%"    => $this->stats["count"]["guests"],
                        "%COUNT_ACTIVE_GUSERS%" => $this->stats["count"]["guestsa"],
                        "%USER_USERS%"          => _n("user", "users", $this->stats["count"]["active"], 'bbpress-improved-statistics-users-online'),
                        "%GUEST_GUESTS%"        => _n("guest", "guests", $this->stats["count"]["guestsa"], 'bbpress-improved-statistics-users-online'),
                        "%ALL_USER_USERS%"      => _n("user", "users", $this->stats["count"]["inactive"] - $this->stats["count"]["guests"], 'bbpress-improved-statistics-users-online'),
                        "%ALL_GUEST_GUESTS%"    => _n("guest", "guests", $this->stats["count"]["guests"], 'bbpress-improved-statistics-users-online'),
                        "%USER_RECORD%"         => $this->most_users( "record" ),
                        "%USER_RECORD_DATE%"    => $this->most_users( "date" ),
                        "%USER_RECORD_TIME%"    => $this->most_users( "time" ),
                        "%LATEST_USER%"         => $this->stats["latest-user"]
                    );
            
            // Apply any filters that may have been applied to the list of existing tags
            if( has_filter('bbpas_replacement_tags') ) {
                $this->_tags = apply_filters( 'bbpas_replacement_tags', $this->_tags );
            }
        }
        
        /**
         * stats_extra
         * Build up the additional stats for the arrays and future data processing
         * 
         * @since 1.5
         * @param array $activity
         * @return array 
         */
        private function stats_extra( $activity ) {
            
            if( $this->parent->option["bbpress_statistics"] == "on" ) {
                // Add the bbPress Statistics
                $activity["bbpress"] = $this->get_formatted_statistics();
            }
            
            if( $this->parent->option["last_user"] == "on" ) {
                // Get the HTML latest usser
                $activity["latest-user"] = $this->get_latestuser( true );
            }
            
            if( $this->parent->option["most_users_online"] == "on" ) {
                // Get/set the most users ever online, users the active online users
                $activity["most-users"] = $this->get_mostusers( $activity["count"]["active"] );
            }
            
            return $activity;
        }
        
        /**
         * get_user_role
         * 
         * @since 1.5
         * @return string role
         */
        private function get_user_role( $userid, $username ) {
            return str_replace(' ', '-', strtolower( bbp_get_user_display_role( $userid, $username ) ) );
        }
        
        /**
         * stats_builder
         * Builds up the stats based on what has been activated
         * 
         * @since 1.5
         * @return array 
         */
        private function stats_builder() {
            
            $stat = $this->parent->option['stats_to_display'];
            $HTMLOutput = array();
            
            // Start with our version
            $HTMLOutput["vers"] = "<!-- Added by bbPress Advanced Statistics " . BBPAS_VERS . " -->";
            
            // Section: Currently Active Users
            if(in_array( 'last_x_mins', $stat ) ) {                
                $HTMLOutput["active"] = $this->section_userstats( $this->stats, 'active' );                
            }
            
            // Section: Users active in last x hours
            if(in_array( 'last_x_hours', $stat ) ) {                
                $HTMLOutput["inactive"] = $this->section_userstats( $this->stats, "inactive" );                
            }
            
            if( $this->parent->option['user_group_key'] === "on" ) {
                $HTMLOutput["forum_key"] = $this->section_rolekey();
            }
            
            if( $this->parent->option['bbpress_statistics'] === "on" ) {
                $HTMLOutput["forum_stats"] = $this->section_bbpress_stats();
            }
            
            if( $this->parent->option['last_user'] === "on" ) {
                $HTMLOutput["last_user"] = $this->section_latestuser();
            }
            
            // Section: Most users online
            if( $this->parent->option['most_users_online'] === "on" ) {
                $HTMLOutput["most_users"] = $this->section_most_users();
            }
            
            // Section builder action for hooks
            do_action( 'bbpas_section_builder' );
            
            return $HTMLOutput;
        }
        
        /**
         * tag_replace
         * Replaces the tags that have been set in the system
         * 
         * @since 1.5
         * @param string $string The string to replace tags
         * @return string replaced text string
         */
        private function tag_replace( $string ) {
                        
            $tags = $this->_tags;
            
            $string = str_replace( array_keys( $tags ), $tags, $string);
            
            return $string;
        }
                
        /**
         * section_userstats
         * Creates the user stats section in the plugin
         * 
         * @since 1.5
         * @param type $data
         * @param type $type
         * @return array 
         */
        private function section_userstats( $data, $type ) {
                        
            $csv_users = "";
            
            if( $type == "active" ) {
                
                $title = $this->tag_replace( $this->parent->option['title_text_currently_active'] );                
                $message = __('No users are currently active', 'bbpress-improved-statistics-users-online'); 
            } else {
                
                /* Translators: 1: number of hours for activity time */
                $message = sprintf( __('No users have been active within the last %1$s hours', 'bbpress-improved-statistics-users-online'), $this->parent->option['user_activity_time'] );
                $title = $this->tag_replace( $this->parent->option['title_text_last_x_hours'] );
            }
            
            // Create the output var, add the title of the section
            $output = $this->build_title( $title, false );
            
            if( $data['count'][$type] > 0 ) {
                foreach( $data["html"][$type] as $key => $value ) {
                    // We know the loop count as we are running through an array. Use the key
                    $loop_count = $key + 1;
                    
                    if( $this->check_userlimit( $loop_count, $data['count'][$type], $type ) ) {
                        // We've hit maximum, bail
                        $csv_users .= $this->check_userlimit( $loop_count, $data['count'][$type], $type );
                        break;
                    } else {
                        // Add to the csv
                        $csv_users .= $value . $this->util_comma( $value, $data["html"][$type], $loop_count, $type );
                    }
                }
                
                $output .= $csv_users;
            } else {
                $output .= $message;
            }
            
            return $output;
        }
        
        /**
         * section_most_users
         * Returns the most users online, using the string provided within the settings
         * 
         * Currently does very little, function added for future-proofing reasons
         * 
         * @since 1.5
         * @return string
         */
        private function section_most_users() {            
            
            return $this->tag_replace( $this->parent->option['title_text_mostusers'] );
        }
        
        /**
         * most_users
         * 
         * Returns part of the most users online value.
         * 
         * @since 1.5
         * @return string
         */
        private function most_users( $type ) {
            
            // Check to see if we have the option enabled, if not, bail
            if( $this->parent->option['most_users_online'] !== "on" ) {
                return false;
            }
            
            $value = $this->stats["most-users"];
            
            if( $type == "record" ) {
                
                return $value["users"];
            }
            
            return date( get_option( $type . '_format' ), strtotime( $value['date'] ) );
        }
        
        /**
         * section_bbpress_stats
         * 
         * Returns the bbPress Stats section
         * 
         * @since 1.5
         * @return string
         */
        private function section_bbpress_stats() {
            
            $output  = $this->build_title( $this->parent->option["title_text_bbpress_stats"], false);
            $output .= $this->bbpress_stats_html( $this->parent->option["title_text_bbpress_stats_form"] );
            
            // Apply any filters that may have been applied
            if( has_filter( 'bbpas_section_bbpress_stats' ) ) {
                $output = apply_filters( 'bbpas_section_bbpress_stats', $output );
            }
            
            return $output;
        }
        
        private function bbpress_stats_html( $stats ) {
            $defaults = array( "Threads: ","Posts: ","Members: " );
            
            // Convert the csv to an array
            $stats = str_getcsv( $stats );
            
            // if the decoded stats aren't the same count as out defaults, use the defaults
            if( count( $stats ) !== count( $defaults ) ) {
                $stats = $defaults;
            }
            
            $html  = '<span class="bbpas-title">' . $stats[0] . ' </span>' . $this->stats["bbpress"]['topic_count'] . ', ';
            $html .= '<span class="bbpas-title">' . $stats[1] . ' </span>' . $this->stats["bbpress"]['reply_count'] . ', ';
            $html .= '<span class="bbpas-title">' . $stats[2] . ' </span>' . $this->stats["bbpress"]['user_count'];
            
            return $html;
            
        }
        
        private function section_rolekey() {
            
            $output = '<div class="bbpas-key">' . $this->get_roles_key() . '</div>';
            
            // Apply any filters that may have been applied
            if( has_filter('bbpas_section_rolekey') ) {
                $output = apply_filters('bbpas_section_rolekey', $output);
            }
            return $output;
        }
        
        private function section_latestuser() {
            
            // Grab the latest registered user on the site
            return $this->tag_replace( $this->parent->option["title_text_latestuser"] );
        }
        
        /**
         * Fetch the latest user to register
         * @return array
         * 
         * @updated 1.5
         * @since 1.3.0
         */
        
        function get_latestuser( $html ) {
            $latest_user = get_users(
                array(
                    'number' => 1,
                    'fields' => array("user_login", "ID", "display_name"),
                    'orderby' => "registered",
                    'order' => "DESC"
                )
            );
            
            $latest_user = reset( $latest_user );
            
            // Default display is the full name
            $name = $latest_user->display_name;
            
            if( $this->parent->option['user_display_format'] == "display_as_username" ) {
                $name = $latest_user->user_login;
            }
            
            if( $html == true ) {
                return "<a href=\"" . bbp_get_user_profile_url( $latest_user->ID ) . "\">" . $name . "</a>";
            } else {
                return $name;
            }
        }
        
        function util_comma( $value, $array, $loop, $type ) {
            
            // Limit number of users for inactive
            $limit = intval( $this->parent->option['user_display_limit'] );
            
            // Count the total users we have
            $users = count( $array );
            
            if( $type == "inactive" ) {
                if( $limit !== 0 && $limit == $loop ) {
                    return false;
                }
            }
            
            // If we're at the end of the array
            if( $value === end( $array ) ) {
                return false;
            }
            
            return ", ";
        }
        
        // Actually build all of this HTML
        function build_html() {
            
            $this->sort_users();
            $data = $this->stats_builder();
            $HTMLOutput = "";
            
            foreach( $data as $key => $html ) {
               $HTMLOutput .= "<div class='bbpas-" . $key . "' id='bbpas-" . $key . "'>" . $html . "</div>";
            }
            
            return $HTMLOutput;
        }
        
        private function check_userlimit( $loop_count, $inactive, $type ) {
                       
            $limit = intval( $this->parent->option['user_display_limit'] );
            $link = $this->parent->option['user_display_limit_link'];
            $inactives = ( $this->stats["count"]["inactive"] - $this->stats["count"]["guests"] ) - $limit;
            $return_link = "#";
            
            if( $limit > 0 && $inactive !== $limit && $loop_count > $limit && $type === "inactive" ) {
                if( $link !== "-1" ) {
                    $return_link = get_page_link( $this->parent->option['user_display_limit_link'] );
                }
                                
                    return " <a href=" . $return_link . " class='bbpas-others'>" . sprintf( _n('and %s other', 'and %s others', $inactives, 'bbpress-improved-statistics-users-online'), $inactives
                    ) . "</a>";
            }
            
            return false;
        }
                
        /**
         * Returns the forum roles that are setup on the forum
         * @since 1.3.13
         * @return string
         */
        
        function get_roles_key() {
            
            $roles = bbp_get_dynamic_roles();
            $role_key = false;
            
            foreach( $roles as $key => $value )
            {
                $role_key .= "<span class=\"" . str_replace(' ', '-', strtolower($value['name']) ) . "\">" . $value['name'] . "</span>" . ( ( $value === end( $roles ) ) ? "" : " " . __('|', 'bbpress-improved-statistics-users-online') . " "  );
            }
            
            return $role_key;
        }
        
        /**
         * Get most users ever online
         * 
         * @since 1.3.0
         * @return array
         */
        
        private function get_mostusers( $count ) {
            
            $record = $this->parent->option['record_users'];
            
            // Update the record if the count of users is bigger than our record
            if( $count > $record['users'] ) {
                
                $record = array(
                    "users" => $count, 
                    "date" => date('Y-m-d H:i:s') 
                );
                
                // Update the record
                update_option( $this->parent->_token . '-' .  "record_users", $record );
            }
            
            // Return the record users            
            return $record;
        }
        
        /**
         * Get the bbPress statistics and remove the keys we don't need
         * @since 1.4.4
         * @return array
         */
        
        function get_bbpress_statistics() {
            
            // lets get the bbPress stats
            $bbp = bbp_get_statistics();
            
            // Define what we need
            $stats = array("topic_count", "reply_count","user_count");
            
            // Apply any filters that may have been applied to the list of existing stats
            if( has_filter('bbpas_bbpress_stats') ) {
                $stats = apply_filters('bbpas_bbpress_stats', $stats);
            }
            
            // Set the stats as we need them, discarding the rest
            foreach( $stats as $key ) {
                // Set the variable that we need, continue
                $return[$key] = $bbp[$key];                
            }
            
            return $return;
        }
                
        /**
         * get_formatted_statistics()
         * Gets the correct bbpress statistics.
         * 
         * @since 1.4.3
         * @updated 1.4.4
         * @param string $type : formatted or basic stats 
         * @return array
         */
        
        function get_formatted_statistics() {
            
            // Get the bbPress stats, replace commas for calculation
            $stats = $this->get_bbpress_statistics();
            
            // If the site has merge statistics enabled, some additional work is needed before we can return the data
            if( $this->parent->option['bbpress_statistics_merge'] == "on" ) {
                
                $needles = array(',', '.');
                
                // Reverse number_format_i18n
                $stats = str_replace($needles, '', $stats);
                
                // Add the reply and topic counts
                $stats['reply_count'] = ( $stats['reply_count'] + $stats['topic_count'] );
                
                // Re-apply the number formatting
                $stats = array_map( 'number_format_i18n', array_map( 'absint', $stats ) );
            }
            
            // Return the statistics
            return $stats;            
        }
        
        /**
         * Forms the header for each section
         * @param string $title
         * @param string $link
         * @since 1.0.0
         * @return string 
        */
        
        function build_title( $title, $link )
        {
            return '<div class="bbpas-header">' . (( $link == false ) ? $title : '<a href="'.$link.'">'.$title.'</a>' ). '</div>';
        }
        
        /**
         * Return a user's value within the online users widget, alongside their
         * profile link
         * 
         * @param Obj $user
         * @since 1.0.0
         * @return string
         */
        private function get_user_link( $user ) {
            
            $name = $user->display_name;
            
            if( $this->parent->option['user_display_format'] == "display_as_username" ) {
                $name = $user->user_login;
            }
            
            $nicetime = human_time_diff( strtotime( $user->date_recorded ), strtotime( $this->_sqlTime ) );
            
            // Build the individual link
            $link = '<a href="' . bbp_get_user_profile_url( $user->userid ) .
                    '"id="bbpress-advanced-statistics-' . $user->id . '' .
                    '"title="' . sprintf( 
                            /* translators: 1: formatted time */
                            __('Last Seen: %1$s ago', 'bbpress-improved-statistics-users-online'), 
                            $nicetime ) . '" class="bbpas-user">' . $name . '</a>';
            
            return $link;
        }

        function bbpress_hook_get()
        {
            // Return the statistics
            do_action('bbpas_before_stats_hook');
                echo $this->build_html();
            do_action('bbpas_after_stats_hook');
        }

        /**
         * Hooks the online plugin into various bbPress-defined hooks
         * @since 1.0.2
         */
        function bbpress_hook_display()
        {
                $enabledPoints = $this->parent->option['forum_display_option'];

                // The only hooks we expect to be posted                      
                $allowedFields = array(
                    "bbp_template_after_forums_index", 
                    "bbp_template_after_topics_index", 
                    "bbp_template_after_single_topic", 
                    "bbp_template_after_single_forum"
                );

                if( isset( $enabledPoints ) && $enabledPoints !== "" && $enabledPoints !== false ) {
                    foreach( $enabledPoints as $k => $v )
                    {
                        if( in_array( "bbp_template_" . $v, $allowedFields ) ) {
                            add_action( "bbp_template_" . $v, array($this, "bbpress_hook_get") );
                        }
                    }
                }
        }
        
        function shortcode_activity() {
            trigger_error('shortcode_activity() is deprecated, use build_html() instead', E_USER_DEPRECATED);
            return $this->build_html();
        }
}