<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'wp-job-manager-field-editor_instance',
	'wp-job-manager-field-editor_hide_key_notice',
	'wp-job-manager-field-editor_email',
	'wp-job-manager-field-editor_licence_key',
	'wp-job-manager-field-editor_errors',
	'jmfe_enable_bug_reporter',
	'smyles_bug_report_force_debug',
	'jmfe_listify_directory_fields_notice',
);

foreach ( $options as $option ) {
	delete_option( $option );
}