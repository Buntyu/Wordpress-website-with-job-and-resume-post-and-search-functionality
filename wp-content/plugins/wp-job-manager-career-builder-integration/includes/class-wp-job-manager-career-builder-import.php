<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Career_Builder_Import
 */
class WP_Job_Manager_Career_Builder_Import extends WP_Job_Manager_Importer {

	/** @var string Importer ID */
	protected $importer_id = 'career_builder';

	/**
	 * Get jobs for a particular request (page, type, and whether or not results need to be offset)
	 */
	public function get_jobs_for_request( $page, $request_type = 'backfill', $offset_before = false ) {
		// See how many listings to get
		switch ( $request_type ) {
			case "backfill" :
				$limit = get_option( 'job_manager_career_builder_backfill' );
			break;
			case "before" :
				$limit = get_option( 'job_manager_career_builder_before_jobs' );
			break;
			case "page" :
				$limit = get_option( 'job_manager_career_builder_per_page' );
			break;
			case "after" :
				$limit = get_option( 'job_manager_career_builder_after_jobs' );
			break;
		}

		if ( ! $limit ) {
			return WP_Job_Manager_Career_Builder_API::response();
		}

		$types            = get_job_listing_types();
		$filter_job_types = array_filter( array_map( 'sanitize_title', (array) $_REQUEST['filter_job_type'] ) );
		$search_location  = sanitize_text_field( stripslashes( $_REQUEST['search_location'] ) );
		$search_keywords  = sanitize_text_field( stripslashes( $_REQUEST['search_keywords'] ) );
		$page             = $offset_before ? $page + 1 : $page;

		// Category
		$categories        = array();
		$search_categories = isset( $_REQUEST['search_categories'] ) ? array_filter( array_map( 'absint', (array) $_REQUEST['search_categories'] ) ) : array();
		if ( ! empty( $search_categories ) ) {
			$career_builder_categories = get_option( 'career_builder_categories', array() );

			foreach ( $search_categories as $term_id ) {
				$term         = get_term_by( 'id', absint( $term_id ), 'job_listing_category' );
				$categories[] = ! empty( $career_builder_categories[ $term_id ] ) ? $career_builder_categories[ $term_id ] : $term->name;
			}
		}

		// Regions and tags integration
		$tags     = array();

		if ( isset( $_REQUEST['form_data'] ) ) {
			parse_str( $_REQUEST['form_data'], $post_data );

			if ( taxonomy_exists( 'job_listing_region' ) && ! empty( $post_data['search_region'] ) ) {
				$term            = get_term_by( 'id', absint( $post_data['search_region'] ), 'job_listing_region' );
				$search_location = $term->name;
			}

			if ( taxonomy_exists( 'job_listing_tag' ) && ! empty( $post_data['job_tag'] ) ) {
				$job_tags = array_filter( array_map( 'sanitize_text_field', (array) $post_data['job_tag'] ) );
				foreach ( $job_tags as $tag ) {
					$tags[] = $tag;
				}
			}
		}

		// See what type of jobs we are querying
		$cb_type = array();
		if ( sizeof( $filter_job_types ) !== sizeof( $types ) ) {
			foreach ( $filter_job_types as $type ) {
				switch ( $type ) {
					case 'full-time' :
						$cb_type[] = 'JTFT';
					break;
					case 'part-time' :
						$cb_type[] = 'JTPT';
					break;
					case 'internship' :
						$cb_type[] = 'JTIN';
					break;
					case 'temporary' :
						$cb_type[] = 'JTSE';
					break;
					case 'freelance' :
						$cb_type[] = 'JTCT';
					break;
				}
			}
		}

		if ( ! $search_country = get_option( 'job_manager_career_builder_country' ) ) {
			// Before querying career_builder, lets ensure the CO variable matches the location by using google geocoding
			$search_country = get_option( 'job_manager_career_builder_default_country' );

			if ( $search_location ) {
				$address_data = WP_Job_Manager_Geocode::get_location_data( $search_location );
				if ( ! empty( $address_data['country_short'] ) ) {
					$search_country = $address_data['country_short'];
				}
			}
		}

		return WP_Job_Manager_Career_Builder_API::get_jobs( array(
			'OrderBy'     => 'Relevance',
			'Keywords'    => $search_keywords ? $search_keywords : get_option( 'job_manager_career_builder_default_keywords' ),
			'Location'    => $search_location ? $search_location : get_option( 'job_manager_career_builder_default_location' ),
			'CountryCode' => $search_country,
			'EmpType'     => implode( ',', array_filter( $cb_type ) ),
			'PageNumber'  => $page,
			'PerPage'     => $limit,
			'Category'    => implode( ',', array_filter( $categories ) ),
			'Tags'        => implode( ',', array_filter( $tags ) )
		) );
	}
}

new WP_Job_Manager_Career_Builder_Import();