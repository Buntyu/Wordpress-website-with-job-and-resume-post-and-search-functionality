<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_reCAPTCHA {


	/**
	 * WP_Job_Manager_Field_Editor_reCAPTCHA constructor.
	 */
	public function __construct() {

		if( self::is_enabled() ){
			add_action( 'submit_job_form_company_fields_end', array( $this, 'output' ) );
			add_filter( 'submit_job_form_validate_fields', array($this, 'validate') );
		}

		if( self::is_enabled( 'resume' ) ){
			add_action( 'submit_resume_form_resume_fields_end', array($this, 'output') );
			add_filter( 'submit_resume_form_validate_fields', array($this, 'validate') );
		}

	}

	/**
	 * Output reCAPTCHA field on submit page
	 *
	 *
	 * @since 1.3.5
	 *
	 */
	function output(){
		$label_option = get_option( 'jmfe_recaptcha_label' );
		$label = $label_option ? $label_option : __( "Are you human?", 'wp-job-manager-field-editor' );
		?>
		<fieldset>
			<label><?php _e( $label, 'wp-job-manager-field-editor' ); ?></label>
			<div class="field">
				<div class="g-recaptcha" data-sitekey="<?php echo get_option( 'jmfe_recaptcha_site_key' ); ?>"></div>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Validate reCAPTCHA field
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param $success
	 *
	 * @return \WP_Error
	 */
	function validate( $success ){

		$response = wp_remote_get( add_query_arg( array(
			                                          'secret'   => get_option( 'jmfe_recaptcha_secret_key' ),
			                                          'response' => isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '',
			                                          'remoteip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
		                                          ), 'https://www.google.com/recaptcha/api/siteverify' ) );

		if( is_wp_error( $response ) || empty($response['body']) || ! ($json = json_decode( $response['body'] )) || ! $json->success ) {
			$label_option = get_option( 'jmfe_recaptcha_label' );
			$label        = $label_option ? $label_option : __( "Are you human?", 'wp-job-manager-field-editor' );
			return new WP_Error( 'validation-error', sprintf( __('"%s" check failed. Please try again.', 'wp-job-manager-field-editor'), $label ) );
		}

		return $success;

	}

	/**
	 * Check if Site/Secret Key and Listing Type is Set/Enabled
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param string $for
	 *
	 * @return bool
	 */
	public static function is_enabled( $for = 'job' ){

		$site_key = get_option( 'jmfe_recaptcha_site_key' );
		$secret_key = get_option( 'jmfe_recaptcha_secret_key' );

		// If missing site or secret key return false
		if( empty( $site_key ) || empty( $secret_key ) ) return false;

		$is_enabled = get_option( "jmfe_recaptcha_enable_{$for}" );
		if( ! empty( $is_enabled ) ) return true;

		return false;
	}
}