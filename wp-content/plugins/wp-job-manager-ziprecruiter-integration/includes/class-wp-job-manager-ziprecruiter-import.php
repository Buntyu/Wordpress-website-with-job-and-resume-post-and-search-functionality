<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_ZipRecruiter_Import
 */
class WP_Job_Manager_ZipRecruiter_Import extends WP_Job_Manager_Importer {

	/** @var string Importer ID */
	protected $importer_id = 'ziprecruiter';

	/**
	 * Get jobs for a particular request (page, type, and whether or not results need to be offset)
	 */
	public function get_jobs_for_request( $page, $request_type = 'backfill', $offset_before = false ) {
		// See how many listings to get
		switch ( $request_type ) {
			case "backfill" :
				$limit = get_option( 'job_manager_ziprecruiter_backfill' );
			break;
			case "before" :
				$limit = get_option( 'job_manager_ziprecruiter_before_jobs' );
			break;
			case "page" :
				$limit = get_option( 'job_manager_ziprecruiter_per_page' );
			break;
			case "after" :
				$limit = get_option( 'job_manager_ziprecruiter_after_jobs' );
			break;
		}

		if ( ! $limit ) {
			return WP_Job_Manager_ZipRecruiter_API::response();
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
			foreach ( $search_categories as $term_id ) {
				$term            = get_term_by( 'id', absint( $term_id ), 'job_listing_category' );
				$search_keywords .= ' +"' . $term->name . '"';
			}
		}

		// Regions integration
		if ( isset( $_REQUEST['form_data'] ) ) {
			parse_str( $_REQUEST['form_data'], $post_data );

			if ( taxonomy_exists( 'job_listing_region' ) && ! empty( $post_data['search_region'] ) ) {
				$term            = get_term_by( 'id', absint( $post_data['search_region'] ), 'job_listing_region' );
				$search_location = $term->name;
			}
		}

		$search_keywords = $search_keywords ? $search_keywords : get_option( 'job_manager_ziprecruiter_default_keywords' );

		// See what type of jobs we are querying
		if ( sizeof( $filter_job_types ) !== sizeof( $types ) ) {
			$search_keywords .= ' "' . implode( ' OR "', $filter_job_types )  . '"';
		}

		return WP_Job_Manager_ZipRecruiter_API::get_jobs( array(
			'search'        => $search_keywords,
			'location'      => $search_location ? $search_location : get_option( 'job_manager_ziprecruiter_default_location' ),
			'page'          => $page,
			'jobs_per_page' => $limit
		) );
	}
}

new WP_Job_Manager_ZipRecruiter_Import();