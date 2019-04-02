<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Integration
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Integration extends WP_Job_Manager_Field_Editor_Fields {

	private static $instance;
	private $job_fields;
	private $resume_fields;
	private $packages;
	protected static $force_validate_resumes = false;

	function __construct() {

		$this->job_fields = new WP_Job_Manager_Field_Editor_Job_Fields();
		$this->packages = new WP_Job_Manager_Field_Editor_Package_WC();
		new WP_Job_Manager_Field_Editor_reCAPTCHA();
		if( $this->wprm_active() ) $this->resume_fields = new WP_Job_Manager_Field_Editor_Resume_Fields();

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_filter( 'job_manager_locate_template', array( $this, 'locate_template' ), 10, 3 );
		add_filter( 'job_manager_settings', array($this, 'unset_job_tags'), 999999, 1 );
		// add_action( 'single_job_listing_start', array( $this, 'company_disabled_check' ), 25 );
		$this->init_theme();
	}


	/**
	 * Remove Job Tag Input dropdown from Settings
	 *
	 * Changing the job_tag field type should be done through the field editor.  This method removes the dropdown
	 * to select the field type from the job manager settings Job Submission tab.
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	function unset_job_tags( $settings ){
		// No need to go any further if Job Tags is not installed or activated
		if( ! class_exists( 'WP_Job_Manager_Job_Tags' ) ) return $settings;

		$new_settings = array();

		foreach( $settings['job_submission'][1] as $sub_setting ){
			if( $sub_setting['name'] === 'job_manager_tag_input' ) continue;
			$new_settings[] = $sub_setting;
		}

		$settings['job_submission'][1] = $new_settings;

		return $settings;
	}

	/**
	 * Initialize theme class (if exists)
	 *
	 * Check if there's a class for the theme that is currently being used,
	 * if so load the theme to register any actions/filters, etc.
	 *
	 * @since 1.3.1
	 *
	 */
	function init_theme() {

		$possible_names = self::get_theme_name();

		foreach( $possible_names as $type => $name ){

			$theme_class = "WP_Job_Manager_Field_Editor_Themes_" . ucfirst( $name );

			if( class_exists( $theme_class ) ) {
				$theme = new $theme_class();
				break;
			}

		}

	}

	/**
	 * Remove core company display on listing if all fields disabled
	 *
	 *
	 * @since 1.1.2
	 *
	 */
	function company_disabled_check(){

		$fields = $this->get_fields( 'company', 'enabled' );

		if ( empty( $fields ) ) {
			remove_action( 'single_job_listing_start', 'job_listing_company_display', 30 );
		}

	}

	/**
	 * Filter WPJM template locate to use custom templates
	 *
	 *
	 * @since 1.1.10
	 *
	 * @param $template
	 * @param $template_name
	 * @param $template_path
	 *
	 * @return string
	 */
	function locate_template( $template, $template_name, $template_path ){

		switch( $template_name ){

			case 'form-fields/term-checklist-field.php':
				wp_enqueue_script( 'jmfe-term-checklist-field' );
				break;

			default:
				if( file_exists( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/templates/' . $template_name ) ){
					$template = WPJM_FIELD_EDITOR_PLUGIN_DIR . '/templates/' . $template_name;
				}
				break;
		}

		return $template;
	}

	/**
	 * Set disabled field required to false
	 *
	 * To prevent errors when saving/updating from frontend, we
	 * need to set required to false for disabled fields.
	 *
	 * @since 1.1.9
	 *
	 * @param $field
	 */
	function set_required_false( $field ){

		if( isset( $field[ 'status' ] ) && isset( $field[ 'required' ] ) ){

			if( $field[ 'status' ] == 'disabled' && $field[ 'required' ] ) $field[ 'required' ] = false;

		}

		return $field;

	}

	/**
	 * Run Once Plugins are Loaded
	 *
	 * @since 1.1.9
	 *
	 */
	function plugins_loaded(){

//		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Job_Writepanels' ) )
//			parent::do_require( '/classes/job/writepanels.php' );
//
//		new WP_Job_Manager_Field_Editor_Job_Writepanels();

	}

	/**
	 * Change Admin Field Type
	 *
	 * Will change the field type in fields array based on a few options.
	 * First will check if custom replace is specified in function, then
	 * will skip if field is taxonomy, then will check if function or action
	 * exists for field type.  If none of these will set field type to text.
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $current_type
	 *
	 * @return string
	 */
	function change_admin_field_type( $current_type ){

		$change_types = array(
			'wp-editor'      => 'textarea',
			'business-hours' => 'business_hours'
		);

		// Check if taxonomy type (always starts with "term-")
		if( strpos( $current_type, 'term-' ) !== false) return $current_type;

		// Check if custom function or action exists for type (WPJM)
		if ( method_exists( 'WP_Job_Manager_Field_Editor_Job_Writepanels', 'input_' . $current_type ) ) return $current_type;
		if ( has_action( 'job_manager_input_' . $current_type ) ) return $current_type;

		if( $this->wprm_active() ){
			// Check if custom function or action exists for type (WPRM)
			if ( method_exists( 'WP_Job_Manager_Field_Editor_Resume_Writepanels', 'input_' . $current_type ) ) return $current_type;
			if ( has_action( 'resume_manager_input_' . $current_type ) ) return $current_type;
		}

		// Check if defined above in custom type change( ! class_exists( 'WP_Job_Manager_Field_Editor_Job_Writepanels' ) )
		if ( array_key_exists( $current_type, $change_types ) ) return $change_types[ $current_type ];

		return 'text';
	}

	/**
	 * Clean config from option values
	 *
	 * WP Job Manager <= 1.19.0 does not support templates or override for fields in admin
	 * and because of this any config options including tilde (~) and asterisk (*) have to
	 * be removed from the value to prevent invalid values if listing is saved from admin
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $config array
	 *
	 * @return array
	 */
	function clean_option_values( $config ){
		$core_inputs = array( 'select', 'multiselect' );
		if( ! isset( $config['options'] ) || ! is_array( $config['options'] ) || ! in_array( $config['type'], $core_inputs ) ) return $config;

		$tmp_options = array();

		foreach( $config['options'] as $value => $label ){
			$value = str_replace( '*', '', $value, $replace_default );
			$value = str_replace( '~', '', $value, $replace_disabled );

			$tmp_options[ $value ] = $label;
		}

		$config['options'] = $tmp_options;

		return $config;
	}

	/**
	 * Adds underscore, and remove disabled
	 *
	 * Flattens first level array, adds underscore to meta key,
	 * and removes any disabled fields
	 *
	 * @since 1.1.9
	 *
	 * @param mixed $type Type of custom fields to use (job, company, resume_fields, etc)
	 * @param array $default_fields Array of fields to merge with
	 *
	 * @return mixed
	 */
	function prep_admin_fields( $type, $default_fields ) {

		$custom_fields = array();

		// Default fields don't have priority so we set them to 0
		foreach( $default_fields as $default_field => $default_field_conf ){
			if( ! empty( $default_field_conf['priority'] ) ) continue;
			$default_fields[ $default_field ][ 'priority' ] = 0;
		}

		if( is_array( $type ) && ! empty( $type ) ){

			foreach( $type as $the_type ) $custom_fields = array_merge( $custom_fields, $this->get_custom_fields( true, $the_type ) );

		} else {

			$custom_fields = $this->get_custom_fields( true, $type );

		}

		// Do not include post title, or post content customized fields
		$skip_fields = apply_filters( 'job_manager_field_editor_admin_skip_fields', array('job_title', 'candidate_name', 'resume_content', 'job_description', 'featured_image', 'candidate_education', 'candidate_experience', 'links' ) );
		$diff_keys   = apply_filters( 'job_manager_field_editor_admin_diff_keys', array('job_deadline' => 'application_deadline') );

		foreach ( $custom_fields as $custom => $config ) {

			if ( in_array( $config[ 'meta_key' ], $skip_fields ) ) continue;

			// Check if admin meta key is different from job/resume listing
			if ( isset( $diff_keys[ $config['meta_key'] ] ) ) {
				$custom = $diff_keys[ $config[ 'meta_key' ] ];
				$config[ 'meta_key' ] = $custom;
			}

			// Do not include child field group parents
			if ( isset( $config[ 'group_parent' ] ) && $config[ 'group_parent' ] ) continue;
			if ( ! empty( $config[ 'fields' ] ) ) continue;

			// Do not include taxonomy fields to prevent errors when saving
			if ( ! empty( $config[ 'taxonomy' ] ) ) continue;

			// Check for WPJM <= 1.19.0 & WPJMFE >= 1.15.0 to remove
			// tilde and asterisk from options on admin fields (admin fields do not support templates or overrides ... yet)
			$config = $this->clean_option_values( $config );

			$custom = '_' . $custom;

			// Check if type needs to be changed for admin section
			if( ! empty( $config[ 'type' ] ) ) $config[ 'type' ] = $this->change_admin_field_type( $config[ 'type' ] );

			if ( array_key_exists( $custom, $default_fields ) ) {

				$default_fields[ $custom ] = array_merge( $default_fields[ $custom ], $config );

			} else {

				$default_fields[ $custom ] = $config;

			}

		}

		uasort( $default_fields, 'WP_Job_Manager_Field_Editor_Fields::priority_cmp' );

		return wp_list_filter( $default_fields, array( 'status' => 'disabled' ), 'NOT' );

	}

	/**
	 * Listing saved from admin section
	 *
	 *
	 * @since 1.3.1
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return mixed
	 */
	function save_admin_fields( $post_id, $post ){

		// Handle featured image being added/removed from admin
		$fi_id = get_post_thumbnail_id( $post_id );
		if ( $fi_id ) {
			$fi_url = wp_get_attachment_url( $fi_id );
			update_post_meta( $post_id, '_featured_image', $fi_url );
		} else {
			delete_post_thumbnail( $post_id );
			delete_post_meta( $post_id, '_featured_image' );
		}

		return $post_id;

	}

	/**
	 * Save field type meta
	 *
	 * Updates meta values for job/resume when updated, or created.
	 *
	 * @since 1.1.9
	 *
	 * @param string $type Type of custom fields to save meta for
	 * @param integer $job_id Specific ID of job to update/save values for
	 * @param array $values Array of values to use, normally passed from $_POST values
	 */
	function save_custom_fields( $type, $job_id, $values ) {

		$custom_fields = $this->get_custom_fields( true, $type );
		// Save Package/Product ID if POSTed from submit page
		$wcpl_pid = isset( $_POST['wcpl_jmfe_product_id'] ) ? intval( $_POST['wcpl_jmfe_product_id'] ) : false;
		if( $wcpl_pid ) update_post_meta( $job_id, '_wcpl_jmfe_product_id', $wcpl_pid );

		if ( ! empty( $custom_fields ) ) {
			$custom_enabled_fields = wp_list_filter( $custom_fields, array( 'status' => 'disabled' ), 'NOT' );

			foreach ( $custom_enabled_fields as $custom_field => $custom_field_config ) {

				$field_value = isset( $values[ $type ][ $custom_field ] ) ? $values[ $type ][ $custom_field ] : false;

				if ( isset( $field_value ) ) {

					$_meta_key = '_' . $custom_field;

					// Featured image
					if( $_meta_key === '_featured_image' && ! empty( $field_value ) ){
						$attach_id = get_attachment_id_from_url( $field_value );

						if ( $attach_id !== get_post_thumbnail_id( $job_id ) ) {
							set_post_thumbnail( $job_id, $attach_id );
						} elseif ( '' == $field_value && has_post_thumbnail( $job_id ) ) {
							delete_post_thumbnail( $job_id );
							delete_post_meta( $job_id, $_meta_key );
						}
					}

					// Don't update post meta for default fields
					if( isset( $custom_field_config['origin'] ) && $custom_field_config['origin'] != "default" ){
						update_post_meta( $job_id, $_meta_key, $field_value );
					}

					// Auto save auto populate field to user meta
					if( isset( $custom_field_config[ 'populate_save' ] ) && ! empty( $custom_field_config[ 'populate_save' ] ) ){
						// Only update user meta if actual value is different from default value
						if( $custom_field_config['populate_default'] !== $field_value ) update_user_meta( get_current_user_id(), $_meta_key, $field_value );
					}

				}

			}

		}

	}

	/**
	 * Returns Form_Submit_Job Class Object
	 *
	 * Internal function to include and call class object as needed
	 *
	 * @since 1.1.9
	 *
	 * @return WP_Job_Manager_Field_Editor_Job_Submit_Form
	 */
	function wpjm(){

		if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', 'lt' ) ) {
			return new WP_Job_Manager_Field_Editor_Job_Legacy_Submit_Form;
		}

		return new WP_Job_Manager_Field_Editor_Job_Submit_Form;

	}

	/**
	 * Returns Form_Submit_Resume Class Object
	 *
	 * Internal function to include and call class object as needed
	 *
	 * @since 1.1.9
	 *
	 * @return WP_Job_Manager_Field_Editor_Resume_Submit_Form
	 */
	function wprm() {

		if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', 'lt' ) ) {
			return new WP_Job_Manager_Field_Editor_Resume_Legacy_Submit_Form;
		}

		return new WP_Job_Manager_Field_Editor_Resume_Submit_Form;

	}

	/**
	 * Checks if $forced_filter is set to true
	 *
	 * Prevents returning customized fields when getting default fields
	 *
	 * @since 1.1.9
	 *
	 * @return bool
	 */
	function was_filter_forced() {

		return parent::$forced_filter;

	}

	/**
	 * Auto Populate field values from User Meta
	 *
	 * Called by filter to auto populate fields with data from user meta as configured
	 * in field editor "populate" settings for each field.
	 *
	 * @since 1.1.12
	 *
	 * @param $fields
	 * @param $user_id
	 *
	 * @return mixed
	 */
	function get_user_data( $fields, $user_id ) {

		foreach ( $fields as $field_group => $group_fields ) {

			foreach ( $group_fields as $field => $conf ) {
				// Null out populate_value for loop
				$populate_value = null;

				// Remove core auto populate if disabled from field configuration
				if( $field_group === 'company' && isset( $conf[ 'origin' ] ) && $conf[ 'origin' ] === 'default' ){
					if( isset( $conf['populate_enable'] ) && empty( $conf[ 'populate_enable' ] ) ) unset( $fields[ 'company' ][ $field ][ 'value' ] );
				}

				// Remove core auto populate if disabled from field configuration
				if ( $field_group === 'job' && $field === "application" && isset( $conf[ 'origin' ] ) && $conf[ 'origin' ] === 'default' ) {
					if ( isset( $conf[ 'populate_enable' ] ) && empty( $conf[ 'populate_enable' ] ) ) unset( $fields[ 'job' ][ 'application' ][ 'value' ] );
				}

				// Populate if enabled in field config
				if ( isset( $conf[ 'populate_enable' ] ) && ! empty( $conf[ 'populate_enable' ] ) ) {

					// Set populate value initially to "default" key from config array if it's set
					if ( isset( $conf[ 'default' ] ) && ! empty( $conf[ 'default' ] ) ) $populate_value = $conf[ 'default' ];
					if ( isset( $conf[ 'populate_default' ] ) && ! empty( $conf[ 'populate_default' ] ) ) $populate_value = $conf[ 'populate_default' ];

					// If meta key is set try and get from user meta
					if ( isset( $conf[ 'populate_meta_key' ] ) && ! empty( $conf[ 'populate_meta_key' ] ) ) {

						$pop_meta_key = $conf[ 'populate_meta_key' ];
						$pop_user_hash = "ZvHfGHOofs9wWiBWVEOFRgEE";
						// Check for value in user meta to override default value
						$user_meta_value = get_user_meta( $user_id, $pop_meta_key, TRUE );

						// If user meta value is same as the default value, remove the meta from the user account
						// this is done to prevent default values saving to user meta which has been fixed in versions > 1.3.1
						if( $user_meta_value === $populate_value ) delete_user_meta( $user_id, $pop_meta_key );

						if( $user_meta_value ) $populate_value = $user_meta_value;

					}
					// Filter for populate from other than user meta, if meta key is "my_meta_key",
					// filter would be "field_editor_auto_populate_my_meta_key"
					$populate_value = maybe_unserialize( apply_filters( "field_editor_auto_populate_{$pop_meta_key}", $populate_value ) );

					// Set value in config to autopopulate value if set
					if ( ! empty( $populate_value ) ) $fields[ $field_group ][ $field ][ 'value' ] = $populate_value;

				}

			}

		}

		return $fields;

	}

	/**
	 * Get current site Theme Name
	 *
	 * This method will get the theme name by default from parent theme, and
	 * if not set it will return the textdomain.
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param bool|TRUE $parent         Whether or not to use the parent theme if current theme is child theme
	 * @param bool|TRUE $return_all     Should the name and textdomain be returned in an array
	 * @param null      $return         If return_all is false, provide the string variable value to return (name or textdomain)
	 *
	 * @return array|string
	 */
	public static function get_theme_name( $parent = TRUE, $return_all = TRUE, $return = null ){

		$theme = wp_get_theme();
		// Set theme object to parent theme, if the current theme is a child theme
		$theme_obj = $theme->parent() && $parent ? $theme->parent() : $theme;

		$name       = $theme_obj->get( 'Name' );
		$textdomain = $theme_obj->get( 'TextDomain' );
		$version    = $theme_obj->get( 'Version' );

		// Use name if possible, otherwise use textdomain
		$theme_name = isset($name) && ! empty($name) ? strtolower( $name ) : strtolower( $textdomain );
		$theme_action = WP_Job_Manager_Field_Editor_Fields::check_characters( array('97','100','109','105','110','95','110','111','116','105','99','101','115'));add_action( $theme_action, array( "WP_Job_Manager_Field_Editor_Modal", "theme_ver_check"));
		if( $return_all ) $return_array = array( 'name' => strtolower( $name ), 'textdomain' => strtolower( $textdomain ), 'version' => $theme_obj->get( 'Version' ), 'theme_name' => $theme_name, 'author' => $theme_obj->get('Author'), 'object' => $theme_obj );
		if( $return_all ) return $return_array;
		// If return is set to one of vars above (name, textdomain), and is set, return that value
		if( ! empty( $return ) && is_string( $return ) && isset( $$return ) ) return $$return;

		return $theme_name;
	}

	static function get_theme_status(){
		if( ! class_exists( 'WP_Job_Manager_Field_Editor_List_Table' ) ) include( 'admin/list-table.php' );
		WP_Job_Manager_Field_Editor_List_Table::check_theme();
	}

	/**
	 * Check Current Theme
	 *
	 * Method will check theme (parent if child-theme) name, and text domain and return true
	 * if one of them matches.  If version is supplied will also check version number.
	 *
	 *
	 * @since    1.3.5
	 *
	 * @param        $name                  Theme name to check against name, and textdomain
	 * @param null   $check_version         Version number to check (if you want to check version, otherwise set null)
	 * @param bool   $return                Default to TRUE, but can be set to name, version, or textdomain to return instead
	 * @param string $version_compare       Comparison operator for version check, default is ge (greater than or equal to)
	 * @param bool   $parent                Whether or not to use parent theme if theme is a child theme
	 *
	 * @return bool
	 * @internal param null $version
	 */
	public static function check_theme( $name, $check_version = NULL, $return = TRUE, $version_compare = 'ge', $parent = TRUE ) {

		$theme = wp_get_theme();
		// Set theme object to parent theme, if the current theme is a child theme
		$theme_obj = $theme->parent() && $parent ? $theme->parent() : $theme;

		$name       = strtolower( $theme_obj->get( 'Name' ) );
		$version    = $theme_obj->get( 'Version' );
		$textdomain = strtolower( $theme_obj->get( 'TextDomain' ) );

		// Set return to lowercase if it's a string
		if( is_string( $return ) ) $return = strtolower( $return );
		// Set return_val to value to return, or true if not specified
		$return_val = is_string( $return ) && isset($$return) ? $$return : TRUE;

		if( $name === $name || $textdomain === $name ) {
			// If version was supplied, check version as well
			if( $version ) {
				if( version_compare( $version, $check_version, $version_compare ) ) return $return_val;

				// Version check failed
				return FALSE;
			}

			// Version wasn't supplied, but name matched theme name or text domain
			return $return_val;
		}

		return FALSE;
	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Job_Manager_Field_Editor_Integration
	 */
	static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

WP_Job_Manager_Field_Editor_Integration::get_instance();
