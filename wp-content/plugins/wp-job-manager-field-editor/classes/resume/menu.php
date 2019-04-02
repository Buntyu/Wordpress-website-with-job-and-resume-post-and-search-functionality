<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Resume_Menu
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Resume_Menu extends WP_Job_Manager_Field_Editor_Admin {

	private static $instance;

	function __construct() {

	}


	/**
	 * Add Resume Submenus
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	function submenu() {

		if ( empty( $this->capabilities ) ) $this->init_capabilities();

		// Resume
		$this->submenu_pages[] = add_submenu_page(
			'edit.php?post_type=resume',
			__( 'Resume Fields', 'wp-job-manager-field-editor' ),
			__( 'Resume Fields', 'wp-job-manager-field-editor' ),
			$this->capabilities[ 'resume' ],
			'edit_resume_fields',
			array( $this, 'fields_list_table' )
		);

		// Resume Education Fields
		$this->submenu_pages[] = add_submenu_page(
			'edit.php?post_type=resume',
			__( 'Education Fields', 'wp-job-manager-field-editor' ),
			__( 'Education Fields', 'wp-job-manager-field-editor' ),
			$this->capabilities[ 'resume' ],
			'edit_education_fields',
			array( $this, 'fields_list_table' )
		);

		// Resume Experience Fields
		$this->submenu_pages[] = add_submenu_page(
			'edit.php?post_type=resume',
			__( 'Experience Fields', 'wp-job-manager-field-editor' ),
			__( 'Experience Fields', 'wp-job-manager-field-editor' ),
			$this->capabilities[ 'resume' ],
			'edit_experience_fields',
			array( $this, 'fields_list_table' )
		);

		// Resume Links Fields
		$this->submenu_pages[] = add_submenu_page(
			'edit.php?post_type=resume',
			__( 'Links Fields', 'wp-job-manager-field-editor' ),
			__( 'Links Fields', 'wp-job-manager-field-editor' ),
			$this->capabilities[ 'resume' ],
			'edit_links_fields',
			array( $this, 'fields_list_table' )
		);

		return $this->submenu_pages;

	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Job_Manager_Field_Editor_Resume_Menu
	 */
	static function get_instance() {

		if ( NULL == self::$instance ) self::$instance = new self;

		return self::$instance;
	}
}