<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Description of UM_WP_Job_Manager
 *
 * @author kishore
 */
if ( ! class_exists( 'UM_WP_Job_Manager' ) ) {
	
	class UM_WP_Job_Manager {
		
		function __construct() {
			global $ultimatemember;
			// Define constants
			$this->define_constants();
			
			// Include required files
			$this->includes();
		}
		
		function includes() {
			// Includes
			include( ULTIMATE_MEMBER_WP_JOB_MANAGER .'um-wp-job-manager/um-wp-job-manager-loader.php' );
			include( ULTIMATE_MEMBER_WP_JOB_MANAGER .'um-wp-job-manager/um-wp-job-manager-functions.php' );
			
			// Includes shortcode-action-handler
			include( ULTIMATE_MEMBER_WP_JOB_MANAGER .'shortcode-action-handler/class-um-wp-job-manager-shortcode-action-handler.php' );
			
		}
		
		function define_constants() {
			// for constants
		}
	
	}
	
}

$GLOBALS['um_wp_job_manager'] = new UM_WP_Job_Manager();
