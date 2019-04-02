<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Job_Manager_Career_Builder_API
 *
 * Handles interaction with the Career Builder API
 */
class WP_Job_Manager_Career_Builder_API {

	/** @var string API endpoint */
	private static $endpoint     = "http://api.careerbuilder.com/v2/";

	/**
	 * Get default args
	 */
	private static function get_default_args() {
		return array(
			'DeveloperKey'        => get_option( 'job_manager_career_builder_key' ),
			'ExcludeKeywords'     => get_option( 'job_manager_career_builder_exclude_keywords' ),
			'ExcludeCompanyNames' => get_option( 'job_manager_career_builder_exclude_company' ),
			'ExcludeJobTitles'    => get_option( 'job_manager_career_builder_exclude_job_title' ),
			'JobTitle'            => get_option( 'job_manager_career_builder_job_title' ),
			'CompanyName'         => get_option( 'job_manager_career_builder_company' ),
			'CountryCode'         => get_option( 'job_manager_career_builder_country' ),
			'Keywords'            => '',
			'BooleanOperator'     => get_option( 'job_manager_career_builder_default_keywords_operator', 'AND' ),
			'Location'            => '',
			'PerPage'             => 10,
			'OrderBy'             => 'Relevance', // Valid values are: Date, Pay, Title, Company, Distance, Location, and Relevance.
			'PageNumber'          => 1,
			'Radius'              => 20,
			'Tags'                => '',
			'Category'            => '',
			'EmpType'             => ''
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
	 * Format country
	 * @param  string $keyword
	 * @return string
	 */
	private static function format_arg_countrycode( $country ) {
		if ( 'GB' === $country ) {
			$country = 'UK';
		}
		return $country;
	}

	/**
	 * Format job type
	 * @param  string $keyword
	 * @return string
	 */
	private static function format_arg_emptype( $types ) {
		return implode( ',', array_intersect( array( 'JTCT', 'JTSE', 'JTIN', 'JTPT', 'JTFT' ), array_map( 'strtoupper', array_map( 'trim', explode( ',', $types ) ) ) ) );
	}

	/**
	 * Return job in standard format
	 * @param  array $raw_job
	 * @return object
	 */
	private static function format_job( $raw_job ) {
		$job = array(
			'title'           => sanitize_text_field( (string) $raw_job['JobTitle'] ),
			'company'         => sanitize_text_field( (string) $raw_job['Company'] ),
			'tagline'         => sanitize_text_field( (string) $raw_job['DescriptionTeaser']  ),
			'url'             => esc_url_raw( (string) $raw_job['JobDetailsURL'] ),
			'location'        => sanitize_text_field( (string) $raw_job['Location'] ),
			'latitude'        => sanitize_text_field( (string) $raw_job['LocationLatitude'] ),
			'longitude'       => sanitize_text_field( (string) $raw_job['LocationLongitude'] ),
			'type'            => sanitize_text_field( (string) $raw_job['EmploymentType'] ),
			'type_slug'       => sanitize_title( (string) $raw_job['EmploymentType'] ),
			'timestamp'       => strtotime( (string) $raw_job['PostedDate'] ),
			'link_attributes' => array(),
			'logo'            => apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' )
		);
		return (object) $job;
	}

	/**
	 * Get catgories from API
	 * @return array
	 */
	public static function get_categories() {
		$args           = array( 'DeveloperKey' => get_option( 'job_manager_career_builder_key' ) );
		$transient_name = 'career_builder_categories';
		$categories     = array();

		if ( false === ( $results = get_transient( $transient_name ) ) ) {
			$results = array();
			$result  = wp_remote_get( self::$endpoint . 'categories?' . http_build_query( $args, '', '&' ), array( 'timeout' => 10 ) );

			if ( ! is_wp_error( $result ) && ! empty( $result['body'] ) ) {
				$results = $result['body'];
				set_transient( $transient_name, $results, WEEK_IN_SECONDS );
			}
		}

		$xml = simplexml_load_string( $results );

		if ( $xml && ! empty( $xml->Categories ) ) {
			$results_array = (array) $xml->Categories;
			foreach ( $results_array['Category'] as $result ) {
				$categories[ (string) $result->Code ] = (string) $result->Name;
			}
		} else {
			delete_transient( $transient_name );
		}

		return $categories;
	}

	/**
	 * Get jobs from the API
	 * @return array()
	 */
	public static function get_jobs( $args ) {
		$args           = self::format_args( wp_parse_args( $args, self::get_default_args() ) );
		$transient_name = 'career_builder_' . md5( json_encode( $args ) );
		$total_pages    = 0;
		$total_jobs     = 0;
		$jobs           = array();

		if ( false === ( $results = get_transient( $transient_name ) ) ) {
			$results = array();
			$result  = wp_remote_get( self::$endpoint . 'jobsearch?' . http_build_query( $args, '', '&' ), array( 'timeout' => 10 ) );

			if ( ! is_wp_error( $result ) && ! empty( $result['body'] ) ) {
				$results = $result['body'];
				set_transient( $transient_name, $results, ( 60 * 60 * 24 ) );
			}
		}

		$xml = simplexml_load_string( $results );

		if ( $xml && ! empty( $xml->Results ) ) {
			$results_array = (array) $xml->Results;
			if ( ! is_array( $results_array['JobSearchResult'] ) ) {
				$results_array['JobSearchResult'] = array( $results_array['JobSearchResult'] );
			}
			foreach ( $results_array['JobSearchResult'] as $result ) {
				$title = (string) $result->JobTitle;
				if ( ! empty( $title ) ) {
					$jobs[] = self::format_job( (array) $result );
				}
			}
			$total_pages    = absint( $xml->TotalPages );
			$total_jobs     = absint( $xml->TotalCount );
		} else {
			delete_transient( $transient_name );
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