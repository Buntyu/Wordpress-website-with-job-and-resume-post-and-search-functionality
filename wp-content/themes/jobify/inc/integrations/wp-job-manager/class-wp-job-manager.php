<?php
/**
 * WP Job Manager
 */

class Jobify_WP_Job_Manager {

	public function __construct() {
		$includes = array(
			'template-tags.php',
			'class-wp-job-manager-map.php',
			'class-wp-job-manager-template.php'
		);

		foreach ( $includes as $file ) {
			require_once( get_template_directory() . '/inc/integrations/wp-job-manager/' . $file );
		}

		add_action( 'init', array( $this, 'init' ), 0 );

		add_action( 'init', array( $this, 'load_posted_form' ) );
		add_filter( 'job_manager_output_jobs_defaults', array( $this, 'job_manager_output_jobs_defaults' ) );
		add_filter( 'pre_get_posts', array( $this, 'archives_query' ) );

		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );

		add_action( 'save_post', array( $this, 'clear_page_shortcode' ) );

		add_filter( 'register_post_type_job_listing', array( $this, 'post_type_job_listing' ) );

		add_filter( 'submit_job_steps', array( $this, 'job_steps' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'job_listing_data_fields' ) );
		add_action( 'job_manager_save_job_listing', array( $this, 'save_job_listing' ), 10, 2 );

		add_filter( 'submit_job_form_login_url', array( $this, 'form_login_url' ) );
		add_filter( 'submit_resume_form_login_url', array( $this, 'form_login_url' ) );

		add_shortcode( 'jobify_login_form', array( $this, 'shortcode_login_form' ) );
		add_shortcode( 'jobify_register_form', array( $this, 'shortcode_register_form' ) );

		add_action( 'jobify_output_job_results', array( $this, 'output_results' ) );

		add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_filters_after' ), 9 );
		add_action( 'resume_manager_resume_filters_search_resumes_end', array( $this, 'job_filters_after' ), 9 );
	}

	public function init() {
		$this->map = new Jobify_WP_Job_Manager_Map();
		$this->template = new Jobify_WP_Job_Manager_Template();
	}

	/**
	 * Sets up theme support.
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'job-manager-templates' );
	}


	/**
	 * Preview view override
	 *
	 * @since Jobify 1.6.0
	 *
	 * @param array $steps
	 * @return array $steps
	 */
	function job_steps( $steps ) {
		$steps[ 'preview' ][ 'view' ] = 'jobify_preview_handler';

		return $steps;
	}

	/**
	 * Job Listing post type arguments.
	 *
	 * @since Jobify 1.0.0
	 *
	 * @param array $args
	 * @return array $args
	 */
	function post_type_job_listing( $args ) {
		$args[ 'supports' ] = array( 'title', 'editor', 'custom-fields', 'thumbnail' );

		return $args;
	}

	/**
	 * When viewing a taxonomy archive, make sure the job manager settings are respected.
	 *
	 * @since Jobify 1.0
	 *
	 * @param $query
	 * @return $query
	 */
	function archives_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() )
			return;

		$taxonomies = array(
			'job_listing_category',
			'job_listing_region',
			'job_listing_type',
			'job_listing_tag'
		);

		if ( is_tax( $taxonomies ) ) {
			$query->set( 'posts_per_page', get_option( 'job_manager_per_page' ) );
			$query->set( 'post_type', array( 'job_listing' ) );
			$query->set( 'post_status', array( 'publish' ) );
			$query->set( 'orderby', 'meta_key' );
			$query->set( 'meta_key', '_featured' );

			add_filter( 'posts_clauses', 'order_featured_job_listing' );

			if ( get_option( 'job_manager_hide_filled_positions' ) == 1 ) {
				$query->set( 'meta_query', array(
					array(
						'key'     => '_filled',
						'value'   => '1',
						'compare' => '!='
					)
				) );
			}
		}

		return $query;
	}

	public function job_manager_output_jobs_defaults( $default ) {
		$type = get_queried_object();

		if ( is_tax( 'job_listing_type' ) ) {
			$default[ 'job_types' ] = $type->slug;
			$default[ 'selected_job_types' ] = $type->slug;
			$default[ 'show_categories' ] = true;
		} elseif ( is_tax( 'job_listing_category' ) ) {
			$default[ 'show_categories' ] = true;
			$default[ 'categories' ] = $type->slug;
			$default[ 'selected_category' ] = $type->slug;
		} elseif ( is_search() ) {
			$default[ 'keywords' ] = get_search_query();
			$default[ 'show_filters' ] = false;
		}

		if ( is_home() || is_page_template( 'page-templates/jobify.php' ) ) {
			$default[ 'show_category_multiselect' ] = false;
		}

		if ( isset( $_GET[ 'search_categories' ] ) ) {
			$category = get_term_by( 'ID', absint( $_GET[ 'search_categories' ] ), 'job_listing_category' );

			$default[ 'show_categories' ] = true;
			$default[ 'categories' ] = $_GET[ 'search_categories' ];
		}

		return $default;
	}

	/**
	 * Add extra fields to the submission form.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	 function submit_job_form_fields( $fields ) {
		$fields[ 'company' ][ 'company_website' ][ 'priority' ] = 4.2;

		$fields[ 'company' ][ 'company_description' ] = array(
			'label'       => _x( 'Description', 'company description on submission form', 'jobify' ),
			'type'        => 'wp-editor',
			'required'    => false,
			'placeholder' => '',
			'priority'    => 3.5
		);

		$fields[ 'company' ][ 'company_facebook' ] = array(
			'label'       => __( 'Facebook username', 'jobify' ),
			'type'        => 'text',
			'required'    => false,
			'placeholder' => __( 'yourcompany', 'jobify' ),
			'priority'    => 4.5
		);

		$fields[ 'company' ][ 'company_google' ] = array(
			'label'       => __( 'Google+ username', 'jobify' ),
			'type'        => 'text',
			'required'    => false,
			'placeholder' => __( 'yourcompany', 'jobify' ),
			'priority'    => 4.5
		);

		$fields[ 'company' ][ 'company_linkedin' ] = array(
			'label'       => __( 'LinkedIn username', 'jobify' ),
			'type'        => 'text',
			'required'    => false,
			'placeholder' => __( 'yourcompany', 'jobify' ),
			'priority'    => 4.6
		);

		return $fields;
	}


	/**
	 * Save the extra frontend fields
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function update_job_data( $job_id, $values ) {
		update_post_meta( $job_id, '_company_description', $values[ 'company' ][ 'company_description' ] );
		update_post_meta( $job_id, '_company_facebook', $values[ 'company' ][ 'company_facebook' ] );
		update_post_meta( $job_id, '_company_google', $values[ 'company' ][ 'company_google' ] );
		update_post_meta( $job_id, '_company_linkedin', $values[ 'company' ][ 'company_linkedin' ] );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_company_description', $values[ 'company' ][ 'company_description' ] );
			update_user_meta( get_current_user_id(), '_company_facebook', $values[ 'company' ][ 'company_facebook' ] );
			update_user_meta( get_current_user_id(), '_company_google', $values[ 'company' ][ 'company_google' ] );
			update_user_meta( get_current_user_id(), '_company_linkedin', $values[ 'company' ][ 'company_linkedin' ] );
		}
	}


	/**
	 * Add extra fields to the WordPress admin.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function job_listing_data_fields( $fields ) {
		$fields[ '_company_description' ] = array(
			'label' => __( 'Company Description', 'jobify' ),
			'placeholder' => '',
			'type'        => 'textarea'
		);

		$fields[ '_company_facebook' ] = array(
			'label' => __( 'Company Facebook', 'jobify' ),
			'placeholder' => ''
		);

		$fields[ '_company_google' ] = array(
			'label' => __( 'Company Google+', 'jobify' ),
			'placeholder' => ''
		);

		$fields[ '_company_linkedin' ] = array(
			'label' => __( 'Company LinkedIn', 'jobify' ),
			'placeholder' => ''
		);

		return $fields;
	}


	/**
	 * Save the extra admin fields.
	 *
	 * WP Job Manager strips our tags out. Resave it after with the tags.
	 *
	 * @since Jobify 1.4.4
	 *
	 * @return void
	 */
	function save_job_listing( $job_id, $post ) {
		update_post_meta( $job_id, '_company_description', wp_kses_post( $_POST[ '_company_description' ] ) );
	}

	/**
	 * Login Form Shortcode
	 *
	 * @since Jobify 1.0
	 *
	 * @return $form HTML form.
	 */
	function shortcode_login_form() {
		ob_start();

		wp_login_form( apply_filters( 'jobify_shortcode_login_form', array(
			'label_log_in' => _x( 'Sign In', 'login for submit label', 'jobify' ),
			'value_remember' => true,
			'redirect' => home_url()
		) ) );

		$form = ob_get_clean();

		return $form;
	}


	/**
	 * Register Form Shortcode
	 *
	 * @since Jobify 1.0
	 *
	 * @return $form HTML form.
	 */
	function shortcode_register_form() {
		ob_start();

		if ( ! class_exists( 'WP_Job_Manager_Form' ) ) {
			include_once( JOB_MANAGER_PLUGIN_DIR . '/includes/abstracts/abstract-wp-job-manager-form.php' );
		}

		include_once( get_template_directory() . '/inc/integrations/wp-job-manager/wp-job-manager-form-register.php' );

		if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', '<' ) ) {
			WP_Job_Manager_Form_Register::output();
		} else {
			$form_class = WP_Job_Manager_Form_Register::instance();
			$form_class->output();
		}

		$form = ob_get_clean();

		return $form;
	}


	/**
	 * Posted Register Form
	 *
	 * @since Jobify 1.0
	 *
	 * @return $form HTML form.
	 */
	function load_posted_form() {
		if ( ! empty( $_POST['job_manager_form'] ) ) {
			$form        = esc_attr( $_POST['job_manager_form'] );

			$form_class  = 'WP_Job_Manager_Form_' . str_replace( '-', '_', $form );
			$form_file   = get_template_directory() . '/inc/integrations/wp-job-manager/wp-job-manager-form-' . $form . '.php';

			if ( class_exists( $form_class ) ) {
				if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', '<' ) ) {
					return $form_class;
				} else {
					return $form_class::instance();
				}
			}

			if ( ! file_exists( $form_file ) ) {
				return false;
			}

			if ( ! class_exists( $form_class ) ) {
				include $form_file;
			}

			if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', '<' ) ) {
				call_user_func( array( $form_class, "init" ) );
			} else {
				return $form_class::instance();
			}
		}
	}


	/**
	 * Clear shortcode options when a post is saved.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function clear_page_shortcode() {
		$shortcodes = array(
			'login_form',
			'register_form',
			'jobify_login_form',
			'jobify_register_form'
		);

		foreach ( $shortcodes as $shortcode ) {
			delete_option( 'job_manager_page_' . $shortcode );
		}
	}

	public function output_results() {
		echo do_shortcode( apply_filters( 'jobify_job_archive_shortcode', '[jobs]' ) );
	}


	/**
	 * Add a submit button the filtering options.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function job_filters_after() {
	?>
		<div class="search_submit">
			<input type="submit" name="submit" value="<?php echo esc_attr_e( 'Search', 'jobify' ); ?>" />
		</div>
	<?php
	}


	/**
	 * Job/Resume login page.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function form_login_url( $url ) {
		$page = jobify_find_page_with_shortcode( array( 'jobify_login_form', 'login_form' ) );

		if ( ! $page ) {
			return $url;
		}

		return get_permalink( $page );
	}
}

$GLOBALS[ 'jobify_job_manager' ] = new Jobify_WP_Job_Manager();
