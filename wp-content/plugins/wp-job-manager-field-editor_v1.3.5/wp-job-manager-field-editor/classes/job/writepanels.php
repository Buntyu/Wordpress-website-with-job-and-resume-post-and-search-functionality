<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Job_Manager_Writepanels' ) )
	include( JOB_MANAGER_PLUGIN_DIR . '/includes/admin/class-wp-job-manager-writepanels.php' );

class WP_Job_Manager_Field_Editor_Job_Writepanels extends WP_Job_Manager_Writepanels {


	function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'rebuild_meta_boxes' ), 100 );

	}

	function rebuild_meta_boxes(){

		remove_meta_box( 'job_listing_data', 'job_listing', 'normal' );
		$this->add_meta_boxes();

	}

	function input_wp_editor( $key, $field ){

		add_meta_box( 'job_listing_data_' . $field['type'], __( 'Job', 'wp-job-manager-field-editor' ) . ' ' . $field['meta_key'], array( $this, 'job_listing_wp_editor' ), 'job_listing' ,'normal', 'high', array( 'key', $key, 'field', $field ) );
	}

	function job_listing_wp_editor( $key, $field ){
		global $thepostid;
		if ( empty( $field[ 'value' ] ) )
			$field[ 'value' ] = get_post_meta( $thepostid, $key, true );

		wp_editor( $field[ 'value' ], 'job_listing_wp_editor' );

	}

}