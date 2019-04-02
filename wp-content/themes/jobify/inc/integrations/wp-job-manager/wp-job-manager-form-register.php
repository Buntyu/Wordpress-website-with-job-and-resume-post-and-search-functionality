<?php
/**
 * WP_Job_Manager_Form_Register class.
 */
if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', '<' ) ) {
	class WP_Job_Manager_Form_Register extends WP_Job_Manager_Form {
		public    static $form_name = 'register';
		protected static $job_id;
		protected static $preview_job;
		protected static $steps;
		protected static $step;

		/**
		 * Init form
		 */
		public static function init() {
			add_action( 'wp', array( __CLASS__, 'process' ) );

			// Get step/job
			self::$step   = ! empty( $_REQUEST['step'] ) ? max( absint( $_REQUEST['step'] ), 0 ) : 0;

			$register = jobify_find_page_with_shortcode( array( 'jobify_register_form', 'register_form' ) );
			$register = get_post( $register );

			self::$action = get_permalink( $register->ID );

			self::$steps  = (array) apply_filters( 'register_form_steps', array(
				'submit' => array(
					'name'     => __( 'Register', 'jobify' ),
					'view'     => array( __CLASS__, 'submit' ),
					'handler'  => array( __CLASS__, 'submit_handler' ),
					'priority' => 10
				),
			) );

			usort( self::$steps, array( __CLASS__, 'sort_by_priority' ) );
		}

		/**
		 * Increase step from outside of the class
		 */
		public function next_step() {
			self::$step ++;
		}

		/**
		 * Decrease step from outside of the class
		 */
		public function previous_step() {
			self::$step --;
		}

		/**
		 * Sort array by priority value
		 */
		private static function sort_by_priority( $a, $b ) {
			return $a['priority'] - $b['priority'];
		}

		/**
		 * init_fields function.
		 *
		 * @access public
		 * @return void
		 */
		public static function init_fields() {
			self::$fields = apply_filters( 'register_form_fields', array(
				'creds' => array(
					'nicename' => array(
						'label'       => __( 'Username', 'jobify' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 1
					),
					'email' => array(
						'label'       => __( 'Email Address', 'jobify' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => __( 'email@example.com', 'jobify' ),
						'priority'    => 2
					),
					'password' => array(
						'label'       => __( 'Password', 'jobify' ),
						'type'        => 'password',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 3
					)
				),
				'info' => array(
					'role' => array(
						'label'       => __( 'About You', 'jobify' ),
						'type'        => 'select',
						'required'    => true,
						'priority'    => 4,
						'options'     => array(
							'none'      => __( '&mdash;Select&mdash;', 'jobify' ),
							'employer'  => __( 'I&#39;m an employer looking to hire', 'jobify' ),
							'candidate' => __( 'I&#39;m a candidate looking for a job', 'jobify' )
						)
					)
				)
			) );
		}

		/**
		 * Get post data for fields
		 *
		 * @return array of data
		 */
		protected static function get_posted_fields() {
			self::init_fields();

			$values = array();

			foreach ( self::$fields as $group_key => $fields ) {
				foreach ( $fields as $key => $field ) {
					$values[ $group_key ][ $key ] = isset( $_POST[ $key ] ) ? stripslashes( $_POST[ $key ] ) : '';
					$values[ $group_key ][ $key ] = sanitize_text_field( $values[ $group_key ][ $key ] );

					// Set fields value
					self::$fields[ $group_key ][ $key ]['value'] = $values[ $group_key ][ $key ];
				}
			}

			return $values;
		}

		/**
		 * Validate hte posted fields
		 *
		 * @return bool on success, WP_ERROR on failure
		 */
		protected static function validate_fields( $values ) {
			foreach ( self::$fields as $group_key => $fields ) {
				foreach ( $fields as $key => $field ) {
					if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) )
						return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'jobify' ), $field['label'] ) );
				}
			}

			return true;
		}

		/**
		 * Process function. all processing code if needed - can also change view if step is complete
		 */
		public static function process() {
			$keys = array_keys( self::$steps );

			if ( isset( $keys[ self::$step ] ) && is_callable( self::$steps[ $keys[ self::$step ] ]['handler'] ) ) {
				call_user_func( self::$steps[ $keys[ self::$step ] ]['handler'] );
			}
		}

		/**
		 * output function. Call the view handler.
		 */
		public static function output() {
			$keys = array_keys( self::$steps );

			self::show_errors();

			if ( isset( $keys[ self::$step ] ) && is_callable( self::$steps[ $keys[ self::$step ] ]['view'] ) ) {
				call_user_func( self::$steps[ $keys[ self::$step ] ]['view'] );
			}
		}

		/**
		 * Submit Step
		 */
		public static function submit() {
			global $job_manager, $post;

			// re-init our current fields
			$check_fields = self::get_fields( 'creds' );

			if ( empty( $check_fields ) ) {
				self::init_fields();
			}

			get_job_manager_template( 'form-register.php', array(
				'form'               => self::$form_name,
				'action'             => self::get_action(),
				'cred_fields'        => self::get_fields( 'creds' ),
				'info_fields'        => self::get_fields( 'info' ),
				'submit_button_text' => __( 'Register', 'jobify' )
			) );

			wp_reset_query();
		}

		/**
		 * Submit Step is posted
		 */
		public static function submit_handler() {
			try {
				// Get posted values
				$values = self::get_posted_fields();

				if ( empty( $_POST[ 'submit_register' ] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'register_form_posted' ) )
					return;

				// Validate required
				if ( is_wp_error( ( $return = self::validate_fields( $values ) ) ) )
					throw new Exception( $return->get_error_message() );

				$role = esc_attr( $values[ 'info' ][ 'role' ] );

				if ( 'none' == $role ) {
					throw new Exception( __( 'Please choose a role for your account', 'jobify' ) );
				} else {
					if ( 'employer' == $role ) {
						$role = get_option( 'job_manager_registration_role' );
					} elseif ( 'candidate' == $role && class_exists( 'WP_Resume_Manager' ) ) {
						$role = apply_filters( 'jobify_default_candidate_role', 'candidate' );
					} else {
						$role = apply_filters( 'jobify_default_role', get_option( 'default_role' ), $role, $values );
					}
				}

				$values = $values[ 'creds' ];

				$user_email = apply_filters( 'user_registration_email', sanitize_email( $values[ 'email' ] ) );

				if ( empty( $user_email ) )
					throw new Exception( __( 'Your email address is required.', 'jobify' ) );

				if ( ! is_email( $user_email ) )
					throw new Exception( __( 'Your email address isn&#8217;t correct.', 'jobify' ) );

				if ( email_exists( $user_email ) )
					throw new Exception( __( 'This email is already registered, please choose another one.', 'jobify' ) );

				// Email is good to go - use it to create a user name
				$username = sanitize_user( $values[ 'nicename' ] );
				$password = esc_attr( $values[ 'password' ] );

				if ( username_exists( $username ) ) {
					throw new Exception( __( 'This username is already in use, please choose another one.', 'jobify' ) );
				}

				// Final error check
				$reg_errors = new WP_Error();
				do_action( 'register_post', $username, $user_email, $reg_errors );
				$reg_errors = apply_filters( 'registration_errors', $reg_errors, $username, $user_email );

				if ( $reg_errors->get_error_code() )
					return $reg_errors;

				// Get the role
				$role = esc_attr( $role );

				// Create account
				$new_user = array(
					'user_login' => $username,
					'user_pass'  => $password,
					'user_email' => $user_email,
					'role'       => $role
				);

				$user_id = wp_insert_user( apply_filters( 'job_manager_create_account_data', $new_user ) );

				if ( is_wp_error( $user_id ) )
					return $user_id;

				// Notify
				wp_new_user_notification( $user_id, $password );

				// Login
				if ( apply_filters( 'jobify_force_login_on_register', true ) ) {
					wp_set_auth_cookie( $user_id, true, is_ssl() );
					$current_user = get_user_by( 'id', $user_id );

					wp_safe_redirect( apply_filters( 'jobify_registeration_redirect', home_url(), $current_user ) );
					exit();
				} else {
					do_action( 'jobify_user_registered', $current_user );
				}

				return true;
			} catch ( Exception $e ) {
				self::add_error( $e->getMessage() );
				return;
			}
		}
	}

	WP_Job_Manager_Form_Register::init();
} else {
	class WP_Job_Manager_Form_Register extends WP_Job_Manager_Form {
		public    $form_name = 'register';
		protected $job_id;
		protected $preview_job;
		protected static $_instance = null;

		/**
		 * Main Instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'process' ) );

			// Get step/job
			$this->step   = ! empty( $_REQUEST['step'] ) ? max( absint( $_REQUEST['step'] ), 0 ) : 0;

			$register = jobify_find_page_with_shortcode( array( 'jobify_register_form', 'register_form' ) );
			$register = get_post( $register );

			$this->action = get_permalink( $register->ID );

			$this->steps  = (array) apply_filters( 'register_form_steps', array(
				'submit' => array(
					'name'     => __( 'Register', 'jobify' ),
					'view'     => array( $this, 'submit' ),
					'handler'  => array( $this, 'submit_handler' ),
					'priority' => 10
				),
			) );

			usort($this->steps, array( $this, 'sort_by_priority' ) );
		}

		/**
		 * init_fields function.
		 *
		 * @access public
		 * @return void
		 */
		public function init_fields() {
			$this->fields = apply_filters( 'register_form_fields', array(
				'creds' => array(
					'nicename' => array(
						'label'       => __( 'Username', 'jobify' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 1
					),
					'email' => array(
						'label'       => __( 'Email Address', 'jobify' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => __( 'email@example.com', 'jobify' ),
						'priority'    => 2
					),
					'password' => array(
						'label'       => __( 'Password', 'jobify' ),
						'type'        => 'password',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 3
					)
				),
				'info' => array(
					'role' => array(
						'label'       => __( 'About You', 'jobify' ),
						'type'        => 'select',
						'required'    => true,
						'priority'    => 4,
						'options'     => array(
							'none'      => __( '&mdash;Select&mdash;', 'jobify' ),
							'employer'  => __( 'I&#39;m an employer looking to hire', 'jobify' ),
							'candidate' => __( 'I&#39;m a candidate looking for a job', 'jobify' )
						)
					)
				)
			) );
		}

		/**
		 * Validate hte posted fields
		 *
		 * @return bool on success, WP_ERROR on failure
		 */
		protected function validate_fields( $values ) {
			foreach ( $this->fields as $group_key => $fields ) {
				foreach ( $fields as $key => $field ) {
					if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) )
						return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'jobify' ), $field['label'] ) );
				}
			}

			return true;
		}

		/**
		 * Submit Step
		 */
		public function submit() {
			global $job_manager, $post;

			// re-init our current fields
			$check_fields = $this->get_fields( 'creds' );

			if ( empty( $check_fields ) ) {
				$this->init_fields();
			}

			get_job_manager_template( 'form-register.php', array(
				'form'               => $this->form_name,
				'action'             => $this->get_action(),
				'cred_fields'        => $this->get_fields( 'creds' ),
				'info_fields'        => $this->get_fields( 'info' ),
				'submit_button_text' => __( 'Register', 'jobify' )
			) );

			wp_reset_query();
		}

		/**
		 * Submit Step is posted
		 */
		public function submit_handler() {
			try {
				// Get posted values
				$values = $this->get_posted_fields();

				if ( empty( $_POST[ 'submit_register' ] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'register_form_posted' ) )
					return;

				// Validate required
				if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) )
					throw new Exception( $return->get_error_message() );

				$role = esc_attr( $values[ 'info' ][ 'role' ] );

				if ( 'none' == $role ) {
					throw new Exception( __( 'Please choose a role for your account', 'jobify' ) );
				} else {
					if ( 'employer' == $role ) {
						$role = get_option( 'job_manager_registration_role' );
					} elseif ( 'candidate' == $role && class_exists( 'WP_Resume_Manager' ) ) {
						$role = apply_filters( 'jobify_default_candidate_role', 'candidate' );
					} else {
						$role = apply_filters( 'jobify_default_role', get_option( 'default_role' ), $role, $values );
					}
				}

				$values = $values[ 'creds' ];

				$user_email = apply_filters( 'user_registration_email', sanitize_email( $values[ 'email' ] ) );

				if ( empty( $user_email ) )
					throw new Exception( __( 'Your email address is required.', 'jobify' ) );

				if ( ! is_email( $user_email ) )
					throw new Exception( __( 'Your email address isn&#8217;t correct.', 'jobify' ) );

				if ( email_exists( $user_email ) )
					throw new Exception( __( 'This email is already registered, please choose another one.', 'jobify' ) );

				// Email is good to go - use it to create a user name
				$username = sanitize_user( $values[ 'nicename' ] );
				$password = esc_attr( $values[ 'password' ] );

				if ( username_exists( $username ) ) {
					throw new Exception( __( 'This username is already in use, please choose another one.', 'jobify' ) );
				}

				// Final error check
				$reg_errors = new WP_Error();
				do_action( 'register_post', $username, $user_email, $reg_errors );
				$reg_errors = apply_filters( 'registration_errors', $reg_errors, $username, $user_email );

				if ( $reg_errors->get_error_code() )
					return $reg_errors;

				// Get the role
				$role = esc_attr( $role );

				// Create account
				$new_user = array(
					'user_login' => $username,
					'user_pass'  => $password,
					'user_email' => $user_email,
					'role'       => $role
				);

				$user_id = wp_insert_user( apply_filters( 'job_manager_create_account_data', $new_user ) );

				if ( is_wp_error( $user_id ) )
					return $user_id;

				// Notify
				wp_new_user_notification( $user_id, $password );

				// Login
				if ( apply_filters( 'jobify_force_login_on_register', true ) ) {
					wp_set_auth_cookie( $user_id, true, is_ssl() );
					$current_user = get_user_by( 'id', $user_id );

					wp_safe_redirect( apply_filters( 'jobify_registeration_redirect', home_url(), $current_user ) );
					exit();
				} else {
					do_action( 'jobify_user_registered', $current_user );
				}

				return true;
			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage() );
				return;
			}
		}
	}

	new WP_Job_Manager_Form_Register();
}
