<?php
/*
Plugin Name: WP Job Manager - ZipRecruiter Integration
Plugin URI: https://wpjobmanager.com/add-ons/ziprecruiter-integration/
Description: Query and show results from ZipRecruiter using the ZipSearch API. Note: ZipRecruiter jobs will be displayed in list format linking offsite (without full descriptions).
Version: 1.0.0
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 4.1

	Copyright: 2015 Mike Jolley
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Updater
if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

// Import Framework
if ( ! class_exists( 'WP_Job_Manager_Importer_Integration' ) ) {
	include_once( 'includes/import-framework/class-wp-job-manager-importer-integration.php' );
}

/**
 * WP_Job_Manager_ZipRecruiter_Integration class.
 */
class WP_Job_Manager_ZipRecruiter_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Define constants
		define( 'JOB_MANAGER_ZIPRECRUITER_VERSION', '1.0.0' );
		define( 'JOB_MANAGER_ZIPRECRUITER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_MANAGER_ZIPRECRUITER_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Add actions
		add_action( 'init', array( $this, 'init' ), 12 );
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_action( 'job_manager_imported_jobs_start', array( $this, 'add_attribution' ) );

		include_once( 'includes/class-wp-job-manager-ziprecruiter-import.php' );
		include_once( 'includes/class-wp-job-manager-ziprecruiter-api.php' );
		include_once( 'includes/class-wp-job-manager-ziprecruiter-shortcode.php' );
	}

	/**
	 * Localisation
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-ziprecruiter-integration' );
		load_textdomain( 'wp-job-manager-ziprecruiter-integration', WP_LANG_DIR . "/wp-job-manager-ziprecruiter-integration/wp-job-manager-ziprecruiter-integration-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-ziprecruiter-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function job_manager_settings( $settings = array() ) {
		$settings['ziprecruiter_integration'] = array(
			__( 'ZipRecruiter', 'wp-job-manager-ziprecruiter-integration' ),
			apply_filters(
				'wp_job_manager_ziprecruiter_integration_settings',
				array(
					array(
						'name' 		=> 'job_manager_ziprecruiter_key',
						'std' 		=> '',
						'label' 	=> __( 'API Key', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> sprintf( __( 'To show search results from ZipRecruiter you will need an API key. %sObtain this here%s.', 'wp-job-manager-ziprecruiter-integration' ), '<a href="https://docs.google.com/a/a8c.com/forms/d/1pryhdX1INYUhMEIx0pamF-_9dMEbX_39R0DNdDUL8fw/viewform">', '</a>' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_backfill',
						'std' 		=> 10,
						'label'     => __( 'Backfilling (no results)', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'If there are no <strong>local</strong> jobs found, backfill with X jobs from ZipRecruiter instead. Leave blank or set to 0 to disable.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_before_jobs',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill before jobs', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Show a maximum of X jobs from ZipRecruiter above your local job listings. Leave blank or set to 0 to disable.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_after_jobs',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill after jobs', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Show a maximum of X jobs from ZipRecruiter after the last page of your local job listings. Leave blank or set to 0 to disable.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_per_page',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill per page', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'For each page of local jobs loaded, show a maximum of X jobs from ZipRecruiter. Leave blank or set to 0 to disable.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_default_keywords',
						'std' 		=> 'Web Developer',
						'label' 	=> __( 'Default Keywords', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Enter keywords to search for by default. Surround multiple terms in quotes to treat them as a single phrase. These will be overridden when a user enters their own keywords.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_exclude_keywords',
						'std' 		=> '',
						'label' 	=> __( 'Exclude Keywords', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Comma separate keywords and phrases to exclude from all searches.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_require_keywords',
						'std' 		=> '',
						'label' 	=> __( 'Require Keywords', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Comma separate keywords and phrases to require for all searches.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_ziprecruiter_default_location',
						'std' 		=> '',
						'label' 	=> __( 'Default location', 'wp-job-manager-ziprecruiter-integration' ),
						'desc'		=> __( 'Enter a location to search for by default. This will be overridden when a user enters their own location.', 'wp-job-manager-ziprecruiter-integration' ),
						'type'      => 'input'
					)
				)
			)
		);
		return $settings;
	}

	/**
	 * Add attribution
	 */
	public function add_attribution( $source ) {
		if ( 'ziprecruiter' === $source && apply_filters( 'job_manager_ziprecruiter_show_attribution', true ) ) {
			get_job_manager_template_part( 'content', 'attribution', 'ziprecruiter', JOB_MANAGER_ZIPRECRUITER_PLUGIN_DIR . '/templates/' );
		}
	}
}

new WP_Job_Manager_ZipRecruiter_Integration();
new WPJM_Updater( __FILE__ );
