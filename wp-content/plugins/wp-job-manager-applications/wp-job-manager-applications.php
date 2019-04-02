<?php
/*
Plugin Name: WP Job Manager - Applications
Plugin URI: https://wpjobmanager.com/add-ons/applications/
Description: Lets candidates submit applications to jobs which are stored on the employers jobs page, rather than simply emailed. Works standalone with it's built in application form.
Version: 2.2.3
Author: Automattic
Author URI: http://wpjobmanager.com
Requires at least: 4.1
Tested up to: 4.4

	Copyright: 2015 Automattic
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
 * WP_Job_Manager_Applications class.
 */
class WP_Job_Manager_Applications extends WPJM_Updater {

	/**
	 * __construct function.
	 */
	public function __construct() {
		// Define constants
		define( 'JOB_MANAGER_APPLICATIONS_VERSION', '2.2.3' );
		define( 'JOB_MANAGER_APPLICATIONS_FILE', __FILE__ );
		define( 'JOB_MANAGER_APPLICATIONS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_MANAGER_APPLICATIONS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Check requirements
		if ( version_compare( phpversion(), '5.3', '<' ) ) {
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				add_action( 'admin_notices', array( $this, 'php_admin_notice' ) );
			}
			return;
		}

		// Includes
		include( 'includes/class-wp-job-manager-applications-post-types.php' );
		include( 'includes/class-wp-job-manager-applications-apply.php' );
		include( 'includes/class-wp-job-manager-applications-dashboard.php' );
		include( 'includes/class-wp-job-manager-applications-past.php' );
		include( 'includes/wp-job-manager-applications-functions.php' );

		// Init classes
		$this->post_types = new WP_Job_Manager_Applications_Post_Types();

		// Add actions
		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 12 );
		add_action( 'plugins_loaded', array( $this, 'integration' ), 12 );
		add_action( 'init', array( $this, 'load_admin' ), 12 );
		add_action( 'after_setup_theme', array( $this, 'template_functions' ) );
		add_action( 'admin_init', array( $this, 'updater' ) );

		// Activate
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		// Init updates
		$this->init_updates( __FILE__ );
	}

	/**
	 * Output a notice when using an old non-supported version of PHP
	 */
	public function php_admin_notice() {
		echo '<div class="error">';
		echo '<p>Unfortunately, WP Job Manager Applications can not run on PHP versions older than 5.3. Read more information about <a href="http://www.wpupdatephp.com/update/">how you can update</a>.</p>';
		echo '</div>';
	}

	/**
	 * Load template functions
	 */
	public function template_functions() {
		include( 'includes/wp-job-manager-applications-template.php' );
	}

	/**
	 * Handle Updates
	 */
	public function updater() {
		if ( version_compare( JOB_MANAGER_APPLICATIONS_VERSION, get_option( 'wp_job_manager_applications_version' ), '>' ) ) {
			$this->install();
		}
	}

	/**
	 * Install
	 */
	public function install() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		if ( is_object( $wp_roles ) ) {
			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
		}

		wp_clear_scheduled_hook( 'job_applications_purge' );
		wp_schedule_event( time(), 'daily', 'job_applications_purge' );

		update_option( 'wp_job_manager_applications_version', JOB_MANAGER_APPLICATIONS_VERSION );
	}

	/**
	 * Get capabilities
	 *
	 * @return array
	 */
	public function get_core_capabilities() {
		$capabilities     = array();
		$capability_types = array( 'job_application' );

		foreach ( $capability_types as $capability_type ) {
			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-applications' );
		load_textdomain( 'wp-job-manager-applications', WP_LANG_DIR . "/wp-job-manager-applications/wp-job-manager-applications-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-applications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Integrate with other plugins
	 */
	public function integration() {
		include_once( 'includes/class-wp-job-manager-applications-integration.php' );
	}

	/**
	 * Init the admin area
	 */
	public function load_admin() {
		if ( is_admin() && class_exists( 'WP_Job_Manager' ) ) {
			include_once( 'includes/class-wp-job-manager-applications-admin.php' );
		}
	}
}

$GLOBALS['job_manager_applications'] = new WP_Job_Manager_Applications();
