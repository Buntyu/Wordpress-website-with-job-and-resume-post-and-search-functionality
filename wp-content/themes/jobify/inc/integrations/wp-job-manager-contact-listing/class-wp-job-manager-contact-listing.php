<?php

class Jobify_WP_Job_Manager_Contact_Listing {

	public function __construct() {
		add_filter( 'job_manager_contact_listing_gravityforms_apply_form_args', array( $this, 'gravityforms_args' ) );
		add_filter( 'job_manager_contact_listing_cf7_apply_form_args', array( $this, 'cf7_args' ) );
	}

	public function gravityforms_args( $args ) {
		$args = str_replace( 'title="false"', 'title="true"', $args );

		return $args;
	}

	public function cf7_args( $args ) {
		global $post;

		if ( 'job_listing' == $post->post_type ) {
			$title = __( 'Apply for Job', 'jobify' );
		} else {
			$title = __( 'Contact Candidate', 'jobify' );
		}

		$args = $args . sprintf( ' title="%s"', $title );

		return $args;
	}

}

$GLOBALS[ 'jobify_job_manager_contact_listing' ] = new Jobify_WP_Job_Manager_Contact_Listing();