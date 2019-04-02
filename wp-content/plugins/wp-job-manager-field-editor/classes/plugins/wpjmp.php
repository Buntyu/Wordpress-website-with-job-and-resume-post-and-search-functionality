<?php

if( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_Plugins_WPJMP {

	/**
	 * WP_Job_Manager_Field_Editor_Plugins_WPJMP constructor.
	 */
	public function __construct() {

		add_filter( 'job_manager_field_editor_job_init_fields', array( $this, 'check_empty_options' ) );

	}

	function check_empty_options( $fields ){
		// Return standard fields if company key, or products key in company is not set
		if( ! isset( $fields['company' ], $fields['company']['products'] ) ) return $fields;
		// Unset field if options is not set
		if( ! isset($fields['company']['products']['options'] ) ) unset($fields['company']['products']);
		// Unset field if options array is empty
		if( empty($fields['company']['products']['options'] ) ) unset( $fields['company']['products'] );

		return $fields;
	}
}

if( class_exists( 'WP_Job_Manager_Products' ) ) new WP_Job_Manager_Field_Editor_Plugins_WPJMP();
