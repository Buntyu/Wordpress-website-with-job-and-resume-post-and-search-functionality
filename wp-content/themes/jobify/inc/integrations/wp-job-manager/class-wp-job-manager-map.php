<?php

class Jobify_WP_Job_Manager_Map {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'jobify_output_map', array( $this, 'output_map' ) );
	}

	public function page_needs_map() {
		$needs = true;

		return $needs;
	}

	public function enqueue_scripts() {
		if ( ! $this->page_needs_map() ) {
			return;
		}

		wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3&libraries=geometry,places' );

		wp_enqueue_script( 'jobify-job-manager-map', get_template_directory_uri() . '/inc/integrations/wp-job-manager/js/wp-job-manager-map.min.js', array( 'jquery', 'google-maps', 'underscore', 'wp-job-manager-ajax-filters' ) );

		$settings = array(
			'useClusters' => jobify_theme_mod( 'map', 'clusters' ),
			'autoFit' => jobify_theme_mod( 'map', 'autofit' ),
			'gridSize' => jobify_theme_mod( 'map', 'grid-size' ),
			'mapOptions' => array(
				'zoom' => jobify_theme_mod( 'map', 'zoom' ),
				'maxZoom' => jobify_theme_mod( 'map', 'max-zoom' ),
			),
			'title' => __( '%d Items Found', 'jobify' )
		);

		if ( '' != ( $center = jobify_theme_mod( 'map', 'center' ) ) ) {
			$settings[ 'mapOptions'][ 'center' ] = array_map( 'trim', explode( ',', $center ) );
		}

		wp_localize_script( 'jobify-job-manager-map', 'jobifyMapSettings', $settings );
	}

	public function output_map( $type = false ) {
		if ( ! $type ) {
			$type = 'job_listing';
		}

		$map = locate_template( array( 'content-job_listing-map.php' ), false, false );

		include( $map );
	}

	public function job_manager_job_filters_distance() {
	?>
		<input type="hidden" id="search_lat" name="search_lat" value="" />
  		<input type="hidden" id="search_lng" name="search_lng" value="" />
  		<input type="hidden" id="search_radius" name="search_radius" value="50" />
	<?php
	}

	public function job_manager_get_listings_custom_filter_text( $text ) {
		$params = array();

		parse_str( $_POST[ 'form_data' ], $params );

		if ( ! isset( $params[ 'search_lat' ] ) || '' == $params[ 'search_lat' ] ) {
			return $text;
		}

		$text .= ' ' . sprintf( __( 'within a %d mile radius', 'classify' ), $params[ 'search_radius' ] );

		return $text;
	}

}