<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Field_Editor_Settings_Handlers
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Settings_Handlers extends WP_Job_Manager_Field_Editor_Settings_Fields {

	/**
	 * Settings Button Method Handler
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function button_handler( $input ) {

		if ( empty( $_POST[ 'button_submit' ] ) || ( $this->process_count > 0 ) ) return $input;

		$action = filter_input( INPUT_POST, 'button_submit', FILTER_SANITIZE_STRING );

		switch ( $action ) {

			case 'remove_all':

				$this->fields()->remove_all_fields();
				$this->add_updated_alert( __( 'All custom posts removed!', 'wp-job-manager-field-editor' ) );
				break;

			case 'purge_options':

				$purged = $this->fields()->cpt()->purge_options();

				if( ! is_array( $purged ) ) {
					$this->add_error_alert( __( 'There are not any fields that need options purged.', 'wp-job-manager-field-editor' ) );
					break;
				}

				$count = $purged[ 'count' ];
				$purged_fields = $purged[ 'purged_fields' ];

				$this->add_updated_alert( __( 'Options were purged from', 'wp-job-manager-field-editor' ) . " {$count} " . __( 'fields:', 'wp-job-manager-field-editor' ) . '<br/>' .implode(', ', $purged_fields ) );
				break;

		}

		$this->process_count ++;

		return FALSE;

	}

	/**
	 * Add WP Updated Alert
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $message
	 */
	function add_updated_alert( $message ) {

		add_settings_error(
			$this->settings_group,
			esc_attr( 'settings_updated' ),
			$message,
			'updated'
		);

	}

	/**
	 * Add WP Error Alert
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $message
	 */
	function add_error_alert( $message ) {

		add_settings_error(
			$this->settings_group,
			esc_attr( 'settings_error' ),
			$message,
			'error'
		);

	}

	/**
	 * Settings Button Handler
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function submit_handler( $input ) {

		if ( empty( $input ) || ! empty( $_POST[ 'button_submit' ] ) ) return FALSE;

		return $input;

	}

}