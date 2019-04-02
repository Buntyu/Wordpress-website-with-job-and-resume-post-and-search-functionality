<?php

class Jobify_WP_Job_Manager_Template {

	public function __construct() {
		// Global
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'template_redirect', array( $this, 'job_archives' ) );

		// Login
		add_filter( 'login_form_middle', array( $this, 'login_form_middle' ) );
		add_filter( 'login_form_top', array( $this, 'login_form_top' ) );

		// Single
		remove_action( 'single_job_listing_start', 'job_listing_meta_display', 20 );
		remove_action( 'single_job_listing_start', 'job_listing_company_display', 30 );

		// Archive
		add_filter( 'jobify_listing_data', array( $this, 'job_listing_data' ) );

		if ( ! get_option( 'job_application_form_for_url_method' ) ) {
			add_action( 'job_manager_application_details_url', array( $this, 'contact_wrapper_start' ), 0 );
			add_action( 'job_manager_application_details_url', array( $this, 'contact_wrapper_end' ), 10.00001 );
		}
	}

	public function contact_wrapper_start() {
		echo '<div class="job_manager_contact_details_inner">';
	}

	public function contact_wrapper_end() {
		echo '</div>';
	}

	public function body_class( $classes ) {


		$style = jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' );

		$classes[] = 'single-listing-style-' . $style;

		$categories = get_terms( 'job_listing_category' );

		if ( get_option( 'job_manager_enable_categories' ) && ! empty( $categories ) ) {
			$classes[] = 'wp-job-manager-categories-enabled';

			if ( get_option( 'job_manager_enable_default_category_multiselect' ) && ! is_page_template( 'page-templates/jobify.php' ) ) {
				$classes[] = 'wp-job-manager-categories-multi-enabled';
			}
		}

		$r_categories = get_terms( 'resume_category' );
		
		if ( get_option( 'resume_manager_enable_categories' ) && ! empty( $r_categories ) ) {
			$classes[] = 'wp-resume-manager-categories-enabled';

			if ( get_option( 'resume_manager_enable_default_category_multiselect' ) && ! is_page_template( 'page-templates/jobify.php' ) ) {
				$classes[] = 'wp-resume-manager-categories-multi-enabled';
			}
		}

		if ( class_exists( 'Astoundify_Job_Manager_Contact_Listing' ) ) {
			$classes[] = 'wp-job-manager-contact-listing';
		}

		global $post;

		$apply = get_the_job_application_method();

		if ( $apply ) {
			$classes[] = 'wp-job-manager-apply-' . $apply->type;
		}

		return $classes;
	}

	public function wp_enqueue_scripts() {
		wp_dequeue_style( 'wp-job-manager-frontend' );
		wp_dequeue_style( 'chosen' );
	}

	/**
	 * Registers widgets, and widget areas.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function widgets_init() {
		/** Widgets */
		$widgets = array(
			'class-widget-job-company-logo.php',
			'class-widget-job-type.php',
			'class-widget-job-location.php',
			'class-widget-job-apply.php',
			'class-widget-job-company-social.php',
			'class-widget-job-categories.php',
			'class-widget-job-more-jobs.php',
			'class-widget-job-share.php',

			'class-widget-jobs-recent.php',
			'class-widget-jobs-spotlight.php',
			'class-widget-jobs-search.php',
			'class-widget-jobs-map.php'
		);

		foreach ( $widgets as $widget ) {
			require_once( get_template_directory() . '/inc/integrations/wp-job-manager/widgets/' . $widget );
		}

		unregister_widget( 'WP_Job_Manager_Widget_Recent_Jobs' );

		register_widget( 'Jobify_Widget_Job_Company_Logo' );
		register_widget( 'Jobify_Widget_Job_Type' );
		register_widget( 'Jobify_Widget_Job_Location' );
		register_widget( 'Jobify_Widget_Job_Apply' );
		register_widget( 'Jobify_Widget_Job_Company_Social' );
		register_widget( 'Jobify_Widget_Job_Categories' );
		register_widget( 'Jobify_Widget_Job_More_Jobs' );
		register_widget( 'Jobify_Widget_Job_Share' );

		register_widget( 'Jobify_Widget_Jobs' );
		register_widget( 'Jobify_Widget_Jobs_Spotlight' );
		register_widget( 'Jobify_Widget_Jobs_Search' );
		register_widget( 'Jobify_Widget_Stats' );
		register_widget( 'Jobify_Widget_Map' );

		if ( 'side' == jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' ) ) {
			register_sidebar( array(
				'name'          => __( 'Job Page Sidebar', 'jobify' ),
				'id'            => 'sidebar-single-job_listing',
				'description'   => __( 'Choose what should display on single job listings.', 'jobify' ),
				'before_widget' => '<aside id="%1$s" class="job_listing-widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="job_listing-widget-title">',
				'after_title'   => '</h3>',
			) );
		} else {
			$columns = jobify_theme_mod( 'jobify_listings', 'jobify_listings_topbar_columns' );

			for ( $i = 1; $i <= $columns; $i++ ) {
				register_sidebar( array(
					'name'          => sprintf( __( 'Job Info Column %s', 'jobify' ), $i ),
					'id'            => sprintf( 'single-job_listing-top-%s', $i ),
					'description'   => sprintf( __( 'Choose what should display on single job listings column #%s.', 'jobify' ), $i ),
					'before_widget' => '<aside id="%1$s" class="job_listing-widget-top %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="job_listing-widget-title-top">',
					'after_title'   => '</h3>',
				) );
			}
		}
	}

	/**
	 * When viewing a taxonomy archive, use the same template for all.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function job_archives() {
		global $wp_query;

		$taxonomies = array(
			'job_listing_category',
			'job_listing_region',
			'job_listing_type',
			'job_listing_tag'
		);

		if ( ! is_tax( $taxonomies ) )
			return;

		locate_template( array( 'taxonomy-job_listing_category.php' ), true );

		exit();
	}

	/**
	 * Add a "Forgot Password" link to the login form
	 *
	 * @since Jobify 1.0
	 *
	 * @return $output HTML output
	 */
	function login_form_middle( $output ) {
		$output .= sprintf( '<p class="has-account"><i class="icon-help-circled"></i> <a href="%s">%s</a></p>', wp_lostpassword_url(), __( 'Forgot Password?', 'jobify' ) );

		return $output;
	}


	function login_form_top( $output ) {
		if ( isset ( $_GET[ 'login' ] ) && 'failed' == $_GET[ 'login' ] ) {
			$output .= '<div class="job-manager-error">' . __( 'Please try again.', 'jobify' ) . '</div>';
		}

		return $output;
	}

	/**
	 * Add supplimentary data to individual listings so we can plot
	 * and other things with it.
	 *
	 * @since Classify 1.0.0
	 *
	 * @param array $data
	 * @return array $data
	 */
	public function job_listing_data( $data ) {
		global $post, $jobify_job_manager;

		$data = $output = array();

		/** Longitude */
		$long = esc_attr( $post->geolocation_long );

		if ( $long ) {
			$data[ 'longitude' ] = $long;
		}

		/** Latitude */
		$lat = esc_attr( $post->geolocation_lat );

		if ( $lat ) {
			$data[ 'latitude' ] = $lat;
		}

		/** Title */
		if ( 'job_listing' == $post->post_type ) {
			if ( $post->_company_name ) {
				$data[ 'title' ] = sprintf( __( '%s at %s', 'jobify' ), $post->post_title, $post->_company_name );
			} else {
				$data[ 'title' ] = $post->post_title;
			}
		} else {
			$data[ 'title' ] = sprintf( __( '%s - %s', 'jobify' ), $post->post_title, $post->_candidate_title );
		}

		/** Link */
		$data[ 'href' ] = get_permalink( $post->ID );

		foreach ( $data as $key => $value ) {
			$output[] .= sprintf( 'data-%s="%s"', $key, $value );
		}

		return implode( ' ', $output );
	}

}
