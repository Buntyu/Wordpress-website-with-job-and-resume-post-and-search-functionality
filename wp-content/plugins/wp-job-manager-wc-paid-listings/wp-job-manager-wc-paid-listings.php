<?php
/*
Plugin Name: WP Job Manager - WooCommerce Paid Listings
Plugin URI: https://wpjobmanager.com/add-ons/wc-paid-listings/
Description: Add paid listing functionality via WooCommerce. Create 'job packages' as products with their own price, listing duration, listing limit, and job featured status and either sell them via your store or during the job submission process. A user's packages are shown on their account page and can be used to post future jobs if they allow more than 1 job listing. Also allows 'resume packages' if using the resumes add-on.
Version: 2.5.5
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 4.2

	Copyright: 2014 Mike Jolley
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

// Define constants
define( 'JOB_MANAGER_WCPL_VERSION', '2.5.5' );
define( 'JOB_MANAGER_WCPL_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'JOB_MANAGER_WCPL_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'JOB_MANAGER_WCPL_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

/**
 * Init the plugin when all plugins are loaded
 */
function wp_job_manager_wcpl_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	/**
	 * WC_Paid_Listings class.
	 */
	class WC_Paid_Listings {

		/** @var object Class Instance */
		private static $instance;

		/**
		 * Get the class instance
		 */
		public static function get_instance() {
			return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			// Hooks
			add_action( 'init', array( $this, 'init' ), 12 );
			add_filter( 'the_job_status', array( $this, 'the_job_status' ), 10, 2 );
			add_filter( 'job_manager_valid_submit_job_statuses', array( $this, 'valid_submit_statuses' ) );
			add_filter( 'resume_manager_valid_submit_resume_statuses', array( $this, 'valid_submit_statuses' ) );
			add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
			add_filter( 'resume_manager_settings', array( $this, 'resume_manager_settings' ) );

			// Includes
			include_once( 'includes/class-wc-product-job-package.php' );
			include_once( 'includes/class-wc-paid-listings-admin.php' );
			include_once( 'includes/class-wc-paid-listings-cart.php' );
			include_once( 'includes/class-wc-paid-listings-orders.php' );
			include_once( 'includes/class-wc-paid-listings-subscriptions.php' );
			include_once( 'includes/class-wc-paid-listings-package.php' );
			include_once( 'includes/class-wc-paid-listings-submit-job-form.php' );
			include_once( 'includes/user-functions.php' );
			include_once( 'includes/package-functions.php' );

			if ( class_exists( 'WP_Resume_Manager' ) ) {
				include_once( 'includes/class-wc-product-resume-package.php' );
				include_once( 'includes/class-wc-paid-listings-submit-resume-form.php' );
			}

			if ( class_exists( 'WC_Subscriptions' ) ) {
				include_once( 'includes/class-wc-product-job-package-subscription.php' );

				if ( class_exists( 'WP_Resume_Manager' ) ) {
					include_once( 'includes/class-wc-product-resume-package-subscription.php' );
				}
			}

			// Updates
			if ( version_compare( get_option( 'wcpl_db_version', 0 ), JOB_MANAGER_WCPL_VERSION, '<' ) ) {
				wp_job_manager_wcpl_install();
			}
		}

		/**
		 * Localisation
		 */
		public function init() {
			global $job_manager;

			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-wc-paid-listings' );
			load_textdomain( 'wp-job-manager-wc-paid-listings', WP_LANG_DIR . "/wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings-$locale.mo" );

			load_plugin_textdomain( 'wp-job-manager-wc-paid-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			register_post_status( 'pending_payment', array(
				'label'                     => _x( 'Pending Payment', 'job_listing', 'wp-job-manager-wc-paid-listings' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-job-manager-wc-paid-listings' ),
			) );

			add_action( 'pending_payment_to_publish', array( $job_manager->post_types, 'set_expirey' ) );
		}

		/**
		 * Filter job status name
		 *
		 * @param  string $nice_status
		 * @param  string $status
		 * @return string
		 */
		public function the_job_status( $status, $job ) {
			if ( $job->post_status == 'pending_payment' ) {
				$status = __( 'Pending Payment', 'wp-job-manager-wc-paid-listings' );
			}
			return $status;
		}

		/**
		 * Ensure the submit form lets us continue to edit/process a job with the pending_payment status
		 * @return array
		 */
		public function valid_submit_statuses( $status ) {
			$status[] = 'pending_payment';
			return $status;
		}

		/**
		 * Add Settings
		 * @param  array $settings
		 * @return array
		 */
		public function job_manager_settings( $settings = array() ) {
			$settings['job_submission'][1][] = array(
				'name' 		=> 'job_manager_paid_listings_flow',
				'std' 		=> '',
				'label' 	=> __( 'Paid Listings Flow', 'wp-job-manager-wc-paid-listings' ),
				'desc'		=> __( 'Define when the user should choose a package for submission.', 'wp-job-manager-wc-paid-listings' ),
				'type'      => 'select',
				'options'   => array(
					'' => __( 'Choose a package after entering job details', 'wp-job-manager-wc-paid-listings' ),
					'before' => __( 'Choose a package before entering job details', 'wp-job-manager-wc-paid-listings' )
				)
			);
			return $settings;
		}

		/**
		 * Add Settings
		 * @param  array $settings
		 * @return array
		 */
		public function resume_manager_settings( $settings = array() ) {
			$settings['resume_submission'][1][] = array(
				'name' 		=> 'resume_manager_paid_listings_flow',
				'std' 		=> '',
				'label' 	=> __( 'Paid Listings Flow', 'wp-job-manager-wc-paid-listings' ),
				'desc'		=> __( 'Define when the user should choose a package for submission.', 'wp-job-manager-wc-paid-listings' ),
				'type'      => 'select',
				'options'   => array(
					'' => __( 'Choose a package after entering resume details', 'wp-job-manager-wc-paid-listings' ),
					'before' => __( 'Choose a package before entering resume details', 'wp-job-manager-wc-paid-listings' )
				)
			);
			return $settings;
		}
	}
	WC_Paid_Listings::get_instance();
}

add_action( 'plugins_loaded', 'wp_job_manager_wcpl_init' );

/**
 * Install the plugin
 */
function wp_job_manager_wcpl_install() {
	global $wpdb;

	$wpdb->hide_errors();

	$collate = '';
    if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty($wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty($wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
    }

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    /**
     * Table for user packages
     */
    $sql = "
CREATE TABLE {$wpdb->prefix}wcpl_user_packages (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL,
  product_id bigint(20) NOT NULL,
  order_id bigint(20) NOT NULL default 0,
  package_featured int(1) NULL,
  package_duration bigint(20) NULL,
  package_limit bigint(20) NOT NULL,
  package_count bigint(20) NOT NULL,
  package_type varchar(100) NOT NULL,
  PRIMARY KEY  (id)
) $collate;
";
    dbDelta( $sql );

    // Upgrades
    if ( get_option( 'wcpl_db_version', 0 ) && version_compare( get_option( 'wcpl_db_version', 0 ), '2.1.2', '<' ) ) {
    	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_job_packages WHERE job_count < job_limit OR job_limit = 0;" );
    	if ( $results ) {
    		foreach ( $results as $result ) {
    			$wpdb->insert(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'user_id'          => $result->user_id,
						'product_id'       => $result->product_id,
						'package_count'    => $result->job_count,
						'package_limit'    => $result->job_limit,
						'package_featured' => $result->job_featured,
						'package_duration' => $result->job_duration,
						'package_type'     => 'job_listing'
					)
				);
    		}
    	}
    }

    // Update version
    update_option( 'wcpl_db_version', JOB_MANAGER_WCPL_VERSION );

	add_action( 'shutdown', 'wp_job_manager_wcpl_delayed_install' );
}

/**
 * Installer (delayed)
 */
function wp_job_manager_wcpl_delayed_install() {
	if ( ! get_term_by( 'slug', sanitize_title( 'job_package' ), 'product_type' ) ) {
		wp_insert_term( 'job_package', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'resume_package' ), 'product_type' ) ) {
		wp_insert_term( 'resume_package', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'job_package_subscription' ), 'product_type' ) ) {
		wp_insert_term( 'job_package_subscription', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'resume_package_subscription' ), 'product_type' ) ) {
		wp_insert_term( 'resume_package_subscription', 'product_type' );
	}
}

register_activation_hook( __FILE__, 'wp_job_manager_wcpl_install' );

new WPJM_Updater( __FILE__ );
