<?php

class Jobify_GravityForms {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Load the form scripts outside of the loop.
	 */
	function enqueue_scripts() {
		global $post;

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		if ( 'resume' == $post->post_type ) {
			$form = get_option( 'job_manager_resume_apply' );
		} else {
			$form = get_option( 'job_manager_job_apply' );
		}

		gravity_form_enqueue_scripts( $form, true );
	}

}

$GLOBALS[ 'jobify_gravityforms' ] = new Jobify_GravityForms();