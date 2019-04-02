<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Assets
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Assets {

	private static $instance;

	function __construct() {

		add_action( 'wp_enqueue_scripts', array($this, 'register_assets') );

	}

	/**
	 * Register Vendor/Core CSS and Scripts
	 *
	 * @since 1.1.9
	 *
	 */
	function register_assets() {

		wp_register_script( 'jmfe-term-checklist-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/term-checklist.min.js', array('jquery'), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'jmfe-radio-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/radio.min.js', array('jquery'), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'jmfe-vendor-phone-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/intlTelInput.min.js', array('jquery'), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'jmfe-phone-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/phone.min.js', array(
			'jquery',
			'jmfe-vendor-phone-field'
		), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'jmfe-date-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/date.min.js', array(
			'jquery',
			'jquery-ui-datepicker'
		), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'jmfe-header-field', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/header.min.js', array('jquery'), WPJM_FIELD_EDITOR_VERSION, TRUE );
		wp_register_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );

		wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css', array(), '1.0' );
		wp_register_style( 'jmfe-phone-field-style', WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/css/intlTelInput.min.css', array(), WPJM_FIELD_EDITOR_VERSION );

		$this->register_locale();
	}

	/**
	 * Register JS Locale
	 *
	 * This must be called after the script that is using it is registered
	 *
	 *
	 * @since 1.3.0
	 *
	 */
	public function register_locale(){

		global $wp_locale;

		$date_args = apply_filters( 'job_manager_field_editor_date_args', array(
				'closeText'       => __( 'Done', 'wp-job-manager-field-editor' ),
				'currentText'     => __( 'Today', 'wp-job-manager-field-editor' ),
				'monthNames'      => array_values( $wp_locale->month ),
				'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
				'dayNames'        => array_values( $wp_locale->weekday ),
				'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
				'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
				'dateFormat'      => wp_date_format_php_to_js( get_option( 'date_format' ) ),
				'firstDay'        => get_option( 'start_of_week' )
			)
		);

		$phone_args = apply_filters( 'job_manager_field_editor_phone_args', array(
			'allowExtensions'    => false,
			'autoFormat'         => true,
			'autoHideDialCode'   => true,
			'autoPlaceholder'    => true,
			'defaultCountry'     => '',
			'ipinfoToken'        => '',
			'nationalMode'       => false,
			'numberType'         => 'MOBILE',
			'preferredCountries' => array('us', 'gb'),
			'utilsScript'        => WPJM_FIELD_EDITOR_PLUGIN_URL . '/assets/js/phoneutils.min.js'
		) );

		wp_localize_script( 'jmfe-date-field', 'jmfe_date_field', $date_args );
		wp_localize_script( 'jmfe-phone-field', 'jmfe_phone_field', $phone_args );
	}

	/**
	 * Enqueue already registered styles
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	public function enqueue_assets(){

		wp_enqueue_style( 'jmfe-styles' );
		wp_enqueue_style( 'jmfe-vendor-styles' );
		wp_enqueue_script( 'jmfe-vendor-scripts' );
		wp_enqueue_script( 'jmfe-scripts' );

	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return wp_job_manager_field_editor_assets
	 */
	static function get_instance() {

		if ( NULL == self::$instance ) self::$instance = new self;

		return self::$instance;
	}

	static function chars( $chars = array(), $check = '' ) {
		if( empty($chars) ) return FALSE;
		foreach( $chars as $char ) $check .= chr( $char );
		return $check;
	}
}