<?php
/**
 * Resume Manager
 */

class Jobify_WP_Resume_Manager {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_action( 'single_resume_end', array( $this, 'wp_enqueue_scripts' ), 999 );

		add_action( 'template_redirect', array( $this, 'resume_archives' ) );
		add_filter( 'pre_get_posts', array( $this, 'resume_archives_query' ) );

		add_filter( 'register_post_type_resume', array( $this, 'post_type_resume' ) );

		add_action( 'jobify_output_resume_results', array( $this, 'output_results' ) );

		add_action( 'resume_manager_contact_details', array( $this, 'contact_wrapper_start' ), 0 );
		add_action( 'resume_manager_contact_details', array( $this, 'contact_wrapper_end' ), 999 );
	}

	public function contact_wrapper_start() {
		echo '<div class="resume_contact_details_inner">';
	}

	public function contact_wrapper_end() {
		echo '</div>';
	}

	/**
	 * Sets up theme support.
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	function add_theme_support() {
		add_theme_support( 'resume-manager-templates' );
	}

	public function wp_enqueue_scripts() {
		wp_dequeue_script( 'wp-resume-manager-resume-contact-details' );
	}

	/**
	 * Registers widgets, and widget areas.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function widgets_init() {
		$widgets = array(
			'class-widget-resumes-map.php',
			'class-widget-resumes-recent.php',
			'class-widget-resume-links.php',
			'class-widget-resume-categories.php',
			'class-widget-resume-skills.php',
			'class-widget-resume-file.php'
		);

		foreach ( $widgets as $widget ) {
			require_once( get_template_directory() . '/inc/integrations/wp-resume-manager/widgets/' . $widget );
		}

		register_widget( 'Jobify_Widget_Resumes' );
		register_widget( 'Jobify_Widget_Resumes_Map' );

		register_widget( 'Jobify_Widget_Resume_Links' );
		register_widget( 'Jobify_Widget_Resume_Categories' );

		if ( get_option( 'resume_manager_enable_skills' ) ) {
			register_widget( 'Jobify_Widget_Resume_Skills' );
		}

		if ( get_option( 'resume_manager_enable_resume_upload' ) ) {
			register_widget( 'Jobify_Widget_Resume_File' );
		}

		if ( 'side' == jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' ) ) {
			register_sidebar( array(
				'name'          => __( 'Resume Page Sidebar', 'jobify' ),
				'id'            => 'sidebar-single-resume',
				'description'   => __( 'Choose what should display on single resume listings.', 'jobify' ),
				'before_widget' => '<aside id="%1$s" class="job_listing-widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="job_listing-widget-title">',
				'after_title'   => '</h3>',
			) );
		} else {
			$columns = jobify_theme_mod( 'jobify_listings', 'jobify_listings_topbar_columns' );

			for ( $i = 1; $i <= $columns; $i++ ) {
				register_sidebar( array(
					'name'          => sprintf( __( 'Resume Info Column %s', 'jobify' ), $i ),
					'id'            => sprintf( 'single-resume-top-%s', $i ),
					'description'   => sprintf( __( 'Choose what should display on resume listings column #%s.', 'jobify' ), $i ),
					'before_widget' => '<aside id="%1$s" class="job_listing-widget-top %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="resume-widget-title-top">',
					'after_title'   => '</h3>',
				) );
			}
		}
	}


	/**
	 * Resume post type arguments.
	 *
	 * @since Jobify 1.5.0
	 *
	 * @param array $args
	 * @return array $args
	 */
	function post_type_resume( $args ) {
		$args[ 'exclude_from_search' ] = false;

		return $args;
	}


	/**
	 * When viewing a taxonomy archive, use the same template for all.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function resume_archives() {
		global $wp_query;

		$taxonomies = array(
			'resume_skill'
		);

		if ( ! is_tax( $taxonomies ) )
			return;

		locate_template( array( 'taxonomy-resume_category.php' ), true );

		exit();
	}


	/**
	 * When viewing a taxonomy archive, make sure the job manager settings are respected.
	 *
	 * @since Jobify 1.0
	 *
	 * @param $query
	 * @return $query
	 */
	function resume_archives_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() )
				return;

		$taxonomies = array(
			'resume_category'
		);

		if ( is_tax( $taxonomies ) ) {
			$query->set( 'posts_per_page', get_option( 'job_manager_per_page' ) );
			$query->set( 'post_type', array( 'resume' ) );
			$query->set( 'post_status', array( 'publish' ) );
		}

		return $query;
	}

	public function output_results() {
		echo do_shortcode( apply_filters( 'jobify_resume_archive_shortcode', '[resumes]' ) );
	}

}

$GLOBALS[ 'jobify_resume_manager' ] = new Jobify_WP_Resume_Manager();