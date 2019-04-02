<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_ZipRecruiter_API
 *
 * Handles interaction with the ZipSearch API
 */
class WP_Job_Manager_ZipRecruiter_API {

	/** @var string API endpoint */
	private static $endpoint     = "https://api.ziprecruiter.com/jobs/v1/";

	/**
	 * Get default args
	 */
	private static function get_default_args() {
		return array(
			'api_key'       => get_option( 'job_manager_ziprecruiter_key' ),
			'search'        => get_option( 'job_manager_ziprecruiter_default_keywords' ),
			'location'      => get_option( 'job_manager_ziprecruiter_default_location' ),
			'jobs_per_page' => 10,
			'page'          => 1,
			'radius_miles'  => 20,
			'days_ago'      => ''
		);
	}

	/**
	 * Format args before sending them to the api
	 * @param  array $args
	 * @return array
	 */
	private static function format_args( $args ) {
		foreach ( $args as $key => $value ) {
			if ( method_exists( __CLASS__, 'format_arg_' . strtolower( $key ) ) ) {
				$args[ $key ] = call_user_func( __CLASS__ . "::format_arg_" . strtolower( $key ), $value );
			}
		}
		return $args;
	}

	/**
	 * Format search
	 * @param  string $value
	 * @return string
	 */
	private static function format_arg_search( $value ) {
		$exclude = array_filter( array_map( 'trim', explode( ',', get_option( 'job_manager_ziprecruiter_exclude_keywords' ) ) ) );
		$require = array_filter( array_map( 'trim', explode( ',', get_option( 'job_manager_ziprecruiter_require_keywords' ) ) ) );

		foreach ( $exclude as $keyword ) {
			$value .= ' -"' . $keyword . '"';
		}

		foreach ( $require as $keyword ) {
			$value .= ' +"' . $keyword . '"';
		}

		return $value;
	}

	/**
	 * Return job in standard format
	 * @param  array $raw_job
	 * @return object
	 */
	private static function format_job( $raw_job ) {
		$job = array(
			'title'           => sanitize_text_field( $raw_job->name ),
			'company'         => sanitize_text_field( $raw_job->hiring_company->name ),
			'tagline'         => sanitize_text_field( $raw_job->snippet  ),
			'url'             => esc_url_raw( $raw_job->url ),
			'location'        => sanitize_text_field( $raw_job->location ),
			'latitude'        => '',
			'longitude'       => '',
			'type'            => '',
			'type_slug'       => '',
			'timestamp'       => strtotime( $raw_job->posted_time ),
			'link_attributes' => array(),
			'logo'            => apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' )
		);
		return (object) $job;
	}

	/**
	 * Get jobs from the API
	 * @return array()
	 */
	public static function get_jobs( $args ) {
		$args           = self::format_args( wp_parse_args( $args, self::get_default_args() ) );
		$transient_name = 'ziprecruiter_' . md5( json_encode( $args ) );
		$total_pages    = 0;
		$total_jobs     = 0;
		$jobs           = array();

		if ( false === ( $results = get_transient( $transient_name ) ) ) {
			$results = array();
			$result  = wp_remote_get( self::$endpoint . '?' . http_build_query( $args, '', '&' ), array( 'timeout' => 10 ) );

			if ( ! is_wp_error( $result ) && ! empty( $result['body'] ) ) {
				$results = json_decode( $result['body'] );

				if ( $results && ! empty( $results->success ) ) {
					set_transient( $transient_name, $results, ( 60 * 60 * 24 ) );
				} else {
					return self::response( 0, 0, array() );
				}
			} else {
				return self::response( 0, 0, array() );
			}
		}

		$total_jobs     = absint( $results->total_jobs );
		$total_pages    = ceil( $total_jobs / $args['jobs_per_page'] );

		foreach ( $results->jobs as $result ) {
			$jobs[] = self::format_job( $result );
		}

		return self::response( $total_pages, $total_jobs, $jobs );
	}

	/**
	 * Return a response containing jobs
	 * @param  integer $total_pages
	 * @param  integer $total_jobs
	 * @param  array   $jobs
	 * @return array
	 */
	public static function response( $total_pages = 0, $total_jobs = 0, $jobs = array() ) {
		return array(
			'total_pages' => $total_pages,
			'total_jobs'  => $total_jobs,
			'jobs'        => $jobs
		);
	}
}