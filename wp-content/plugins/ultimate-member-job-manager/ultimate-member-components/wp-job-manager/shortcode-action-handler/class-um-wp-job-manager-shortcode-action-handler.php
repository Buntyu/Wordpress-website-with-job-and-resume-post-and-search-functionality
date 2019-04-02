<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WP_Job_Manager_Shortcodes' ) ) {
	/**
	 * UM_WP_Job_Manager_Shortcode_Action_Handler class.
	 */
	class UM_WP_Job_Manager_Shortcode_Action_Handler extends WP_Job_Manager_Shortcodes {
	
		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'shortcode_action_handler' ) );
		}
	
		/**
		 * Handle actions which need to be run before the shortcode e.g. post actions
		 */
		public function shortcode_action_handler() {
			global $post;
			$this->job_dashboard_handler();
		}
	}
	
	new UM_WP_Job_Manager_Shortcode_Action_Handler();
}

if ( class_exists( 'WP_Job_Manager_Alerts_Shortcodes' ) ) {
	/**
	 * UM_WP_Job_Manager_Alerts_Shortcodes_Action_Handler class.
	 */
	class UM_WP_Job_Manager_Alerts_Shortcodes_Action_Handler extends WP_Job_Manager_Alerts_Shortcodes {
	
		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			add_action( 'wp', array( $this, 'shortcode_action_handler' ) );
	
			$this->action = isset( $_REQUEST['action'] ) ? sanitize_title( $_REQUEST['action'] ) : '';
		}
	
		/**
		 * Handle actions which need to be run before the shortcode e.g. post actions
		 */
		public function shortcode_action_handler() {
			global $post;
	
			$this->job_alerts_handler();
		}
	}
	
	new UM_WP_Job_Manager_Alerts_Shortcodes_Action_Handler();
}

if ( class_exists( 'WP_Job_Manager_Applications_Dashboard' ) ) {
	
	/**
	 * UM_WP_Job_Manager_Applications_Dashboard_Shortcodes_CSV_Handler class.
	 */
	class UM_WP_Job_Manager_Applications_Dashboard_Shortcodes_CSV_Handler extends  WP_Job_Manager_Applications_Dashboard {
	
		/**
		 * __construct function.
		 */
		public function __construct() {
			remove_action( 'wp', array( $this, 'csv_handler' ) );
			add_action( 'wp_loaded', array( $this, 'csv_handler' ) );
		}
	}
	
	new UM_WP_Job_Manager_Applications_Dashboard_Shortcodes_CSV_Handler();
}