<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WP_Job_Manager_SPL_Stripe
 */
class WP_Job_Manager_SPL_Stripe extends WP_Job_Manager_SPL_Gateway {

	private $api_endpoint = 'https://api.stripe.com/';

	/**
	 * __construct function.
	 */
	public function __construct() {
		$this->gateway_id   = 'stripe';
		$this->gateway_name = __( 'Stripe Checkout', 'job_manager' );
		$this->settings     = array(
			array(
				'name' 		=> 'job_manager_spl_stripe_testmode',
				'std' 		=> 'no',
				'label' 	=> __( 'Test Mode', 'job_manager' ),
				'desc'		=> __( 'Enable Test Mode', 'job_manager' ),
				'options'   => array(
					'yes' => __( 'Yes', 'job_manager' ),
					'no' => __( 'No', 'job_manager' ),
				),
				'type'      => 'select',
				'class'     => 'gateway-settings gateway-settings-stripe'
			),
			array(
				'name' 		=> 'job_manager_spl_stripe_secret_key',
				'std' 		=> '',
				'label' 	=> __( 'Secret Key', 'job_manager' ),
				'desc'		=> __( 'Get your API keys from your stripe account.', 'job_manager' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-stripe'
			),
			array(
				'name' 		=> 'job_manager_spl_stripe_publishable_key',
				'std' 		=> '',
				'label' 	=> __( 'Publishable Key', 'job_manager' ),
				'desc'		=> __( 'Get your API keys from your stripe account.', 'job_manager' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-stripe'
			)
		);
		parent::__construct();

		if ( WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		}
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		wp_enqueue_script( 'stripe', 'https://checkout.stripe.com/v2/checkout.js', '', '2.0', true );
		wp_enqueue_script( 'wp-job-manager-spl-stripe', JOB_MANAGER_SPL_PLUGIN_URL . '/assets/js/stripe-checkout.js', array( 'jquery', 'stripe' ), '1.0', true );

		// Get email address
		$email = false;

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$email        = $current_user->user_email;
		} else {
			$job_id = ! empty( $_REQUEST['job_id'] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

			if ( $job_id && ( $application = get_post_meta( $job_id, '_application', true ) ) && is_email( $application ) ) {
				$email = $application;
			}

			if ( ! empty( $_POST['application'] ) && is_email( $_POST['application'] ) ) {
				$email = $_POST['application'];
			}
		}

		wp_localize_script( 'wp-job-manager-spl-stripe', 'stripe_checkout_params', array(
			'key'         => get_option( 'job_manager_spl_stripe_publishable_key' ),
			'label'       => __( 'Pay for job', 'wp-job-manager-simple-paid-listings' ),
			'amount'      => WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() * 100,
			'currency'    => strtolower( get_option( 'job_manager_spl_currency' ) ),
			'name'        => get_bloginfo( 'name' ),
			'email'       => $email
		) );
	}

	/**
	 * Pay for a job listing action
	 */
	public function pay_for_listing( $job_id ) {

		try {
			$stripe_token = isset( $_POST['stripe_token'] ) ? sanitize_text_field( $_POST['stripe_token'] ) : '';

			if ( empty( $stripe_token ) )
				throw new Exception( __( 'Please make sure your card details have been entered correctly and that your browser supports JavaScript.', 'wp-job-manager-simple-paid-listings' ) );

			$response = wp_remote_post( $this->api_endpoint . 'v1/charges', array(
					'method'		=> 'POST',
					'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( get_option( 'job_manager_spl_stripe_secret_key' ) . ':' )
				),
				'body' 			=> array(
					'amount'      => WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() * 100,
					'currency'    => strtolower( get_option( 'job_manager_spl_currency' ) ),
					'description' => __( 'New Job Listing', 'wp-job-manager-simple-paid-listings' ) . ' &quot;' . get_the_title( $job_id ) . '&quot;',
					'capture'     => 'true',
					'card'        => $stripe_token
				),
				'timeout' 		=> 60,
				'sslverify' 	=> false,
				'user-agent' 	=> 'WP_Job_Manager'
			));

			if ( is_wp_error($response) )
				throw new Exception( __( 'There was a problem connecting to the gateway.', 'wp-job-manager-simple-paid-listings' ) );

			if( empty( $response['body'] ) )
				throw new Exception( __( 'Empty response.', 'wp-job-manager-simple-paid-listings' ) );

			$parsed_response = json_decode( $response['body'] );

			// Handle response
			if ( ! empty( $parsed_response->error ) ) {

				throw new Exception( $parsed_response->error->message );

			} elseif ( empty( $parsed_response->id ) ) {

				throw new Exception( __( 'Invalid response.', 'wp-job-manager-simple-paid-listings' ) );

			} else {

				// Store charge ID
				update_post_meta( $job_id, 'Charge ID', $parsed_response->id );
				update_post_meta( $job_id, 'Payment ID', $parsed_response->id );
				update_post_meta( $job_id, 'Stripe Fee', number_format( $parsed_response->fee / 100, 2, '.', '' ) );

				// Notify admin
				if ( get_option( 'job_manager_submission_requires_approval' ) )
                	$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job is ready for admin approval.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );
               	else
               		$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job has been automatically approved.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );

				$this->payment_complete( $job_id );

				return true;
			}

		} catch( Exception $e ) {
			WP_Job_Manager_Form_Submit_Job::add_error( $e->getMessage() );
			return false;
		}
	}
}

return new WP_Job_Manager_SPL_Stripe();