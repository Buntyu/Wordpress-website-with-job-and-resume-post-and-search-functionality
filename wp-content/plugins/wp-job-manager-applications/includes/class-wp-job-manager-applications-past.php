<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Applications_Past class.
 */
class WP_Job_Manager_Applications_Past {

	/**
	 * Constructor
	 */
	function __construct() {
 		add_shortcode( 'past_applications', array( $this, 'past_applications' ) );
    }

    /**
     * Past Applications
     */
    public function past_applications( $atts ) {
    	// If user is not logged in, abort
    	if ( ! is_user_logged_in() ) {
			do_action( 'job_manager_job_applications_past_logged_out' );
			return;
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

    	$args = apply_filters( 'job_manager_job_applications_past_args', array(
			'post_type'           => 'job_application',
			'post_status'         => array_keys( get_job_application_statuses() ),
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'ignore_sticky_posts' => 1,
			'meta_key'            => '_candidate_user_id',
			'meta_value'          => get_current_user_id(),
		) );

		$applications = new WP_Query( $args );

		ob_start();

		if ( $applications->have_posts() ) {
			get_job_manager_template( 'past-applications.php', array( 'applications' => $applications->posts, 'max_num_pages' => $applications->max_num_pages ), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
		} else {
			get_job_manager_template( 'past-applications-none.php', array(), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
		}

		return ob_get_clean();
    }

}

new WP_Job_Manager_Applications_Past();