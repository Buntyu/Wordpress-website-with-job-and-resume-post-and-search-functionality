<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_Widget extends WP_Widget {

	private $output_as;
	private $jmfe;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		$this->init_fields();

		parent::__construct(
			'jmfe_widget',
			__( 'Custom Field', 'wp-job-manager-field-editor' ),
			array(
				'description' => __( 'Output a custom/default field value from WP Job Manager Field Editor on any listing that supports widgets.', 'wp-job-manager-field-editor' ),
			)
		);
	}

	function init_fields(){

		$auto_output = WP_Job_Manager_Field_Editor_Auto_Output::get_instance();
		$output_as = $auto_output->get_output_as( true );
		$this->output_as = $output_as;

	}

	/**
	 * Front-end display of widget (Widget Output)
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $config   Saved values from database.
	 */
	public function widget( $args, $config ) {

		if ( ! function_exists( 'the_custom_field' ) || ! function_exists( 'get_custom_field' ) ) return;
		if( empty( $config[ 'meta_key' ] ) ) return;
		$meta_key = $config[ 'meta_key' ];

		// Set output_classes key to extra_classes value to be compatible with function
		$config['output_classes'] = $config['extra_classes'];
		// Set output_caption = caption to be compatible with function
		$config['output_caption'] = $config['caption'];

		$custom_fields = $this->jmfe()->get_custom_fields( TRUE );
		foreach ( $custom_fields as $group => $fields ) {
			if ( array_key_exists( $meta_key, $fields ) ) {
				$config = array_merge( $custom_fields[ $group ][ $meta_key ], $config );
				break;
			}
		}

		// Exit if there is no value for field (to prevent widget output)
		$field_value = get_custom_field( $config[ 'meta_key' ], get_the_id(), $config );
		if( empty( $field_value ) ) return;

		$widget_styles = isset( $config['widget_styles'] ) && ! empty( $config['widget_styles'] ) ? true : false;

		if( $widget_styles ){
			echo $args[ 'before_widget' ];
			if ( ! empty( $config[ 'title' ] ) ) echo $args[ 'before_title' ] . apply_filters( 'widget_title', $config[ 'title' ] ) . $args[ 'after_title' ];
		}

		the_custom_field( $config[ 'meta_key' ], NULL, $config );

		if( $widget_styles ) echo $args[ 'after_widget' ];
	}

	/**
	 * Back-end widget form (Output admin widget options form)
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$widget_styles = ( isset( $instance[ 'widget_styles' ] ) && ! empty( $instance[ 'widget_styles' ] ) ) ? $instance[ 'widget_styles' ] : '0';
		$output_show_label = ( isset( $instance[ 'output_show_label' ] ) && ! empty( $instance['output_show_label']) ) ? $instance[ 'output_show_label' ] : '0';
		$meta_key = isset( $instance[ 'meta_key' ] ) ? $instance[ 'meta_key' ] : '';
		$output_as = isset( $instance[ 'output_as' ] ) ? $instance[ 'output_as' ] : 'text';
		$extra_classes = isset( $instance[ 'extra_classes' ] ) ? $instance[ 'extra_classes' ] : '';
		$caption = isset( $instance[ 'caption' ] ) ? $instance[ 'caption' ] : '';
		$output_oembed_width = isset( $instance[ 'output_oembed_width' ] ) ? $instance[ 'output_oembed_width' ] : '';
		$output_oembed_height = isset( $instance[ 'output_oembed_height' ] ) ? $instance[ 'output_oembed_height' ] : '';
		$output_check_true = isset( $instance[ 'output_check_true' ] ) ? $instance[ 'output_check_true' ] : '';
		$output_check_false = isset( $instance[ 'output_check_false' ] ) ? $instance[ 'output_check_false' ] : '';
		$output_video_width = isset( $instance[ 'output_video_width' ] ) ? $instance[ 'output_video_width' ] : '';
		$output_video_height = isset( $instance[ 'output_video_height' ] ) ? $instance[ 'output_video_height' ] : '';
		$output_video_allowdl = ( isset( $instance[ 'output_video_allowdl' ] ) && ! empty( $instance[ 'output_video_allowdl' ] ) ) ? $instance[ 'output_video_allowdl' ] : '1';
		$output_video_poster = isset( $instance[ 'output_video_poster' ] ) ? $instance[ 'output_video_poster' ] : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-job-manager-field-editor' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_styles' ); ?>"><?php _e( 'Use Widget Style:', 'wp-job-manager-field-editor' ); ?></label>
			<input class="checkbox" id="<?php echo $this->get_field_id( 'widget_styles' ); ?>" name="<?php echo $this->get_field_name( 'widget_styles' ); ?>" type="checkbox" value="<?php echo esc_attr( $widget_styles ); ?>" <?php checked( 1, $widget_styles, TRUE ); ?>>
		</p>
		<hr>
		<p>
			<label for="<?php echo $this->get_field_id( 'output_show_label' ); ?>"><?php _e( 'Show Label:', 'wp-job-manager-field-editor' ); ?></label>
			<input class="checkbox" id="<?php echo $this->get_field_id( 'output_show_label' ); ?>" name="<?php echo $this->get_field_name( 'output_show_label' ); ?>" type="checkbox" value="<?php echo esc_attr( $output_show_label ); ?>" <?php checked( 1, $output_show_label, true ); ?>>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><?php _e( 'Field Meta Key:', 'wp-job-manager-field-editor' ); ?></label>
			<select class='widefat' id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>" type="text">

				<?php
				$field_groups = $this->jmfe()->get_fields( null, 'all', FALSE );

				foreach( $field_groups as $group ){

					foreach ( $group as $field_mk => $field ) {
						$selected = ( ( $field_mk == $meta_key ) ? 'selected' : '' );

						echo "<option value=\"{$field_mk}\" {$selected}>";
						echo $field_mk;
						echo "</option>";
					}

				}

				?>

			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'extra_classes' ); ?>"><?php _e( 'Extra Classes:', 'wp-job-manager-field-editor' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'extra_classes' ); ?>" name="<?php echo $this->get_field_name( 'extra_classes' ); ?>" type="text" value="<?php echo esc_attr( $extra_classes ); ?>" placeholder="<?php _e( 'my-class my-class2', 'wp-job-manager-field-editor' ); ?>">
		</p>
		<p class="jmfe-widget-output-as">
			<label for="<?php echo $this->get_field_id( 'output_as' ); ?>"><?php _e( 'Output As:', 'wp-job-manager-field-editor' ); ?></label>
			<select class='widefat' id="<?php echo $this->get_field_id( 'output_as' ); ?>" name="<?php echo $this->get_field_name( 'output_as' ); ?>" type="text">

				<?php
				foreach ( $this->output_as as $type => $select_label ) {

					if ( FALSE !== strpos( $select_label, '---' ) ) {
						$dropdownLabel = str_replace( '---', '', $select_label );
						echo "<option style='text-indent: 5%;' class='disabled' disabled>--- {$dropdownLabel} ---</option>";
					} else {
						$selected = ( ( $type == $output_as ) ? 'selected' : '' );
						echo "<option value=\"{$type}\" {$selected}>";
						echo $select_label;
						echo "</option>";
					}
				}

				?>

			</select>

		</p>
		<div id="<?php echo $this->get_field_id( 'conditionals' ); ?>">
			<p id="<?php echo $this->get_field_id( 'con_link' ); ?>" style="<?php if ( $output_as != 'link' ) echo "display: none;"; ?>">
				<label for="<?php echo $this->get_field_id( 'caption' ); ?>"><?php _e( 'Caption:', 'wp-job-manager-field-editor' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'caption' ); ?>" name="<?php echo $this->get_field_name( 'caption' ); ?>" type="text" value="<?php echo esc_attr( $caption ); ?>" placeholder="<?php _e( 'Click Here!', 'wp-job-manager-field-editor' ); ?>">
				<small><?php _e('If you want to use a specific caption for the link enter it above. Leave blank to use the URL as the caption.', 'wp-job-manager-field-editor'); ?></small>
			</p>
			<p id="<?php echo $this->get_field_id( 'con_oembed' ); ?>" style="<?php if( $output_as != 'oembed' ) echo "display: none;"; ?>">
				<label for="<?php echo $this->get_field_id( 'output_oembed_height' ); ?>"><?php _e( 'oEmbed Height', 'wp-job-manager-field-editor' ); ?><small><strong> (<?php _e( 'Optional', 'wp-job-manager-field-editor' ); ?>):</strong></small></label>
				<input size="4" id="<?php echo $this->get_field_id( 'output_oembed_height' ); ?>" name="<?php echo $this->get_field_name( 'output_oembed_height' ); ?>" type="text" value="<?php echo esc_attr( $output_oembed_height ); ?>" placeholder="700">
				<br/>
				<small><?php _e( 'Set a specific width for oEmbed (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ); ?></small>
				<br />
				<label for="<?php echo $this->get_field_id( 'output_oembed_width' ); ?>"><?php _e( 'oEmbed Width', 'wp-job-manager-field-editor' ); ?><small><strong> (<?php _e( 'Optional', 'wp-job-manager-field-editor' ); ?>):</strong></small></label>
				<input size="4" id="<?php echo $this->get_field_id( 'output_oembed_width' ); ?>" name="<?php echo $this->get_field_name( 'output_oembed_width' ); ?>" type="text" value="<?php echo esc_attr( $output_oembed_width ); ?>" placeholder="500">
				<br />
				<small><?php _e('Set a specific width for oEmbed (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor'); ?></small>
			</p>
			<p id="<?php echo $this->get_field_id( 'con_video' ); ?>" style="<?php if ( $output_as != 'video' ) echo "display: none;"; ?>">
				<label for="<?php echo $this->get_field_id( 'output_video_allowdl' ); ?>"><?php _e( 'Allow Download:', 'wp-job-manager-field-editor' ); ?></label>
				<input class="checkbox" id="<?php echo $this->get_field_id( 'output_video_allowdl' ); ?>" name="<?php echo $this->get_field_name( 'output_video_allowdl' ); ?>" type="checkbox" value="<?php echo esc_attr( $output_video_allowdl ); ?>" <?php checked( 1, $output_video_allowdl, TRUE ); ?>>
				<br />
				<small><?php _e("Will display a download link for browsers incompatible with HTML5 video.", 'wp-job-manager-field-editor'); ?></small>
				<br /><br />
				<label for="<?php echo $this->get_field_id( 'output_video_poster' ); ?>"><?php _e( 'Poster URL:', 'wp-job-manager-field-editor' ); ?><small><strong> (<?php _e( 'Optional', 'wp-job-manager-field-editor' ); ?>):</strong></small></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'output_video_poster' ); ?>" name="<?php echo $this->get_field_name( 'output_video_poster' ); ?>" type="text" value="<?php echo esc_attr( $output_video_poster ); ?>" placeholder="http://somedomain.com/video-poster.png">
				<small><?php _e( "A URL for an image to show until the user plays or seeks. If not specified, the first frame of video will be used when it becomes available.", 'wp-job-manager-field-editor' ); ?></small>
				<br/><br/>
				<label for="<?php echo $this->get_field_id( 'output_video_height' ); ?>"><?php _e( 'Video Height', 'wp-job-manager-field-editor' ); ?><small><strong> (<?php _e( 'Optional', 'wp-job-manager-field-editor' ); ?>):</strong></small></label>
				<input size="4" id="<?php echo $this->get_field_id( 'output_video_height' ); ?>" name="<?php echo $this->get_field_name( 'output_video_height' ); ?>" type="text" value="<?php echo esc_attr( $output_video_height ); ?>" placeholder="700">
				<br />
				<small><?php _e( 'Set a specific height for video (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ); ?></small>
				<br />
				<label for="<?php echo $this->get_field_id( 'output_video_width' ); ?>"><?php _e( 'Video Width', 'wp-job-manager-field-editor' ); ?><small><strong> (<?php _e( 'Optional', 'wp-job-manager-field-editor' ); ?>):</strong></small></label>
				<input size="4" id="<?php echo $this->get_field_id( 'output_video_width' ); ?>" name="<?php echo $this->get_field_name( 'output_video_width' ); ?>" type="text" value="<?php echo esc_attr( $output_video_width ); ?>" placeholder="500">
				<br />
				<small><?php _e( 'Set a specific width for video (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ); ?></small>
			</p>
			<p id="<?php echo $this->get_field_id( 'con_checkcustom' ); ?>" style="<?php if ( $output_as != 'checkcustom' ) echo "display: none;"; ?>">
				<label for="<?php echo $this->get_field_id( 'output_check_true' ); ?>"><?php _e( 'Checkbox True:', 'wp-job-manager-field-editor' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'output_check_true' ); ?>" name="<?php echo $this->get_field_name( 'output_check_true' ); ?>" type="text" value="<?php echo esc_attr( $output_check_true ); ?>" placeholder="<?php _e( 'Yes', 'wp-job-manager-field-editor' ); ?>">
				<small><?php _e( 'Custom caption to use if checkbox field type is checked.', 'wp-job-manager-field-editor' ); ?></small>
				<br />
				<label for="<?php echo $this->get_field_id( 'output_check_false' ); ?>"><?php _e( 'Checkbox False:', 'wp-job-manager-field-editor' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'output_check_false' ); ?>" name="<?php echo $this->get_field_name( 'output_check_false' ); ?>" type="text" value="<?php echo esc_attr( $output_check_false ); ?>" placeholder="<?php _e( 'No', 'wp-job-manager-field-editor' ); ?>">
				<small><?php _e( 'Custom caption to use if checkbox field type is not checked.', 'wp-job-manager-field-editor' ); ?></small>
			</p>
		</div>
		<script>
			jQuery(function($){
				$( '#<?php echo $this->get_field_id( 'output_as' ); ?>' ).on( 'change', function () {
					$( '#<?php echo $this->get_field_id( 'conditionals' ); ?> p' ).hide();
					$( '#<?php echo $this->get_field_id( 'con_' ); ?>' + $(this).val() ).show();
				});
			});
		</script>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance            = array();
		$instance[ 'title' ] = ( ! empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
		$instance[ 'output_show_label' ] = isset( $new_instance[ 'output_show_label' ] ) ? '1' : '0';
		$instance[ 'widget_styles' ] = isset( $new_instance[ 'widget_styles' ] ) ? '1' : '0';
		$instance[ 'meta_key' ] = ( ! empty( $new_instance[ 'meta_key' ] ) ) ? strip_tags( $new_instance[ 'meta_key' ] ) : '';
		$instance[ 'output_as' ] = ( ! empty( $new_instance[ 'output_as' ] ) ) ? strip_tags( $new_instance[ 'output_as' ] ) : '';
		$instance[ 'extra_classes' ] = ( ! empty( $new_instance[ 'extra_classes' ] ) ) ? strip_tags( $new_instance[ 'extra_classes' ] ) : '';
		$instance[ 'caption' ] = ( ! empty( $new_instance[ 'caption' ] ) ) ? strip_tags( $new_instance[ 'caption' ] ) : '';
		$instance[ 'output_oembed_height' ] = ( ! empty( $new_instance[ 'output_oembed_height' ] ) ) ? strip_tags( $new_instance[ 'output_oembed_height' ] ) : '';
		$instance[ 'output_oembed_width' ] = ( ! empty( $new_instance[ 'output_oembed_width' ] ) ) ? strip_tags( $new_instance[ 'output_oembed_width' ] ) : '';
		$instance[ 'output_check_true' ] = ( ! empty( $new_instance[ 'output_check_true' ] ) ) ? strip_tags( $new_instance[ 'output_check_true' ] ) : '';
		$instance[ 'output_check_false' ] = ( ! empty( $new_instance[ 'output_check_false' ] ) ) ? strip_tags( $new_instance[ 'output_check_false' ] ) : '';
		$instance[ 'output_video_allowdl' ] = isset( $new_instance[ 'output_video_allowdl' ] ) ? '1' : '0';
		$instance[ 'output_video_poster' ] = ( ! empty( $new_instance[ 'output_video_poster' ] ) ) ? strip_tags( $new_instance[ 'output_video_poster' ] ) : '';
		$instance[ 'output_video_width' ] = ( ! empty( $new_instance[ 'output_video_width' ] ) ) ? strip_tags( $new_instance[ 'output_video_width' ] ) : '';
		$instance[ 'output_video_height' ] = ( ! empty( $new_instance[ 'output_video_height' ] ) ) ? strip_tags( $new_instance[ 'output_video_height' ] ) : '';

		return $instance;
	}

	/**
	 * WP_Job_Manager_Field_Editor_Fields Class Object
	 *
	 * @since 1.1.9
	 *
	 * @return WP_Job_Manager_Field_Editor_Fields
	 */
	private function jmfe() {
		if ( ! $this->jmfe ) $this->jmfe = WP_Job_Manager_Field_Editor_Fields::get_instance();
		return $this->jmfe;
	}

}

add_action( 'widgets_init', 'register_wp_job_manager_field_editor_widget' );

function register_wp_job_manager_field_editor_widget() {

	register_widget( 'WP_Job_Manager_Field_Editor_Widget' );

}