<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Settings extends bbPress_Advanced_Statistics_Admin_API {
    /**
     * The single instance of bbPress_Advanced_Statistics_Settings.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var 	object
     * @access  public
     * @since 	1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();
    
     /**
     * Default settings for plugin.
     * @var     array
     * @access  public
     * @since   1.5
     */
    public $defaults = array();

    public function __construct ( $parent ) {
            $this->parent = $parent;
            $this->defaults = $this->parent->defaultOptionsArray;

            $this->base = 'bbpress-advanced-statistics-';

            // Initialise settings
            add_action( 'init', array( $this, 'init_settings' ), 11 );

            // Register plugin settings
            add_action( 'admin_init' , array( $this, 'register_settings' ) );

            // Add settings page to menu
            add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

            // Add settings link to plugins page
            add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );

            // Get the assets URL
            $this->assets_url = $this->parent->assets_url;

            // Load backend JS & CSS
            add_action( 'admin_css', array( $this, 'settings_assets' ), 10 );
    }

    /**
     * Initialise settings
     * @since 1.0.0
     * @return void
     */
    public function init_settings () {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @since 1.0.0
     * @return void
     */
    public function add_menu_item () {
        $page = add_submenu_page("edit.php?post_type=forum", 'bbPress Advanced Statistics', __('bbPress Advanced Statistics', 'bbpress-improved-statistics-users-online'), 'delete_forums' , $this->parent->_token . '_settings', array( $this, 'settings_page' ));
        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }

    /**
     * Load backend CSS.
     * @access  public
     * @since   1.3.4
     * @return void
     */
    public function settings_assets () {
        wp_register_style( $this->parent->_token . '-backend', esc_url( $this->assets_url ) . 'css/backend.css', array(), $this->parent->_version );
        wp_enqueue_style( $this->parent->_token . '-backend' );
    }

    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @since 1.0.0
     * @return array Modified links
     */
    public function add_settings_link ( $links ) {
        $settings_link = '<a href="edit.php?post_type=forum&page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'bbpress-improved-statistics-users-online' ) . '</a>';
        array_push( $links, $settings_link );
        
        return $links;
    }

    /**
     * Build settings fields
     * @since 1.0
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields () {

        $settings['standard'] = array(
            'title'         => __( 'Basic Settings', 'bbpress-improved-statistics-users-online' ),
            'description'   => __( 'The most basic yet important settings used to set up the general functionality of the plugin.', 'bbpress-improved-statistics-users-online' ),
            'type'          => 'options',
            'view'          => 'single',
            'state'         => 'enabled',
            'fields'        => array(
                array(
                    'id'            => 'user_inactivity_time',
                    'label'         => __( 'User Active Time' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'The amount of time before a user is marked as inactive, default is 15 minutes.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'number',
                    'default'       => $this->defaults["user_inactivity_time"],
                    'class'         => 'number-box'
                ),
                array(
                    'id'            => 'user_activity_time',
                    'label'         => __( 'Active Users' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'The amount of time to consider when listing activity, default is 24 hours.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'number',
                    'default'       => $this->defaults['user_activity_time'],
                    'class'         => 'number-box'
                ),
                array(
                    'id'            => 'user_display_limit',
                    'label'         => __( 'User Display Limit' , 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'A limit for the amount of users displayed in the Active Users section. Set to 0 to disable', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'number',
                    'default'       => $this->defaults['user_display_limit'],
                    'class'         => 'number-box'
                ),
                array(
                    'id'            => 'user_display_limit_link',
                    'label'         => __( 'Display Limit Page' , 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'If the limit of users has been met, you can choose a page to display if the user was to click the link. Ideal for BuddyPress/bbPress Setups.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'select',                                    
                    'class'         => '',
                    'options'       => $this->build_pages_list(),
                    'default'       => $this->defaults['user_display_limit_link'],
                ),
                array(
                    'id'            => 'stats_to_display',
                    'label'         => __( 'Statistics to Display', 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Which of the statistics should be displayed?', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox_multi',
                    'options'       => array( 'last_x_mins' => __('Users Currently Active', 'bbpress-improved-statistics-users-online'), 'last_x_hours' => __('All Active Users', 'bbpress-improved-statistics-users-online') ),
                    'default'       => $this->defaults['stats_to_display']
                ),
                array(
                    'id'            => 'user_group_key',
                    'label'         => __( 'Display Usergroup Key?' , 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'Display a key of the usergroups available on the forum, with their colours', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults['user_group_key'],
                    'class'         => ''
                ),
                array(
                    'id'            => 'bbpress_statistics',
                    'label'         => __( 'bbPress Statistics', 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'Display the bbPress Statistics?', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults['bbpress_statistics'],
                    'class'         => ''
                ),                                
                array(
                    'id'            => 'last_user',
                    'label'         => __( 'Latest Registered user', 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'Display the latest user to register to the site?', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults['last_user'],
                    'class'         => ''
                ),
                array(
                    'id'            => 'most_users_online',
                    'label'         => __( 'Most Users Online', 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'Display the most users recorded online?', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults['most_users_online'],
                    'class'         => ''
                ),
                array(
                    'id'            => 'track_guests',
                    'label'         => __( 'Track Guests', 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( "Track users who aren't logged in", 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults['track_guests'],
                    'class'         => ''
                ),
            )
        );

        $settings['style'] = array(
            'title'         => __( 'Customisation Settings', 'bbpress-improved-statistics-users-online' ),
            'description'   => __( 'Some Customisation Options to enable more user control over the plugin', 'bbpress-improved-statistics-users-online' ),
            'type'          => 'options',
            'state'         => 'enabled',
            'view'          => 'double',
            'fields'        => array(
                array(
                    'id'            => 'title_text_currently_active',
                    'label'         => __( 'Users Currently Active' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'String displayed for the active users, useful parameters: %MINS%, %COUNT_ACTIVE_USERS%, %COUNT_ACTIVE_GUSERS%, %USER_USERS%, %GUEST_GUESTS%', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'textarea',
                    'default'       => $this->defaults["title_text_currently_active"],
                    'placeholder'   => $this->defaults["title_text_currently_active"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'title_text_last_x_hours',
                    'label'         => __( 'Recently Active Users' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Title displayed for the recently active users section, useful parameters: %HOURS%, %COUNT_ALL_USERS%, %COUNT_ALL_GUSERS%, %ALL_USER_USERS%, %ALL_GUEST_GUESTS%', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'textarea',
                    'default'       => $this->defaults["title_text_last_x_hours"],
                    'placeholder'   => $this->defaults["title_text_last_x_hours"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'title_text_latestuser',
                    'label'         => __( 'Latest User' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'You can define a custom string for the latest registered user section here, use %LATEST_USER% to return the profile link', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'text',
                    'default'       => $this->defaults["title_text_latestuser"],
                    'placeholder'   => $this->defaults["title_text_latestuser"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'title_text_mostusers',
                    'label'         => __( 'Most Users Online' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'String displayed for all time user record, useful parameters: %USER_RECORD%, %USER_RECORD_DATE%, %USER_RECORD_TIME%', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'text',
                    'default'       => $this->defaults["title_text_mostusers"],
                    'placeholder'   => $this->defaults["title_text_mostusers"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'title_text_bbpress_stats',
                    'label'         => __( 'bbPress Statistics Title' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'You can define the title of the bbPress Statistics here.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'text',
                    'default'       => $this->defaults["title_text_bbpress_stats"],
                    'placeholder'   => $this->defaults["title_text_bbpress_stats"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'title_text_bbpress_stats_form',
                    'label'         => __( 'bbPress Statistics Format' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Here you can define the text of the bbPress Statistics here. CSV Format is required.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'text',
                    'default'       => $this->defaults["title_text_bbpress_stats_form"],
                    'placeholder'   => $this->defaults["title_text_bbpress_stats_form"],
                    'class'         => 'regular-text'
                ),
                array(
                    'id'            => 'user_display_format',
                    'label'         => __( 'User display format', 'bbpress-improved-statistics-users-online' ),
                    'description'	=> __( 'Choose whether to display the user&#8217;s login or display name', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'radio',
                    'options'       => array( 'display_as_username' => __('Usernames', 'bbpress-improved-statistics-users-online'), 'display_as_dp' => __('Display Names', 'bbpress-improved-statistics-users-online') ),
                    'default'       => $this->defaults["user_display_format"],
                    'class'         => 'radio-button'
                ),
                array(
                    'id'            => 'bbpress_statistics_merge',
                    'label'         => __( 'bbPress Statistics Merge', 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Include Thread Opening Posts into the Postcount?', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults["bbpress_statistics_merge"],
                    'class'         => ''
                ),
                array(
                    'id'            => 'forum_display_option',
                    'label'         => __( 'Location of Statistics (WordPress Hooks)', 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Define where you would like the statistics to be placed. If you prefer to use the shortcode or widget, leave these options unchecked.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox_multi',
                    'options'       => array( 'after_forums_index' => __('After Forums Index', 'bbpress-improved-statistics-users-online'), 'after_topics_index' => __('After Topics Index', 'bbpress-improved-statistics-users-online'), 'after_single_topic' => __('After Single Topic', 'bbpress-improved-statistics-users-online'), 'after_single_forum' => __('After Single Forum', 'bbpress-improved-statistics-users-online') ),
                    'default'       => $this->defaults["forum_display_option"]
                ),
            )
        );

        $settings['extras'] = array(
            'title'         => __( 'Extras', 'bbpress-improved-statistics-users-online' ),
            'description'   => __( 'Additional Functionality this Plugin provides that don&#8217;t quite fit into the other tabs.', 'bbpress-improved-statistics-users-online' ),
            'type'          => 'options',
            'state'         => 'enabled',
            'fields'        => array(                       
                array(
                    'id'            => 'extra_enable_shortcode',
                    'label'         => __( 'Enable shortcode?' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Enabling this will allow you to use a shortcode([bbpas-activity]) to display the Statistics within a text widget.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults["extra_enable_shortcode"]
                ),                            
                array(
                    'id'            => 'extra_enable_whitelist',
                    'label'         => __( 'Enable BBCode/Shortcode Whitelist?' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Enabling this will allow you to setup a BBCode/Shortcode whitelist for bbPress.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults["extra_enable_whitelist"],
                ),                            
                array(
                    'id'            => 'extra_whitelist_fields_array',
                    'label'         => __( 'Whitelisted Fields' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Enabling this will allow you to setup a BBCode whitelist for bbPress. Format is comma separated', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'csvtextarea',
                    'default'       => $this->defaults["extra_whitelist_fields_array"],
                    'placeholder'   => 'b,img,i,form,gallery',
                    'class'         => ''
                ),                            
                array(
                    'id'            => 'extra_keep_db',
                    'label'         => __( 'Keep Database and Options upon uninstallation?' , 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'If you would like to keep the associated Database and Options upon uninstallation, tick this box.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults["extra_keep_db"]
                ),
                array(
                    'id'            => 'disable_css',
                    'label'         => __( 'Disable CSS Loading', 'bbpress-improved-statistics-users-online' ),
                    'description'   => __( 'Disable CSS file provided by the plugin, useful if you are overriding the style provided.', 'bbpress-improved-statistics-users-online' ),
                    'type'          => 'checkbox',
                    'default'       => $this->defaults["disable_css"],
                    'class'         => ''
                ),
            )
        );

        $settings['info'] = array(
            'title'         => __( 'Plugin Information', 'bbpress-improved-statistics-users-online' ),
            'description'   => __( 'Important Information relating to the plugin. You may be asked for details from this page when requesting support, when posting support queries please copy the entirety of the information in the textarea', 'bbpress-improved-statistics-users-online' ),
            'type'          => 'info',
            'state'         => 'enabled',
            'data'          => $this->get_debug_info()
        );

        $settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

        return $settings;
    }
    
    /**
     * Get the current tab name
     * @since 1.0.0
     * @return string
     */
    public function get_current_tab() {

        $current_tab = false;

        if ( isset( $_POST['tab'] ) ) {
            $current_tab = $_POST['tab'];
        } else if ( isset( $_GET['tab'] ) ) {
            $current_tab = $_GET['tab'];
        }

        return $current_tab;
    }
    
    /**
     * Register plugin settings
     * @since 1.0.0
     * @return void
     */
    public function register_settings () {
        if ( is_array( $this->settings ) ) {

            // Get the current tab
            $current_tab = $this->get_current_tab();

            foreach ( $this->settings as $section => $data ) {
                // Only display settings pertinent to the current tab
                if ( $current_tab && $current_tab !== $section ) continue;

                // Add section to page                            
                add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

                // If the tab has been disabled or is an information tab, stop the rest of the code being run
                if ( $data['type'] == 'info' || $data['state'] == 'disabled' ) { 
                    continue;
                }
                
                foreach ( $data['fields'] as $field ) {
                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting( $this->parent->_token . '_settings', $option_name, array( $this, 'validate_field') );

                    // Add field to page
                    add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
                }

                if ( !$current_tab ) break;
            }
        }

        $this->check_tab( $this->get_current_tab() );
    }  

    /**
     * Dispalys sections within the tabs containing the settings
     * @since 1.0.0
     * @returns string
     */
    public function settings_section ( $section ) {
        $html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>';
        echo $html;
    }
    
    /**
     * Build up a list of published, private and draft pages
     * @since 1.4
     * @return array
     */

    private function build_pages_list() {
        $wp_pages = get_pages(array(
            "post_status" => 'publish,private,draft'
        ));
        $data = array( -1 => __( 'Unlinked / No Page', 'bbpress-improved-statistics-users-online' ) );

        foreach ($wp_pages as $k => $v )
        {
            $data[$v->ID] = $v->post_title . ' (' . $v->post_status . ')';
        }

        return $data;
    }

    /**
     * Build up the debug page
     * @since 1.4.3
     * @return html string
     */
    private function get_debug_info() {

        $html = "";

        if( $this->get_current_tab() == "info" )
        {
            $data = array( 
                "general_info" => array( 
                    __( 'Plugin Version:', 'bbpress-improved-statistics-users-online' ) => $this->parent->_dbVersion["plugin"],
                    __( 'Database Version:', 'bbpress-improved-statistics-users-online' ) => $this->parent->_dbVersion["table"],
                    __( 'Loaded Locale:', 'bbpress-improved-statistics-users-online' ) => $this->parent->loaded_locale,
                    __( 'PHP Version:', 'bbpress-improved-statistics-users-online' ) => phpversion(),
                    __( 'WordPress Version:', 'bbpress-improved-statistics-users-online' ) => get_bloginfo('version'),
                    __( 'WordPress URL:', 'bbpress-improved-statistics-users-online' ) => get_bloginfo('url'),
                ),
                "plugin-data"   => $this->get_plugin_info(),
                "stats-data"    => $this->get_stats_info(),
                "option-data"   => $this->parent->option
            );
            foreach( $data["general_info"] as $key => $value) {
                $html .= "<div><span>" . $key . " </span>" . $value . "</div>";
            }

            $html .= "<div id='debug-info'><span>" . __( 'Debug Information:', 'bbpress-improved-statistics-users-online' ) . "</span></div><textarea id='debug_data' readonly>" . wp_json_encode( $data ) . "</textarea>";

            return $html;
        }

        return false;
    }
    
    private function customisation_tab() {
        return "This is a test";
    }

    /**
     * Fetch all currently installed plugins
     * @since 1.4.3
     * @return array
     */

    private function get_plugin_info() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();
        $dataRaw = false;
        $dataArray = false;

        // Open the array, turn it into an array we want.
        foreach ($all_plugins as $key => $value) {
            $dataArray["plugins"][$value['TextDomain']] = array("name" => $value['Name'],
                "URI" => $value['PluginURI'],
                "version" => $value['Version']);
        }

        return $dataArray;
    }

    /**
     * get_stats_info
     * Gets statistics about the plugin for the admin screen
     * 
     * @since 1.4.4
     * @return array
     */

    private function get_stats_info() {

        $stats["bbpress"]   = $this->parent->online->get_bbpress_statistics();
        $stats["formatted"] = $this->parent->online->get_formatted_statistics();

        foreach ($stats as $key => $value) {
            
            $return[$key] = $value;
        }
        
        return $return;
    }

    /**
     * Create the tabs for the settings page
     * @since 1.1.4
     * @return string
     */

    private function settings_createtabs() {

        if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

            $tab  = $this->get_current_tab();
            $html = '<h2 class="nav-tab-wrapper">';
            
            $c = 0;
            foreach ( $this->settings as $section => $data ) {
                if ( $data['state'] == 'disabled' ) continue;

                $class = 'nav-tab nav-tab-' . $data['type'];
                if ( isset( $tab ) && $section == $tab ) {
                    $class .= ' nav-tab-active';
                } else if( $tab == false && $c == 0 ) {
                    $class .= ' nav-tab-active';
                }

                    // Set tab link
                    $tab_link = add_query_arg( array( 'tab' => $section ) );
                    $tab_link = remove_query_arg( 'settings-updated', $tab_link );

                    // Output tab
                    $html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

                    ++$c;
            }

            $html .= '</h2>';

        }

        // this is dirty
        if ( isset( $_GET['settings-updated'] ) ) {
            $html .= '<div class="updated"><p>' . __('Settings have been saved successfully.', 'bbpress-improved-statistics-users-online') . '</p></div>';
        }

        return $html;
    }

    /**
     * Determine whether the current tab exists and is active
     * @since 1.1.4
     * @return mixed
     */
    private function check_tab( $key ) {
        $key = sanitize_key( $key );

        if(isset( $_GET['page'] ) && $_GET['page'] == $this->parent->_token . '_settings' ) {    
            $keys = array_keys( $this->settings );

            if( in_array($key, $keys) && $this->settings[$key]["state"] !== "disabled") {
                return true;
            }

            wp_redirect(sprintf( '?post_type=forum&page=%1$s&tab=%2$s', $this->parent->_token . '_settings', $keys[0] ));
        }
    }

    /**
     * Load settings page content
     * @since 1.0.0
     * @return void
     */

    public function settings_page () {   

            $current_tab = $this->get_current_tab();
            ob_start();
            // Build page HTML
            $html = '<div class="wrap" id="' . $this->parent->_token . '-' . $current_tab . '">';
            $html .= '<h1>' . __( 'bbPress Advanced Statistics' , 'bbpress-improved-statistics-users-online' ) . '</h1>';

                // Create the tabs required
                $html .= $this->settings_createtabs();
                
                $html .= '<div class="wrap" id="' . $this->parent->_token . '-' . $current_tab . '">';

                if( ( $this->settings[$current_tab]["type"] ) == "info" )
                {
                    
                    do_settings_sections( $this->parent->_token . '_settings' );

                    $html .= ob_get_clean();
                    $html .= $this->settings[$current_tab]["data"];
                } else {

                    $html .= '<form method="post" action="options.php" id="' . $current_tab . '" enctype="multipart/form-data">' . "\n";

                            // Get settings fields
                            ob_start();
                            settings_fields( $this->parent->_token . '_settings' );
                            do_settings_sections( $this->parent->_token . '_settings' );

                            $html .= ob_get_clean();
                            $html .= '<p class="submit">' . "\n";
                                $html .= '<input type="hidden" name="tab" value="' . $current_tab . '" />' . "\n";
                                $html .= '<input name="submit-'. $current_tab .'" type="submit" class="button-primary" value="' . esc_attr( __( 'Update Settings' , 'bbpress-improved-statistics-users-online' ) ) . '" />' . "\n";
                            $html .= '</p>' . "\n";
                        $html .= '</form>' . "\n";
                    $html .= '</div>' . "\n";
                }

            echo $html;
    }

    /**
     * Main bbPress_Advanced_Statistics_Settings Instance
     *
     * Ensures only one instance of bbPress_Advanced_Statistics_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see bbPress_Advanced_Statistics()
     * @return Main bbPress_Advanced_Statistics_Settings instance
     */
    public static function instance ( $parent ) {
            if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self( $parent );
            }
            return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bbpress-improved-statistics-users-online' ), $this->parent->_version );
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'bbpress-improved-statistics-users-online' ), $this->parent->_version );
    } // End __wakeup()

}