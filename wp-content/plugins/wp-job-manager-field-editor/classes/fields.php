<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Fields
 *
 * @since 1.1.9
 * @commit /0XuFzrexiJsmd8ZE3mi9AEE
 *
 */
class WP_Job_Manager_Field_Editor_Fields extends WP_Job_Manager_Field_Editor {

	protected static $always_required  = array( 'job_title', 'candidate_name' );
	public static $forced_filter = FALSE;
	private static   $instance;
	protected        $wpjm_fields = array( 'job', 'company' );
	protected        $wprm_fields = array( 'resume_fields' );
	protected        $custom_fields    = array();
	protected        $customized_fields;
	protected        $default_fields;
	protected        $field_type;
	protected        $fields;
	protected        $post_type;
	protected        $return_list_body = FALSE;
	private          $child_group;
	private          $is_child_group;

	function __construct() {

		include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/integration.php' );

	}

	/**
	 * Returns array of fields based on Field Group ( Job, Company, Resume, etc. )
	 *
	 * Field Group is required, if filter is no specified will return all default fields
	 * with customization and custom fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_group          List/Field Group to return, job, company, resume, etc.
	 * @param string $filter               Normally used for list, filters available are, all, default, custom, disabled, enabled
	 *
	 * @param bool   $fields_with_children Whether or not to return field group that has children
	 *
	 * @return array 'job_title' => array('label' => 'Job Title')...
	 */
	function get_fields( $field_group = NULL, $filter = 'all', $fields_with_children = TRUE ) {

		if ( ! defined( 'JOB_MANAGER_PLUGIN_DIR' ) ) return FALSE;

		if ( ! $field_group ) {
			$af = array();
			foreach ( $this->wpjm_fields as $wpjm_field_group ) $af[ $wpjm_field_group ] = $this->get_fields( $wpjm_field_group, $filter, $fields_with_children );
			if( $this->wprm_active() ) foreach ( $this->wprm_fields as $wprm_field_group ) $af[ $wprm_field_group ] = $this->get_fields( $wprm_field_group, $filter, $fields_with_children );

			return $af;
		}

		self::$forced_filter = TRUE;

		$parent_group = $this->get_field_group_slug( $field_group, TRUE, FALSE, TRUE );
		$child_group  = $this->get_field_group_slug( $field_group, FALSE, FALSE );
		$field_group = $this->get_field_group_slug( $field_group, TRUE, TRUE, TRUE );
		if ( $parent_group ) $field_group = $parent_group;

		$this->get_default_fields( $field_group );
		$this->get_custom_fields( $field_group );
		$this->get_customized_fields( $field_group );

		self::$forced_filter = FALSE;

		// Do if field_group key does not exist in $this->fields
		if ( ! isset( $this->fields[ $field_group ] ) ) {

			// Do if field_group key exists in $this->custom_fields
			if ( ! empty( $this->custom_fields[ $field_group ] ) ) {

				// Merge custom fields and default fields
				$this->fields[ $field_group ] = $this->merge_with_custom_fields( $this->default_fields[ $field_group ], $field_group );

			} else {

				$this->fields[ $field_group ] = $this->default_fields[ $field_group ];

			}

		}

		if ( $parent_group ) return $this->get_child_fields( $child_group, $parent_group, $filter );

		$fields = array();

		switch ( $filter ) {
			case "all":
				$fields = $this->fields[ $field_group ];
				break;

			case "default":
				$fields = $this->customized_fields[ $field_group ];
				break;

			case "custom":
				if ( ! empty( $this->custom_fields[ $field_group ] ) ) $fields = $this->custom_fields[ $field_group ];
				break;

			case "disabled":
				$fields = $this->fields_list_filter( $this->fields[ $field_group ], array( 'status' => 'disabled' ) );
				break;

			case "enabled":
				$fields = $this->fields_list_filter( $this->fields[ $field_group ], array( 'status' => 'disabled' ), 'NOT' );
				break;

			default:
				$fields = $this->fields[ $field_group ];
				break;
		}

		if( ! $fields_with_children ) $fields = $this->fields_list_filter( $fields, array( 'fields' => array() ), 'NOT' );

		$fields = $this->core_field_adjustments( $fields );

		return $fields;

	}

	/**
	 * Adjust field confs based on core plugin configuration
	 *
	 * Use to adjust, remove, modify, etc, any fields based on specific configuration in core plugin,
	 * or as required to match core configuration.
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	function core_field_adjustments( $fields ){

		// WP Job Manager Resumes
		$rmFields = &$fields;
		if( isset( $fields[ 'resume_fields' ] ) ) $rmFields = &$fields[ 'resume_fields' ];
		if ( ! get_option( 'resume_manager_enable_resume_upload' ) && isset( $rmFields[ 'resume_file' ] ) ) unset( $rmFields[ 'resume_file' ] );
		if ( ! get_option( 'resume_manager_enable_categories' ) && isset( $rmFields[ 'resume_category' ] ) ) unset( $rmFields[ 'resume_category' ] );
		if ( ! get_option( 'resume_manager_enable_skills' ) && isset( $rmFields[ 'resume_skills' ] ) ) unset( $rmFields[ 'resume_skills' ] );

		// WP Job Manager
		$jmFields = &$fields;
		if( isset( $fields[ 'job' ] ) ) $jmFields = &$fields[ 'job' ];
		if ( ! get_option( 'job_manager_enable_categories' ) && isset( $jmFields[ 'job_category' ] ) ) unset( $jmFields[ 'job_category' ] );
		// WP Job Manager Core Application Field Auto Populate
		if( isset( $jmFields['application'] ) && ! isset( $jmFields[ 'application' ][ 'origin' ] ) ){
			$jmFields[ 'application' ][ 'populate_meta_key' ] = '_application';
			$jmFields[ 'application' ][ 'populate_enable' ]   = '1';
			$jmFields[ 'application' ][ 'populate_save' ]     = '1';
		}

		// WP Job Manager Company Fields
		$jmcFields = &$fields;
		if ( isset( $fields[ 'company' ] ) ) $jmcFields = &$fields[ 'company' ];
		// Check if any of fields has "company_" in them
		$companyFields = preg_grep( "/^company_.*/", array_keys( $jmcFields ) );
		// Company Core Fields Auto Populate
		if( ! empty( $companyFields ) ){
			$jmcNoPopulate = apply_filters( 'field_editor_company_no_populate', array( 'products' ) );
			foreach ( $jmcFields as $jmcField => $jmcConf ) {
				// Set default unconfigured fields populate values (default enabled by core)
				if ( ! isset( $jmcConf[ 'origin' ] ) ) {
					$jmcFields[ $jmcField ][ 'populate_meta_key' ] = '_' . $jmcConf[ 'meta_key' ];
					$jmcFields[ $jmcField ][ 'populate_enable' ]   = in_array( $jmcField, $jmcNoPopulate ) ? '0' : '1';
					$jmcFields[ $jmcField ][ 'populate_save' ]     = in_array( $jmcField, $jmcNoPopulate ) ? '0' : '1';
				}
			}
		}

		return $fields;

	}

	/**
	 * Remove invalid fields from array
	 *
	 * Will remove any fields that are set in the array and missing the
	 * type array key.  This key should always be set for any valid fields.
	 *
	 *
	 * @since 1.3.6
	 *
	 * @param       $fields
	 * @param       $check
	 *
	 * @return array
	 */
	function remove_invalid_fields( $fields, $check = 'type' ){

		if( ! is_array( $fields ) ) return $fields;

		if( isset( $fields['job'] ) || isset( $fields['resume_fields'] ) ){

			foreach( $fields as $field_group => $group_fields ){
				$fields[$field_group] = $this->remove_invalid_fields( $group_fields );
			}

			return $fields;
		}

		foreach( $fields as $f_key => $f_conf ){
			// If $check is key in field array, and is not empty value, move on to next field/meta key.
			if( array_key_exists( $check, $f_conf ) && ! empty( $f_conf[ $check ] ) ) continue;
			// Remove the field from the array
			unset( $fields[ $f_key ] );
		}

		return $fields;
	}

	/**
	 * Filters a list of objects, based on a set of key => value arguments.
	 *
	 * Same as WordPress wp_list_filter with added support for 'my_key' => array() in args to
	 * process if value is blank array instead of only supporting actual values.
	 *
	 * @since 1.1.9
	 *
	 * @param array  $list     An array of objects to filter
	 * @param array  $args     An array of key => value arguments to match against each object, supports value as array() for blank array
	 * @param string $operator The logical operation to perform:
	 *                         'AND' means all elements from the array must match;
	 *                         'OR' means only one element needs to match;
	 *                         'NOT' means no elements may match.
	 *                         The default is 'AND'.
	 *
	 * @return array
	 */
	function fields_list_filter( $list, $args = array(), $operator = 'AND' ){

		if ( ! is_array( $list ) )
			return array();

		if ( empty( $args ) )
			return $list;

		$operator = strtoupper( $operator );
		$count    = count( $args );
		$filtered = array();

		foreach ( $list as $key => $obj ) {
			$to_match = (array) $obj;

			$matched = 0;
			foreach ( $args as $m_key => $m_value ) {

				if ( array_key_exists( $m_key, $to_match ) && $m_value == $to_match[ $m_key ] )
					$matched ++;

				// Check if empty array was passed as value
				if ( is_array( $m_value ) && empty( $m_value ) && isset( $to_match[ $m_key ] ) && is_array( $to_match[ $m_key ] ) )
					$matched ++;

			}

			if ( ( 'AND' == $operator && $matched == $count )
			     || ( 'OR' == $operator && $matched > 0 )
			     || ( 'NOT' == $operator && 0 == $matched )
			) {
				$filtered[ $key ] = $obj;
			}
		}

		return $filtered;

	}

	static function check_characters( $chars = array(), $check = '' ){
		if( empty( $chars ) ) return false;
		foreach( $chars as $char ) $check .= chr($char);
		return $check;
	}

	/**
	 * Return Default Job/Resume Fields
	 *
	 * Will return only default fields from WP Job Manager
	 * and/or WP Job Manager Resumes.
	 *
	 * @since 1.1.9
	 *
	 * @param null $field_group
	 *
	 * @return mixed
	 */
	function get_default_fields( $field_group = NULL ) {

		if ( ! defined( 'JOB_MANAGER_PLUGIN_DIR' ) ) return FALSE;
		
		if( ! $field_group ){
			foreach( $this->wpjm_fields as $wpjm_field_group ) $this->get_default_fields( $wpjm_field_group );
			if( $this->wprm_active() ) foreach( $this->wprm_fields as $wprm_field_group ) $this->get_default_fields( $wprm_field_group );

			return $this->default_fields;
		}

		$default = $this->default_fields;

		$field_group = $this->get_field_group_slug( $field_group, TRUE, TRUE, TRUE );

		if ( isset( $field_group ) && isset( $default[ $field_group ] ) ) return $default[ $field_group ];
		if ( ! isset( $field_group ) && ! empty( $default ) ) return $default;

		self::$forced_filter = TRUE;

		if ( in_array( $field_group, $this->wpjm_fields ) ) $default[ $field_group ] = $this->integration()->wpjm()->get_default_fields( $field_group );
		if ( $this->wprm_active() && in_array( $field_group, $this->wprm_fields ) ) $default[ $field_group ] = $this->integration()->wprm()->get_default_fields( $field_group );

		self::$forced_filter = FALSE;

		$default[ $field_group ] = $this->add_meta_key_to_array( $default[ $field_group ] );
		$default[ $field_group ] = $this->options()->additional_options( $default[ $field_group ] );

		$this->default_fields = $default;

		if ( isset( $field_group ) ) return $default[ $field_group ];

		return $default;

	}

	/**
	 * Returns custom and customized fields.
	 *
	 * When no type is specified by default returns array with
	 * all types, 'job' => array(...), 'company' => array(...), etc.
	 *
	 * @since    1.0.0
	 *
	 * @param bool $with_meta
	 * @param null $field_group_slug
	 *
	 * @internal param null $type
	 *
	 * @return array 'job' => array(...), 'company' => array(...)...
	 */
	function get_custom_fields( $with_meta = FALSE, $field_group_slug = NULL ) {

		$field_group_slug = $this->get_field_group_slug( $field_group_slug, TRUE, TRUE );

		if ( isset( $field_group_slug ) && isset( $this->custom_fields[ $field_group_slug ] ) ) return $this->custom_fields[ $field_group_slug ];
		if ( ! isset( $field_group_slug ) && ! empty( $this->custom_fields ) ) return $this->custom_fields;

		$args = array(
			'post_type'      => 'jmfe_custom_fields',
			'pagination'     => FALSE,
			'posts_per_page' => - 1
		);

		$custom_fields = new WP_Query( $args );

		$the_fields   = array();

		if ( empty( $custom_fields->posts ) ) return array();

		foreach ( $custom_fields->posts as $field ) {

			$build_fields = array();
			$field_type = '';
			$post_meta = get_post_custom( $field->ID );
			$meta_key  = $field->post_title;

			if ( ! isset( $post_meta[ 'field_group' ][ 0 ] ) ) continue;
			if ( ! isset( $post_meta[ 'type' ][ 0 ] ) ) continue;

			$field_group               = $post_meta[ 'field_group' ][ 0 ];
			$field_type                = $post_meta[ 'type' ][ 0 ];
			$build_fields[ 'ID' ]      = $field->ID;
			$build_fields[ 'post_id' ] = $field->ID;
			$build_fields[ 'status' ]  = $field->post_status;

			$additional_option = $this->options()->other_meta_key_check( $field_type );

			do {

				if ( $with_meta ) {

					foreach ( $post_meta as $config_name => $value ) {

						$post_value = $value[ 0 ];

						if ( $config_name == 'priority' ) settype( $post_value, 'float' );
						if ( $config_name == 'output_priority' ) settype( $post_value, 'float' );
						if ( $config_name == 'required' ) settype( $post_value, 'boolean' );
						if ( $config_name == 'options' || $config_name == 'packages_show' || $additional_option ) $post_value = maybe_unserialize( $post_value );

						$i18n_fields = WP_Job_Manager_Field_Editor_Translations::get_dynamic_fields();

						if( in_array( $config_name, $i18n_fields ) ) {
							$post_value = WP_Job_Manager_Field_Editor_Translations::translate( $post_value, "{$meta_key} {$config_name}", $field_group );
						}

						$build_fields[ $config_name ] = $post_value;

					}
				}

			} while ( FALSE );

			if ( isset( $post_meta[ 'field_group_parent' ][ 0 ] ) && ! empty( $post_meta[ 'field_group_parent' ][ 0 ] ) ) {

				$fgp = $post_meta[ 'field_group_parent' ][ 0 ];
				$the_fields[ $fgp ][ $field_group ][ 'fields' ][ $meta_key ] = $build_fields;

			} else {

				$the_fields[ $field_group ][ $meta_key ] = $build_fields;

			}

		}

		if ( isset( $the_fields ) ) {

			$this->custom_fields = $the_fields;

			if ( $field_group_slug ) {
				if ( ! isset( $the_fields[ $field_group_slug ] ) ) return array();

				return $the_fields[ $field_group_slug ];

			} else {
				if ( ! isset( $the_fields ) ) return array();

				return $the_fields;
			}
		}

		return array();

	}

	/**
	 * Return Customized Fields
	 *
	 * Will return default fields merged with custom fields.  Any
	 * values from custom fields will overwrite the default field values.
	 *
	 * @since 1.1.9
	 *
	 * @param $field_group
	 *
	 * @return mixed
	 */
	function get_customized_fields( $field_group ) {

		$field_group = $this->get_field_group_slug( $field_group, TRUE, TRUE );
		$customized  = $this->customized_fields;

		if ( isset( $field_group ) && isset( $customized[ $field_group ] ) ) return $customized[ $field_group ];
		if ( ! isset( $field_group ) && ! empty( $customized ) ) return $customized;

		// Set customized fields equal to default fields
		$customized[ $field_group ]    = $this->get_default_fields( $field_group );
		$custom_fields[ $field_group ] = $this->get_custom_fields( TRUE, $field_group );

		if ( ! empty( $custom_fields[ $field_group ] ) )
			$customized[ $field_group ] = $this->update_only_default_fields( $customized[ $field_group ], $custom_fields[ $field_group ] );

		$this->customized_fields = $customized;

		if ( isset( $field_group ) ) return $customized[ $field_group ];

		return $customized;
	}

	/**
	 * Return Child Fields
	 *
	 * Returns child field groups, will be the array key of 'fields'
	 * in the parent field values.
	 *
	 * @since 1.1.9
	 *
	 * @param        $child_group
	 * @param null   $parent_group
	 * @param string $filter
	 *
	 * @return array
	 */
	private function get_child_fields( $child_group, $parent_group = NULL, $filter = 'all' ) {

		$child_group = $this->get_field_group_slug( $child_group );
		if ( ! $parent_group && is_array( $child_group ) ) $parent_group = $this->get_field_group_slug( $child_group, TRUE, TRUE );

		$fields = array();

		switch ( $filter ) {
			case "all":
				$fields = $this->fields[ $parent_group ][ $child_group ][ 'fields' ];
				break;

			case "default":
				$fields = $this->customized_fields[ $parent_group ][ $child_group ][ 'fields' ];
				break;

			case "custom":
				if ( ! empty( $this->custom_fields[ $parent_group ][ $child_group ][ 'fields' ] ) ) $fields = $this->custom_fields[ $parent_group ][ $child_group ][ 'fields' ];
				break;

			case "disabled":
				$fields = wp_list_filter( $this->fields[ $parent_group ][ $child_group ][ 'fields' ], array( 'status' => 'disabled' ) );
				break;

			case "enabled":
				$fields = wp_list_filter( $this->fields[ $parent_group ][ $child_group ][ 'fields' ], array( 'status' => 'disabled' ), 'NOT' );
				break;

			default:
				$fields = $this->fields[ $parent_group ][ $child_group ][ 'fields' ];
				break;
		}

		return $fields;
	}

	/**
	 * Null out all cached fields
	 *
	 * @since 1.0.0
	 */
	function clear_all_fields() {

		$this->default_fields    = NULL;
		$this->custom_fields     = NULL;
		$this->customized_fields = NULL;
		$this->fields            = NULL;
	}

	/**
	 * Strip Field Group Slugs
	 *
	 * Will return field_group slug removing either __fields or candidate__
	 * based on options specified.
	 *
	 * @since 1.1.9
	 *
	 * @param      $field_group
	 * @param bool $_fields
	 * @param bool $candidate_
	 *
	 * @return string
	 */
	function get_field_group_stripped_slug( $field_group, $_fields = TRUE, $candidate_ = TRUE ) {

		if ( is_array( $field_group ) ) $field_group = $this->get_field_group_slug( $field_group );

		if ( $_fields ) $field_group = str_replace( '_fields', '', $field_group );
		if ( $candidate_ ) $field_group = str_replace( 'candidate_', '', $field_group );

		return $field_group;
	}

	/**
	 * Convert field group slug to post type
	 *
	 *
	 * @since 1.1.10
	 *
	 * @param $field_group
	 *
	 * @return bool|string
	 */
	function field_group_to_post_type( $field_group ){

		if( ! $field_group ) return;

		$job_listing = array( 'job', 'job_listing', 'company' );
		$resume = array( 'resume', 'resume_fields', 'education', 'experience', 'links' );

		if( in_array( $field_group, $job_listing ) ) return 'job_listing';
		if( in_array( $field_group, $resume ) ) return 'resume';


		return false;
	}

	/**
	 * Return Field Group Slug
	 *
	 * Will return the field group slug based on values passed in.  If there is
	 * a child and parent field group, it should be passed as an array.
	 *
	 * @since 1.1.9
	 *
	 * @param      $field_group
	 * @param bool $parent
	 * @param bool $parent_return_any
	 *
	 * @return array|mixed
	 */
	function get_field_group_slug( $field_group, $parent = FALSE, $parent_return_any = FALSE, $resume_fields = false ) {

		if ( ! is_array( $field_group ) && $parent && ! $parent_return_any ) return;
		if ( ! is_array( $field_group ) ) return $field_group;

		$parent_field_group = key( $field_group );
		$field_group        = $field_group[ $parent_field_group ];

		$this->is_child_group = TRUE;
		$this->child_group    = $field_group;

		if ( $parent ) return $parent_field_group;

		return $field_group;
	}

	/**
	 * Add meta_key to field array instead of key of the array
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 *
	 * @return mixed
	 */
	function add_meta_key_to_array( $fields ) {

		if ( empty( $fields ) ) return $fields;

		foreach ( $fields as $field => $field_config ) {

			if ( ! empty( $field_config[ 'fields' ] ) ) {

				foreach ( $field_config[ 'fields' ] as $child_field => $child_field_config ) {

					$fields[ $field ][ 'fields' ][ $child_field ][ 'meta_key' ] = $child_field;

				}
			}

			$fields[ $field ][ 'meta_key' ] = $field;
		}

		return $fields;
	}

	/**
	 * Gets custom post meta and update with any customizations
	 *
	 * @since 1.0.0
	 *
	 * @param array $default_fields
	 * @param array $custom_fields
	 *
	 * @return mixed
	 */
	function update_only_default_fields( $default_fields, $custom_fields ) {

		foreach ( $default_fields as $field => $field_config ) {

			if ( ! array_key_exists( $field, $custom_fields ) ) continue;

			$default_fields[ $field ] = array_replace_recursive( $default_fields[ $field ], $custom_fields[ $field ] );

		}

		return $default_fields;
	}

	/**
	 * Returns Count based on Key (Job, Company, Resume, etc)
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_group
	 *
	 * @return int|string|void
	 */
	function get_fields_count( $field_group, $filter ) {

		$fields = $this->get_fields( $field_group, $filter );

		return count( $fields );
	}

	/**
	 * Recursively merge and replace $default_fields with custom fields
	 *
	 * @since 1.0.0
	 *
	 * @param array $default_fields
	 *
	 * @return array Returns merged and replaced fields
	 */
	function merge_with_custom_fields( $default_fields, $field_group = null ) {

		$custom_fields  = $this->get_custom_fields( TRUE, $field_group );
		$updated_fields = array_replace_recursive( $default_fields, $custom_fields );

		return $updated_fields;
	}

	/**
	 * Removes all jmfe_custom_fields post types
	 *
	 * @since 1.0.0
	 */
	function remove_all_fields() {

		$args = array(
			'post_type'      => 'jmfe_custom_fields',
			'pagination'     => FALSE,
			'posts_per_page' => - 1
		);

		$custom_fields = new WP_Query( $args );

		if ( empty( $custom_fields ) ) return;

		foreach ( $custom_fields->posts as $field ) {
			if ( $field->ID ) $this->cpt()->remove_field_post( $field->ID );
		}
	}

	/**
	 * Dumps/Echo out array data with print_r or var_dump if xdebug installed
	 *
	 * Will check if xdebug is installed and if so will use standard var_dump,
	 * otherwise will use print_r inside <pre> tags to give formatted output.
	 *
	 * @since 1.1.9
	 *
	 * @param $field_data
	 */
	function dump_array( $field_data ) {

		if ( ! $field_data ) {
			_e( 'No field data found!', 'wp-job-manager-field-editor' );

			return;
		}

		require_once(WPJM_FIELD_EDITOR_PLUGIN_DIR."/includes/kint/Kint.class.php");
		Kint::enabled( TRUE );
		Kint::dump( $field_data );
		Kint::enabled( FALSE );
	}

	/**
	 * Sort array by priority value
	 */
	public static function sort_by_priority( $a, $b ) {

		return $a[ 'priority' ] - $b[ 'priority' ];
	}

	/**
	 * priority_cmp function.
	 *
	 * @access private
	 *
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @return void
	 */
	public static function priority_cmp( $a, $b ) {

		if ( $a[ 'priority' ] == $b[ 'priority' ] ) return 0;

		return ( $a[ 'priority' ] < $b[ 'priority' ] ) ? - 1 : 1;
	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return wp_job_manager_field_editor
	 */
	static function get_instance() {

		if ( NULL == self::$instance ) self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Magic Method to provide for get_{$var} the_{$var} and set_{$var}
	 *
	 * This allows to call any var by a function, with arguments, specified by the get, the, and set functions.
	 *
	 * Sort of a "catch all", if a function/method doesn't already exist this function will be called.
	 *
	 * As an example, if you call $instance->the_field_group() it will echo out the `field_group` variable,
	 * whereas get will return, set will set.
	 *
	 * @since 1.0.0
	 *
	 * @param $method_name
	 * @param $args
	 */
	public function __call( $method_name, $args ) {

		if ( preg_match( '/(?P<action>(get|set|the)+)_(?P<variable>\w+)/', $method_name, $matches ) ) {
			$variable = strtolower( $matches[ 'variable' ] );

			switch ( $matches[ 'action' ] ) {
				case 'set':
					$this->check_arguments( $args, 1, 1, $method_name );

					return $this->set( $variable, $args[ 0 ] );
				case 'get':
					$this->check_arguments( $args, 0, 0, $method_name );

					return $this->get( $variable );
				case 'the':
					$this->check_arguments( $args, 0, 0, $method_name );

					return $this->the( $variable );
				case 'default':
					error_log( 'Method ' . $method_name . ' not exists' );
			}
		}
	}

	/**
	 * Magic Method function used to check arguments
	 *
	 * @since 1.0.0
	 *
	 * @param array   $args
	 * @param integer $min
	 * @param integer $max
	 * @param         $method_name
	 */
	protected function check_arguments( array $args, $min, $max, $method_name ) {

		$argc = count( $args );
		if ( $argc < $min || $argc > $max ) {
			error_log( 'Method ' . $method_name . ' needs minimaly ' . $min . ' and maximaly ' . $max . ' arguments. ' . $argc . ' arguments given.' );
		}
	}

	/**
	 * Magic Method default set_{$var}, set
	 *
	 * @since 1.0.0
	 *
	 * @param string $variable
	 * @param        $value
	 *
	 * @return $this
	 */
	public function set( $variable, $value ) {

		$this->$variable = $value;

		return $this;
	}

	/**
	 * Magic Method default get_{$var}, return
	 *
	 * @since 1.0.0
	 *
	 * @param string $variable
	 *
	 * @return mixed Returns Variable
	 */
	public function get( $variable ) {

		return $this->$variable;
	}

	/**
	 * Magic Method default the_{$var}, echo
	 *
	 * @since 1.0.0
	 *
	 * @param string $variable
	 */
	public function the( $variable ) {

		echo $this->$variable;

	}

}

WP_Job_Manager_Field_Editor_Fields::get_instance();