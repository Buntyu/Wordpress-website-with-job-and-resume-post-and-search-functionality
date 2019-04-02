<?php

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class WP_Job_Manager_Field_Editor_Taxonomy
 *
 * @since 1.1.9
 *
 */
class WP_Job_Manager_Field_Editor_Taxonomy extends WP_Job_Manager_Field_Editor_Fields {

	/**
	 * Check to make sure taxonomy is registered to post_type
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $taxonomy
	 * @param $object_type
	 */
	function check( $taxonomy, $object_type ) {

		global $wp_taxonomies;

		// Taxonomy already registered to post type
		if ( in_array( $object_type, $wp_taxonomies[ $taxonomy ]->object_type ) )
			return true;

		$post_type_groups = array(
			'job'           => 'job_listing',
			'company'       => 'job_listing',
			'resume_fields' => 'resume'
		);

		if ( ! empty( $custom_field_config[ 'taxonomy' ] ) ) {

			if ( isset( $custom_field_config[ 'field_group' ] ) )
				$the_field_group = $custom_field_config[ 'field_group' ];

			if ( isset( $custom_field_config[ 'field_group_parent' ] ) )
				$the_field_group = $custom_field_config[ 'field_group_parent' ];

			if ( array_key_exists( $the_field_group, $post_type_groups ) )
				$term_object = $post_type_groups[ $the_field_group ];

			register_taxonomy_for_object_type( $custom_field_config[ 'taxonomy' ], $term_object );

		}

	}

}