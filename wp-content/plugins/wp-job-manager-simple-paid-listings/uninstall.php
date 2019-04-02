<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'job_manager_spl_paypal_email',
	'job_manager_spl_paypal_identity_token',
	'job_manager_spl_paypal_sandbox',
	'job_manager_spl_stripe_testmode',
	'job_manager_spl_stripe_secret_key',
	'job_manager_spl_stripe_publishable_key',
	'job_manager_spl_listing_cost',
	'job_manager_spl_currency',
	'job_manager_spl_gateway'
);

foreach ( $options as $option ) {
	delete_option( $option );
}