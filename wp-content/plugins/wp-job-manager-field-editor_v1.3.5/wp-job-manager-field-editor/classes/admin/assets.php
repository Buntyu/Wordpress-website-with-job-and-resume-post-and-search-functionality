<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Admin_Assets
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Admin_Assets {

	private $hooks;

	function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'in_admin_header', array( $this, 'add_popover_div' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'death_to_heartbeat' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'death_to_sloppy_devs' ), 99999 );

		$this->hooks = array(
			'job_listing_page_edit_job_fields',
			'job_listing_page_edit_company_fields',
			'job_listing_page_field-editor-settings',
			'resume_page_edit_resume_fields'
		);
	}


	/**
	 * Dequeue scripts/styles that conflict with plugin
	 *
	 * Sloppy developers eneuque their scripts and styles on all pages instead of
	 * only the pages they are needed on.  This almost always causes problems and
	 * to try and prevent this, I dequeue any known scripts/styles that have known
	 * compatibility issues.
	 *
	 * @since 1.2.1
	 *
	 * @param $hook
	 */
	function death_to_sloppy_devs( $hook ){
		// Return if not on plugin page, which some devs fail to check!
		if ( empty( $hook ) || ! empty( $hook ) && ! in_array( $hook, $this->hooks ) ) return;

		$assets = array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'scporderjs', 'kwayyhs-custom-js', 'mobiloud-menu-config', 'wp-seo-premium-quickedit-notification' );

		foreach( $assets as $asset ){ if( wp_script_is( $asset, 'enqueued' ) ) wp_dequeue_script( $asset ); }

	}

	/**
	 * Check if current page is one of plugin pages
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return bool
	 */
	function is_plugin_page(){

		global $pagenow;

		$plugin_pages = array(
			'edit_job_fields',
			'edit_company_fields',
			'edit_resume_fields',
			'edit_education_fields',
			'edit_links_fields',
			'edit_experience_fields',
			'field-editor-settings'
		);

		$current_page = ( isset( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : '' );

		if ( $pagenow == 'edit.php' && in_array( $current_page, $plugin_pages ) ) return true;

		return false;
	}

	/**
	 * Add <div> between #wpcontent and #body
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	function add_popover_div(){

		if( $this->is_plugin_page() ) echo "<div id=\"jmfe-popover-viewport\"></div>";

	}

	/**
	 * Register Vendor/Core CSS and Scripts
	 *
	 * @since 1.1.9
	 *
	 */
	function register_assets() {

		$styles          = '/assets/css/jmfe.min.css';
		$vendor_styles   = '/assets/css/vendor.min.css';
		$vendor_scripts  = '/assets/js/vendor.min.js';
		$radio           = '/assets/js/radio.min.js';
		$date            = '/assets/js/date.min.js';
		$vendor_phone    = '/assets/js/intlTelInput.min.js';
		$phone           = '/assets/js/phone.min.js';
		$scripts         = '/assets/js/jmfe.min.js';
		$scripts_version = WPJM_FIELD_EDITOR_VERSION;

		if ( defined( 'WPJMFE_DEBUG' ) ) {

			$styles          = '/assets/css/build/jmfe.css';
			$vendor_styles   = '/assets/css/build/vendor.css';
			$vendor_scripts  = '/assets/js/build/vendor.js';
			$radio           = '/assets/js/build/radio.js';
			$date            = '/assets/js/build/date.js';
			$vendor_phone    = '/assets/js/build/intlTelInput.js';
			$phone           = '/assets/js/build/phone.js';
			$scripts         = '/assets/js/build/jmfe.js';
			$scripts_version = filemtime( WPJM_FIELD_EDITOR_PLUGIN_DIR . $scripts );

		}

		wp_register_style( 'jmfe-styles', WPJM_FIELD_EDITOR_PLUGIN_URL . $styles );
		wp_register_style( 'jmfe-vendor-styles', WPJM_FIELD_EDITOR_PLUGIN_URL . $vendor_styles );
		// wp_register_style( 'jmfe-phone-field-style', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/css/intlTelInput.min.css', array(), WPJM_FIELD_EDITOR_VERSION );

		wp_register_script( 'jmfe-vendor-scripts', WPJM_FIELD_EDITOR_PLUGIN_URL . $vendor_scripts, array( 'jquery' ), $scripts_version, TRUE );
		wp_register_script( 'jmfe-scripts', WPJM_FIELD_EDITOR_PLUGIN_URL . $scripts, array( 'jquery' ), $scripts_version, TRUE );

		$assets = WP_Job_Manager_Field_Editor_Assets::get_instance();
		$assets->register_assets();
	}

	/**
	 * Enqueue already registered styles
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	public function enqueue_assets(){

		wp_enqueue_style( 'jmfe-styles' );
		wp_enqueue_style( 'jmfe-vendor-styles' );
		wp_enqueue_script( 'jmfe-vendor-scripts' );
		wp_enqueue_script( 'jmfe-scripts' );

	}

	/**
	 * Deregister WP Heartbeat Script
	 *
	 * @since 1.1.9
	 *
	 */
	function death_to_heartbeat() {

		if( $this->is_plugin_page() ) wp_deregister_script( 'heartbeat' );

	}
}