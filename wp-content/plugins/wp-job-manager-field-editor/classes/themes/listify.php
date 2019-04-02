<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_Themes_Listify {

	public static $COMPAT_GIT_COMMIT = "srIe5+NUNu5VFTOIUN4OzgEE";

	function __construct() {

		add_filter( 'field_editor_output_options', array( $this, 'auto_output' ), 10, 2 );
		add_filter( 'job_manager_field_editor_admin_skip_fields', array( $this, 'admin_fields' ) );
		add_action( 'admin_notices', array( $this, 'check_directory_fields' ) );
		add_action( 'wp_ajax_jmfe_listify_dfd', array( $this, 'dismiss_directory_fields' ) );
	}

	function admin_fields( $fields ){

		$fields[] = 'gallery_images';

		return $fields;
	}

	function dismiss_directory_fields(){
		check_ajax_referer( 'jmfe-listify-dfd', 'nonce' );
		update_option( 'jmfe_listify_directory_fields_notice', true );
		die;
	}

	function check_directory_fields(){

		if( get_theme_mod( 'custom-submission', true ) || get_option( 'jmfe_listify_directory_fields_notice' ) ) return false;
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function ( $ ) {
				$('.jmfe-listify-dfd.notice.is-dismissible' ).on('click', '.notice-dismiss', function(e){
					$.post( ajaxurl, {
						'action': 'jmfe_listify_dfd',
						'nonce' : '<?php echo wp_create_nonce( "jmfe-listify-dfd" ); ?>',
					}, function () {} );
				});
			} );
		</script>
		<div class="jmfe-listify-dfd notice is-dismissible update-nag">
            <?php echo sprintf(__( 'When using <em>WP Job Manager Field Editor</em> with the <em>Listify</em> theme it is <strong>strongly</strong> recommended that you use/check/enable the <a href="%s" target="_blank">Directory Submission Fields</a>', 'wp-job-manager-field-editor' ), 'http://listify.astoundify.com/article/238-enable-job-manager-submission-fields'); ?>
        </div>
		<?php
	}


	/**
	 * Listify Theme custom action output areas
	 *
	 * Requires Listify 1.0.2 or newer
	 *
	 * @since @@since
	 *
	 * @param $current_options
	 * @param $type
	 *
	 * @return array|bool
	 */
	function auto_output( $current_options, $type ) {

		if( $type === 'company' ) $type = "job";
		if( $type === 'resume_fields' ) $type = "resume";

		$field_groups = ! empty( $type ) ? array( $type ) : array( 'job', 'resume' );

		$theme_version = WP_Job_Manager_Field_Editor_Integration::check_theme( 'listify', '1.0.2', 'version' );
		if( ! $theme_version ) return FALSE;

		$listify_options_job = array(
				'1.0.2' => array(
						'job_listing_listify_list_page'                    => '---' . __( "Listify Listing List", 'wp-job-manager-field-editor' ),
						'listify_content_job_listing_header_before'        => __( 'List Before Header', 'wp-job-manager-field-editor' ),
						'listify_content_job_listing_meta'                 => __( 'List Meta', 'wp-job-manager-field-editor' ),
						'listify_content_job_listing_header_after'         => __( 'List After Header', 'wp-job-manager-field-editor' ),
						'listify_content_job_listing_footer'               => __( 'List Footer', 'wp-job-manager-field-editor' ),
						'single_job_listing_listify'                       => '---' . __( "Listify Single Listing", 'wp-job-manager-field-editor' ),
						'listify_single_job_listing_meta'                  => __( 'Single Listing Meta', 'wp-job-manager-field-editor' ),
						'listify_single_job_listing_actions'               => __( 'Single Listing Actions', 'wp-job-manager-field-editor' ),
						'single_job_listing_below_location_map'            => __( 'Single Listing Below Location Map', 'wp-job-manager-field-editor' ),
						'single_job_listing_listify_widgets'               => '---' . __( "Listify Theme Widgets", 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_map_before'            => __( 'Single Listing Top of Map Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_map_after'             => __( 'Single Listing Bottom of Map Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_hours_before'          => __( 'Single Listing Top of Hours Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_author_after'          => __( 'Single Listing Bottom of Author Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_content_before'        => __( 'Single Listing Top of Main Content Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_content_after'         => __( 'Single Listing Bottom of Main Content Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_gallery_slider_before' => __( 'Single Listing Top of Gallery Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_gallery_slider_after'  => __( 'Single Listing Bottom of Gallery Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_tags_before'           => __( 'Single Listing Top of Tags Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_tags_after'            => __( 'Single Listing Bottom of Tags Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_video_before'          => __( 'Single Listing Top of Video Widget', 'wp-job-manager-field-editor' ),
						'listify_widget_job_listing_video_after'           => __( 'Single Listing Bottom of Video Widget', 'wp-job-manager-field-editor' ),
				),
		);

		foreach( $field_groups as $group ){

			if( ! isset( ${"listify_options_$group"} ) ) continue;

			foreach( ${"listify_options_$group"} as $version => $options ) {

				if( version_compare( $theme_version, $version, 'ge' ) ) {
					$current_options = array_merge( $current_options, $options );
				}

			}
		}

		return $current_options;

	}
}