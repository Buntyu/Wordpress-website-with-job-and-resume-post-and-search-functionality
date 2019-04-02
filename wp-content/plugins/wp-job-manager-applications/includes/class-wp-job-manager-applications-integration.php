<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Applications_Integration class.
 *
 * Integrates the applications plugin with other form plugins.
 */
class WP_Job_Manager_Applications_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Integrate apply with LinkedIn, XING and/or Facebook if forms are enabled
		if ( get_option( 'job_application_form_for_url_method', '1' ) ) {
			add_filter( 'wp_job_manager_apply_with_linkedin_enable_http_post', '__return_true' );
			add_filter( 'wp_job_manager_apply_with_xing_enable_http_post', '__return_true' );
			add_filter( 'wp_job_manager_apply_with_facebook_enable_http_post', '__return_true' );
		}

		add_action( 'wp_job_manager_apply_with_linkedin_application', array( $this, 'handle_apply_with_linkedin' ), 10, 3 );
		add_action( 'wp_job_manager_apply_with_xing_application', array( $this, 'handle_apply_with_xing' ), 10, 3 );
		add_action( 'wp_job_manager_apply_with_facebook_application', array( $this, 'handle_apply_with_facebook' ), 10, 4 );

		// Integrate with Resume Manager's apply form
		add_action( 'applied_with_resume', array( $this, 'handle_applied_with_resume' ), 10, 5 );
	}

	/**
	 * Handle an application from LinkedIn
	 * @param  array $application
	 */
	public function handle_apply_with_linkedin( $job_id, $profile_data, $cover_letter ) {
		if ( ! $job_id || empty( $profile_data ) ) {
			return;
		}

		$candidate_name      = $profile_data->formattedName;
		$candidate_email     = $profile_data->emailAddress;
		$application_message = $cover_letter;
		$application_meta    = array();

		if ( ! $application_message ) {
			$application_message = $profile_data->headline;
		} else {
			$application_meta[ __( 'Title', 'wp-job-manager-applications' ) ] = $profile_data->headline;
		}

		// Add meta data from submitted profile
		$application_meta[ __( 'Location', 'wp-job-manager-applications' ) ]     = $profile_data->location->name;
		$application_meta[ __( 'Full Profile', 'wp-job-manager-applications' ) ] = $profile_data->publicProfileUrl;

		create_job_application( $job_id, $candidate_name, $candidate_email, $application_message, $application_meta, false, 'linkedin' );
	}

	/**
	 * Handle an application from XING
	 * @param  array $application
	 */
	public function handle_apply_with_xing( $job_id, $profile_data, $cover_letter ) {
		if ( ! $job_id || empty( $profile_data ) ) {
			return;
		}

		$candidate_name      = $profile_data->display_name;
		$candidate_email     = $profile_data->active_email;
		$application_message = $cover_letter;
		$application_meta    = array();

		if ( ! $application_message ) {
			$application_message = $profile_data->haves;
		} else {
			$application_meta[ __( 'Skills', 'wp-job-manager-applications' ) ] = $profile_data->haves;
		}

		$location = __( 'Unknown location', 'wp-job-manager-applications' );
		$address  = false;

		if ( $profile_data->business_address ) {
			$address = $profile_data->business_address;
		} elseif ( $profile_data->private_address ) {
			$address = $profile_data->private_address;
		}

		if ( $address ) {
			$location = '';
			if ( $address->city ) {
				$location = $address->city . ', ';
			}
			$location .= $address->country;
		}

		// Add meta data from submitted profile
		$application_meta[ __( 'Location', 'wp-job-manager-applications' ) ]     = $location;
		$application_meta[ __( 'Full Profile', 'wp-job-manager-applications' ) ] = $profile_data->permalink;

		create_job_application( $job_id, $candidate_name, $candidate_email, $application_message, $application_meta, false, 'xing' );
	}

	/**
	 * Handle an application from Facebook
	 * @param  array $application
	 */
	public function handle_apply_with_facebook( $job_id, $profile_data, $profile_picture, $cover_letter ) {
		if ( ! $job_id || empty( $profile_data ) ) {
			return;
		}

		$candidate_name      = $profile_data->name;
		$candidate_email     = $profile_data->email;
		$application_message = $cover_letter;
		$application_meta    = array();

		if ( ! $application_message ) {
			$application_message = $profile_data->bio;
		} else {
			$application_meta[ __( 'Title', 'wp-job-manager-applications' ) ] = $profile_data->bio;
		}

		// Add meta data from submitted profile
		$application_meta[ __( 'Location', 'wp-job-manager-applications' ) ]     = $profile_data->location->name;
		$application_meta[ __( 'Full Profile', 'wp-job-manager-applications' ) ] = $profile_data->link;

		create_job_application( $job_id, $candidate_name, $candidate_email, $application_message, $application_meta, false, 'facebook' );
	}

	/**
	 * Handle applications via Resume Manager's Form
	 * @param  int $user_id
	 * @param  int $job_id
	 * @param  int $resume_id
	 * @param  string $application_message
	 */
	public function handle_applied_with_resume( $user_id, $job_id, $resume_id, $application_message, $sent_email = true ) {
		if ( ! $job_id ) {
			return;
		}

		$user            = get_user_by( 'id', $user_id );
		$resume_link     = get_resume_share_link( $resume_id );
		$candidate_name  = get_post_meta( $resume_id, '_candidate_name', true );
		$candidate_email = get_post_meta( $resume_id, '_candidate_email', true );

		if ( empty( $candidate_email ) ) {
			$candidate_email = $user->user_email;
		}

		$application_meta               = array();
		$application_meta['_resume_id'] = $resume_id;

		$get_meta = array(
			'_candidate_title'    => __( 'Title', 'wp-job-manager-applications' ),
			'_candidate_location' => __( 'Location', 'wp-job-manager-applications' )
		);

		foreach ( $get_meta as $key => $label ) {
			if ( $value = get_post_meta( $resume_id, $key, true ) ) {
				$application_meta[ $label ] = $value;
			}
		}

		create_job_application( $job_id, $candidate_name, $candidate_email, $application_message, $application_meta, ! $sent_email, 'resume-manager' );
	}
}
new WP_Job_Manager_Applications_Integration();