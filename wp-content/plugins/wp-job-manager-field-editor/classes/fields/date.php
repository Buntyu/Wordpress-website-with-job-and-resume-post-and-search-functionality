<?php

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Fields_Date
 *
 * @since @@since
 *
 */
class WP_Job_Manager_Field_Editor_Fields_Date {

	/**
	 * WP_Job_Manager_Field_Editor_Fields_Date constructor.
	 */
	public function __construct() { }

	/**
	 * Get Meta Keys to Skip Date Handling
	 *
	 *
	 * @since 1.3.7
	 *
	 * @return mixed|void
	 */
	static function get_skip_keys(){
		return apply_filters( 'field_editor_dp_skip_conversion', array( 'job_expires', 'resume_expires', 'job_deadline', 'application_deadline' ) );
	}

	/**
	 * Check if Meta Key should be skipped
	 *
	 *
	 * @since 1.3.7
	 *
	 * @param $meta_key
	 *
	 * @return bool
	 */
	static function should_skip( $meta_key ){
		// Check if meta key has an underscore as the first character, remove if so
		if( strpos( $meta_key, '_' ) === 0 ) $meta_key = substr( $meta_key, 1 );
		if( in_array( $meta_key, self::get_skip_keys() ) ) return true;
		return false;
	}

	/**
	 * Convert Date to display using core WordPress format
	 *
	 *
	 * @since    1.3.7
	 *
	 * @param      $date
	 * @param bool $meta_key
	 * @param bool $post_id
	 *
	 * @return bool|string
	 * @internal param $date
	 */
	static function convert_to_display( $date, $meta_key = false, $post_id = false ){

		if( ! $date || empty( $date ) ) return $date;
		// Don't try to convert meta keys that we skip
		if( $meta_key && self::should_skip( $meta_key ) ) return $date;

		// If get_epoch returns false, probably already in epoch format
		$epoch = self::get_epoch( $date, $meta_key, $post_id );
		if( ! $epoch ) $epoch = $date;

		// Epoch must be numeric, if not return the original
		if( ! is_numeric( $epoch ) ) return $date;

		$use_wp = get_option( 'jmfe_fields_dp_i18n' );
		$date_format = get_option( 'date_format' );

		if( ! empty( $use_wp ) ) {
			$date = date_i18n( $date_format, $epoch );
		} else {
			$date = date( $date_format, $epoch );
		}

		return $date;
	}

	/**
	 * Convert Date Values for Output
	 *
	 * This method is called by frontend get_user_data and sets the value to the format
	 * expected by the date picker field.  Admin fields do not have the value set in the
	 * array, when $admin_fields is set to true this method will attempt to pull value
	 * from post meta.
	 *
	 *
	 * @since 1.3.7
	 *
	 * @param      $fields
	 * @param bool $admin_fields    Set to TRUE to pull value from post meta
	 *
	 * @return array
	 */
	static function convert_fields( $fields, $admin_fields = false ){
		global $post;

		if( ! is_array( $fields ) ) return $fields;

		$save_as = get_option( 'jmfe_fields_dp_saveas' );
		if( ! $save_as || $save_as == 'default' ) return $fields;

		if( isset($fields['job']) || isset($fields['resume_fields']) ) {

			foreach( $fields as $field_group => $group_fields ) {
				$fields[ $field_group ] = self::convert_fields( $group_fields );
			}

			return $fields;
		}

		$post_id = isset( $post, $post->ID ) ? $post->ID : FALSE;

		foreach( $fields as $field => $conf ){
			if( ! isset( $conf['type'] ) || ( ! isset( $conf['value'] ) && ! $admin_fields ) || $conf['type'] !== 'date' || self::should_skip( $field ) ) continue;
			// If a value isn't set, and the global post ID is available, set the conf value to that post meta value
			// ... this should only be used whenever writepanels (admin) is calling the fields init and field should already have underscore as first character
			if( ! isset( $conf['value'] ) && $post_id ) $conf['value'] = get_post_meta( $post_id, $field, true );

			// There is a value and the type is date, let's convert it to display correctly
			$fields[ $field ]['value'] = self::convert_to_display( $conf['value'], $field, $post_id );
		}

		return $fields;
	}

	/**
	 * Get Epoch Timestamp
	 *
	 * Will return the epoch timestamp after attempting to convert using the format the
	 * date was originally saved in.  If format does not exist, or there is an error with
	 * the DateTime object, will use strtotime() instead.
	 *
	 *
	 * @since 1.3.7
	 *
	 * @param      $date        Date stamp to convert
	 * @param bool $meta_key    Meta key the date stamp is associated with
	 * @param bool $post_id
	 * @param bool $format      Only specify if you want to use a specific format to convert from
	 *
	 * @return int
	 */
	static function get_epoch( $date, $meta_key = false, $post_id = false, $format = false ){

		// When a post is updated with a custom date format, the format used at that
		// time is saved to the same meta key with "_format" added to end, if that value
		// doesn't exist, then the default format is used.
		if( ! empty($meta_key) && ! empty($post_id) && empty( $format ) ) {
			// Check for underscore prepended on meta key, and add if doesn't exist (all meta is saved with underscore prepended)
			if( strpos( $meta_key, '_' ) !== 0 ) $meta_key = "_{$meta_key}";
			$format = get_post_meta( $post_id, "{$meta_key}_format", TRUE );

			// No need to convert if already in epoch format
			if( ! empty( $format ) && $format == 'U' && is_numeric( $date ) ) return $date;
		}

		// New listing or _METAKEY_format does not exist
		if( empty($format) ) $format = get_option( 'date_format' );

		$from_format = date_create_from_format( $format, $date );
		// Error when using above func will return false instead of object
		$epoch = ! empty( $from_format ) ? date_format( $from_format, 'U' ) : strtotime( $date );

		return $epoch;

	}

	/**
	 * Convert Date String
	 *
	 * Convert a date string to a format configured in settings, as well as update/add meta
	 * to the post, using original meta key appended with _format, with the format used when
	 * converting the date stamp.
	 *
	 * @since    1.3.7
	 *
	 * @param       $date
	 * @param array $field
	 * @param bool  $meta_key
	 * @param bool  $post_id
	 *
	 * @return int|mixed|string
	 *
	 */
	static function convert_to_save( $date, $field = array(), $meta_key = false, $post_id = false ){

		$format = false;
		$save_as = get_option( 'jmfe_fields_dp_saveas' );
		// Return passed date as config is set to default or not set yet
		if( ! $save_as || $save_as == 'default' || empty( $date ) ) return $date;

		// Filter for skipping specific meta keys from being converted before saving
		if( ! empty( $field ) && isset( $field['meta_key'] ) ) if( self::should_skip( $field['meta_key'] ) ) return $date;

		$epoch = self::get_epoch( $date, $meta_key, $post_id );

		switch( $save_as ){

			case 'epoch':
				$format = 'U';
				$date = $epoch;
				break;

			case 'iso':
				$date = date( $format = 'c', $epoch );
				break;

			case 'rfc':
				$date = date( $format = 'r', $epoch );
				break;

			case 'ymd':
				$date = date( $format = 'ymd', $epoch );
				break;

			case 'Ymd':
				$date = date( $format = 'Ymd', $epoch );
				break;

			case 'datetime':
				$date = date( $format = 'Y-m-d H:i:s', $epoch );
				break;

			case 'custom':
				$format = get_option( 'jmfe_fields_dp_custom' );
				if( ! empty( $format ) ) $date = date( $format, $epoch );
				break;
		}

		if( $meta_key && $post_id && $format ) update_post_meta( $post_id, "{$meta_key}_format", $format );

		return $date;
	}


}