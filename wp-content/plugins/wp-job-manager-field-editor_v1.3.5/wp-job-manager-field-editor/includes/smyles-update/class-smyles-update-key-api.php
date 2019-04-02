<?php
/**
 * sMyles WordPress Plugin Updater API
 *
 * Author:       Myles McNamara
 * Author URI:   http://plugins.smyl.es
 * License:      GPL 3+
 * Version:      1.0.3
 * Last Updated: Fri Aug 01 2014 10:55:58
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * sMyles_Update_Key_API
 */
class sMyles_Update_Key_API extends sMyles_Update {

	// Start Workspace
	private static $endpoint = 'https://plugins.smyl.es/?wc-api=am-software-api';

	/**
	 * Attempt to activate a plugin licence
	 * @return string
	 */
	public static function activate( $args ) {

		$defaults = array(
			'request'  => 'activation',
			'platform' => site_url()
		);

		$args           = wp_parse_args( $defaults, $args );
		$request_string = self::$endpoint . '&' . http_build_query( $args, '', '&' );
		$request        = wp_remote_get( $request_string );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		} else {
			return wp_remote_retrieve_body( $request );
		}
	}

	/**
	 * Attempt t deactivate a licence
	 */
	public static function deactivate( $args ) {

		$defaults = array(
			'request'  => 'deactivation',
			'platform' => site_url()
		);

		$args    = wp_parse_args( $defaults, $args );
		$request = wp_remote_get( self::$endpoint . '&' . http_build_query( $args, '', '&' ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		} else {
			return wp_remote_retrieve_body( $request );
		}
	}
}