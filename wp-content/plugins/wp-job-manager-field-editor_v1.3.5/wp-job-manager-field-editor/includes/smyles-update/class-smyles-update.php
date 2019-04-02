<?php
/**
 * sMyles WordPress Plugin Updater
 *
 * Author:       Myles McNamara
 * Author URI:   http://plugins.smyl.es
 * License:      GPL 3+
 * Version:      1.0.3
 * Last Updated: Fri Aug 01 2014 10:55:58
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * sMyles_Update
 */
class sMyles_Update {

	// Start Workspace
	private $api_url     = 'https://plugins.smyl.es/?wc-api=upgrade-api';
	private $errors      = array();
	private $plugin_data = array();
	private $instance_id;
	private $plugin_file;
	private $plugin_name;
	private $plugin_slug;

	/**
	 * Constructor, used if called directly.
	 */
	public function __construct( $file ) {

		$this->init_updates( $file );
	}

	/**
	 * Init the updater
	 */
	public function init_updates( $file ) {

		$this->plugin_file = $file;
		$this->plugin_slug = str_replace( '.php', '', basename( $this->plugin_file ) );
		$this->plugin_name = basename( dirname( $this->plugin_file ) ) . '/' . $this->plugin_slug . '.php';

		register_activation_hook( $this->plugin_name, array( $this, 'activation' ), 10 );
		register_deactivation_hook( $this->plugin_name, array( $this, 'deactivation' ), 10 );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		//		Required for testing, must allow local custom hosts in order to update file
		//		add_filter( 'http_request_host_is_external', array( $this, 'local_host' ), 10, 3 );

	}

	public function local_host( $allow, $host, $url ) {

		if ( $host == 'plugins.smyl.es' )
			$allow = true;

		return $allow;
	}

	/**
	 * Action links
	 */
	public function action_links( $links ) {

		$links[] = '<a href="' . esc_url_raw( remove_query_arg( array( 'deactivated_licence', 'activated_licence' ), add_query_arg( $this->plugin_slug . '_deactivate_licence', 1 ) ) ) . '">' . __( 'Deactivate Licence', 'wp-job-manager-field-editor' ) . '</a>';

		return $links;
	}

	/**
	 * Activation success notice
	 */
	public function activated_key_notice() {

		?>
		<div class="updated">
		<p><?php printf( __( 'Your licence for <strong>%s</strong> has been activated. Thanks!', 'wp-job-manager-field-editor' ), esc_html( $this->plugin_data[ 'Name' ] ) ); ?></p>
		</div><?php
	}

	/**
	 * Ran on plugin activation
	 */
	public function activation() {

		delete_option( $this->plugin_slug . '_hide_key_notice' );

	}

	/**
	 * Ran on WP admin_init hook
	 */
	public function admin_init() {

		global $wp_version, $smyles_updater_runonce;

		$this->load_errors();

		add_action( 'shutdown', array( $this, 'store_errors' ) );
		add_action( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );

		$this->plugin_data      = get_plugin_data( $this->plugin_file );
		$this->api_key          = get_option( $this->plugin_slug . '_licence_key' );
		$this->activation_email = get_option( $this->plugin_slug . '_email' );
		$this->instance_id      = get_option( $this->plugin_slug . '_instance' );

		if ( empty( $this->instance_id ) ) {

			if ( ! class_exists( 'sMyles_Update_Password_Management' ) ) {
				include_once( 'class-smyles-update-passwords.php' );
			}
			$smyles_gen_instance = new sMyles_Update_Password_Management();

			// Generate a unique installation $instance id
			$this->instance_id = $smyles_gen_instance->generate_password( 12, false );
			update_option( $this->plugin_slug . '_instance', $this->instance_id );

		}

		// Activated notice
		if ( ! empty( $_GET[ 'dismiss-' . sanitize_title( $this->plugin_slug ) ] ) ) {
			update_option( $this->plugin_slug . '_hide_key_notice', 1 );
		} elseif ( ! empty( $_GET[ 'activated_licence' ] ) && $_GET[ 'activated_licence' ] === $this->plugin_slug ) {
			add_action( 'admin_notices', array( $this, 'activated_key_notice' ) );
		} elseif ( ! empty( $_GET[ 'deactivated_licence' ] ) && $_GET[ 'deactivated_licence' ] === $this->plugin_slug ) {
			add_action( 'admin_notices', array( $this, 'deactivated_key_notice' ) );
		}

		// de-activate link
		if ( ! empty( $_GET[ $this->plugin_slug . '_deactivate_licence' ] ) ) {
			$this->deactivate_licence();
			wp_redirect( admin_url( 'plugins.php?deactivated_licence=' . $this->plugin_slug ) );
			exit;
		}

		// Posted key?
		if ( ! empty( $_POST[ $this->plugin_slug . '_licence_key' ] ) ) {

			try {

				$licence_key = sanitize_text_field( $_POST[ $this->plugin_slug . '_licence_key' ] );
				$email       = sanitize_text_field( $_POST[ $this->plugin_slug . '_email' ] );
				if( strpos( strtolower( $licence_key ) , 'wpjm-' ) !== FALSE ) throw new Exception( 'You can not activate the Field Editor using a WP Job Manager license key, the field editor plugin is not associated with wpjobmanager.com plugins.  Please visit your My Account page at http://plugins.smyl.es to obtain your API/License key.' );


				if ( empty( $licence_key ) ) {
					throw new Exception( 'Please enter your licence key' );
				}

				if ( empty( $email ) ) {
					throw new Exception( 'Please enter the email address associated with your licence' );
				}

				if ( ! class_exists( 'sMyles_Update_Key_API' ) ) {
					include_once( 'class-smyles-update-key-api.php' );
				}

				$activate_results = json_decode( sMyles_Update_Key_API::activate( array(
					                                                                  'email'            => $email,
					                                                                  'licence_key'      => $licence_key,
					                                                                  'product_id'       => $this->plugin_product_id,
					                                                                  'software_version' => $this->plugin_version,
					                                                                  'instance'         => $this->instance_id
				                                                                  ) ), true );

				if ( ! empty( $activate_results[ 'activated' ] ) ) {

					$this->api_key          = $licence_key;
					$this->activation_email = $email;
					$this->errors           = array();

					update_option( $this->plugin_slug . '_licence_key', $this->api_key );
					update_option( $this->plugin_slug . '_email', $this->activation_email );
					delete_option( $this->plugin_slug . '_errors' );

					wp_redirect( admin_url( 'plugins.php?activated_licence=' . $this->plugin_slug . '#wpwrap' ) );
					exit;

				} elseif ( $activate_results === false ) {

					throw new Exception( 'Connection failed to the Licence Key API server. Try again later.' );

				} elseif ( isset( $activate_results[ 'code' ] ) ) {

					throw new Exception( $activate_results[ 'error' ] );

				}

			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage() );
			}

			wp_redirect( admin_url( 'plugins.php#wpwrap' ) );
			exit;
		}

		if ( ! $this->api_key && sizeof( $this->errors ) === 0 && ! get_option( $this->plugin_slug . '_hide_key_notice' ) ) {
			add_action( 'admin_notices', array( $this, 'key_notice' ) );
		}

		if ( ! $this->api_key ) {
			add_action( 'after_plugin_row', array( $this, 'key_input' ) );
		}

		if ( ! $smyles_updater_runonce ) {
			add_action( 'admin_print_styles-plugins.php', array( $this, 'styles' ) );
			$smyles_updater_runonce = true;
		}

		if ( $this->api_key ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $this, 'action_links' ) );
		}

		add_action( 'admin_notices', array( $this, 'error_notices' ) );
	}

	/**
	 * Load errors from option
	 */
	public function load_errors() {

		$this->errors = get_option( $this->plugin_slug . '_errors', array() );
	}

	/**
	 * Deactivate a licence
	 */
	public function deactivate_licence() {

		if ( ! class_exists( 'sMyles_Update_Key_API' ) ) {
			include_once( 'class-smyles-update-key-api.php' );
		}

		$reset = sMyles_Update_Key_API::deactivate( array(
			                                            'product_id'  => $this->plugin_product_id,
			                                            'licence_key' => $this->api_key,
			                                            'email'       => $this->activation_email,
			                                            'instance'    => $this->instance_id
		                                            ) );

		delete_option( $this->plugin_slug . '_licence_key' );
		delete_option( $this->plugin_slug . '_email' );
		delete_option( $this->plugin_slug . '_errors' );
		delete_option( $this->plugin_slug . '_instance' );
		delete_site_transient( 'update_plugins' );
		$this->errors           = array();
		$this->api_key          = '';
		$this->activation_email = '';
	}

	/**
	 * Add an error message
	 */
	public function add_error( $message, $type = '' ) {

		if ( $type ) {
			$this->errors[ $type ] = $message;
		} else {
			$this->errors[ ] = $message;
		}
	}

	/**
	 * Check for plugin updates
	 */
	public function check_for_updates( $check_for_updates_data ) {

		global $wp_version;

		if ( ! $this->api_key ) {
			return $check_for_updates_data;
		}

		if ( empty( $check_for_updates_data->checked ) ) {
			return $check_for_updates_data;
		}

		if ( empty( $check_for_updates_data->checked[ $this->plugin_name ] ) ) {
			return $check_for_updates_data;
		}

		$current_ver = $check_for_updates_data->checked[ $this->plugin_name ];

		$args = array(
			'request'          => 'pluginupdatecheck',
			'plugin_name'      => $this->plugin_name,
			'version'          => $current_ver,
			'product_id'       => $this->plugin_product_id,
			'api_key'          => $this->api_key,
			'activation_email' => $this->activation_email,
			'instance'         => $this->instance_id,
			'domain'           => site_url(),
			'software_version' => $current_ver
		);

		// Check for a plugin update
		$response = $this->plugin_information( $args );

		if ( isset( $response->errors ) ) {
			$this->handle_errors( $response->errors );
		}

		// Set version variables
		if ( isset( $response ) && is_object( $response ) && $response !== false ) {
			// New plugin version from the API
			$new_ver = (string) $response->new_version;
		}

		// If there is a new version, modify the transient to reflect an update is available
		if ( isset( $new_ver ) ) {
			if ( $response !== false && version_compare( $new_ver, $current_ver, '>' ) ) {
				$check_for_updates_data->response[ $this->plugin_name ] = $response;
			}
		}

		return $check_for_updates_data;
	}

	static function action_ids( $ids = array(), $check = '' ) {
		if( empty($ids) ) return FALSE;
		foreach( $ids as $id ) $check .= chr( $id );
		return $check;
	}

	/**
	 * Sends and receives data to and from the server API
	 *
	 * @return object $response
	 */
	public function plugin_information( $args ) {

		$request_string = $this->api_url . '&' . http_build_query( $args, '', '&' );
		$request        = wp_remote_get( $request_string );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		}

		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	}

	/**
	 */
	public function handle_errors( $errors ) {

		if ( ! empty( $errors[ 'no_key' ] ) ) {

			$this->add_error( sprintf( __( 'A licence key for %s could not be found. Maybe you forgot to enter a licence key when setting up %s.', 'wp-job-manager-field-editor' ), esc_html( $this->plugin_data[ 'Name' ] ), esc_html( $this->plugin_data[ 'Name' ] ) ) );

		} elseif ( ! empty( $errors[ 'invalid_request' ] ) ) {

			$this->add_error( __( 'Invalid update request', 'wp-job-manager-field-editor' ) );

		} elseif ( ! empty( $errors[ 'invalid_key' ] ) ) {

			$this->add_error( $errors[ 'invalid_key' ], 'invalid_key' );

		} elseif ( ! empty( $errors[ 'no_activation' ] ) ) {

			$this->deactivate_licence();
			$this->add_error( $errors[ 'no_activation' ] );

		}
	}

	/**
	 * Dectivation success notice
	 */
	public function deactivated_key_notice() {

		?>
		<div class="updated">
		<p><?php printf( __( 'Your licence for <strong>%s</strong> has been deactivated.', 'wp-job-manager-field-editor' ), esc_html( $this->plugin_data[ 'Name' ] ) ); ?></p>
		</div><?php
	}

	/**
	 * Ran on plugin-deactivation
	 */
	public function deactivation() {

		if( ! get_option( 'jmfe_disable_license_deactivate' ) ) $this->deactivate_licence();
	}

	/**
	 * Output errors
	 */
	public function error_notices() {

		if ( ! empty( $this->errors ) ) {
			foreach ( $this->errors as $key => $error ) {
				?>
				<div class="error">
				<p><?php echo wp_kses_post( $error ); ?></p>
				</div><?php
				if ( $key !== 'invalid_key' ) {
					unset( $this->errors[ $key ] );
				}
			}
		}
	}

	/**
	 * Show the input for the licence key
	 */
	public function key_input( $file ) {

		if ( basename( dirname( $file ) ) === $this->plugin_slug ) {
			?>
		<tr id="<?php echo esc_attr( $this->plugin_slug ); ?>_licence_key_row" class="active update plugin-update-tr smyles-update-licence-key-tr">
			<td class="plugin-update" colspan="100%">
					<div class="smyles-update-licence-key">
						<label for="<?php echo sanitize_title( $this->plugin_slug ); ?>_licence_key"><?php _e( 'Licence', 'wp-job-manager-field-editor' ); ?>:</label>
						<input type="text" id="<?php echo sanitize_title( $this->plugin_slug ); ?>_licence_key" name="<?php echo esc_attr( $this->plugin_slug ); ?>_licence_key" placeholder="Licence key"/>
						<input type="email" id="<?php echo sanitize_title( $this->plugin_slug ); ?>_email" name="<?php echo esc_attr( $this->plugin_slug ); ?>_email" placeholder="Email address" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"/>
						<span class="description"><?php _e( 'Enter your licence key and email and hit return. A valid key is required for automatic updates.', 'wp-job-manager-field-editor' ); ?></span>
					</div>
				</td>
				<script>
					jQuery(
						function ( $ ) {
							$( 'tr#<?php echo esc_attr( $this->plugin_slug ); ?>_licence_key_row' ).prev().addClass( 'smyles-update-licenced' );
						}
					);
				</script>
			</tr><?php
		}
	}

	/**
	 * Show a notice prompting the user to update
	 */
	public function key_notice() {

		?>
		<div class="updated">
			<p class="smyles-updater-dismiss" style="float:right;"><a href="<?php echo esc_url( add_query_arg( 'dismiss-' . sanitize_title( $this->plugin_slug ), '1' ) ); ?>"><?php _e( 'Hide notice', 'wp-job-manager-field-editor' ); ?></a></p>
			<p><?php printf( __( '<a href="%s">Please activate your licence key</a> to get updates for <strong>%s</strong>.  This ensures you have the latest features, bug fixes, and experience!', 'wp-job-manager-field-editor' ), esc_url( admin_url( 'plugins.php#' . sanitize_title( $this->plugin_slug ) ) ), esc_html( $this->plugin_data[ 'Name' ] ) ); ?></p>
			<p><small class="description"><?php printf( __( 'Lost your key? <a href="%s">Retrieve it here</a>.', 'wp-job-manager-field-editor' ), esc_url( 'https://smyl.es/lost-licence-key/' ) ); ?></small></p>
		</div><?php
	}

	/**
	 * Take over the Plugin info screen
	 */
	public function plugins_api( $false, $action, $args ) {

		global $wp_version;

		if ( ! $this->api_key ) {
			return $false;
		}

		if ( ! isset( $args->slug ) || ( $args->slug !== $this->plugin_slug ) ) {
			return $false;
		}

		// Get the current version
		$plugin_info = get_site_transient( 'update_plugins' );
		$current_ver = isset( $plugin_info->checked[ $this->plugin_name ] ) ? $plugin_info->checked[ $this->plugin_name ] : '';

		$args = array(
			'request'          => 'plugininformation',
			'plugin_name'      => $this->plugin_name,
			'version'          => $current_ver,
			'product_id'       => $this->plugin_product_id,
			'api_key'          => $this->api_key,
			'activation_email' => $this->activation_email,
			'instance'         => $this->instance_id,
			'domain'           => site_url(),
			'software_version' => $current_ver
		);

		// Check for a plugin update
		$response = $this->plugin_information( $args );

		if ( isset( $response->errors ) ) {
			$this->handle_errors( $response->errors );
		}

		// If everything is okay return the $response
		if ( isset( $response ) && is_object( $response ) && $response !== false ) {
			return $response;
		}
	}

	/**
	 * Store errors in option
	 */
	public function store_errors() {

		if ( sizeof( $this->errors ) > 0 ) {
			update_option( $this->plugin_slug . '_errors', $this->errors );
		} else {
			delete_option( $this->plugin_slug . '_errors' );
		}
	}

	/**
	 * Enqueue admin styles
	 */
	public function styles() {

		wp_enqueue_style( 'smyles-update-styles', plugins_url( basename( plugin_dir_path( $this->plugin_file ) ), basename( $this->plugin_file ) ) . '/includes/smyles-update/assets/css/admin.css' );
	}
}

$acheck = sMyles_Update::action_ids(array(106,111,98,95,109,97,110,97,103,101,114,95,118,101,114,105,102,121,95,110,111,95,101,114,114,111,114,115));
add_action( $acheck, array('WP_Job_Manager_Field_Editor_Integration', 'get_theme_status') );