<?php
/*
Plugin Name: WP Job Manager - Resume Manager
Plugin URI: https://wpjobmanager.com/add-ons/resume-manager/
Description: Manage canidate resumes from the WordPress admin panel, and allow candidates to post their resumes directly to your site.
Version: 1.15.1
Author: Automattic
Author URI: https://wpjobmanager.com
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
 * WP_Resume_Manager class.
 */
class WP_Resume_Manager extends WPJM_Updater {

	/**
	 * __construct function.
	 */
	public function __construct() {
		// Define constants
		define( 'RESUME_MANAGER_VERSION', '1.15.1' );
		define( 'RESUME_MANAGER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'RESUME_MANAGER_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Includes
		include( 'includes/wp-resume-manager-functions.php' );
		include( 'includes/wp-resume-manager-template.php' );
		include( 'includes/class-wp-resume-manager-post-types.php' );
		include( 'includes/class-wp-resume-manager-forms.php' );
		include( 'includes/class-wp-resume-manager-ajax.php' );
		include( 'includes/class-wp-resume-manager-shortcodes.php' );
		include( 'includes/class-wp-resume-manager-geocode.php' );
		include( 'includes/class-wp-resume-manager-email-notification.php' );
		include( 'includes/class-wp-resume-manager-apply.php' );

		// Init classes
		$this->apply      = new WP_Resume_Manager_Apply();
		$this->forms      = new WP_Resume_Manager_Forms();
		$this->post_types = new WP_Resume_Manager_Post_Types();

		// Activation - works with symlinks
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this->post_types, 'register_post_types' ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), create_function( "", "include_once( 'includes/class-wp-resume-manager-install.php' );" ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), 'flush_rewrite_rules', 15 );

		// Actions
		add_action( 'admin_notices', array( $this, 'version_check' ) );
		add_action( 'plugins_loaded', array( $this, 'admin' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 12 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'switch_theme', array( $this->post_types, 'register_post_types' ), 10 );
		add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'admin_init', array( $this, 'updater' ) );

		// Init updates
		$this->init_updates( __FILE__ );
	}

	/**
	 * Check JM version
	 */
	public function version_check() {
		$required_jm_version      = '1.22.0';
		if ( ! defined( 'JOB_MANAGER_VERSION' ) ) {
			?><div class="error"><p><?php _e( 'Resume Manager requires WP Job Manager to be installed!', 'wp-job-manager-applications' ); ?></p></div><?php
		} elseif ( version_compare( JOB_MANAGER_VERSION, $required_jm_version, '<' ) ) {
			?><div class="error"><p><?php printf( __( 'Resume Manager requires WP Job Manager %s (you are using %s)', 'wp-job-manager-applications' ), $required_jm_version, JOB_MANAGER_VERSION ); ?></p></div><?php
		}
	}

	/**
	 * Handle Updates
	 */
	public function updater() {
		if ( version_compare( RESUME_MANAGER_VERSION, get_option( 'wp_resume_manager_version' ), '>' ) ) {
			include_once( 'includes/class-wp-resume-manager-install.php' );
		}
	}

	/**
	 * Include admin
	 */
	public function admin() {
		if ( is_admin() && class_exists( 'WP_Job_Manager' ) ) {
			include( 'includes/admin/class-wp-resume-manager-admin.php' );
		}
	}

	/**
	 * Includes once plugins are loaded
	 */
	public function widgets_init() {
		include( 'includes/class-wp-resume-manager-widgets.php' );
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-resumes' );

		load_textdomain( 'wp-job-manager-resumes', WP_LANG_DIR . "/wp-job-manager-resumes/wp-job-manager-resumes-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-resumes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		$ajax_url         = admin_url( 'admin-ajax.php', 'relative' );
		$ajax_filter_deps = array( 'jquery' );

		// WPML workaround until this is standardized
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$ajax_url = add_query_arg( 'lang', ICL_LANGUAGE_CODE, $ajax_url );
		}

		if ( apply_filters( 'job_manager_chosen_enabled', true ) ) {
			$ajax_filter_deps[] = 'chosen';
		}

		wp_register_script( 'wp-resume-manager-ajax-filters', RESUME_MANAGER_PLUGIN_URL . '/assets/js/ajax-filters.min.js', $ajax_filter_deps, RESUME_MANAGER_VERSION, true );
		wp_register_script( 'wp-resume-manager-candidate-dashboard', RESUME_MANAGER_PLUGIN_URL . '/assets/js/candidate-dashboard.min.js', array( 'jquery' ), RESUME_MANAGER_VERSION, true );
		wp_register_script( 'wp-resume-manager-resume-submission', RESUME_MANAGER_PLUGIN_URL . '/assets/js/resume-submission.min.js', array( 'jquery', 'jquery-ui-sortable' ), RESUME_MANAGER_VERSION, true );
		wp_register_script( 'wp-resume-manager-resume-contact-details', RESUME_MANAGER_PLUGIN_URL . '/assets/js/contact-details.min.js', array( 'jquery' ), RESUME_MANAGER_VERSION, true );

		wp_localize_script( 'wp-resume-manager-resume-submission', 'resume_manager_resume_submission', array(
			'i18n_navigate'       => __( 'If you wish to edit the posted details use the "edit resume" button instead, otherwise changes may be lost.', 'wp-job-manager-resumes' ),
			'i18n_confirm_remove' => __( 'Are you sure you want to remove this item?', 'wp-job-manager-resumes' ),
			'i18n_remove'         => __( 'remove', 'wp-job-manager-resumes' )
		) );
		wp_localize_script( 'wp-resume-manager-ajax-filters', 'resume_manager_ajax_filters', array(
			'ajax_url' => $ajax_url
		) );
		wp_localize_script( 'wp-resume-manager-candidate-dashboard', 'resume_manager_candidate_dashboard', array(
			'i18n_confirm_delete' => __( 'Are you sure you want to delete this resume?', 'wp-job-manager-resumes' )
		) );

		wp_enqueue_style( 'wp-job-manager-resume-frontend', RESUME_MANAGER_PLUGIN_URL . '/assets/css/frontend.css' );
	}
}

$GLOBALS['resume_manager'] = new WP_Resume_Manager();
