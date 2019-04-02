<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_Field_Editor_Sort
 *
 * @since 1.1.14
 *
 */
class WP_Job_Manager_Field_Editor_Sort {

	private $fields;
	private $orderby;
	private $order = 'asc';

	function __construct( $fields, $orderby, $order = 'asc' ) {

		if ( $fields ) $this->fields = $fields;
		if ( $orderby ) $this->orderby = $orderby;
		if ( $order ) $this->order = $order;

	}

	/**
	 * Reorder based on values by float (integer with decimals)
	 *
	 *
	 * @since 1.1.14
	 *
	 * @return array
	 */
	function float() {

		usort( $this->fields, array( &$this, "usort_float" ) );

		return $this->fields;
	}

	/**
	 * Reorder based on string values
	 *
	 *
	 * @since 1.1.14
	 *
	 * @return array
	 */
	function string() {

		usort( $this->fields, array( &$this, "usort_string" ) );

		return $this->fields;
	}

	/**
	 * Reorder decimal integers lowest to highest
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	function usort_float( $a, $b ) {

		$result = $a[ $this->orderby ] < $b[ $this->orderby ] ? - 1 : ( $a[ $this->orderby ] === $b[ $this->orderby ] ? 0 : 1 );
		return ( $this->order === 'asc' ) ? $result : - $result;

	}

	/**
	 * Reorder based on string values
	 *
	 *
	 * @since 1.1.14
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	function usort_string( $a, $b ) {

		$result = strcmp( $a[ $this->orderby ], $b[ $this->orderby ] ); //Determine sort order
		return ( $this->order === 'asc' ) ? $result : - $result;

	}

}