<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'get_job_field' ) ){

	/**
	 * Get Job Field Value
	 *
	 * Will return any default or custom job field values
	 * from specific job if post ID is included, otherwise
	 * will return from current job posting.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_job_field( $field_slug, $job_id = null, $args = array() ){

		if( ! $job_id ) $job_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $job_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_job_field' ) ) {

	/**
	 * Echo Job Field Value
	 *
	 * Same as get_job_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_job_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_job_field( $field_slug, $job_id );

		if ( isset( $args['output_as'] ) && ! empty( $args['output_as'] ) ) {
			the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );
		} else {
			if ( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );
			echo $field_value;
		}

	}

}

if ( ! function_exists( 'get_company_field' ) ) {

	/**
	 * Get Company Field Value
	 *
	 * Will return any default or custom job field values
	 * from specific job if post ID is included, otherwise
	 * will return from current job posting.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_company_field( $field_slug, $job_id = null, $args = array() ) {

		if ( ! $job_id ) $job_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $job_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_company_field' ) ) {

	/**
	 * Echo Company Field Value
	 *
	 * Same as get_company_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from job posting
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_company_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_company_field( $field_slug, $job_id );

		if ( isset( $args[ 'output_as' ] ) && ! empty( $args[ 'output_as' ] ) ) {
			the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );
		} else {
			if ( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );
			echo $field_value;
		}

	}

}

if ( ! function_exists( 'get_resume_field' ) ) {

	/**
	 * Get Resume Field Value
	 *
	 * Will return any default or custom resume field values
	 * from specific resume if post ID is included, otherwise
	 * will return from current resume listing.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from resume listing
	 * @param null  $resume_id  Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_resume_field( $field_slug, $resume_id = null, $args = array() ) {

		if ( ! $resume_id ) $resume_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $resume_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_resume_field' ) ) {

	/**
	 * Echo Resume Field Value
	 *
	 * Same as get_resume_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from resume listing
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_resume_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_resume_field( $field_slug, $job_id );

		if ( isset( $args[ 'output_as' ] ) && ! empty( $args[ 'output_as' ] ) ) {
			the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );
		} else {
			if ( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );
			echo $field_value;
		}

	}

}

if ( ! function_exists( 'get_custom_field' ) ) {

	/**
	 * Get Custom Field Value
	 *
	 * Will return any default or custom field values
	 * from specific post if post ID is included, otherwise
	 * will return from current post.
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug Meta key from post
	 * @param null  $post_id    Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @return mixed|null
	 */
	function get_custom_field( $field_slug, $post_id = null, $args = array() ) {

		if ( ! $post_id ) $post_id = get_the_ID();

		$field_value = get_custom_field_listing_meta( $field_slug, $post_id, $args );

		return $field_value;

	}

}

if ( ! function_exists( 'the_custom_field' ) ) {

	/**
	 * Echo Custom Field Value
	 *
	 * Same as get_custom_field except will echo out the value
	 *
	 * @since    1.1.8
	 *
	 * @param       $field_slug Meta key from post
	 * @param null  $job_id     Optional, Post ID to get value from
	 * @param array $args
	 *
	 * @internal param null $output_as
	 * @internal param null $caption
	 * @internal param null $extra_classes
	 */
	function the_custom_field( $field_slug, $job_id = null, $args = array() ) {

		$field_value = get_custom_field( $field_slug, $job_id, $args );

		if ( isset( $args[ 'output_as' ] ) && ! empty( $args[ 'output_as' ] ) ) {
			the_custom_field_output_as( $field_slug, $job_id, $field_value, $args );
		} else {
			if ( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );
			echo $field_value;
		}

	}

}

if ( ! function_exists( 'get_custom_field_listing_meta' ) ){

	/**
	 * Get meta key or taxonomy value from listing
	 *
	 * Check for arguments that specify taxonomy, if specified check if the listing has
	 * any values saved for taxonomy, otherwise get value from post meta
	 *
	 *
	 * @since 1.2.6
	 *
	 * @param $field_slug
	 * @param $listing_id
	 * @param $args
	 *
	 * @return mixed|null
	 */
	function get_custom_field_listing_meta( $field_slug, $listing_id, $args = array() ){

		// Make sure the listing ID passed is not a page ID
		if ( is_page( $listing_id ) ) return FALSE;

		$field_value = array();

		if( $field_slug === 'job_title' || $field_slug === 'candidate_name' ) return apply_filters( 'field_listing_title', get_the_title( $listing_id ) );
		if( $field_slug === 'job_description' || $field_slug === 'resume_content' ) return apply_filters( 'field_listing_content', get_the_content( $listing_id ) );

		$jmfe = WP_Job_Manager_Field_Editor_Fields::get_instance();
		$all_fields = $jmfe->get_fields();

		// Loops through field groups checking if meta key exists
		foreach( $all_fields as $field_group => $fields ){
			if( array_key_exists( $field_slug, $all_fields[ $field_group ] ) ){
				// Merge configured arguments with arguments passed to function.
				// Arguments passed to function take precendence
				$args = array_merge( $fields[ $field_slug ], $args );
				// Break out of for loop once meta key is found
				break;
			}
		}

		// Handle taxonomy items
		if ( isset( $args[ 'taxonomy' ] ) && ! empty( $args[ 'taxonomy' ] ) ) {
			$field_value = wp_get_post_terms( $listing_id, $args[ 'taxonomy' ], array('fields' => 'names') );
		}

		if ( empty( $field_value ) ) $field_value = get_post_meta( $listing_id, '_' . $field_slug, TRUE );

		return $field_value;
	}

}

if ( ! function_exists( 'the_custom_field_output_as' ) ) {

	/**
	 * General function to output HTML for custom fields
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param       $field_slug
	 * @param null  $job_id
	 * @param       $field_value
	 * @param array $args
	 */
	function the_custom_field_output_as( $field_slug, $job_id = null, $field_value, $args = array() ) {

		if( $field_value !== 0 && empty( $field_value ) ) return;

		$tax_labels = array();
		$label_show_colon = true;

		// Handle multiple values output (probably file upload)
		if( is_array( $field_value ) && isset( $args['multiple'] ) && $args['multiple'] > 0 ){
			// Put label in its own div for mutliple output
			if( ! empty( $args[ 'output_show_label' ] ) ){
				echo "<div id=\"jmfe-wrap-{$field_slug}-multi-label\" class=\"jmfe-custom-field-wrap jmfe-custom-field-multi-label\">";
				echo "<strong id=\"jmfe-label-{$field_slug}\" class=\"jmfe-custom-field-label\">{$args['label']}:</strong> ";
				echo "</div>";
				$args[ 'output_show_label' ] = 0;
			}

			foreach( $field_value as $single_value ){
				the_custom_field_output_as( $field_slug, null, $single_value, $args );
			}
			return;
		}

		// Output array as comma separated values
		if( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );

		$output_as     = ( ! empty( $args[ 'output_as' ] ) ) ? $args[ 'output_as' ] : '';
		$extra_classes = ( ! empty( $args[ 'output_classes' ] ) ) ? $args[ 'output_classes' ] : '';

		$oembed_args = array();
		$oembed_args['width']  = ( ! empty( $args[ 'output_oembed_width' ] ) ) ? $args[ 'output_oembed_width' ] : '';
		$oembed_args['height'] = ( ! empty( $args[ 'output_oembed_height' ] ) ) ? $args[ 'output_oembed_height' ] : '';

		$open_wrapper  = "<div id=\"jmfe-wrap-{$field_slug}\" class=\"jmfe-custom-field-wrap\">";
		$close_wrapper = "</div>";

		// If li should be used, replace wrapper open and close HTML
		if( isset( $args[ 'li' ] ) && ! empty( $args[ 'li' ] ) ) {
			$open_wrapper = "<li id=\"jmfe-wrap-{$field_slug}\" class=\"jmfe-custom-field-wrap\">";
			$close_wrapper = "</li>";
		}

		// If output as is checkbox label only if checked, set output_show_label to true
		if( $output_as == 'checklabel' && (int) $field_value == 1 ) {
			$label_show_colon = false;
			$args[ 'output_show_label' ] = true;
		}

		$label = ( ! empty( $args[ 'output_show_label' ] ) && ! empty( $args[ 'label' ] ) ) ? $args[ 'label' ] : NULL;

		ob_start();

		echo $open_wrapper;

		// Show label if set
		if ( $label ) {
			if( $label_show_colon ) $label .= ':';
			echo "<strong id=\"jmfe-label-{$field_slug}\" class=\"jmfe-custom-field-label\">{$label}</strong> ";
		}

		switch( $output_as ){

			case 'oembed':
				$oembed_html = wp_oembed_get( $field_value, $oembed_args );

				// Exit and clear buffer if error with oembed
				if( ! $oembed_html ) {
					ob_end_clean();
					return;
				}

				echo "<div id=\"jmfe-custom-{$field_slug}\" class=\"jmfe-custom-field {$extra_classes}\">";
				echo $oembed_html;
				echo "</div>";
				break;

			case 'video':

				$video_width = ( isset( $args['output_video_width'] ) && ! empty( $args['output_video_width'] ) ) ? " width=\"" . $args[ 'output_video_width' ] . "\""  : '';
				$video_height = ( isset( $args['output_video_height'] ) && ! empty( $args['output_video_height'] ) ) ? " height=\"" . $args[ 'output_video_height' ] . "\""  : '';
				$video_poster = ( isset( $args['output_video_poster'] ) && ! empty( $args['output_video_poster'] ) ) ? " poster=\"" . $args[ 'output_video_poster' ] . "\""  : '';

				echo "<div id=\"jmfe-custom-{$field_slug}\" class=\"jmfe-custom-field {$extra_classes}\">";
				echo "<video src=\"{$field_value}\"{$video_width}{$video_height}{$video_poster} controls>";
				_e( "Sorry, your browser doesn't support embedded videos, you should upgrade to a modern browser.", 'wp-job-manager-field-editor' );
				if( isset( $args['output_video_allowdl'] ) && ! empty( $args['output_video_allowdl'] ) ) echo "<br />" . __( "Or you can", 'wp-job-manager-field-editor') . " <a href=\"{$field_value}\">" . __( "Download The File", 'wp-job-manager-field-editor' ) . "</a>. " . __("(right click and select Save As)", 'wp-job-manager-field-editor');
				echo "</video></div>";
				break;

			case 'link':
				if ( empty( $args[ 'output_caption' ] ) ) $args[ 'output_caption' ] = $field_value;
				echo "<a target=\"_blank\" id=\"jmfe-custom-{$field_slug}\" href=\"{$field_value}\" class=\"jmfe-custom-field {$extra_classes}\">{$args['output_caption']}</a>";
				break;

			case 'image':
				echo "<img id=\"jmfe-custom-{$field_slug}\" src=\"{$field_value}\" class=\"jmfe-custom-field {$extra_classes}\" />";
				break;

			// Output checkbox label only if checked has no output besides the label
			case 'checklabel':
				break;

			case 'checkcustom':
				$check_caption = '';
				// Checked
				if( (int) $field_value == 1 ){
					$check_caption = __( 'True', 'wp-job-manager-field-editor' );
					if( ! empty( $args[ 'output_check_true' ] ) ) $check_caption = $args[ 'output_check_true' ];
				}
				// Unchecked
				if( (int) $field_value == 0 ){
					$check_caption == __( 'False', 'wp-job-manager-field-editor' );
					if( ! empty( $args[ 'output_check_false' ] ) ) $check_caption = $args[ 'output_check_false' ];
				}

				echo "<div id=\"jmfe-custom-{$field_slug}\" class=\"jmfe-custom-field {$extra_classes}\">{$check_caption}</div>";
				break;

			default:
				echo "<div id=\"jmfe-custom-{$field_slug}\" class=\"jmfe-custom-field {$extra_classes}\">{$field_value}</div>";
				break;

		}

		echo $close_wrapper;

		ob_end_flush();
	}

}

if ( ! function_exists( 'wp_date_format_php_to_js') ){

	/**
	 * Convert a date format to a jQuery UI DatePicker format
	 *
	 * @param string $dateFormat a date format
	 *
	 * @return string
	 */
	function wp_date_format_php_to_js( $dateFormat ) {

		$chars = array(
			// Day
			'd' => 'dd',
			'j' => 'd',
			'l' => 'DD',
			'D' => 'D',
			// Month
			'm' => 'mm',
			'n' => 'm',
			'F' => 'MM',
			'M' => 'M',
			// Year
			'Y' => 'yy',
			'y' => 'y',
		);

		return strtr( (string) $dateFormat, $chars );
	}

}

if ( ! function_exists( 'get_attachment_id_from_url' ) ){

	/**
	 * Get image attachment ID from URL
	 *
	 * Will return the attachment ID of an image when provided the URL.
	 * This is commonly needed for getting image thumbnails, etc.
	 *
	 *
	 * @since 1.2.7
	 *
	 * @param string $attachment_url
	 *
	 * @return bool|null|string|void
	 */
	function get_attachment_id_from_url( $attachment_url = '' ) {

		global $wpdb;
		$attachment_id = FALSE;

		// If there is no url, return.
		if ( '' == $attachment_url ) return;

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( FALSE !== strpos( $attachment_url, $upload_dir_paths[ 'baseurl' ] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths[ 'baseurl' ] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}

}