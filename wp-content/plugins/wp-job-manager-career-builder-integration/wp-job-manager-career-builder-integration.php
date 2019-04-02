<?php
/*
Plugin Name: WP Job Manager - Career Builder Integration
Plugin URI: https://wpjobmanager.com/add-ons/career-builder-integration/
Description: Query and show sponsored results from Career Builder when listing jobs and list Career Builder jobs via a shortcode. Note: Career Builder jobs will be displayed in list format linking offsite (without full descriptions). Requires SimpleXML support on the server.
Version: 1.0.5
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 4.1

	Copyright: 2013 Mike Jolley
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
 * WP_Job_Manager_Career_Builder_Integration class.
 */
class WP_Job_Manager_Career_Builder_Integration {

	/**
	 * __construct function.
	 */
	public function __construct() {
		// Define constants
		define( 'JOB_MANAGER_CAREER_BUILDER_VERSION', '1.0.5' );
		define( 'JOB_MANAGER_CAREER_BUILDER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_MANAGER_CAREER_BUILDER_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Add actions
		add_action( 'init', array( $this, 'init' ), 12 );
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_action( 'job_manager_imported_jobs_start', array( $this, 'add_attribution' ) );

		include_once( 'includes/class-wp-job-manager-career-builder-import.php' );
		include_once( 'includes/class-wp-job-manager-career-builder-api.php' );
		include_once( 'includes/class-wp-job-manager-career-builder-shortcode.php' );
		include_once( 'includes/class-wp-job-manager-career-builder-categories.php' );

		// Install and uninstall
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activation_hook' ), 10 );
	}

	/**
	 * Runs on activation
	 */
	public function activation_hook() {
		if ( ! function_exists( 'simplexml_load_string' ) ) {
	        deactivate_plugins( basename( __FILE__ ) );
	        wp_die( "Sorry, but you cannot run this plugin, it requires the SimpleXML library installed on your server/hosting to function." , 'wp-job-manager-career-builder-integration' );
		}
	}

	/**
	 * Localisation
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-career-builder-integration' );
		load_textdomain( 'wp-job-manager-career-builder-integration', WP_LANG_DIR . "/wp-job-manager-career-builder-integration/wp-job-manager-career-builder-integration-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-career-builder-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function job_manager_settings( $settings = array() ) {
		$settings['career_builder_integration'] = array(
			__( 'Career Builder', 'wp-job-manager-career-builder-integration' ),
			apply_filters(
				'wp_job_manager_career_builder_integration_settings',
				array(
					array(
						'name' 		=> 'job_manager_career_builder_key',
						'std' 		=> '',
						'label' 	=> __( 'Developer Key', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'To show search results from Career Builder you will need a developer key. Obtain this here: http://developer.careerbuilder.com/', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_backfill',
						'std' 		=> 10,
						'label'     => __( 'Backfilling (no results)', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'If there are no jobs found, backfill with X jobs from career_builder instead. Leave blank or set to 0 to disable.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_before_jobs',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill before jobs', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Show a maximum of X jobs from career_builder above your job listings. Leave blank or set to 0 to disable.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_after_jobs',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill after jobs', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Show a maximum of X jobs from career_builder after the last page of your job listings. Leave blank or set to 0 to disable.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_per_page',
						'std' 		=> '0',
						'label' 	=> __( 'Backfill per page', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'For each page of jobs loaded, show a maximum of X jobs from career_builder. Leave blank or set to 0 to disable.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),

					array(
						'name' 		=> 'job_manager_career_builder_default_keywords',
						'std' 		=> 'Web Developer',
						'label' 	=> __( 'Default Keywords', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter keywords to search for by default. Comma-separate multiple values. These will be overridden when a user enters their own keywords.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_default_keywords_operator',
						'std' 		=> 'Web Developer',
						'label' 	=> __( 'Default Keyword Operator', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Choose "AND" to find jobs matching all of your default keywords, or "OR" to find jobs matching any of your keywords.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'select',
						'options'   => array(
							'AND' => __( "AND", 'wp-job-manager-career-builder-integration' ),
							'OR'  => __( "OR", 'wp-job-manager-career-builder-integration' )
						)
					),
					array(
						'name' 		=> 'job_manager_career_builder_default_location',
						'std' 		=> '',
						'label' 	=> __( 'Default location', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter a location to search for by default. This will be overridden when a user enters their own location.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_default_country',
						'std' 		=> 'us',
						'label' 	=> __( 'Default country', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Choose a default country to show jobs from.  This will be overridden when a user enters their own location.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),

					array(
						'name' 		=> 'job_manager_career_builder_exclude_keywords',
						'std' 		=> '',
						'label' 	=> __( 'Exclude Keywords', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter keywords to exclude from searches. Comma-separate multiple values.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_exclude_job_title',
						'std' 		=> '',
						'label' 	=> __( 'Exclude Job Titles', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter job titles to exclude from searches. Comma-separate multiple values.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_exclude_company',
						'std' 		=> '',
						'label' 	=> __( 'Exclude Company Names', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter company names to exclude from searches. Comma-separate multiple values.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),

					array(
						'name' 		=> 'job_manager_career_builder_job_title',
						'std' 		=> '',
						'label' 	=> __( 'Limit Results to Job Title', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Accepts a single value. Will return only jobs with the provided job title in their title.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_company',
						'std' 		=> '',
						'label' 	=> __( 'Limit Results to Company', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Accepts a single value. Will limit the Job Search to companies whose names contain the value provided.', 'wp-job-manager-career-builder-integration' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_career_builder_country',
						'std' 		=> '',
						'label' 	=> __( 'Limit Results to Country', 'wp-job-manager-career-builder-integration' ),
						'desc'		=> __( 'Enter a country code to limit results to jobs only within this country.', 'wp-job-manager-career-builder-integration' ),
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
		if ( 'career_builder' === $source && apply_filters( 'job_manager_career_builder_show_attribution', true ) ) {
			get_job_manager_template_part( 'content', 'attribution', 'career-builder', JOB_MANAGER_CAREER_BUILDER_PLUGIN_DIR . '/templates/' );
		}
	}
}

new WP_Job_Manager_Career_Builder_Integration();
new WPJM_Updater( __FILE__ );