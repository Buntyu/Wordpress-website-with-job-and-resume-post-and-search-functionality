<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Fields_Options
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Fields_Options extends WP_Job_Manager_Field_Editor_Fields {

	public $additional_options_meta_key;

	function __construct() {

		$this->additional_options_meta_key = array(
			'allowed_mime_types' => 'file'
		);

	}

	/**
	 * Unserialize array from Ajax POST
	 *
	 * js serialize is used on the form submitted through ajax and
	 * the array needs to be reformatted to match the format expected
	 * by the rest of the plugin.  If array is not in expected serialized
	 * format it will return the original array.
	 *
	 *
	 * @since 1.2.1
	 *
	 * @param $soptions array
	 *
	 * @return array
	 */
	function unserialize( $soptions ){

		if( ! is_array($soptions) || ! isset( $soptions['option_value'] ) ) return $soptions;

		if( empty( $soptions['option_value'][0] ) ) return array();

		$options = array();

		foreach( $soptions['option_value'] as $index => $value ){
			// Remove any ~ or * set in value if they snuck through somehow
			$value = str_replace( '*', '', $value );
			$value = str_replace( '~', '', $value );
			$value = stripslashes( $value );

			// If no label was set, use the value for label
			$label = isset( $soptions['option_label'][$index] ) && $soptions[ 'option_label' ][ $index ] !== "" ? $soptions[ 'option_label' ][ $index ] : $value;
			// Add ~ if current option has key set same as the index
			if ( isset( $soptions['option_disabled'][$index] ) ) $value .= '~';
			// Add * if current option has key set same as the index
			if ( isset( $soptions['option_default'][$index] ) ) $value .= '*';

			$options[ $value ] = $label;
		}

		return $options;
	}

	/**
	 * Used to convert dropdown options to/from Array/CSV
	 *
	 * Expects string to be in this format:
	 * value1|Caption 1,value2|Caption2 ....
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $convert_options
	 * @param bool         $to_array
	 * @param bool         $add_slashes
	 *
	 * @return array|string
	 */
	function convert( $convert_options, $to_array = false, $add_slashes = false ) {
		$options = '';
		$defaultSelect = null;

		if ( $to_array ) {

			if( is_array( $convert_options ) ) return $this->unserialize( $convert_options );

			$options   = array();

			if( function_exists('str_getcsv') ){
				$structure = str_getcsv( $convert_options, ',', '"', '\\');
			} else {
				$structure = explode( ',', $convert_options );
			}

			foreach ( $structure as & $option ) {

				if ( false !== strpos( $option, '||' ) ) {

					$parts                  = explode( '||', $option );
					$options[ $parts[ 0 ] ] = $parts[ 1 ];

					if ( false !== stripos( $option, '*' ) ) {

						$defaultSelect = $parts[ 0 ];

					}

				} else {

					$options[ $option ] = ucwords( $option );

					if ( false !== stripos( $option, '*' ) ) {

						$defaultSelect = $option;

					}
				}
			}

		} else {

			if ( is_array( $convert_options ) ) {

				// Check if maybe array is from js serialize
				$options = $this->unserialize( $convert_options );

				if( $add_slashes ) $convert_options = $this->add_slashes( $convert_options );

				$options = implode( ',', array_map( array(
					                                    $this,
					                                    'add_separator'
				                                    ), $convert_options, array_keys( $convert_options ) ) );
			}

		}

		return $options;

	}

	/**
	 * Add slashes for commas in array key and value
	 *
	 *
	 * @since 1.2.6
	 *
	 * @param $options
	 *
	 * @return array
	 */
	function add_slashes( $options ){
		$slashed_options = array();
		foreach( $options as $key => $value ){
			$slashed_options[ str_replace( ',', '\\,', $key ) ] = str_replace( ',', '\\,', $value );
		}

		return $slashed_options;
	}

	/**
	 * Add Separator ( || ) Between Two Values
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $v
	 * @param $k
	 *
	 * @return string
	 */
	function add_separator( $v, $k) {
		return $k . '||' . $v;
	}

	/**
	 * Check for other fields that require option value
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $type
	 *
	 * @return bool|mixed
	 */
	function other_meta_key_check( $type ){

		// Add additional meta meta for specific options values
		$additional_meta_key = array_search( $type, $this->additional_options_meta_key );
		if( $additional_meta_key ) return $additional_meta_key;

		return false;
	}

	/**
	 * Set Additional Fields with Options values
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function additional_options( $fields ){

		if( empty( $fields ) ) return $fields;

		foreach( $fields as $field => $field_config ){

			$additional_option = $this->other_meta_key_check( $field_config[ 'type' ] );
			if( $additional_option && ! empty( $field_config[ $additional_option ] ) ){
				$fields[ $field ][ 'options' ] = $field_config[ $additional_option ];
			}

		}

		return $fields;

	}

}