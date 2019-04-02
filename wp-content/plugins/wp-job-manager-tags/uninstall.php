<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'job_manager_enable_tag_archive',
	'job_manager_max_tags',
	'job_manager_tag_input'
);

foreach ( $options as $option ) {
	delete_option( $option );
}