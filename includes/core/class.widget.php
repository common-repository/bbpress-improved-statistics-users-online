<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Widget extends WP_Widget {
    
    /**
    * The main plugin object.
    * @var 	object
    * @access   private
    * @since 	1.3.0
    */
    private $_parent;
     
    function __construct() {
        
        // Get our parent instance
        $this->_parent = bbPress_Advanced_Statistics::instance( __FILE__, BBPAS_VERS );
        
        parent::__construct(
            'bbpress-advanced-statistics-widget',
            __('bbPress Advanced Statistics', 'bbpress-improved-statistics-users-online' ),
            array (
                'description' => __( 'Displays bbPress Forum Statistics in widget form', 'bbpress-improved-statistics-users-online' )
            )
        );
    }
     
    function form( $instance ) {
        $defaults = array(
            'heading' => __( 'Forum Statistics', 'bbpress-improved-statistics-users-online' )
        );
        
        // Set default fields
        $instance = wp_parse_args( $instance, $defaults );
        
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'heading' ); ?>">Heading for Statistics:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'heading' ); ?>" name="<?php echo $this->get_field_name( 'heading' ); ?>" value="<?php echo esc_attr( $instance['heading'] ); ?>">
        </p>

        <?php
        }
     
    function update( $new_instance, $old_instance ) {       
    
        $instance = $old_instance;
        $instance[ 'heading' ] = strip_tags( $new_instance[ 'heading' ] );
        return $instance;
     
    }
    
    /*
     * Build up the Widget contents
     */
    function widget( $arg, $instance ) {
        
        echo $arg['before_widget'] . 
             $arg['before_title'] . 
             $instance['heading'] . 
             $arg['after_title'] . 
             $this->_parent->online->build_html() . 
             $arg['after_widget'];
    }     
}

function widget_register() { 
    register_widget( 'bbPress_Advanced_Statistics_Widget' ); 
}

add_action( 'widgets_init', 'widget_register' );