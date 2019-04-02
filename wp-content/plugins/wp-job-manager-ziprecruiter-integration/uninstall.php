<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'job_manager_ziprecruiter_key',
	'job_manager_ziprecruiter_default_keywords',
	'job_manager_ziprecruiter_require_keywords',
	'job_manager_ziprecruiter_exclude_keywords',
	'job_manager_ziprecruiter_default_location',
	'job_manager_ziprecruiter_per_page',
	'job_manager_ziprecruiter_after_jobs',
	'job_manager_ziprecruiter_before_jobs',
	'job_manager_ziprecruiter_backfill'
);

foreach ( $options as $option ) {
	delete_option( $option );
}