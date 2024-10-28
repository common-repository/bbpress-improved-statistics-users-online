<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class bbPress_Advanced_Statistics_Admin_API {

	/**
	 * Constructor function
	 */
        public function __construct () {}

	/**
	 * Generate HTML for displaying fields
	 * @param  array   $field Field data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function display_field ( $data = array(), $post = false, $echo = true ) {

                // Get field info, replace any null values                
                $field = $data['field'];
                
                foreach( $field as $k => $v ) {
                    
                    if(!isset( $field[$k] ) )
                    {
                        $field[$k] = '';
                    }
                }
                
		// Check for prefix on option name
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
                    $option_name = $data['prefix'] . $field['id'];
		}
                
                $option = get_option( $option_name );

                // Get data to display in field
                if ( isset( $option ) ) {
                    $data = $option;
                }

		// Show default data if no option saved and default is supplied
		if ( $data === false && isset( $field['default'] ) ) {
                    $data = $field['default'];
                } elseif ( $data === false ) {
                    $data = '';
		}

		$html = '';

		switch( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $data ) . '" class="' . esc_attr( $field['class'] ) . '" />' . "\n";
			break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
                                    $min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
                                    $max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( isset( $field['placeholder'] ) ) . '" value="' . esc_attr( $data ) . '" class="' . esc_attr( $field['class'] ) . '" ' . $min . '' . $max . '/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="hidden" name="' . esc_attr( $option_name ) . '" value="" class="' . esc_attr( $field['class'] ) . '" />' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" class="' . esc_attr( $field['class'] ) . '">' . $data . '</textarea>'. "\n";
			break;
                    
                        case 'csvtextarea':
                            $html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" class="' . esc_attr( $field['class'] ) . '">' . $data . '</textarea>'. "\n";
			break;

			case 'checkbox':
                            $checked = '';
                            if ( $data && 'on' == $data ) {
                                $checked = 'checked="checked"';
                            }
                            $html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if( isset( $data ) && $data !== "" ) {
						if ( in_array( $k, $data ) ) {
                                                    $checked = true;
						}
					}
					
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
                                            $checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="label-radio"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" class="' . esc_attr( $field['class'] ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
                                            $selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . $k . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
                            $html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';
                            
                            foreach ( $field['options'] as $k => $v ) {
                                
                                $selected = false;
                                if ( in_array( $k, $data ) ) {
                                    $selected = true;
                                }
                                $html .= '<option ' . selected( $selected, true, false ) . ' value="' . $k  . '">' . $v . '</option>';
                            }
                            
                            $html .= '</select> ';
			break;

			case 'image':
                            $image_thumb = '';
                            if ( $data ) {
                                $image_thumb = wp_get_attachment_thumb_url( $data );
                            }
                            $html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
                            $html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'bbpress-improved-statistics-users-online' ) . '" data-uploader_button_text="' . __( 'Use image' , 'bbpress-improved-statistics-users-online' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'bbpress-improved-statistics-users-online' ) . '" />' . "\n";
                            $html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'bbpress-improved-statistics-users-online' ) . '" />' . "\n";
                            $html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				if ( !$post ) {
                                    $html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				$html .= '<br/><span class="description">' . $field['description'] . '</span>' . "\n";

				if ( !$post ) {
                                    $html .= '</label>' . "\n";
				}
			break;
		}

		if ( !$echo ) {
                    return $html;
		}

		echo $html;
	}

	/**
	 * Validate form field
	 * @param  string $data Submitted value
	 * @return string       Validated value
	 */
	public function validate_field ( $data ) {
            if(is_array( $data ) ) {
                               
                foreach( $data as $key => $value ) {
                    $array[] = esc_attr( $value );
                }
                
                return $array;
            }
            
            return esc_attr( $data );
	}
}