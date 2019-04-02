<?php

class Jobify_WP_Job_Manager_Applications {

	public function __construct() {
		add_action( 'job_application_form_fields_start', array( $this, 'add_form_title' ) );
	}

	public function add_form_title() {
		echo '<h2 class="modal-title">' . __( 'Apply', 'jobify' ) . '</h2>';
	}

}

$GLOBALS[ 'jobify_job_manager_applications' ] = new Jobify_WP_Job_Manager_Applications();