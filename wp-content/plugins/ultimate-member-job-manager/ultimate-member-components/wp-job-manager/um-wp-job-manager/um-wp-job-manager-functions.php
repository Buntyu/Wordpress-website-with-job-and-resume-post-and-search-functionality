<?php

/**
 * UM_WP_Job_Manager Settings Functions
 *
 * @package Ultimate Member Job Manager
 * @subpackage SettingsFunctions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function um_function_to_change_dashboard_jobs_args( $args ) {
    //if ( is_buddypress() ) {
    	// ....If not show the job dashboard
		$posts_per_page = 25;
		$args     = array(
			'post_type'           => 'job_listing',
			'post_status'         => array( 'publish', 'expired', 'pending' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => um_profile_id()
		);
		
		return $args;
    //}
}

add_filter('job_manager_pagination_args', 'um_function_to_change_job_manager_pagination_args',10,1);

function um_function_to_change_job_manager_pagination_args($args) {
	//if ( is_buddypress() ) {
		$args_new = array(
			'base' 	 => str_replace( '/page/999999999/', '?paged=%#%', get_pagenum_link( 999999999 ) ),
			'format' => '?paged=%#%',
		);
		$args = array_merge($args,$args_new );
		return $args;
	//} else {
		return $args;
	//}
	
}


add_filter( 'job_manager_my_job_actions', 'um_function_to_change_job_manager_my_job_actions', 10, 2 );

function um_function_to_change_job_manager_my_job_actions( $actions, $job ) {
	$um_core_can_edit_settings = um_core_can_edit_settings();
	if ( 1 != $um_core_can_edit_settings )
		$actions = array();

    return $actions;
}

/**
 * Check whether the logged-in user can edit settings for the displayed user.
 *
 * @return bool True if editing is allowed, otherwise false.
 */
function um_core_can_edit_settings() {
	if ( um_is_my_profile() ) {
		return true;
	}

	if ( is_super_admin( um_profile_id() ) && ! is_super_admin() ) {
		return false;
	}

	if ( current_user_can( 'edit_users' ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page part of the profile of the logged-in user?
 *
 * Will return true for any subpage of the logged-in user's profile, eg
 * http://example.com/members/joe/friends/.
 *
 * @return True if the current page is part of the profile of the logged-in user.
 */
function um_is_my_profile() {
	if ( is_user_logged_in() && get_current_user_id() == um_profile_id() ) {
		$my_profile = true;
	} else {
		$my_profile = false;
	}

	return apply_filters( 'um_is_my_profile', $my_profile );
}