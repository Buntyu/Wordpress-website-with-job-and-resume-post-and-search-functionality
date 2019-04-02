<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Job_Manager_Form' ) )
	include( JOB_MANAGER_PLUGIN_DIR . '/includes/abstracts/abstract-wp-job-manager-form.php' );

if ( ! class_exists( 'WP_Resume_Manager_Form_Submit_Resume' ) )
	require_once( RESUME_MANAGER_PLUGIN_DIR . '/includes/forms/class-wp-resume-manager-form-submit-resume.php' );

/**
 * Class WP_Job_Manager_Field_Editor_Resume_Legacy_Submit_Form
 *
 * @since 1.1.9
 *
 */
Class WP_Job_Manager_Field_Editor_Resume_Legacy_Submit_Form extends WP_Resume_Manager_Form_Submit_Resume {

	function __construct() {

	}

	function get_current_step(){
		return parent::$step;
	}

	/**
	 * Null Resume $fields
	 *
	 * @since 1.1.9
	 *
	 */
	function remove_traces(){
		self::$fields = null;
	}

	/**
	 * Null and regenerate Resume $fields
	 *
	 * @since 1.1.9
	 *
	 * @param string $type
	 */
	function regenerate_fields( $type ){

		$this->remove_traces();

		if( $type == 'resume_fields' ){
			self::init_fields();
		}
	}

	/**
	 * Get default Resume $fields
	 *
	 * @since 1.1.9
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	function get_default_fields( $type ){

		$this->regenerate_fields( $type );

		return self::get_fields( $type );
	}

	/**
	 * Force Check for Validation Errors
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return bool
	 * @throws \Exception
	 */
	function validation_errors() {

		try {

			$values   = self::get_posted_fields();

			if ( is_wp_error( ( $return = self::validate_fields( $values ) ) ) ) {
				throw new Exception( TRUE );
			}

			$this->remove_traces();

			return FALSE;

		} catch ( Exception $e ) {

			$this->remove_traces();
			return TRUE;
		}

	}
}