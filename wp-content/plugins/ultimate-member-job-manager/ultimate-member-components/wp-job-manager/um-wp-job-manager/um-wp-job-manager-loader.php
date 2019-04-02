<?php

/**
 * UM_WP_Job_Manager_Component Loader
 *
 * @package Ultimate Member
 * @subpackage SettingsLoader
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class UM_WP_Job_Manager_Component {

	/**
	 * Start the Job Manager component creation process
	 * 
	 */
	public function __construct() {
		add_filter('um_profile_tabs', array( $this, 'jobs_tab' ), 1000 );
		add_action('um_profile_content_job_manager_default', array( $this, 'um_profile_content_job_manager_default' ));
		add_action('um_profile_content_job_manager_job_dashboard', array( $this, 'um_profile_subnav_content_job_dashboard_default' ));
		add_action('um_profile_content_job_manager_jobs', array( $this, 'um_profile_subnav_content_jobs_default' ));
		add_action('um_profile_content_job_manager_post_a_job', array( $this, 'um_profile_subnav_content_post_a_job_default' ));
		add_action('um_profile_content_job_manager_my_bookmarks', array( $this, 'um_profile_subnav_content_my_bookmarks_default' ));
		add_action('um_profile_content_job_manager_job_alerts', array( $this, 'um_profile_subnav_content_job_alerts_default' ));
	}
	
	function jobs_tab( $tabs ) {
		$sub_nav = array(
			'job_dashboard' => __('Job Dashboard','ultimate-member-job-manager'),
		);
		
		// Add Jobs nav item.
		if ( get_current_user_id() == um_profile_id() ) {
			$sub_nav['jobs'] = __('Jobs','ultimate-member-job-manager');
		}

		// Add Post a Job nav item.
		if ( get_current_user_id() == um_profile_id() ) {
			$sub_nav['post_a_job'] = __('Post a Job','ultimate-member-job-manager');
		}
		
		// Add My Bookmarks nav item.
		if ( class_exists( 'WP_Job_Manager_Bookmarks' ) && get_current_user_id() == um_profile_id() ) {
			$sub_nav['my_bookmarks'] = __('My Bookmarks','ultimate-member-job-manager');
		}
		
		// Add Job Alerts nav item.
		if ( class_exists( 'WP_Job_Manager_Alerts' ) && get_current_user_id() == um_profile_id() ) {
			$sub_nav['job_alerts'] = __('Job Alerts','ultimate-member-job-manager');
		}
		
		
		$tabs['job_manager'] = array(
			'name'            => __( 'Job Manager','ultimate-member-job-manager' ),
			'icon'            => 'um-faicon-briefcase',
			'custom'          => true,
			'subnav'          => $sub_nav,
			'subnav_default'  => 'job_dashboard'
		);	
		return $tabs;
	}
	
	function um_profile_content_job_manager_default( $args ) {
		// include  file or echo do_shortcode
		add_filter( 'job_manager_get_dashboard_jobs_args', 'um_function_to_change_dashboard_jobs_args', 10, 1 );
		get_job_manager_template_part( 'um-job-dashboard', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}

	function um_profile_subnav_content_job_dashboard_default( $args ) {
		// include  file or echo do_shortcode
		add_filter( 'job_manager_get_dashboard_jobs_args', 'um_function_to_change_dashboard_jobs_args', 10, 1 );
		get_job_manager_template_part( 'um-job-dashboard', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}
	
	function um_profile_subnav_content_jobs_default( $args ) {
		// include  file or echo do_shortcode
		get_job_manager_template_part( 'um-jobs', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}

	function um_profile_subnav_content_post_a_job_default( $args ) {
		// include  file or echo do_shortcode
		get_job_manager_template_part( 'um-submit-job-form', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}
	
	function um_profile_subnav_content_my_bookmarks_default( $args ) {
		 // include  file or echo do_shortcode
		 get_job_manager_template_part( 'um-my-bookmarks', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}
	
	function um_profile_subnav_content_job_alerts_default( $args ) {
		 // include  file or echo do_shortcode
		 get_job_manager_template_part( 'um-job-alerts', 'content', 'um_job_manager', ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	}
	


}

new UM_WP_Job_Manager_Component();