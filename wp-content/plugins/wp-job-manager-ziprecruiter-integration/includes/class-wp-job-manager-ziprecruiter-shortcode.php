<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_ZipRecruiter_Shortcode
 */
class WP_Job_Manager_ZipRecruiter_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_job_manager_get_ziprecruiter_listings', array( $this, 'get_jobs_for_shortcode' ) );
		add_action( 'wp_ajax_nopriv_job_manager_get_ziprecruiter_listings', array( $this, 'get_jobs_for_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'ziprecruiter_jobs', array( $this, 'ziprecruiter_jobs_shortcode' ) );
	}

	/**
	 * Enqueue scripts
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'job-manager-ziprecruiter', JOB_MANAGER_ZIPRECRUITER_PLUGIN_URL . '/assets/css/frontend.css' );
		wp_register_script( 'wp-job-manager-ziprecruiter-jobs', JOB_MANAGER_ZIPRECRUITER_PLUGIN_URL . '/assets/js/jobs.js', array( 'jquery', 'wp-job-manager-ajax-filters' ), JOB_MANAGER_ZIPRECRUITER_VERSION, true );
		wp_localize_script( 'wp-job-manager-ziprecruiter-jobs', 'job_manager_ziprecruiter_jobs', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Get listings via ajax
	 */
	public function get_jobs_for_shortcode() {
		ob_start();

		add_filter( 'job_manager_ziprecruiter_show_attribution', '__return_false' );

		foreach ( (array) $_REQUEST['api_args'] as $key => $value ) {
			$api_args[ $key ] = sanitize_text_field( $value );
		}

		$api_args = array( 'page' => absint( $_REQUEST['page'] ) );

		if ( ( $jobs = WP_Job_Manager_ZipRecruiter_API::get_jobs( $api_args ) ) && $jobs['jobs'] ) {
			echo WP_Job_Manager_Importer_Integration::get_jobs_html( $jobs['jobs'], 'ziprecruiter', $this );
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
	 * ziprecruiter jobs shortcode
	 *
	 * @param mixed $atts
	 */
	public function ziprecruiter_jobs_shortcode( $atts ) {
		ob_start();

		$api_args = shortcode_atts( apply_filters( 'job_manager_ziprecruiter_jobs_shortcode_defaults', array(
			'search'        => get_option( 'job_manager_ziprecruiter_default_keywords' ),
			'location'      => get_option( 'job_manager_ziprecruiter_default_location' ),
			'page'          => 1,
			'jobs_per_page' => 10,
			'radius_miles'  => 20
		) ), $atts );

		$jobs = WP_Job_Manager_ZipRecruiter_API::get_jobs( $api_args );

		if ( $jobs['jobs'] ) {
			echo '<ul class="job_listings">';
			echo WP_Job_Manager_Importer_Integration::get_jobs_html( $jobs['jobs'], 'ziprecruiter' );
			echo '</ul>';

			if ( $jobs['total_pages'] > 1 ) {
				wp_enqueue_script( 'wp-job-manager-ziprecruiter-jobs' );
				echo '<a class="load_more_ziprecruiter_jobs load_more_jobs" href="#"><strong>' . __( 'Load more job listings', 'wp-job-manager-ziprecruiter-jobs' ) . '</strong></a>';
			}
		}

		return '<div class="ziprecruiter_job_listings job_listings" data-api_args="' . esc_attr( json_encode( $api_args ) ) . '">' . ob_get_clean() . '</div>';
	}
}

new WP_Job_Manager_ZipRecruiter_Shortcode();