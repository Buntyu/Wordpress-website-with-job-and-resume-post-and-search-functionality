<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Admin
 *
 * @since 1.1.9
 *
 * @method set_field_type( string $field_type )
 * @method set_post_type( string $post_type )
 * @method boolean get_return_list_body()
 *
 */
class WP_Job_Manager_Field_Editor_Admin extends WP_Job_Manager_Field_Editor_Fields {

	private static $instance;
	public $return_list_body;
	private $settings_page;
	protected $assets;
	protected $capabilities;
	protected $list_table;
	protected $submenu_pages = array();
	protected $field_pages = array();

	function __construct() {

		$this->init_capabilities();

		add_action( 'admin_init', array( $this, 'check_install' ) );
		add_action( 'admin_menu', array( $this, 'submenu' ) );
		add_filter( 'set-screen-option', array( $this, 'set_option' ), 10, 3 );

		include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin/ajax.php' );
		include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin/modal.php' );
		include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin/list-table.php' );
		include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin/settings.php' );

		$this->settings_page = new WP_Job_Manager_Field_Editor_Settings();
		$this->assets();

	}

	/**
	 * Get Assets Class Object
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return \WP_Job_Manager_Field_Editor_Admin_Assets
	 */
	function assets(){

		if( ! class_exists( 'WP_Job_Manager_Field_Editor_Admin_Assets' ) ) include( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin/assets.php' );
		$this->assets = new WP_Job_Manager_Field_Editor_Admin_Assets();
		return $this->assets;

	}

	/**
	 * Initialize Capabilities for Editing Fields
	 *
	 * Array keys should match post type
	 *
	 * @since 1.1.9
	 *
	 */
	function init_capabilities(){

		$this->capabilities = array();
		$this->capabilities[ 'job_listing' ] = 'manage_job_fields';
		$this->capabilities[ 'resume' ] = 'manage_resume_fields';

	}

	/**
	 * WordPress Submenus
	 *
	 * @since 1.0.0
	 */
	function submenu() {

		// Settings
		add_submenu_page(
			'edit.php?post_type=job_listing',
			__( 'Field Editor Settings', 'wp-job-manager-field-editor' ),
			__( 'Field Editor Settings', 'wp-job-manager-field-editor' ),
			$this->capabilities[ 'job_listing' ],
			'field-editor-settings',
			array( $this, 'settings' )
		);

		$this->init_pages();

		foreach( $this->field_pages as $group => $page ){

			foreach( $page as $slug => $label ){

				$this->submenu_pages[] = add_submenu_page(
					"edit.php?post_type={$group}", $label, $label, $this->capabilities[ $group ], $slug, array( $this, 'fields_list_table' )
				);

			}

		}

		if ( ! empty( $this->submenu_pages ) ) $this->submenu_actions();

	}

	/**
	 * Loop through each submenu and add load-$submenu action
	 *
	 * Adds actions that are loaded on plugin pages when loaded,
	 * format will be {$post_type}_page_{$page}
	 *
	 * @since 1.1.10
	 *
	 */
	function submenu_actions(){

		if( empty( $this->submenu_pages ) ) return false;

		foreach( $this->submenu_pages as $submenu ){

			add_action( "load-{$submenu}", array( $this, 'add_screen_options' ), 10 );

		}

	}

	/**
	 * Add Plugin Page Screen Option Per Page
	 *
	 *
	 * @since 1.1.10
	 *
	 */
	public function add_screen_options(){
		$table_title       = null;
		$this->page        = sanitize_text_field( $_REQUEST[ 'page' ] );
		$this->post_type   = sanitize_text_field( $_REQUEST[ 'post_type' ] );
		$this->page_array  = explode( '_', $this->page );
		$this->field_group = ( $this->page_array[1] == 'resume' ? 'resume_fields' : $this->page_array[1] );

		$args = array(
			'label'   => __('Fields Per Page', 'wp-job-manager-field-editor'),
			'default' => 10,
			'option'  => "{$this->field_group}_fields_per_page"
		);

		add_screen_option( 'per_page', $args );

		if( $this->post_type === 'job_listing' ){
			$job_singular = WP_Job_Manager_Field_Editor::get_job_post_label();
			$table_title = sprintf( __( '%1$s Field', 'wp-job-manager-field-editor' ), $job_singular );
		}

		// Initialize List Table after adding Screen Options
		$this->list_table = $this->list_table( $this->field_group, $this->post_type, $table_title );
	}

	/**
	 * Save screen option by returning value through filter
	 *
	 * WP by default does not save unless value is returned.  Not necessary to
	 * check if current page is plugin page, but it also doesn't hurt either.
	 *
	 * @see   /wp-admin/includes/misc.php#L425
	 *
	 * @since 1.1.10
	 *
	 * @param          $status Screen option value. Default false to skip.
	 * @param string   $option The option name.
	 * @param bool|int $value  The number of rows to use.
	 *
	 * @return bool|int        Returns either integer or false to prevent updating user meta
	 */
	public function set_option( $status, $option, $value){

		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST[ 'page' ] ) : '';

		if( ! $page ) return $status;

		$field_group = explode( '_', $page );

		if( strpos( $option, "{$field_group[1]}_fields") !== false ) return $value;

		return $status;
	}

	/**
	 * Initialize and Return Field Pages
	 *
	 *
	 * @since 1.1.10
	 *
	 * @return array
	 */
	function init_pages(){

		$this->wpjm_pages();
		if ( $this->wprm_active() ) $this->wprm_pages();

		return $this->field_pages;

	}

	/**
	 * Set resume field pages
	 *
	 *
	 * @since 1.1.10
	 *
	 * @return array
	 */
	function wprm_pages() {

		if ( $this->wprm_active() ) {
			$this->field_pages['resume'] = array(
				'edit_resume_fields' => __('Resume Fields', 'wp-job-manager-field-editor'),
//				'edit_links_fields' => __( 'Links Fields' ),
//				'edit_education_fields' => __( 'Education Fields' ),
//				'edit_experience_fields' => __( 'Experience Fields' )
			);

			return $this->field_pages[ 'resume' ];
		}

		return array();
	}

	/**
	 * Set job_listing field pages
	 *
	 *
	 * @since 1.1.10
	 *
	 * @return mixed
	 */
	function wpjm_pages(){

		$job_singular = WP_Job_Manager_Field_Editor::get_job_post_label();

		$this->field_pages[ 'job_listing' ] = array(
			'edit_job_fields'     => sprintf( __( '%1$s Fields', 'wp-job-manager-field-editor' ), $job_singular ),
			'edit_company_fields' => __( 'Company Fields', 'wp-job-manager-field-editor' )
		);

		return $this->field_pages[ 'job_listing' ];
	}

	/**
	 * Enqueue Assets and return Settings Page Output
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	function settings(){

		$this->assets()->enqueue_assets();
		$this->settings_page->output();

	}

	/**
	 * General Fields List Table Function
	 *
	 * Sets up fields, based on params, and returns or echos the list table HTML
	 *
	 * @since 1.1.0
	 *
	 *
	 * @return string Only returns if `return_list_body` is true
	 */
	function fields_list_table(){

		if ( ! $this->list_table ) $this->list_table = $this->list_table( $this->field_group, $this->post_type );

		// Ajax call, only return list table
		if ( $this->return_list_body ) return $this->list_table->do_list_table( true );

		// Check if user has meta for hidden columns
		$this->check_hidden_columns();

		// Enqueue Assets and Output List Table
		$this->assets()->enqueue_assets();
		$this->list_table->do_list_table();

	}

	/**
	 * Check if should include install file
	 *
	 * @since 1.1.9
	 *
	 */
	public function check_install() {

		$current_version = get_option( 'wp_job_manager_field_editor_version' );
		$plugin_activated = get_option( 'wp_job_manager_field_editor_activated' );

		if ( $plugin_activated || ! $current_version || version_compare( WPJM_FIELD_EDITOR_VERSION, $current_version, '>' ) ) {
			// Remove option if was set on plugin activation
			if( $plugin_activated ) delete_option( 'wp_job_manager_field_editor_activated' );
			// Include install class
			include_once( WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/install.php' );
		}

	}

	/**
	 * Check/Add hidden column to user meta/option
	 *
	 * WordPress uses core PHP functions in WP_List_Table that expects $hidden
	 * to be an array.  If the user does not have this meta saved (even if its empty)
	 * then PHP will throw an in_array warning.
	 *
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function check_hidden_columns(){

		$current_id           = get_current_user_id();
		$option_key           = "manage{$this->post_type}_page_{$this->page}columnshidden";
		$current_option_value = get_user_option( $option_key, $current_id );

		// Exit function to prevent existing values being saved over with default hidden columns
		if( ! empty( $current_option_value ) ) return false;

		$hidden = array('output', 'output_as', 'output_show_label', 'origin', 'post_id');
		update_user_option( $current_id, $option_key, $hidden, TRUE );

	}

	/**
	 * Set Default Hidden Columns User Option Meta
	 *
	 * Numerous columns are available but a few of them need to be hidden
	 * by default.  This function runs once on activate/install and updates
	 * the current user's meta with those columns set to hidden.
	 *
	 *
	 * @since    1.1.10
	 *
	 */
	public function set_hidden_columns() {

		$this->init_pages();

		foreach( $this->field_pages as $post_type => $pages ){

			foreach( $pages as $page => $label ){
				$hidden = array( 'output', 'output_as', 'output_show_label', 'origin', 'post_id' );
				$option_key = "manage{$post_type}_page_{$page}columnshidden";
				$current_option_value = get_user_option( $option_key );

				if( $current_option_value && ! empty( $current_option_value ) ){

					// Remove empty array values
					$current_option_value = array_filter( $current_option_value );

					// Merge and Remove any Duplicate Values
					$hidden = array_unique( array_merge( $current_option_value, $hidden ) );
				}

				update_user_option( get_current_user_id(), $option_key, $hidden, TRUE );
			}

		}


	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Job_Manager_Field_Editor_Admin
	 */
	static function get_instance() {

		if ( NULL == self::$instance ) self::$instance = new self;

		return self::$instance;
	}

}

WP_Job_Manager_Field_Editor_Admin::get_instance();