<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_CPT
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_CPT {

	private static $instance;

	function __construct() {

		add_action( 'init', array( $this, 'custom_post_setup' ), 0 );

	}

	/**
	 * Purge Options from Fields
	 *
	 * Purge/Remove option values from fields that do not
	 * use or need options (fixes old plugin bug)
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return array|int
	 */
	function purge_options(){

		$count = 0;
		$purged_fields = array();
		$option_types = array( 'select', 'file', 'radio' );
		$custom_fields = $this->fields()->get_custom_fields( true );

		// Return false if there are no custom fields
		if( empty( $custom_fields ) ) return false;

		// Loop through each field group (Job, Company, Resume, etc)
		foreach( $custom_fields as $type => $fields ){

			// Loop through each field
			foreach( $fields as $field => $config ){
				// Skip field types that use options
				if( in_array( $config[ 'type' ], $option_types ) ) continue;
				// If missing post_id or field doesn't have options continue to next in loop
				if( empty( $config[ 'post_id' ] ) || ! isset( $config[ 'options' ] ) ) continue;

				delete_post_meta( $config[ 'post_id' ], 'options' );
				$purged_fields[] = $field;
				$count++;
			}

		}

		if( empty( $purged_fields ) ) return 0;

		return array( 'count' => $count, 'purged_fields' => $purged_fields );

	}

	/**
	 * Remove WordPress Post
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id
	 *
	 * @return boolean
	 */
	function remove_field_post( $post_id ) {

		if ( ! $post_id ) return false;

		if ( ! wp_delete_post( $post_id, true ) ) return false;

		return true;

	}

	/**
	 * Adds new post and calls update_field_post_meta on success
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_meta_key
	 *
	 * @return bool|int|\WP_Error
	 */
	function insert_field_post( $post_meta_key ) {

		$slashed_meta_key = addslashes( $post_meta_key );

		$post_exists = get_page_by_title( $slashed_meta_key, OBJECT, 'jmfe_custom_fields' );

		if( empty( $post_exists ) ){

			$insert_post = array(
				'post_type'   => 'jmfe_custom_fields',
				'post_title'  => addslashes( $slashed_meta_key ),
				'post_status' => 'publish'
			);

			$post_id = wp_insert_post( $insert_post );
			update_post_meta( $post_id, 'status', 'enabled' );

			if ( $post_id == 0 ) return false;
		}

		if( isset( $post_exists->ID ) ) $post_id = $post_exists->ID;

		if( ! empty( $post_id ) ) $this->update_field_post_meta( $post_id );

		return $post_id;
	}

	/**
	 * Update post meta from $_POST fields
	 *
	 * @since @@since
	 *
	 * @param integer $post_id
	 */
	function update_field_post_meta( $post_id ) {

		// action, nonce, filter, options(array), paged, post_id
		$skip_update = apply_filters( 'field_editor_update_post_meta_skip_update', array('action', 'nonce', 'filter', 'options', 'packages_show', 'paged', 'post_id', 'modal_action') );
		$skip_purge = apply_filters( 'field_editor_update_post_meta_skip_purge', array( 'status' ) );
		$checkboxes = apply_filters( 'field_editor_update_post_meta_checkboxes', array( 'admin_only', 'multiple', 'ajax', 'required', 'output_show_label', 'populate_enable', 'populate_save', 'option_default', 'option_disabled', 'packages_require') );
		$meta_checks = apply_filters( 'field_editor_update_post_meta_checks', array( 'taxonomy' ) );

		$meta_key = filter_input( INPUT_POST, 'meta_key', FILTER_SANITIZE_STRING );
		$field_type = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );
		$action = filter_input( INPUT_POST, 'modal_action', FILTER_SANITIZE_STRING );
		$field_group = filter_input( INPUT_POST, 'field_group', FILTER_SANITIZE_STRING );

		// Update Checkbox post meta values (should be before checking post meta)
		foreach( $checkboxes as $checkbox ) {
			$checkbox_value = isset($_POST[ $checkbox ]) ? '1' : '0';
			update_post_meta( $post_id, $checkbox, $checkbox_value );
		}

		$existing_post_meta = get_post_meta( $post_id );
		$meta_check_diff = array_diff( array_keys( $existing_post_meta ), array_keys( $_POST ) );

		// TODO: handle removal of existing post meta if not set in $_POST

		//foreach( $meta_check_diff as $meta_check_old ){
		//	// Do not remove any checkbox metas if not in $_POST
		//	if( in_array( $meta_check_old, $checkboxes ) ) continue;
		//	// Or any other specified meta keys
		//	if( in_array( $meta_check_old, $skip_purge ) ) continue;
		//
		//}

		// Update all other POST values, skip any in $skip_update or $checkboxes
		foreach( $_POST as $key => $value ){
			if( in_array($key, $skip_update) || in_array($key, $checkboxes) ) continue;
			update_post_meta( $post_id, $key, $_POST[$key]);
		}

		// If action was to enable/disable field, exit function to prevent processing options
		// or anything else below this point.
		if( $action === 'enable' || $action === 'disable' ) return;

		// Handle Options array
		$post_options = isset( $_POST[ 'options' ] ) ? $_POST['options'] : array();
		// If options are removed there will be no POST values
		if ( $action === "edit" && empty( $post_options ) ) delete_post_meta( $post_id, 'options' );
		if ( ! empty( $post_options ) ) {
			// Convert options Ajax array to array expected by rest of plugin
			$options_converted = $this->fields()->options()->unserialize( $post_options );
			// Check if options are for any extra field types (file, etc)
			$additional_meta_key = $this->fields()->options()->other_meta_key_check( sanitize_text_field( $_POST[ 'type' ] ) );
			if ( $additional_meta_key ) update_post_meta( $post_id, $additional_meta_key, $options_converted );

			update_post_meta( $post_id, 'options', $options_converted );
		}

		// Handle Packages Show array
		$packages_show = isset( $_POST[ 'packages_show' ] ) ? $_POST[ 'packages_show' ] : array();
		// If no packages are select remove post meta
		if ( $action === "edit" && empty( $packages_show ) ) delete_post_meta( $post_id, 'packages_show' );
		if ( ! empty( $packages_show ) && isset( $packages_show[0] ) ){
			foreach( $packages_show[0] as $product_id ){
				$enabled_packages[] = $product_id;
			}
			update_post_meta( $post_id, 'packages_show', $enabled_packages );
		}

		// Hack to prevent issues with custom options being saved for resume_category or job_type (job_type only for WPJM <= 1.13)
		if ( $meta_key == 'resume_category' || $meta_key == 'job_type' ) delete_post_meta( $post_id, 'options' );
		// Prevent options from being saved for products meta key under company fields (for WP Job Manager Products)
		if ( class_exists( 'WPJMP_Products' ) && $meta_key == 'products' && $field_group == 'company') delete_post_meta( $post_id, 'options' );

		// Handle updating job_manager_tag_input option when field is edited to match field type
		if( $meta_key == 'job_tags' ){
			$tag_type = $field_type === 'term-multiselect' ? 'multiselect' : '';
			if( $field_type === 'term-checklist' ) $tag_type = 'checkboxes';
			update_option( 'job_manager_tag_input', $tag_type );
		}

		// Hack to remove taxonomy post meta if was saved to post previously but field type was changed to something else
		// this should be removed once removal of existing post meta handling is integrated/finished
		if( isset($existing_post_meta['taxonomy']) && ! empty($existing_post_meta['taxonomy']) ){
			if( isset($_POST['type']) && strpos( $_POST['type'], 'term-' ) === FALSE ) delete_post_meta( $post_id, 'taxonomy' );
		}

	}

	/**
	 * Return Fields Class Object
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return \wp_job_manager_field_editor
	 */
	function fields(){

		return WP_Job_Manager_Field_Editor_Fields::get_instance();

	}

	/**
	 * Set Custom Post Status (Enable/Disable)
	 *
	 * @since 1.0.0
	 */
	function custom_post_setup() {

		if ( post_type_exists( "jmfe_custom_fields" ) ) return;

		$admin_capability = 'manage_job_fields';

		register_post_type( 'jmfe_custom_fields', array(
			'labels'              => array(
				'name'          => __( 'WPJM Custom Fields', 'wp-job-manager-field-editor' ),
				'singular_name' => __( 'WPJM Custom Field', 'wp-job-manager-field-editor' )
			),
			'public'              => FALSE,
			'exclude_from_search' => TRUE,
			'publicly_queryable'  => FALSE,
			'can_export'          => TRUE,
			'capability_type' => 'post',
			'capabilities'    => array(
				'publish_posts'       => $admin_capability,
				'edit_posts'          => $admin_capability,
				'edit_others_posts'   => $admin_capability,
				'delete_posts'        => $admin_capability,
				'delete_others_posts' => $admin_capability,
				'read_private_posts'  => $admin_capability,
				'edit_post'           => $admin_capability,
				'delete_post'         => $admin_capability,
				'read_post'           => $admin_capability
			),
		) );

		$disabled_args = array(
			'label'                     => _x( 'disabled', 'Disabled Field Status', 'wp-job-manager-field-editor' ),
			'label_count'               => _n_noop( 'Disabled (%s)', 'Disabled (%s)', 'wp-job-manager-field-editor' ),
			'public'                    => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
			'exclude_from_search'       => true,
		);

		$enabled_args = array(
			'label'                     => _x( 'enabled', 'Enabled Field Status', 'wp-job-manager-field-editor' ),
			'label_count'               => _n_noop( 'Enabled (%s)', 'Enabled (%s)', 'wp-job-manager-field-editor' ),
			'public'                    => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
			'exclude_from_search'       => true,
		);

		register_post_status( 'disabled', $disabled_args );
		register_post_status( 'enabled', $enabled_args );
	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.1.9
	 *
	 * @return WP_Job_Manager_Field_Editor_CPT
	 */
	static function get_instance() {
		if ( null == self::$instance ) self::$instance = new self;
		return self::$instance;
	}
}