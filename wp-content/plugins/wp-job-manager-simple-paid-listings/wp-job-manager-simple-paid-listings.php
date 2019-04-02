<?php
/*
Plugin Name: WP Job Manager - Simple Paid Listings
Plugin URI: https://wpjobmanager.com/add-ons/simple-paid-listings/
Description: Add paid listing functionality. Set a price per listing and take payment via Stripe or PayPal before the listing becomes published.
Version: 1.1.15
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 3.9

	Copyright: 2013 Mike Jolley
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

/**
 * WP_Job_Manager_Simple_Paid_Listings class.
 */
class WP_Job_Manager_Simple_Paid_Listings extends WPJM_Updater {

	private $job_id  = '';

	/**
	 * __construct function.
	 */
	public function __construct() {
		define( 'JOB_MANAGER_SPL_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		$this->plugin_slug = basename( dirname( __FILE__ ) );
		$this->job_id      = ! empty( $_REQUEST['job_id'] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

		add_action( 'init', array( $this, 'init' ), 12 );
		add_filter( 'the_job_status', array( $this, 'the_job_status' ), 10, 2 );
		add_filter( 'job_manager_valid_submit_job_statuses', array( $this, 'valid_submit_job_statuses' ) );

		add_filter( 'submit_job_steps', array( $this, 'submit_job_steps' ), 10 );
		add_filter( 'submit_job_step_preview_submit_text', array( $this, 'submit_button_text' ), 10 );

		add_action( 'job_manager_job_submitted_content_pending_payment', array( $this, 'job_submitted' ), 10 );
		add_action( 'job_manager_job_submitted_content_expired', array( $this, 'job_submitted' ), 10 );
		add_filter( 'job_manager_settings', array( $this, 'settings' ) );
		add_action( 'job_manager_api_' . get_class( $this ), array( $this, 'api_handler' ) );
		add_filter( 'job_manager_get_dashboard_jobs_args', array( $this, 'dashboard_job_args' ) );
		add_filter( 'job_manager_my_job_actions', array( $this, 'my_job_actions' ), 10, 2 );
		add_action( 'job_manager_my_job_do_action', array( $this, 'my_job_do_action' ), 10, 2 );

		// Updater
		$this->init_updates( __FILE__ );

		// Get the gateway we are using
		$this->gateway = $this->get_gateway();
	}

	/**
	 * Get cost
	 * @return float
	 */
	public static function get_job_listing_cost() {
		return apply_filters( 'wp_job_manager_spl_get_job_listing_cost', number_format( get_option( 'job_manager_spl_listing_cost' ), 2, '.', '' ) );
	}

	/**
	 * Filter job status name
	 *
	 * @param  string $nice_status
	 * @param  string $status
	 * @return string
	 */
	public function the_job_status( $status, $job ) {
		if ( $job->post_status == 'pending_payment' )
			$status = __( 'Pending Payment', 'wp-job-manager-simple-paid-listings' );
		return $status;
	}

	/**
	 * Ensure the submit form lets us continue to edit/process a job with the pending_payment status
	 * @return array
	 */
	public function valid_submit_job_statuses( $status ) {
		$status[] = 'pending_payment';

		return $status;
	}

	/**
	 * Change the steps during the submission process
	 *
	 * @param  array $steps
	 * @return array
	 */
	public function submit_job_steps( $steps ) {
		if ( self::get_job_listing_cost() ) {
			// We need to hijack the preview submission so we can take a payment
			$steps['preview']['handler'] = array( $this, 'preview_handler' );
		}
		return $steps;
	}

	public function payment_result_page() {
		get_job_manager_template( 'job-submitted.php', array( 'job' => $job ) );
	}

	/**
	 * Handle the form when the preview page is submitted
	 */
	public function preview_handler() {
		if ( ! $_POST ) {
			return;
		}

		// Edit = show submit form again
		if ( ! empty( $_POST['edit_job'] ) ) {
			WP_Job_Manager_Form_Submit_Job::previous_step();
		}

		// Continue = Take Payment
		if ( ! empty( $_POST['continue'] ) ) {

			$job = get_post( $this->job_id );

			if ( $job->post_status == 'preview' ) {
				$update_job                = array();
				$update_job['ID']          = $job->ID;
				$update_job['post_status'] = 'pending_payment';
				wp_update_post( $update_job );
			}

			if ( $this->gateway->pay_for_listing( $this->job_id ) ) {
				// If pay for listing returns true we can proceed, otherwise stay in preview mode
				WP_Job_Manager_Form_Submit_Job::next_step();
			}
		}
	}

	/**
	 * Change submit button text
	 * @return string
	 */
	public function submit_button_text( $button_text ) {
		if ( self::get_job_listing_cost() ) {
			return __( 'Pay for listing &rarr;', 'wp-job-manager-simple-paid-listings' );
		}
		return $button_text;
	}

	/**
	 * Show a message if pending payment when the done step is reached
	 */
	public function job_submitted( $job ) {
		$this->gateway->return_handler();

		printf( __( 'Thanks. Your Job listing was submitted successfully and will be visible once payment is verified.', 'wp-job-manager-simple-paid-listings' ), get_permalink( $job->ID ) );
	}

	/**
	 * API Handler
	 * @return [type]
	 */
	function api_handler() {
		if ( ! empty( $_GET['gateway'] ) ) {
			$gateway = $this->get_gateway( $_GET['gateway'] );
			$gateway->api_handler();
		}
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function init() {
		global $job_manager;

		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-simple-paid-listings' );
		load_textdomain( 'wp-job-manager-simple-paid-listings', WP_LANG_DIR . "/wp-job-manager-simple-paid-listings/wp-job-manager-simple-paid-listings-$locale.mo" );

		load_plugin_textdomain( 'wp-job-manager-simple-paid-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		register_post_status( 'pending_payment', array(
			'label'                     => _x( 'Pending Payment', 'job_listing', 'wp-job-manager-simple-paid-listings' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-job-manager-simple-paid-listings' ),
		) );

		add_action( 'pending_payment_to_publish', array( $job_manager->post_types, 'set_expirey' ) );
	}

	/**
	 * Get configured gateway
	 *
	 * @return class Gateway
	 */
	public function get_gateway( $gateway = '' ) {
		if ( ! $gateway )
			$gateway   = get_option( 'job_manager_spl_gateway', 'paypal' );
		$gateway_class = apply_filters( 'wp_job_manager_spl_gateway_class', 'WP_Job_Manager_SPL_' . $gateway );

		include_once( 'gateways/abstract-class-wp-job-manager-spl-gateway.php' );

		if ( ! class_exists( $gateway_class ) )
			return include( 'gateways/class-' . str_replace( '_', '-', strtolower( $gateway_class ) ) . '.php' );

		return new $gateway_class;
	}

	/**
	 * Include gateways
	 */
	public function include_gateways() {
		include_once( 'gateways/abstract-class-wp-job-manager-spl-gateway.php' );
		include_once( 'gateways/class-wp-job-manager-spl-paypal.php' );
		include_once( 'gateways/class-wp-job-manager-spl-stripe.php' );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function settings( $settings = array() ) {
		$this->include_gateways();

		add_action( 'admin_footer', array( $this, 'settings_js' ) );

		$settings['paid_listings'] = array(
			__( 'Paid Listings', 'wp-job-manager-simple-paid-listings' ),
			apply_filters(
				'wp_job_manager_spl_settings',
				array(
					array(
						'name' 		=> 'job_manager_spl_listing_cost',
						'std' 		=> '5.00',
						'label' 	=> __( 'Listing Cost', 'wp-job-manager-simple-paid-listings' ),
						'desc'		=> __( 'Enter the cost of new listings, excluding any currency symbols. E.g. <code>9.99</code>', 'wp-job-manager-simple-paid-listings' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_spl_currency',
						'std' 		=> 'USD',
						'label' 	=> __( 'Currency Code', 'wp-job-manager-simple-paid-listings' ),
						'desc'		=> __( 'Enter the currency code you wish to use. E.g. for US dollars enter <code>USD</code>. Your gateway must support your input currency for payments to work.', 'wp-job-manager-simple-paid-listings' ),
						'type'      => 'input'
					),
					array(
						'name' 		=> 'job_manager_spl_gateway',
						'std' 		=> 'paypal',
						'label' 	=> __( 'Payment Gateway', 'wp-job-manager-simple-paid-listings' ),
						'desc'		=> __( 'Choose the gateway to use for paid listings. If using Stripe you should ensure your Submit Job page is served over HTTPS. You can use <a href="http://wordpress.org/plugins/wordpress-https/">WordPress HTTPS</a> to do this.', 'wp-job-manager-simple-paid-listings' ),
						'options'   => apply_filters( 'wp_job_manager_spl_gateways', array() ),
						'type'      => 'select'
					),
				)
			)
		);
		return $settings;
	}

	/**
	 * After settings
	 */
	public function settings_js() {
		?>
		<script type="text/javascript">
			jQuery('select#setting-job_manager_spl_gateway').change(function() {
				jQuery(this).closest('form').find( 'tr.gateway-settings' ).hide();
				jQuery(this).closest('form').find( 'tr.gateway-settings-' + jQuery(this).val() ).show();
			}).change();

			jQuery('.nav-tab-wrapper a:first').click();
		</script>
		<?php
	}

	/**
	 * Change what jobs are shown on dashboard
	 * @param  array $args
	 * @return array
	 */
	public function dashboard_job_args( $args = array() ) {
		$args['post_status'][] = 'pending_payment';

		return $args;
	}

	/**
	 * [my_job_actions description]
	 * @param  array $actions
	 * @param  object $job
	 * @return array
	 */
	public function my_job_actions( $actions, $job ) {
		if ( $job->post_status == 'pending_payment' && get_option( 'job_manager_submit_page_slug' ) ) {
			$actions['pay'] = array( 'label' => __( 'Pay', 'job_manager' ), 'nonce' => true );
		}

		return $actions;
	}

	/**
	 * Do pay action
	 * @param  string $action
	 * @param  integer $job_id
	 * @return [type]
	 */
	public function my_job_do_action( $action = '', $job_id = 0 ) {
		if ( $action == 'pay' && $job_id ) {
			wp_redirect( add_query_arg( array( 'step' => 'preview', 'job_id' => absint( $job_id ) ), get_permalink( get_page_by_path( get_option( 'job_manager_submit_page_slug' ) )->ID ) ) );
			exit;
		}
	}
}

new WP_Job_Manager_Simple_Paid_Listings();