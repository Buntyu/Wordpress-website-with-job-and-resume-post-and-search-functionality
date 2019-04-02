<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Requires
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Requires {

	private $config = array();
	private $required_plugins = array();

	function __construct() {

		add_action( 'admin_notices', array($this, 'check_requires') );

	}

	/**
	 * Check required plugins are activated/installed
	 *
	 * @since 1.1.9
	 *
	 */
	function check_requires() {

		if ( ! defined( 'JOB_MANAGER_VERSION' ) ) {
			?>
			<div class="error"><p><?php _e( 'WP Job Manager <strong>MUST</strong> be installed and activated for WP Job Manager Field Editor to work!', 'wp-job-manager-field-editor' ); ?></p></div><?php
		}

	}

}

new WP_Job_Manager_Field_Editor_Requires();