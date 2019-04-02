<?php
/*
Plugin Name: WP Job Manager - Job Tags
Plugin URI: https://wpjobmanager.com/add-ons/job-tags/
Description: Adds tags to Job Manager for tagging jobs with requried Skills and Technologies. Also adds some extra shortcodes. Requires Job Manager 1.14.0+
Version: 1.3.6
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 4.2

	Copyright: 2013 Mike Jolley
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

/**
 * WP_Job_Manager_Job_Tags class.
 */
class WP_Job_Manager_Job_Tags extends WPJM_Updater {

	/**
	 * __construct function.
	 */
	public function __construct() {
		define( 'JOB_MANAGER_TAGS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'job_manager_settings', array( $this, 'settings' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'job_tag_field' ) );
		add_filter( 'submit_job_form_validate_fields', array( $this, 'validate_job_tag_field' ), 10, 3 );
		add_action( 'job_manager_update_job_data', array( $this, 'save_job_tag_field' ), 10, 2 );
		add_action( 'submit_job_form_fields_get_job_data', array( $this, 'get_job_tag_field_data' ), 10, 2 );
		add_filter( 'the_job_description', array( $this, 'display_tags' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_filter( 'format_job_tag', array( 'WP_Job_Manager_Job_Tags', 'format_job_tag' ) );

		// Feeds
		add_filter( 'job_feed_args', array( $this, 'job_feed_args' ) );

		// Add column to admin
		add_filter( 'manage_edit-job_listing_columns', array( $this, 'columns' ), 20 );
		add_action( 'manage_job_listing_posts_custom_column', array( $this, 'custom_columns' ), 2 );

		// Includes
		include( 'includes/class-job-manager-job-tags-shortcodes.php' );

		$this->init_updates( __FILE__ );
	}

	/**
	 * CSS
	 */
	public function frontend_scripts() {
		wp_enqueue_style( 'wp-job-manager-tags-frontend', JOB_MANAGER_TAGS_PLUGIN_URL . '/assets/css/style.css' );
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-tags' );
		load_textdomain( 'wp-job-manager-tags', WP_LANG_DIR . "/wp-job-manager-tags/wp-job-manager-tags-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-tags', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_taxonomy() {
		if ( taxonomy_exists( "job_listing_tag" ) ) {
			return;
		}

		$singular         = __( 'Job Tag', 'wp-job-manager-tags' );
		$plural           = __( 'Job Tags', 'wp-job-manager-tags' );
		$admin_capability = 'manage_job_listings';

		register_taxonomy( "job_listing_tag",
	        array( "job_listing" ),
	        array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> $plural,
	            'labels' => array(
                    'name' 				=> $plural,
                    'singular_name' 	=> $singular,
                    'search_items' 		=> sprintf( __( 'Search %s', 'wp-job-manager-tags' ), $plural ),
                    'all_items' 		=> sprintf( __( 'All %s', 'wp-job-manager-tags' ), $plural ),
                    'parent_item' 		=> sprintf( __( 'Parent %s', 'wp-job-manager-tags' ), $singular ),
                    'parent_item_colon' => sprintf( __( 'Parent %s:', 'wp-job-manager-tags' ), $singular ),
                    'edit_item' 		=> sprintf( __( 'Edit %s', 'wp-job-manager-tags' ), $singular ),
                    'update_item' 		=> sprintf( __( 'Update %s', 'wp-job-manager-tags' ), $singular ),
                    'add_new_item' 		=> sprintf( __( 'Add New %s', 'wp-job-manager-tags' ), $singular ),
                    'new_item_name' 	=> sprintf( __( 'New %s Name', 'wp-job-manager-tags' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> apply_filters( 'enable_job_tag_archives', get_option( 'job_manager_enable_tag_archive' ) ),
	            'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),
	            'rewrite' 				=> array( 'slug' => _x( 'job-tag', 'permalink', 'wp-job-manager-tags' ), 'with_front' => false ),
	        )
	    );
	}

	/**
	 * Add Settings
	 * @param  array $settings
	 * @return array
	 */
	public function settings( $settings = array() ) {
		$settings['job_listings'][1][] = array(
			'name' 		=> 'job_manager_enable_tag_archive',
			'std' 		=> '',
			'label' 	=> __( 'Tag Archives', 'wp-job-manager-tags' ),
			'cb_label'  => __( 'Enable Tag Archives', 'wp-job-manager-tags' ),
			'desc'		=> __( 'Enabling tag archives will make job tags (inside jobs and tag clouds) link through to an archive of all jobs with said tag. Please note, tag archives will look like your post archives unless you create a special template to handle the display of job listings called <code>taxonomy-job_listing_tag.php</code> inside your theme. See <a href="http://codex.wordpress.org/Template_Hierarchy#Custom_Taxonomies_display">Template Hierarchy</a> for more information.', 'wp-job-manager-tags' ),
			'type'      => 'checkbox'
		);
		$settings['job_listings'][1][] = array(
			'name'       => 'job_manager_tags_filter_type',
			'std'        => 'any',
			'label'      => __( 'Tags Filter Type', 'wp-job-manager-tags' ),
			'desc'       => __( 'Determines how jobs are queried when selecting tags.', 'wp-job-manager-tags' ),
			'type'       => 'select',
			'options' => array(
				'any' => __( 'Jobs will be shown if within ANY chosen tag', 'wp-job-manager-tags' ),
				'all' => __( 'Jobs will be shown if within ALL chosen tags', 'wp-job-manager-tags' ),
			)
		);
		$settings['job_submission'][1][] = array(
			'name' 		=> 'job_manager_max_tags',
			'std' 		=> '',
			'label' 	=> __( 'Maximum Job Tags', 'wp-job-manager-tags' ),
			'desc'		=> __( 'Enter the number of tags per job submission you wish to allow, or leave blank for unlimited tags.', 'wp-job-manager-tags' ),
			'type'      => 'input'
		);
		$settings['job_submission'][1][] = array(
			'name' 		=> 'job_manager_tag_input',
			'std' 		=> '',
			'label' 	=> __( 'Tag Input', 'wp-job-manager-tags' ),
			'options'   => array(
				''            => 'Text box (comma select tags)',
				'multiselect' => 'Multiselect (list of pre-defined tags)',
				'checkboxes'  => 'Checkboxes (list of pre-defined tags)'
			),
			'desc'		=> '',
			'type'      => 'select'
		);
		return $settings;
	}

	/**
	 * Add the job tag field to the submission form
	 * @return array
	 */
	public function job_tag_field( $fields ) {
		if ( $max = get_option( 'job_manager_max_tags' ) ) {
			$max = ' ' . sprintf( __( 'Maximum of %d.', 'wp-job-manager-tags' ), $max );
		}

		switch ( get_option( 'job_manager_tag_input' ) ) {
			case "multiselect" :
				$fields['job']['job_tags'] = array(
					'label'       => __( 'Job tags', 'wp-job-manager-tags' ),
					'description' => __( 'Choose some tags, such as required skills or technologies, for this job.', 'wp-job-manager-tags' ) . $max,
					'placeholder' => __( 'Choose some tags&hellip;', 'wp-job-manager-tags' ),
					'type'        => 'term-multiselect',
					'taxonomy'    => 'job_listing_tag',
					'required'    => false,
					'priority'    => "4.5"
				);
			break;
			case "checkboxes" :
				$fields['job']['job_tags'] = array(
					'label'       => __( 'Job tags', 'wp-job-manager-tags' ),
					'description' => __( 'Choose some tags, such as required skills or technologies, for this job.', 'wp-job-manager-tags' ) . $max,
					'type'        => 'term-checklist',
					'taxonomy'    => 'job_listing_tag',
					'required'    => false,
					'priority'    => "4.5"
				);
			break;
			default :
				$fields['job']['job_tags'] = array(
					'label'       => __( 'Job tags', 'wp-job-manager-tags' ),
					'description' => __( 'Comma separate tags, such as required skills or technologies, for this job.', 'wp-job-manager-tags' ) . $max,
					'type'        => 'text',
					'required'    => false,
					'placeholder' => __( 'e.g. PHP, Social Media, Management', 'wp-job-manager-tags' ),
					'priority'    => "4.5"
				);
			break;
		}

		return $fields;
	}

	/**
	 * validate fields
	 * @param  bool $passed
	 * @param  array $fields
	 * @param  array $values
	 * @return bool on success, wp_error on failure
	 */
	public function validate_job_tag_field( $passed, $fields, $values ) {
		$max  = get_option( 'job_manager_max_tags' );
		$tags = is_array( $values['job']['job_tags'] ) ? $values['job']['job_tags'] : array_filter( explode( ',', $values['job']['job_tags'] ) );

		if ( $max && sizeof( $tags ) > $max )
			return new WP_Error( 'validation-error', sprintf( __( 'Please enter no more than %d tags.', 'wp-job-manager-tags' ), $max ) );

		return $passed;
	}

	/**
	 * Format a tag
	 */
	public static function format_job_tag( $tag ) {
		// We'll assume that small tags less than or equal to 3 chars are abbreviated. Uppercase them.
		if ( strlen( $tag ) <= 3 ) {
			$tag = strtoupper( $tag );
		} else {
			$tag = strtolower( $tag );
		}
		return $tag;
	}

	/**
	 * Save posted tags to the job
	 */
	public function save_job_tag_field( $job_id, $values ) {
		switch ( get_option( 'job_manager_tag_input' ) ) {
			case "multiselect" :
			case "checkboxes" :
				$tags = array_map( 'absint', $values['job']['job_tags'] );
			break;
			default :
				if ( is_array( $values['job']['job_tags'] ) ) {
					$tags = array_map( 'absint', $values['job']['job_tags'] );
				} else {
					$raw_tags = array_filter( array_map( 'sanitize_text_field', explode( ',', $values['job']['job_tags'] ) ) );

					// Loop tags we want to set and put them into an array
					$tags = array();

					foreach ( $raw_tags as $tag ) {
						$tags[] = apply_filters( 'format_job_tag', $tag );
					}
				}
			break;
		}

		if ( ! empty( $tags ) ) {
			wp_set_object_terms( $job_id, $tags, 'job_listing_tag', false );
		}
	}

	/**
	 * Get Job Tags for the field when editing
	 * @param  object $job
	 * @param  class $form
	 */
	public function get_job_tag_field_data( $data, $job ) {
		switch ( get_option( 'job_manager_tag_input' ) ) {
			case "multiselect" :
			case "checkboxes" :
				$data[ 'job' ][ 'job_tags' ]['value'] = wp_get_object_terms( $job->ID, 'job_listing_tag', array( 'fields' => 'ids' ) );
			break;
			default :
				$data[ 'job' ][ 'job_tags' ]['value'] = implode( ', ', wp_get_object_terms( $job->ID, 'job_listing_tag', array( 'fields' => 'names' ) ) );
			break;
		}
		return $data;
	}

	/**
	 * Show tags on job pages
	 * @return string
	 */
	public function display_tags( $content ) {
		global $post;

		if ( $terms = $this->get_job_tag_list( $post->ID ) ) {
			$content .= '<p class="job_tags">' . __( 'Tagged as:', 'wp-job-manager-tags' ) . ' ' . $terms . '</p>';
		}

		return $content;
	}

	/**
	 * Add a job tag column to admin
	 * @return array
	 */
	public function columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			if ( $key == 'job_listing_category' )
				$new_columns['job_tags'] = __( 'Tags', 'wp-job-manager-tags' );

			$new_columns[ $key ] = $value;
		}

		return $new_columns;
	}

	/**
	 * Handle display of new column
	 * @param  string $column
	 */
	public function custom_columns( $column ) {
		global $post;

		if ( $column == 'job_tags' ) {
			if ( ! $terms = $this->get_job_tag_list( $post->ID ) )
				echo '<span class="na">&ndash;</span>';
			else
				echo $terms;
		}
	}

	/**
	 * Gets a formatted list of job tags for a post ID
	 * @return string
	 */
	public function get_job_tag_list( $job_id ) {
		$terms = get_the_term_list( $job_id, 'job_listing_tag', '', apply_filters( 'job_manager_tag_list_sep', ', ' ), '' );

		if ( ! apply_filters( 'enable_job_tag_archives', get_option( 'job_manager_enable_tag_archive' ) ) )
			$terms = strip_tags( $terms );

		return $terms;
	}

	/**
	 * Tag support for feeds
	 * @param  [type] $args
	 * @return [type]
	 */
	public function job_feed_args( $args ) {
		if ( ! empty( $_GET['job_tags'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_listing_tag',
				'field'    => 'slug',
				'terms'    => explode( ',', sanitize_text_field( $_GET['job_tags'] ) )
			);
		}

		return $args;
	}
}

$GLOBALS['job_manager_tags'] = new WP_Job_Manager_Job_Tags();
