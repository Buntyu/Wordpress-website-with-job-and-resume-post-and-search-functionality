<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WP_Job_Manager_SPL_PayPal
 */
class WP_Job_Manager_SPL_PayPal extends WP_Job_Manager_SPL_Gateway {

	private $liveurl = 'https://www.paypal.com/cgi-bin/webscr';
	private $testurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	/**
	 * __construct function.
	 */
	public function __construct() {
		$this->gateway_id   = 'paypal';
		$this->gateway_name = __( 'PayPal Standard', 'wp-job-manager-simple-paid-listings' );
		$this->settings     = array(
			array(
				'name' 		=> 'job_manager_spl_paypal_email',
				'std' 		=> '',
				'label' 	=> __( 'PayPal Email', 'wp-job-manager-simple-paid-listings' ),
				'desc'		=> __( 'Your PayPal email address.', 'wp-job-manager-simple-paid-listings' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-paypal'
			),
			array(
				'name' 		=> 'job_manager_spl_paypal_identity_token',
				'std' 		=> '',
				'label' 	=> __( 'PayPal Identity Token', 'wp-job-manager-simple-paid-listings' ),
				'desc'		=> __( 'Optionally enable "Payment Data Transfer" (Profile > Website Payment Preferences) and then copy your identity token here. This will allow payments to be verified without the need for PayPal IPN.', 'wp-job-manager-simple-paid-listings' ),
				'type'      => 'input',
				'class'     => 'gateway-settings gateway-settings-paypal'
			),
			array(
				'name' 		=> 'job_manager_spl_paypal_sandbox',
				'std' 		=> 'no',
				'label' 	=> __( 'PayPal Sandbox', 'wp-job-manager-simple-paid-listings' ),
				'desc'		=> __( 'Enable PayPal Sandbox (used for testing)', 'wp-job-manager-simple-paid-listings' ),
				'options'   => array(
					'yes' => __( 'Yes', 'wp-job-manager-simple-paid-listings' ),
					'no' => __( 'No', 'wp-job-manager-simple-paid-listings' ),
				),
				'type'      => 'select',
				'class'     => 'gateway-settings gateway-settings-paypal'
			)
		);
		parent::__construct();
	}

	/**
	 * Pay for a job listing action
	 */
	public function pay_for_listing( $job_id ) {
		$payment_link = $this->get_paypal_payment_link( $job_id );
		wp_redirect( $payment_link );
		exit;
	}

	/**
	 * Get PayPal payment link
	 * @return string
	 */
	private function get_paypal_payment_link( $job_id ) {
		$paypal_args = apply_filters( 'job_manager_spl_paypal_args', array(
			'cmd'           => '_cart',
			'business'      => get_option( 'job_manager_spl_paypal_email' ),
			'currency_code' => get_option( 'job_manager_spl_currency' ),
			'charset'       => 'UTF-8',
			'rm'            => 2,
			'upload'        => 1,
			'no_note'       => 1,
			'return'        => add_query_arg( array( 'success' => 'true', 'job_id' => $job_id, 'step' => $_REQUEST['step'] + 1 ), get_permalink() ),
			'cancel_return' => add_query_arg( array( 'cancel' => 'true', 'job_id' => $job_id, 'step' => $_REQUEST['step'] ), get_permalink() ),
			'invoice'       => strtoupper( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-JOB-' . $job_id,
			'custom'        => $job_id,
			'notify_url'    => add_query_arg( array( 'job-manager-api' => 'WP_Job_Manager_Simple_Paid_Listings', 'gateway' => $this->gateway_id ), home_url( '/' ) ),
			'no_shipping'   => 1,
			'item_name_1'   => __( 'New Job Listing', 'wp-job-manager-simple-paid-listings' ) . ' &quot;' . get_the_title( $job_id ) . '&quot;',
			'quantity_1'    => 1,
			'amount_1'      => WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost()
		) );

		$paypal_args = http_build_query( $paypal_args, '', '&' );

		if ( get_option( 'job_manager_spl_paypal_sandbox' ) == 'yes' )
			$paypal_adr = $this->testurl . '?test_ipn=1&';
		else
			$paypal_adr = $this->liveurl . '?';

		return $paypal_adr . $paypal_args;
	}

	/**
	 * API Handler
	 */
	public function api_handler() {

		if ( get_option( 'job_manager_spl_paypal_sandbox' ) == 'yes' )
			$paypal_adr = $this->testurl . '?test_ipn=1&';
		else
			$paypal_adr = $this->liveurl . '?';

    	// Get recieved values from post data
		$received_values = array( 'cmd' => '_notify-validate' );
		$received_values += stripslashes_deep( $_POST );

        // Send back post vars to paypal
        $params = array(
        	'body' 			=> $received_values,
        	'sslverify' 	=> false,
        	'timeout' 		=> 60,
        	'user-agent'	=> 'WP_Job_Manager',
        	'httpversion'   => '1.1',
        	'headers'       => array( 'host' => 'www.paypal.com' )
        );

		// Post back to get a response
        $response = wp_remote_post( $paypal_adr, $params );

        // check to see if the request was valid
        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && ( strcmp( $response['body'], "VERIFIED" ) == 0 ) ) {

        	$this->valid_paypal_ipn_request();
        }
    }

    /**
     * Return handler
     *
     * Alternative to IPN
     */
    public function return_handler() {
		$posted = stripslashes_deep( $_REQUEST );

	    if ( ! empty( $posted['cm'] ) ) {

	    	$job_id = absint( $posted['cm'] );
	    	$job    = get_post( $job_id );

	    	$posted['st'] = strtolower( $posted['st'] );

			switch ( $posted['st'] ) {
            	case 'completed' :

            		// Only complete pending_payment jobs
            		if ( ! in_array( $job->post_status, array( 'pending_payment', 'expired' ) ) ) {
            			return false;
            		}

					// Validate Amount
				    if ( WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() != $posted['amt'] ) {
				    	$this->send_admin_email( $job_id, __( "The PayPal amount recieved does not match the job listing fee - please manually check payment before approving this listing. The job has *not* been automatically approved.", 'wp-job-manager-simple-paid-listings' ) );

				    	return false;
				    }

				    // Validate transaction
				    if ( get_option( 'job_manager_spl_paypal_sandbox' ) == 'yes' )
						$paypal_adr = $this->testurl;
					else
						$paypal_adr = $this->liveurl;

			        $pdt = array(
			        	'body' 			=> array(
			        		'cmd' => '_notify-synch',
			        		'tx'  => $posted['tx'],
			        		'at'  => get_option( 'job_manager_spl_paypal_identity_token' )
			        	),
			        	'sslverify' 	=> false,
			        	'timeout' 		=> 60,
			        	'user-agent'	=> 'WP_Job_Manager',
			        	'httpversion'   => '1.1',
        				'headers'       => array( 'host' => 'www.paypal.com' )
			        );

					// Post back to get a response
			        $response = wp_remote_post( $paypal_adr, $pdt );

			        if ( is_wp_error( $response ) )
			        	return false;

			        if ( ! strpos( $response['body'], "SUCCESS" ) === 0 )
			        	return false;

					// Store PP Details
					update_post_meta( $job_id, 'Transaction ID', $posted['tx'] );

					// Notify admin
					if ( get_option( 'job_manager_submission_requires_approval' ) )
	                	$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job is ready for admin approval.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );
	               	else
	               		$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job has been automatically approved.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );

	                // Change the job status
	               	$this->payment_complete( $job_id );

	            break;
	        }
        }
    }

    /**
     * Triggered when a valid IPN request comes in
     */
    public function valid_paypal_ipn_request() {
		$posted = stripslashes_deep( $_POST );

	    if ( ! empty( $posted['custom'] ) ) {

	    	$job_id = absint( $posted['custom'] );
	    	$job    = get_post( $job_id );

	    	$posted['payment_status'] 	= strtolower( $posted['payment_status'] );

	    	if ( $posted['test_ipn'] == 1 && $posted['payment_status'] == 'pending' )
        		$posted['payment_status'] = 'completed';

			switch ( $posted['payment_status'] ) {
            	case 'completed' :

            		// Only complete pending_payment jobs
            		if ( ! in_array( $job->post_status, array( 'pending_payment', 'expired' ) ) ) {
            			return false;
            		}
            		
					// Validate Amount
				    if ( WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() != $posted['mc_gross'] ) {
				    	$this->send_admin_email( $job_id, __( "The PayPal amount recieved does not match the job listing fee - please manually check payment before approving this listing. The job has *not* been automatically approved.", 'wp-job-manager-simple-paid-listings' ) );

				    	return false;
				    }

					// Store PP Details
	            	update_post_meta( $job_id, 'Payer PayPal address', $posted['payer_email'] );
					update_post_meta( $job_id, 'Transaction ID', $posted['txn_id'] );

					// Notify admin
					if ( get_option( 'job_manager_submission_requires_approval' ) )
	                	$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job is ready for admin approval.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );
	               	else
	               		$this->send_admin_email( $job_id, sprintf( __( "Payment has been received in full for Job Listing #%d - this job has been automatically approved.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );

	                // Change the job status
	               	$this->payment_complete( $job_id );

	            break;
	            case 'pending' :

	            	$this->send_admin_email( $job_id, sprintf( __( "PayPal payment is pending for Job Listing #%d - this job has *not* been automatically approved.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );

	            break;
	            case "reversed" :
	            case "chargeback" :

	            	$this->send_admin_email( $job_id, sprintf( __( "The payment for Job Listing #%d was reversed. Please check this job/payment and change the status manually if need be.", 'wp-job-manager-simple-paid-listings' ), $job_id ) );

	            break;
	        }
        }
		exit;
    }
}

return new WP_Job_Manager_SPL_PayPal();