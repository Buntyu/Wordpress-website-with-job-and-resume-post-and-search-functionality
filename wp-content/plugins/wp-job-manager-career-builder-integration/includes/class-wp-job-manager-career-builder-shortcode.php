<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Career_Builder_Shortcode
 */
class WP_Job_Manager_Career_Builder_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_job_manager_get_career_builder_listings', array( $this, 'get_jobs_for_shortcode' ) );
		add_action( 'wp_ajax_nopriv_job_manager_get_career_builder_listings', array( $this, 'get_jobs_for_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'career_builder_jobs', array( $this, 'career_builder_jobs_shortcode' ) );
	}

	/**
	 * Enqueue scripts
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'job-manager-career-builder', JOB_MANAGER_CAREER_BUILDER_PLUGIN_URL . '/assets/css/frontend.css' );
		wp_register_script( 'wp-job-manager-career-builder-jobs', JOB_MANAGER_CAREER_BUILDER_PLUGIN_URL . '/assets/js/jobs.js', array( 'jquery', 'wp-job-manager-ajax-filters' ), JOB_MANAGER_CAREER_BUILDER_VERSION, true );
		wp_localize_script( 'wp-job-manager-career-builder-jobs', 'job_manager_career_builder_jobs', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Get listings via ajax
	 */
	public function get_jobs_for_shortcode() {
		ob_start();

		add_filter( 'job_manager_career_builder_show_attribution', '__return_false' );

		$api_args = array( 'PageNumber' => absint( $_REQUEST['page'] ) );

		foreach ( (array) $_REQUEST['api_args'] as $key => $value ) {
			$api_args[ $key ] = sanitize_text_field( $value );
		}

		if ( ( $jobs = WP_Job_Manager_Career_Builder_API::get_jobs( $api_args ) ) && $jobs['jobs'] ) {
			echo WP_Job_Manager_Importer_Integration::get_jobs_html( $jobs['jobs'], 'career_builder', $this );
		}

		$result                  = array();
		$result['html']          = ob_get_clean();
		$result['found_jobs']    = ! empty( $jobs['jobs'] );
		$result['max_num_pages'] = $jobs['total_pages'];

		echo '<!--WPJM-->';
		echo json_encode( $result );
		echo '<!--WPJM_END-->';

		die();
	}

	/**
	 * career_builder jobs shortcode
	 *
	 * @param mixed $atts
	 */
	public function career_builder_jobs_shortcode( $atts ) {
		ob_start();

		$api_args = shortcode_atts( apply_filters( 'job_manager_career_builder_jobs_shortcode_defaults', array(
			'ExcludeKeywords'     => '',
			'ExcludeCompanyNames' => '',
			'ExcludeJobTitles'    => '',
			'JobTitle'            => '',
			'CompanyName'         => '',
			'CountryCode'         => get_option( 'job_manager_career_builder_default_country' ),
			'Keywords'            => get_option( 'job_manager_career_builder_default_keywords' ),
			'BooleanOperator'     => 'AND',
			'Location'            => get_option( 'job_manager_career_builder_default_location' ),
			'PerPage'             => 10,
			'OrderBy'             => 'Date', // Valid values are: Date, Pay, Title, Company, Distance, Location, and Relevance.
			'PageNumber'          => 1,
			'Radius'              => 20,
			'Tags'                => '',
			'Category'            => '',
			'EmpType'             => ''
		) ), $atts );

		if ( $api_args['Keywords'] === get_option( 'job_manager_career_builder_default_keywords' ) ) {
			$api_args['BooleanOperator'] = get_option( 'job_manager_career_builder_default_keywords_operator' );
		}

		$jobs = WP_Job_Manager_Career_Builder_API::get_jobs( $api_args );

		if ( $jobs['jobs'] ) {
			echo '<ul class="job_listings">';
			echo WP_Job_Manager_Importer_Integration::get_jobs_html( $jobs['jobs'], 'career_builder' );
			echo '</ul>';

			if ( $jobs['total_pages'] > 1 ) {
				wp_enqueue_script( 'wp-job-manager-career-builder-jobs' );
				echo '<a class="load_more_career_builder_jobs load_more_jobs" href="#"><strong>' . __( 'Load more job listings', 'wp-job-manager-career-builder-jobs' ) . '</strong></a>';
			}
		}

		return '<div class="career_builder_job_listings job_listings" data-api_args="' . esc_attr( json_encode( $api_args ) ) . '">' . ob_get_clean() . '</div>';
	}
}

new WP_Job_Manager_Career_Builder_Shortcode();