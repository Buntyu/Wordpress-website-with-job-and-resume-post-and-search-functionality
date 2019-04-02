<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Translations
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Translations {

	private $js_translations = array();
	private static $context = 'Listing Fields';

	function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'js_translations' ), 100 );
	}

	/**
	 * Get Dynamic Fields to Translate
	 *
	 *
	 * @since 1.4.0
	 *
	 * @return mixed|void
	 */
	public static function get_dynamic_fields(){

		$i18n_fields = apply_filters( 'field_editor_custom_fields_i18n_meta_keys', array('label', 'description', 'placeholder') );

		return $i18n_fields;
	}

	/**
	 * Unregister Strings when Custom Post is Removed
	 *
	 * When the custom post type that holds configuration for fields is removed, we also need
	 * to remove any registered strings as well.
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param $post_id
	 */
	static function post_removed( $post_id ){

		$i18n_fields = self::get_dynamic_fields();
		$meta_key = get_post_meta( $post_id, 'meta_key', true );
		$field_group = get_post_meta( $post_id, 'field_group', true );

		foreach( $i18n_fields as $i18n_field ){
			self::unregister( "{$meta_key} {$i18n_field}", $field_group, true );
		}

	}

	/**
	 * Get the Context for String Translations
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param $context
	 *
	 * @return string
	 */
	static function get_context( $context = false ){

		if( empty( $context ) ) return self::$context;

		// Remove _fields if $context is resume_fields
		$context = str_replace( '_fields', '', $context );
		$context = ucfirst( $context );

		return $context . " " . self::$context;
	}

	/**
	 * Translate Dynamic String
	 *
	 * Will attempt to translate dynamic string through icl_t function, which is native to WPML,
	 * but also works with Polylang through the compatibility function.
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param        $value
	 * @param string $name
	 * @param bool   $context
	 *
	 * @return bool|mixed|string|void
	 */
	public static function translate( $value, $name = '', $context = false ){

		$context = self::get_context( $context );

		// Polylang
		if( function_exists( 'icl_t' ) ) {
			$value = icl_t( $context, $name, $value );
		} elseif( function_exists( 'pll__' ) ) {
			// Polylang Specific, shouldn't be needed as Polylang has above WPML compatibility
			// function, but just in case, you never know...
			$value = pll__( $value );
		} else {
			$value = __( $value, 'wp-job-manager-field-editor' );
		}

		return $value;
	}

	/**
	 * Register Dynamic String
	 *
	 * Will register dynamic string using icl_register_string which is specific to WPML,
	 * but Polylang has compatibility function to support this function as well.
	 *
	 * By default the Polylang function does not save to DB, so using the compatibility
	 * function we don't have to register the string on each page load.
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param        $value
	 * @param string $desc
	 * @param bool   $context
	 */
	public static function register( $value, $desc = 'Field', $context = false ){

		$context = self::get_context( $context );

		// Use Polylang WPML compatibility function to store in DB
		// depreciated in WPML but still works, using this function adds compatibility for both WPML and Polylang
		if( function_exists( 'icl_register_string' ) ) icl_register_string( $context, $desc, $value );

	}

	/**
	 * UnRegister Dynamic String
	 *
	 * Will unregister dynamic string using icl_unregister_string which is specific to WPML,
	 * but Polylang has compatibility function to support this function as well.
	 *
	 * Because string translations are saved to the database, any time we register a new string
	 * translation that is different, we have to remove the old one.
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param string $desc
	 * @param bool   $context
	 * @param bool   $try_all
	 */
	public static function unregister( $desc = 'Field', $context = false, $try_all = false ) {

		$context = self::get_context( $context );

		// Use Polylang WPML compatibility function (works with WPML as well)
		if( function_exists( 'icl_unregister_string' ) ) {

			if( $try_all ){
				// Try to unregister string with default context, just in case
				icl_unregister_string( 'Job ' . self::$context, $desc );
				icl_unregister_string( 'Resume ' . self::$context, $desc );
				icl_unregister_string( 'Company ' . self::$context, $desc );
			}

			icl_unregister_string( $context, $desc );
		}

	}

	/**
	 * Register/Unregister Dynamic Strings
	 *
	 * Method will run through all i18n fields and check the existing meta against
	 * the new meta, if they do not match, will unregister original string, and then
	 * register new one.  If meta doesn't exist, will just register the new one.
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param $meta_key
	 * @param $old_meta
	 */
	public static function do_update( $meta_key, $old_meta ){

		$i18n_fields = self::get_dynamic_fields();
		$context = isset($_POST['field_group']) && ! empty($_POST['field_group']) ? $_POST['field_group'] : FALSE;

		// Register dynamic string translations when field is updated
		foreach( $i18n_fields as $i18n_field ) {
			if( ! isset($_POST[ $i18n_field ]) ) continue;

			// Check if meta already exists for the field, and if it does but is not the same,
			// we need to unregister the original translation string.
			if( isset($old_meta[ $i18n_field ], $old_meta[ $i18n_field ][0]) ) {

				if( $old_meta[ $i18n_field ][0] !== $_POST[ $i18n_field ] ) {
					self::unregister( "{$meta_key} {$i18n_field}", $context );
				}
			}
			// Continue to next (we dont register empty strings)
			if( empty($_POST[ $i18n_field ]) ) continue;

			self::register( $_POST[ $i18n_field ], "{$meta_key} {$i18n_field}", $context );
		}
	}

	/**
	 * Setup and Localize JS Script Translations
	 *
	 * @since 1.1.9
	 *
	 */
	function js_translations(){

		$support_ticket_url = 'https://plugins.smyl.es/support/new/';

		// JS Translation Vars
		$this->js_translations = array(
			'error_submit_ticket' => sprintf( __( 'If you continue receive this error, please submit a <a target="_blank" href="%s">support ticket</a>.', 'wp-job-manager-field-editor' ), esc_url( $support_ticket_url ) ),
			'view_alert'         => __( 'If you want to edit this field, please click the <strong>Edit</strong> link from the list table.', 'wp-job-manager-field-editor' ),
			'meta_key_required'  => __( 'A valid meta key is required!', 'wp-job-manager-field-editor' ),
			'meta_key_no_spaces' => __( 'Meta keys can NOT have spaces in them, use an underscore instead! As an example, job shift should be job_shift.', 'wp-job-manager-field-editor' ),
			'meta_key_query_var' => sprintf( __( 'You can not use a <a href="%s" target="_blank">WordPress Public Query Variable</a> as a meta key as it will cause the submit listing page to show a 404 error! Choose something different!', 'wp-job-manager-field-editor' ), 'https://codex.wordpress.org/WordPress_Query_Vars' ),
			'edit_change_meta_key' => __( 'If you change the meta key it will be saved as a new field!  You should NOT do this unless you know what your doing!', 'wp-job-manager-field-editor' ),
			'meta_key_chars'     => __( 'The ONLY supported characters for meta keys are a-z (lowercase), 0-9 (numbers), and _ (underscores, in place of space) for meta keys!<br />Do <strong>NOT</strong> use any other characters or you will have issues!', 'wp-job-manager-field-editor' ),
			'no_spaces'          => __( 'Spaces are not allowed in this field!', 'wp-job-manager-field-editor' ),
			'type_required'      => __( 'A valid type is required!', 'wp-job-manager-field-editor' ),
			'field_required'     => __( 'This field is required!', 'wp-job-manager-field-editor' ),
			'options_required'   => __( 'Options are required for this field type!  Value IS required, label is optional.  If label is not provided the value will be used instead.', 'wp-job-manager-field-editor' ),
			'options_badchars'   => __( 'Option values can NOT contain the asterisk (*) or tilde (~) characters! Labels are allowed to have these characters, but values can not!', 'wp-job-manager-field-editor' ),
			'priority_required'  => __( 'A valid priority is required! Priority must be a numerical value.', 'wp-job-manager-field-editor' ),
			'only_num'           => __( 'This field MUST be a number/integer.  Decimals are allowed.', 'wp-job-manager-field-editor' ),
			'add_new_field'      => __( 'Add New Field', 'wp-job-manager-field-editor' ),
			'edit_field'         => __( 'Edit Field', 'wp-job-manager-field-editor' ),
			'view_field'         => __( 'View Field', 'wp-job-manager-field-editor' ),
			'save_field'         => __( 'Save Field', 'wp-job-manager-field-editor' ),
			'remove_field'       => __( 'Remove Field', 'wp-job-manager-field-editor' ),
			'enable_field'       => __( 'Enable Field', 'wp-job-manager-field-editor' ),
			'disable_field'      => __( 'Disable Field', 'wp-job-manager-field-editor' ),
			'type'               => __( 'type', 'wp-job-manager-field-editor' ),
			'label'              => __( 'label', 'wp-job-manager-field-editor' ),
			'description'        => __( 'description', 'wp-job-manager-field-editor' ),
			'placeholder'        => __( 'placeholder', 'wp-job-manager-field-editor' ),
			'priority'           => __( 'priority', 'wp-job-manager-field-editor' ),
			'required'           => __( 'required', 'wp-job-manager-field-editor' ),
			'remove'             => __( 'remove', 'wp-job-manager-field-editor' ),
			'disable'            => __( 'disable', 'wp-job-manager-field-editor' ),
			'yes'                => __( 'Yes', 'wp-job-manager-field-editor' ),
			'no'                 => __( 'No', 'wp-job-manager-field-editor' ),
			'options'            => __( 'Options', 'wp-job-manager-field-editor' ),
			'cancel'             => __( 'Cancel', 'wp-job-manager-field-editor' ),
			'close'              => __( 'Close', 'wp-job-manager-field-editor' ),
			'enable'             => __( 'Enable', 'wp-job-manager-field-editor' ),
			'disable'            => __( 'Disable', 'wp-job-manager-field-editor' ),
			'error'              => __( 'Error', 'wp-job-manager-field-editor' ),
			'unknown_error'      => __( 'Uknown Error! Refresh the page and try again.', 'wp-job-manager-field-editor' ),
			'success'            => __( 'Success', 'wp-job-manager-field-editor' ),
			'ays_remove'         => __( 'Are you sure you want to remove', 'wp-job-manager-field-editor' ),
			'ays_disable'        => __( 'Are you sure you want to disable', 'wp-job-manager-field-editor' ),
			'ays_enable'         => __( 'Are you sure you want to enable', 'wp-job-manager-field-editor' ),
			'remove_all_confirm' => __( 'Are you sure?  This will remove ALL of your custom and customized field data!', 'wp-job-manager-field-editor' ),
			'using_the_syntax'   => __( 'Using the syntax ', 'wp-job-manager-field-editor'),
			'tax_options_edit'   => __( 'Edit Field Options', 'wp-job-manager-field-editor' ),
		    'options_detail'     => array(
				'file'   => sprintf( __( 'Allowed<br/><a href="%1$s" target="_blank">Mime Types</a><br/><small>NOT required</small>', 'wp-job-manager-field-editor'), 'http://codex.wordpress.org/Function_Reference/get_allowed_mime_types#Default_allowed_mime_types' ),
		        'select' => __( 'Options', 'wp-job-manager-field-editor' )
		    ),
		    'options_ph_label'    => array(
			    'file'   => __( 'image/jpeg', 'wp-job-manager-field-editor' ),
		        'select' => __( 'Caption', 'wp-job-manager-field-editor' )
		    ),
		    'options_ph_value' => array(
			    'file'   => __( 'jpg', 'wp-job-manager-field-editor' ),
		        'select' => __( 'value', 'wp-job-manager-field-editor' )
		    ),
			'options_label' => array(
				'file'   => __( 'Type', 'wp-job-manager-field-editor' ),
				'select' => __( 'Label', 'wp-job-manager-field-editor' )
			),
			'options_value' => array(
				'file'   => __( 'Extension', 'wp-job-manager-field-editor' ),
				'select' => __( 'Value', 'wp-job-manager-field-editor' )
			),
		);

		$theme = WP_Job_Manager_Field_Editor_Integration::get_theme_name();
		$theme_name = $theme['theme_name'];
		$theme_version = $theme['version'];

		$this->js_translations['wpjmp_exists'] = class_exists( 'WPJMP_Products' ) ? TRUE : FALSE;
		if( $theme_name ) $this->js_translations['theme_name'] = $theme_name;
		if( $theme_version ) $this->js_translations['theme_version'] = $theme_version;

		wp_localize_script( 'jmfe-scripts', 'jmfelocale', $this->js_translations );

	}

}

new WP_Job_Manager_Field_Editor_Translations();