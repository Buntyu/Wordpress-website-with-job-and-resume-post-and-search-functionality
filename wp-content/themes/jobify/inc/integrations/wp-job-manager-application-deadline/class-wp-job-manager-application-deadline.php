<?php

class Jobify_WP_Job_Manager_Application_Deadline {

	public function __construct() {
		add_action( 'init', array( $this, 'widgets_init' ) );
	}

	public function widgets_init() {
		require_once( get_template_directory() . '/inc/integrations/wp-job-manager-application-deadline/widgets/class-widget-job-deadline.php' );

		register_widget( 'Jobify_Widget_Job_Deadline' );
	}

}

$GLOBALS[ 'jobify_job_manager_application_deadline' ] = new Jobify_WP_Job_Manager_Application_Deadline();