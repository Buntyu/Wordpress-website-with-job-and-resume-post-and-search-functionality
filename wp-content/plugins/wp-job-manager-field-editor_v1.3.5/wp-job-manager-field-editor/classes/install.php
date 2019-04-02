<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Job_Manager_Field_Editor_Install
 */
class WP_Job_Manager_Field_Editor_Install extends WP_Job_Manager_Field_Editor_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return \WP_Job_Manager_Field_Editor_Install
	 */
	public function __construct() {

		$this->init_user_roles();
//		$this->cpt()->purge_options();
		$this->set_hidden_columns();

		update_option( 'wp_job_manager_field_editor_version', WPJM_FIELD_EDITOR_VERSION );

		if ( ! get_option( 'jmfe_update_origin' ) ) {
			$this->update_origins();
			update_option( 'jmfe_update_origin', WPJM_FIELD_EDITOR_VERSION );
		}

		if ( ! get_option( 'jmfe_set_core_company_auto_output' ) ){
			$this->set_core_company_auto_output_values();
			update_option( 'jmfe_set_core_company_auto_output', WPJM_FIELD_EDITOR_VERSION );
		}

		delete_option( 'jmfe_enable_bug_reporter' );
	}


	/**
	 * Set any default configured fields auto output value to enabled
	 *
	 * This function is ran once on activation or upgrade of plugin, this is
	 * done to support the core enabled feature to allow you to disable these
	 * fields if you want from auto population.
	 *
	 * @since 1.1.14
	 *
	 * @return bool
	 */
	function set_core_company_auto_output_values() {

		$companyFields = $this->get_customized_fields( 'company' );

		if ( empty( $companyFields ) ) return FALSE;

		foreach ( $companyFields as $companyField => $companyConf ) {
			if ( ! isset( $companyConf[ 'origin' ] ) || $companyConf[ 'origin' ] != 'default' ) continue;
			if ( isset( $companyConf[ 'post_id' ] ) && ! empty( $companyConf[ 'post_id' ] ) ) {
				update_post_meta( $companyConf[ 'post_id' ], 'populate_enable', '1' );
				update_post_meta( $companyConf[ 'post_id' ], 'populate_save', '1' );
				update_post_meta( $companyConf[ 'post_id' ], 'populate_meta_key', '_' . $companyField );
			}
		}

	}

	/**
	 * Init user roles
	 *
	 * @access public
	 * @return void
	 */
	public function init_user_roles() {

		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		if ( is_object( $wp_roles ) ) {

			if ( empty( $this->capabilities ) ) $this->init_capabilities();

			foreach ( $this->capabilities as $type => $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}

		}
	}

	/**
	 * Loop through custom fields and update origin values
	 *
	 * Used to fix old bug in plugin that marked all fields as default.
	 *
	 *
	 * @since 1.1.9
	 *
	 */
	function update_origins() {

		$default_fields = $this->get_default_fields();

		if( ! $default_fields ) return false;

		$custom_fields  = $this->get_custom_fields( TRUE );

		foreach ( $custom_fields as $field_group => $fields ) {

			foreach ( $fields as $field => $config ) {

				if ( ! empty( $config[ 'origin' ] ) ) {

					if ( array_key_exists( $field, $default_fields[ $field_group ] ) ) {
						if ( isset( $config[ 'post_id' ] ) ) update_post_meta( $config[ 'post_id' ], 'origin', 'default' );
					}

				}

			}

		}

	}

}

new WP_Job_Manager_Field_Editor_Install();