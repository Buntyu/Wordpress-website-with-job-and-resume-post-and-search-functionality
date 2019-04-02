<?php
/**
 * sMyles WordPress Plugin Bug Reporter
 *
 * Author:       Myles McNamara
 * Author URI:   http://plugins.smyl.es
 * License:      GPL 3+
 * Version:      1.0.3
 * Last Updated: Thu Jul 31 2014 18:29:30
 */

if ( ! class_exists( 'sMyles_Bug_Report' ) ) {

	class sMyles_Bug_Report {

		private $version = '1.0.3';
		private $force_debug;
		private $plugin_prod_id;
		private $plugin_version;
		protected static $instance;

		function __construct() {

			$this->plugin_product_id = WPJM_FIELD_EDITOR_PROD_ID;
			$this->plugin_version = WPJM_FIELD_EDITOR_VERSION;

			if ( ! defined( 'SMYLES_BUG_REPORT_DIR' ) ) define( 'SMYLES_BUG_REPORT_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			if ( ! defined( 'SMYLES_BUG_REPORT_URL' ) ) define( 'SMYLES_BUG_REPORT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

			add_action( 'wp_ajax_smyles_submit_bug', array( $this, 'submit_bug' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			//add_action( 'contextual_help', array( $this, 'screen_help' ), 10, 3 );

			$this->force_debug = get_option( 'smyles_bug_report_force_debug' );
			$this->check_debug_toggle();

			if( $this->force_debug ) $this->set_debug();
		}


		/**
		 * Output debugging information in contextual help area
		 *
		 * Original code courtesy of tutsplus:
		 * http://code.tutsplus.com/articles/quick-tip-get-the-current-screens-hooks--wp-26891
		 *
		 *
		 * @since 1.1.2
		 *
		 * @param $contextual_help
		 * @param $screen_id
		 * @param $screen
		 *
		 * @return mixed
		 */
		function screen_help( $contextual_help, $screen_id, $screen ) {

			// The add_help_tab function for screen was introduced in WordPress 3.3.
			if ( ! method_exists( $screen, 'add_help_tab' ) ) {
				return $contextual_help;
			}

			global $hook_suffix;

			// List screen properties
			$variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
			             . sprintf( '<li> Screen id : %s</li>', $screen_id )
			             . sprintf( '<li> Screen base : %s</li>', $screen->base )
			             . sprintf( '<li>Parent base : %s</li>', $screen->parent_base )
			             . sprintf( '<li> Parent file : %s</li>', $screen->parent_file )
			             . sprintf( '<li> Hook suffix : %s</li>', $hook_suffix )
			             . '</ul>';

			// Append global $hook_suffix to the hook stems
			$hooks = array(
				"load-$hook_suffix",
				"admin_print_styles-$hook_suffix",
				"admin_print_scripts-$hook_suffix",
				"admin_head-$hook_suffix",
				"admin_footer-$hook_suffix"
			);

			// If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
			if ( did_action( 'add_meta_boxes_' . $screen_id ) ) {
				$hooks[ ] = 'add_meta_boxes_' . $screen_id;
			}

			if ( did_action( 'add_meta_boxes' ) ) {
				$hooks[ ] = 'add_meta_boxes';
			}

			// Get List HTML for the hooks
			$hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode( '</li><li>', $hooks ) . '</li></ul>';

			// Combine $variables list with $hooks list.
			$help_content = $variables . $hooks;

			// Add help panel
			$screen->add_help_tab( array(
				                       'id'      => 'wptuts-screen-help',
				                       'title'   => 'Screen Information',
				                       'content' => $help_content,
			                       ) );

			return $contextual_help;
		}

		public function debug_disabled_notice() {

			?>
			<div class="updated">
			<p><?php _e( 'Debug logging has been disabled.', 'wp-job-manager-field-editor' ); ?></p>
			</div><?php
		}

		public function debug_enabled_notice() {

			?>
			<div class="updated">
			<p><?php _e( 'Debug logging has been enabled.', 'wp-job-manager-field-editor' ); ?></p>
			</div><?php
		}

		function check_debug_toggle(){

			if( isset($_GET[ 'smyles-debug-toggle' ]) ){

				if( $_GET['smyles-debug-toggle'] == 'enable' ){
					$this->enable_debug();
				} elseif ( $_GET[ 'smyles-debug-toggle' ] == 'disable' ){
					$this->disable_debug();
				}

			}

		}

		function get_version() {

			return $this->version;
		}

		function enable_debug(){
			$this->force_debug = true;
			add_action( 'admin_notices', array( $this, 'debug_enabled_notice' ) );
			update_option( 'smyles_bug_report_force_debug', true );
		}

		function disable_debug(){
			$this->force_debug = false;
			add_action( 'admin_notices', array( $this, 'debug_disabled_notice' ) );
			delete_option( 'smyles_bug_report_force_debug' );
		}

		public function set_debug() {

			error_reporting( E_ALL );
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );

		}

		function check_debug( $only_wp = false ){

			if( defined('WP_DEBUG') && defined('WP_DEBUG_LOG') ) if( WP_DEBUG && WP_DEBUG_LOG ) return true;
			if( $only_wp ) return false;

			if( $this->force_debug ) return true;

			return false;

		}

		public function admin_assets() {

			wp_enqueue_style( 'smyles-bug-report', SMYLES_BUG_REPORT_URL . '/assets/css/core.min.css' );
			wp_enqueue_style( 'smyles-bug-report-vendor', SMYLES_BUG_REPORT_URL . '/assets/css/vendor.min.css' );
			wp_enqueue_script( 'smyles-bug-report-vendor', SMYLES_BUG_REPORT_URL . '/assets/js/vendor.min.js', array( 'jquery' ), $this->version, true );

			wp_register_script( 'smyles-bug-report', SMYLES_BUG_REPORT_URL . '/assets/js/core.min.js', array( 'jquery' ), $this->version, true );

			// JS Translation Vars
			$translation_array = array(
				'submit_success' => __( 'Bug Report submitted succesfully!  Thank You!', 'wp-job-manager-field-editor' ),
				'submit_error'   => __( 'There was an error submitting your bug report, please try again later.', 'wp-job-manager-field-editor' )
			);

			wp_localize_script( 'smyles-bug-report', 'smyles_bug_report', $translation_array );
			wp_enqueue_script( 'smyles-bug-report' );
		}

		public function server_details() {

			$indices = array(
				'PHP_SELF',
				'GATEWAY_INTERFACE',
				'SERVER_ADDR',
				'SERVER_NAME',
				'SERVER_SOFTWARE',
				'SERVER_PROTOCOL',
				'REQUEST_METHOD',
				'REQUEST_TIME',
				'DOCUMENT_ROOT',
				'HTTP_ACCEPT',
				'HTTP_ACCEPT_CHARSET',
				'HTTP_ACCEPT_ENCODING',
				'HTTP_ACCEPT_LANGUAGE',
				'HTTP_CONNECTION',
				'HTTP_HOST',
				'HTTP_REFERER',
				'HTTP_USER_AGENT',
				'HTTPS',
				'REMOTE_ADDR',
				'REMOTE_HOST',
				'REMOTE_PORT',
				'REMOTE_USER',
				'REDIRECT_REMOTE_USER',
				'SCRIPT_FILENAME',
				'SERVER_ADMIN',
				'SERVER_PORT',
				'SERVER_SIGNATURE',
				'PATH_TRANSLATED',
				'SCRIPT_NAME',
				'REQUEST_URI',
				'PHP_AUTH_DIGEST',
				'PHP_AUTH_USER',
				'PHP_AUTH_PW',
				'AUTH_TYPE',
				'PATH_INFO',
				'ORIG_PATH_INFO'
			);

			echo '<table cellpadding="10">';
			foreach ( $indices as $arg ) {
				if ( isset( $_SERVER[ $arg ] ) ) {
					echo '<tr><td>' . $arg . '</td><td>' . $_SERVER[ $arg ] . '</td></tr>';
				} else {
					echo '<tr><td>' . $arg . '</td><td>-</td></tr>';
				}
			}
			echo '</table>';
		}

		public function active_plugin_data() {

			$active_plugins = wp_get_active_and_valid_plugins();
			$output_data    = array( 'Name', 'Version', 'Author', 'PluginURI', 'Description' );

			if ( ! empty( $active_plugins ) ) {

				foreach ( $active_plugins as $plugin_file ) {

					$plugin_data = get_plugin_data( $plugin_file, true, false );

					echo '<i> - File:</i> ' . $plugin_file . '<br />';

					if ( ! empty( $plugin_data ) ) {

						foreach ( $output_data as $data_value ) {

							if ( $plugin_data[ $data_value ] ) {
								echo '   - <strong>' . $data_value . ':</strong> ' . $plugin_data[ $data_value ] . '<br />';
							}

						}

					}

					echo '-- <br />';

				}

			}

		}


		public function submit_bug() {

			check_ajax_referer( 'smyles_submit_bug', 'nonce' );

			$email          = sanitize_email( $_POST[ 'email' ] );
			$description    = sanitize_text_field( $_POST[ 'description' ] );
			$details        = sanitize_text_field( $_POST[ 'details' ] );
			$active_plugins = get_option( 'active_plugins' );

			if ( ! is_email( $email ) ) {

				$response[ 'status' ] = 'error';
				$response[ 'error' ]  = __( 'Invalid Email', 'wp-job-manager-field-editor' );

			} else {

				ob_start();

				$headers[ ]  = 'Content-Type: text/html; charset=UTF-8';
				$attachments = array();

				if ( defined( 'WP_CONTENT_DIR' ) ) {
					if ( file_exists( WP_CONTENT_DIR . '/debug.log' ) ) {
						$attachments[ ] = WP_CONTENT_DIR . '/debug.log';
					}
				} elseif ( defined( 'ABSPATH' ) ) {
					if ( is_dir( ABSPATH . '/wp-content' ) ) {
						$attachments[ ] = ABSPATH . '/wp-content/debug.log';
					}
				}

				$plugin_key = get_option( 'wp-job-manager-field-editor_licence_key' );
				$plugin_key_email = get_option( 'wp-job-manager-field-editor_email' );
				$plugin_key_instance = get_option( 'wp-job-manager-field-editor_instance' );

				if( ! $plugin_key ) $plugin_key = 'Not Activated';
				if( ! $plugin_key_email ) $plugin_key_email = 'Unknown';
				if( ! $plugin_key_instance ) $plugin_key_instance = 'Unknown';

				echo '<strong>From:</strong> ' . $email . "<br />";
				echo '<strong>Description:</strong> ' . $description . "<br />";
				echo '<strong>Details:</strong> ' . "<br />" . $details . "<br /><br />";
				echo '<strong>---</strong><br />';
				echo '<strong>Plugin Name:</strong> ' . $this->plugin_product_id . '<br />';
				echo '<strong>Plugin Version:</strong> ' . $this->plugin_version . '<br />';
				echo '<strong>Plugin Key:</strong> ' . $plugin_key . '<br />';
				echo '<strong>Plugin Key Email:</strong> ' . $plugin_key_email . '<br />';
				echo '<strong>Plugin Key Instance:</strong> ' . $plugin_key_instance . '<br />';
				echo '<strong>---</strong><br />';
				echo '<strong><u>Active Plugins:</u></strong><br />';

				$this->active_plugin_data();

				echo '<br /><br /><strong>Server Details:</strong><br />';

				$this->server_details();

				$message = ob_get_clean();

				$prod_id = str_replace( ' ', '', $this->plugin_product_id );
				preg_match_all( '#([A-Z]+)#', $prod_id, $prod_id_only_uppercase );

				if ( wp_mail( 'myles@smyl.es', '[ ' . implode( '', $prod_id_only_uppercase[ 0 ] ) . ' ' . $this->plugin_version . ' BUG ] ' . $description, $message, $headers, $attachments ) ) {

					$response[ 'status' ] = 'success';

				} else {

					$response[ 'status' ] = 'error';

				}

			}

			ob_clean();

			echo json_encode( $response );

			die();

		}

		public static function current_url( $additional_args ){
			$current_args = $_GET;
			if( ! empty( $additional_args ) ) $current_args = array_merge( $current_args, $additional_args );
			$current_url = '?' . http_build_query( $current_args );

			return $current_url;
		}

		public function output_html() {

			ob_start();
			?>
			<div id="smyles-report-bug">
				<?php wp_nonce_field( 'smyles_submit_bug', 'smyles-bug-nonce' ); ?>
				<div id="smyles-bug-toggle" data-toggle="tooltip" data-placement="left" title="<?php _e( 'Report a Bug', 'wp-job-manager-field-editor' ); ?>">
					<i class="fa fa-lg fa-bug"></i>
			</div>
			<h4 id="smyles-bug-header"><?php _e( 'Report a Bug', 'wp-job-manager-field-editor' ); ?></h4>
			<div id="smyles-bug-content">
				<div id="smyles-bug-alert" class="alert"></div>
				<div id="smyles-bug-spin" class="smyles-spin-wrapper"><div class="smyles-spinner"><i class="fa fa-circle-o-notch fa-3x fa-spin"></i></div></div>
				<form id="smyles-bug-form" role="form">
					<div class="form-group">
						<label for="smyles-bug-email"><?php _e( 'Email Address', 'wp-job-manager-field-editor' ); ?></label>
						<input type="email" class="form-control" id="smyles-bug-email" placeholder="<?php _e( 'Your Email', 'wp-job-manager-field-editor' ); ?>" required>
					</div>
					<div class="form-group">
						<label for="smyles-bug-description"><?php _e( 'Bug Description', 'wp-job-manager-field-editor' ); ?></label>
						<input type="text" class="form-control" id="smyles-bug-description" placeholder="<?php _e( 'Short Bug Description ...', 'wp-job-manager-field-editor' ); ?>" required>
					</div>
					<div class="form-group">
						<label for="smyles-bug-details"><?php _e( 'Bug Details', 'wp-job-manager-field-editor' ); ?></label>
						<textarea class="form-control" id="smyles-bug-details" rows="3" placeholder="<?php _e( 'Please describe the bug, how to replicate the bug, and any other details you can add.  Links to screenshots would be helpful as well.', 'wp-job-manager-field-editor' ); ?>" required></textarea>
					</div>
					<div id="smyles-bug-debug">
						<?php
						if ( $this->check_debug() ) {
							echo '<div id="smyles-bug-debug-enabled">';
							echo '<i class="fa fa-thumbs-o-up"></i> ';
							_e( 'Debug is <strong>ENABLED</strong>', 'wp-job-manager-field-editor' );
							echo '</div>';
						} else {
							echo '<div class="alert alert-danger">';
							echo '<i class="fa fa-exclamation pull-left fa-3x"></i> ';
							_e( 'Debug is <strong>DISABLED</strong>, please <a href="' . self::current_url( array( 'smyles-debug-toggle' => 'enable' ) ) . '">enable</a> debug!<br /><strong>THEN</strong> follow the steps again that caused the error,<br /><strong>BEFORE</strong> submitting a bug report!', 'wp-job-manager-field-editor' );
							echo '</div>';
						}
						?>
					</div>
					<button id="smyles-bug-reset" type="reset" class="button button-default">Reset</button>
					<a href="#" id="smyles-bug-submit" class="button button-primary">Submit Bug</a>
					<?php
						if( $this->force_debug ){
							$debug_toggle_text = __( 'Disable Force Debug', 'wp-job-manager-field-editor' );
							$debug_toggle_action = 'disable';
						} else {
							$debug_toggle_text   = __( 'Enable Force Debug', 'wp-job-manager-field-editor' );
							$debug_toggle_action = 'enable';
						}

						if( ! $this->check_debug( true ) ){
							?>
							<a href="<?php echo self::current_url( array( 'smyles-debug-toggle' => $debug_toggle_action ) ); ?>" id="smyles-bug-debug-toggle" class="button button-default"><?php echo $debug_toggle_text; ?></a>
							<?php
						}
					?>
				</form>
			</div>
		</div>

			<?php
			ob_end_flush();
		}

		/**
		 * Singleton Instance
		 *
		 * @since 1.0.0
		 *
		 * @return sMyles_Bug_Report
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

	}
}