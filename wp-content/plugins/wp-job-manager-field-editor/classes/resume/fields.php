<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Resume_Fields
 *
 * @since 1.1.9
 *
*/
class WP_Job_Manager_Field_Editor_Resume_Fields extends WP_Job_Manager_Field_Editor_Integration {

	private $validate_errors = false;
	private $force_validate = false;
	private $original_submit_handler;


	function __construct() {

		add_filter( 'submit_resume_form_fields', array( $this, 'init_fields' ), 100 );
		add_filter( 'submit_resume_form_fields_get_resume_data', array( $this, 'get_resume_data' ), 100, 2 );
		add_filter( 'resume_manager_resume_fields', array( $this, 'admin_fields' ), 100 );
		add_action( 'resume_manager_update_resume_data', array( $this, 'save_fields' ), 100, 2 );
		// add_filter( 'submit_resume_steps', array( $this, 'steps' ), 100 );
		add_filter( 'submit_resume_wp_handle_upload_overrides', array( $this, 'upload_overrides' ), 100 );
		add_filter( 'submit_resume_form_fields_get_user_data', array( $this, 'get_user_data' ), 100, 2 );
		add_filter( 'submit_resume_form_required_label', array( $this, 'custom_required_label' ), 100, 2 );
		add_filter( 'submit_resume_form_submit_button_text', array( $this, 'custom_submit_button' ), 100 );
		add_action( 'submit_resume_form_start', array($this, 'resume_package_field') );

	}

	/**
	 * Get Resume Field Data
	 *
	 * Called by submit() in both edit and submit form classes and includes all
	 * fields with the value set in the field array config.
	 *
	 * Submit form class calls this method when editing a listing already previewed (so the listing is draft)
	 * Edit form class class this method when editing a listing to populate the values
	 *
	 *
	 * @since 1.3.6
	 *
	 * @param $fields
	 * @param $resume
	 *
	 * @return mixed
	 */
	function get_resume_data( $fields, $resume ) {

		$fields = $this->new_resume_fields( $fields );
		$fields = $this->remove_invalid_fields( $fields );
		$fields = WP_Job_Manager_Field_Editor_Fields_Date::convert_fields( $fields );

		return $fields;

	}

	/**
	 * WPRM Step Filtering
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $steps
	 *
	 * @return mixed
	 */
	function steps( $steps ) {

		// Cache the original default handler so we can call it after our submit handler
		$this->original_submit_handler = $steps[ 'submit' ][ 'handler' ];

		$steps[ 'submit' ][ 'handler' ] = array( $this, 'submit_handler' );

		return $steps;

	}

	/**
	 * WPRM Submit Handler Override
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	function submit_handler() {

		$this->force_validate = TRUE;
		// Compatibility with WPRM >= 1.9.1 where uploads now go through the core
		// WPJM upload, and wp upload override args filter is removed
		parent::$force_validate_resumes = TRUE;
		$this->validate_errors = $this->wprm()->validation_errors();

		// Call original cached submit handler
		call_user_func( $this->original_submit_handler );

	}

	/**
	 * Update Custom Resume Fields Post Meta
	 *
	 * Called after WPJM updates resume post meta with default fields
	 *
	 * @since 1.1.9
	 *
	 * @param integer $job_id
	 * @param array   $values
	 *
	 */
	function save_fields( $job_id, $values ) {

		$this->save_custom_fields( 'resume_fields', $job_id, $values );

	}

	/**
	 * Output Resume fields in Admin
	 *
	 * Called by WP Resume Manager filter on admin side to
	 * return resume fields with user customization.
	 *
	 * @since 1.1.9
	 *
	 * @param array $fields
	 *
	 * @return mixed
	 */
	function admin_fields( $fields ) {

		return $this->prep_admin_fields( 'resume_fields', $fields );

	}

	/**
	 * Add a hidden field with product id to form
	 *
	 *
	 * @since @@since
	 *
	 */
	function resume_package_field() {

		if( WP_Job_Manager_Field_Editor_reCAPTCHA::is_enabled( 'resume' ) ) wp_enqueue_script( 'jmfe-recaptcha' );

		$product_id = isset($_REQUEST['wcpl_jmfe_product_id']) ? intval( $_REQUEST['wcpl_jmfe_product_id'] ) : FALSE;
		$package    = isset($_REQUEST['resume_package']) ? sanitize_text_field( $_REQUEST['resume_package'] ) : $product_id;

		if( $package ) {
			$package = WP_Job_Manager_Field_Editor_Package_WC::get_product_id( $package );
			echo "<input type=\"hidden\" name=\"wcpl_jmfe_product_id\" value=\"{$package}\" />";
		}

	}

	/**
	 * Initialize Resume Fields
	 *
	 * Called by WP Job Manager filter in init_fields() to return
	 * resume fields with user customization.
	 *
	 * @since 1.1.9
	 *
	 * @param array $fields
	 *
	 * @return mixed
	 */
	function init_fields( $fields ) {

		if ( ! $this->was_filter_forced() ) {

			$fields = $this->merge_with_custom_fields( $fields );

			// Remove job fields after merge
			if ( isset( $fields[ 'job' ] ) ) unset( $fields[ 'job' ] );
			// Remove company fields after merge
			if ( isset( $fields[ 'company' ] ) ) unset( $fields[ 'company' ] );

			$product_id     = isset($_REQUEST['wcpl_jmfe_product_id']) ? intval( $_REQUEST['wcpl_jmfe_product_id'] ) : '';
			$resume_package = isset($_REQUEST['resume_package']) ? sanitize_text_field( $_REQUEST['resume_package'] ) : $product_id;
			$action         = isset($_REQUEST['action']) ? sanitize_text_field( $_REQUEST['action'] ) : FALSE;
			$resume_id      = isset($_REQUEST['resume_id']) ? intval( $_REQUEST['resume_id'] ) : FALSE;

			// Admin only filter
			$fields = $this->admin_only_fields( $fields );

			// Product/Package Handling, get job_package from post meta
			if( $resume_id && empty($resume_package) ) $resume_package = WP_Job_Manager_Field_Editor_Package_WC::get_post_package_id( $resume_id );

			// If listing is tied to package, filter so only fields for that package are shown
			if( $resume_package ) $fields = WP_Job_Manager_Field_Editor_Package_WC::filter_fields( $fields, $resume_package );

			// If fields init by post new resume, return fields with disabled removed
			if ( $this->validate_errors || ! empty($resume_package) || ! $resume_id || (isset($_POST['submit_resume']) && ! empty($_POST['submit_resume'])) ) $fields = $this->new_resume_fields( $fields );

			// If called by force validation, set fields equal to field config for validation
			if ( $this->force_validate ) $fields = $this->validation_fields( $fields );

		}

		return $fields;

	}

	/**
	 * Format fields to work with test validation
	 *
	 * In order to return all fields even those disabled we must test validation to determine
	 * fields to return.  To prevent errors when running through core validation, we have to
	 * customize some of the fields.
	 *
	 *
	 * @since 1.2.2
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function validation_fields( $fields ) {

		$fields[ 'resume_fields' ] = array_map( array( $this, 'set_required_false' ), $fields[ 'resume_fields' ] );

		if ( version_compare( RESUME_MANAGER_VERSION, '1.7.5', 'le' ) ) {
			// Version 1.7.5 and earlier do not have filter for upload test, so we have to remove file fields
			// to prevent error when testing validation.
			$fields[ 'resume_fields' ] = $this->fields_list_filter( $fields[ 'resume_fields' ], array( 'type' => 'file' ), 'NOT' );
		}

		return $fields;
	}

	/**
	 * Set wp_handle_upload Arguments
	 *
	 * When testing validation on form we need to set upload validation test
	 * form to TRUE in order to prevent actually uploading the file.
	 *
	 * @since 1.1.11
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	function upload_overrides( $args ){

		// If filter wasn't forced don't set test form true
		if ( ! $this->force_validate ) return $args;

		$this->force_validate = FALSE;
		parent::$force_validate_resumes = FALSE;
		$args['test_form'] = TRUE;
		return $args;
	}

	/**
	 * Output Resume Fields for Template
	 *
	 * Called by WP Job Manager filter in submit() to return resume
	 * fields with user customization for output in template.
	 *
	 * @since 1.1.9
	 *
	 * @param array $fields
	 *
	 * @return mixed
	 */
	function new_resume_fields( $fields ) {

		// Fields were initialized to output form, removed disabled fields from array
		$fields[ 'resume_fields' ] = wp_list_filter( $fields[ 'resume_fields' ], array( 'status' => 'disabled' ), 'NOT' );

		return $fields;

	}

	/**
	 * Filter out Admin Only fields
	 *
	 * If configuration value is set for field to be admin only
	 * this function will remove those fields from the array.
	 *
	 *
	 * @since 1.2.5
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function admin_only_fields( $fields ) {

		$fields[ 'resume_fields' ] = wp_list_filter( $fields[ 'resume_fields' ], array('admin_only' => '1'), 'NOT' );

		return $fields;

	}

	/**
	 * Custom Resume Fields Required Label
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $label
	 * @param $field
	 *
	 * @return string
	 */
	function custom_required_label( $label, $field = false ) {

		// Required Field
		if ( $label === '' ) {
			$custom_req_label = get_option( 'jmfe_resume_required_label' );
			if ( get_option( 'jmfe_enable_resume_required_label' ) && $custom_req_label ) {
				$custom_req_label= html_entity_decode( $custom_req_label);
				$label = ' ' . __( $custom_req_label, 'wp-job-manager-field-editor' );
			}
		}

		// Optional Field
		$defaultOptional = ' <small>' . __( '(optional)', 'wp-job-manager' ) . '</small>';

		$skip_field_types = apply_filters( 'field_editor_resume_required_label_field_types', array('header', 'html', 'actionhook') );
		if( isset($field, $field['type']) && in_array( $field['type'], $skip_field_types ) ) return '';

		if ( $label === $defaultOptional ) {
			$custom_opt_label = get_option( 'jmfe_resume_optional_label' );
			if ( get_option( 'jmfe_enable_resume_optional_label' ) && $custom_opt_label ) {
				$custom_opt_label= html_entity_decode( $custom_opt_label);
				$label = ' ' . __( $custom_opt_label, 'wp-job-manager-field-editor' );
			} elseif ( get_option( 'jmfe_enable_resume_required_label' ) ) {
				$label = '';
			}
		}

		return $label;
	}

	/**
	 * Custom Resume Submit Button Label
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $label
	 *
	 * @return mixed|void
	 */
	function custom_submit_button( $label ){

		$custom_submit_button = get_option( 'jmfe_resume_submit_button' );

		if ( get_option( 'jmfe_enable_resume_submit_button' ) && $custom_submit_button ) {
			$label = __( $custom_submit_button, 'wp-job-manager-field-editor' );
		}

		return $label;
	}
}