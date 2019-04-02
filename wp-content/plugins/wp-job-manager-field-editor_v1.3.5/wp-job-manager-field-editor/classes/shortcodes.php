<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_ShortCodes
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_ShortCodes {


	function __construct() {

		add_shortcode( 'job_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'company_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'custom_field', array( $this, 'shortcode_output' ) );
		add_shortcode( 'resume_field', array( $this, 'shortcode_output' ) );

	}

	/**
	 * Output for Shortcode
	 *
	 * @since 1.1.9
	 *
	 * @param $atts
	 *
	 * @return mixed|null
	 */
	function shortcode_output( $atts ) {

		$default_atts = array(
			'key'    => '',
			'field'  => '',
			'job_id' => get_the_ID(),
		);
		$merged_atts = array_merge( $default_atts, $atts );

		try {
			// Attributes
			$args = shortcode_atts( $merged_atts, $atts, 'jmfe' );

			if ( empty( $args['key'] ) && empty( $args['field'] ) ) {
				throw new Exception( __( 'Meta Key was not specified!', 'wp-job-manager-field-editor' ) );
			}

			if ( empty( $args['job_id'] ) ) {
				throw new Exception( __( 'Unable to determine correct job/resume/post ID!', 'wp-job-manager-field-editor' ) );
			}

			if( $args['key'] ) $meta_key = $args['key'];
			if( $args['field'] ) $meta_key = $args['field'];

			ob_start();
			the_custom_field( $meta_key, $args['job_id'], $args );
			$shortcode_output = ob_get_contents();
			ob_end_clean();

			return $shortcode_output;

		} catch ( Exception $error ) {

			error_log( 'Shortcode output error: ' . $error->getMessage() );

		}

	}
}

new WP_Job_Manager_Field_Editor_ShortCodes();