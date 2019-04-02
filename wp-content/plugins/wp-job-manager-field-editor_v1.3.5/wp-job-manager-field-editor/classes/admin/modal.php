<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_Field_Editor_Modal
 *
 * @since 1.1.9
 *
 */
Class WP_Job_Manager_Field_Editor_Modal extends WP_Job_Manager_Field_Editor_Fields {

	// Start Workspace
	public    $modal_title;
	protected $list_field_group;
	protected $modal_fields = array();


	/**
	 * @param string $modal_title
	 */
	function __construct( $modal_title = NULL, $list_field_group = NULL ) {

		if ( $list_field_group ) {
			$this->list_field_group = $list_field_group;
		}
		$this->modal_title = $modal_title;
		$this->set_modal_fields();
	}

	/**
	 * Static Function to Return Modal Fields
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return array|mixed|void
	 */
	public static function get_modal_fields() {

		return self::set_modal_fields();

	}

	/**
	 * Set or use default Modal Fields
	 *
	 * @since 1.1.9
	 *
	 * @param array $fields
	 *
	 * @return array|mixed|void
	 */
	function set_modal_fields( $fields = array() ) {

		if ( empty( $fields ) ) {
			$this->modal_fields = array(
				'label'    => __( 'Configuration', 'wp-job-manager-field-editor' ),
				'id'       => '108101543',
				'master'   => 'meta_key',
				'tabs'     => array(
					'config'   => array(
						'label'  => __( 'Config', 'wp-job-manager-field-editor' ),
						'fields' => array(
							'meta_key'    => array(
								'label'       => __( 'Meta Key', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Should be something unique and lowercase, like <code>job_pay</code>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'default'     => '',
								'placeholder' => 'job_position_shift',
							),
							'type'        => array(
								'label'       => __( 'Type', 'wp-job-manager-field-editor' ),
								'placeholder' => __( 'Textbox, WP-Editor, Dropdown, Upload, etc.', 'wp-job-manager-field-editor' ),
								'caption'     => '',
								'type'        => 'dropdown',
								'default'     => $this->field_types()->get_field_types( FALSE, $this->list_field_group ),
							),
							'multiple' => array(
								'label'   => __( 'Multiple', 'wp-job-manager-field-editor' ),
								'caption' => __( 'Allow multiple files to be selected in select file window.', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'default' => '1||Enabled',
								'hidden'  => TRUE
							),
							'ajax' => array(
								'label'   => __( 'Ajax', 'wp-job-manager-field-editor' ),
								'caption' => __( 'Use built-in Ajax uploader', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'default' => '1||Enabled',
								'hidden'  => TRUE
							),
							'default'     => array(
								'label'       => __( 'Default', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Default item(s) to select, not required.', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'default'     => '',
								'placeholder' => '',
							),
							'taxonomy'    => array(
								'label'       => __( 'Taxonomy', 'wp-job-manager-field-editor' ),
								'caption'     => __( '<a target="_blank" href="http://codex.wordpress.org/Taxonomies">WordPress Taxonomy</a>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'default'     => '',
								'placeholder' => 'custom_taxonomy',
								'help'        => array(
									'icon' => 'question',
									'url'  => 'https://plugins.smyl.es/docs-kb/how-to-createadd-a-custom-taxonomy-to-use-with-checklist-dropdown-or-multiselect/'
								),
							),
							'label'       => array(
								'label'       => __( 'Label', 'wp-job-manager-field-editor' ),
								'caption'     => '',
								'type'        => 'textfield',
								'default'     => '',
								'placeholder' => __( 'This will be the label next to or above your field.', 'wp-job-manager-field-editor' )
							),
							'description' => array(
								'label'       => __( 'Description', 'wp-job-manager-field-editor' ),
								'caption'     => '',
								'type'        => 'textbox',
								'default'     => '',
								'placeholder' => __( 'This should be the help text below the field.', 'wp-job-manager-field-editor' )
							),
							'placeholder' => array(
								'label'       => __( 'Placeholder', 'wp-job-manager-field-editor' ),
								'caption'     => '',
								'type'        => 'textfield',
								'default'     => '',
								'placeholder' => __( 'This text you are reading.', 'wp-job-manager-field-editor' )
							),
							'priority'    => array(
								'label'       => __( 'Priority', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Highest number will be the last field on the form, can include decimal.', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'type'        => 'textfield',
								'placeholder' => '4.5'
							),
							'maxlength'    => array(
								'label'       => __( 'Max Length', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Max characters allowed in field (including spaces)', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'type'        => 'textfield',
								'placeholder' => '50',
								'hidden'      => TRUE
							),
							'admin_only'    => array(
								'label'   => __( 'Visibility', 'wp-job-manager-field-editor' ),
								'caption' => __( 'If enabled this field will not show on frontend.', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'default' => '1||Admin Only',
							),
							'required'    => array(
								'label'   => __( 'Required', 'wp-job-manager-field-editor' ),
								'caption' => '',
								'type'    => 'checkbox',
								'default' => '1||Required',
							),
						)
					),
					'options' => array(
						'label'  => __( 'Options', 'wp-job-manager-field-editor' ),
						'multiple' => true,
						'fields' => array(
							'option_value'     => array(
								'label'   => __( 'Value', 'wp-job-manager-field-editor' ),
								'caption' => __( '', 'wp-job-manager-field-editor' ),
								'type'    => 'textfield',
								'default' => '',
								'placeholder' => '',
								'multiple' => TRUE,
							),
							'option_label'   => array(
								'label'   => __( 'Label', 'wp-job-manager-field-editor' ),
								'caption' => __( '', 'wp-job-manager-field-editor' ),
								'type'    => 'textfield',
								'default' => '',
								'placeholder' => '',
								'multiple'  => true,
							),
							'option_default' => array(
								'label'   => __( 'Default Selection', 'wp-job-manager-field-editor' ),
								'caption' => __( '', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'class' => 'jmfe-option-default',
								'default' => '1||',
								'multiple' => TRUE,
								'template_style' => TRUE
							),
							'option_disabled' => array(
								'label'   => __( 'Disabled Option', 'wp-job-manager-field-editor' ),
								'caption' => __( '', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'class'   => 'jmfe-option-disabled',
								'default' => '1||',
								'multiple' => TRUE,
								'template_style' => TRUE
							)
						)
					),
					'output'   => array(
						'label'  => __( 'Output', 'wp-job-manager-field-editor' ),
						'help'   => array(
							'icon' => 'question',
							'url'  => 'https://plugins.smyl.es/docs-kb/field-output-configuration/'
						),
						'fields' => array(
							'output'               => array(
								'label'       => __( 'Output', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Automatically output on the Job/Resume listing.', 'wp-job-manager-field-editor' ),
								'type'        => 'dropdown',
								'placeholder' => __( 'Do not automatically output the value', 'wp-job-manager-field-editor' ),
								'default'     => $this->auto_output()->get_options( FALSE, $this->list_field_group ),
							),
							'output_as'            => array(
								'label'   => __( 'Output As', 'wp-job-manager-field-editor' ),
								'caption' => __( 'Choose what you want the value to be output as.', 'wp-job-manager-field-editor' ),
								'type'    => 'dropdown',
								'default' => $this->auto_output()->get_output_as( FALSE, $this->list_field_group ),
								'hidden'  => TRUE
							),
							'output_oembed_width'  => array(
								'label'       => __( 'oEmbed Width', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Set a specific width for oEmbed (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ) . __( '<strong>(optional)</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => '500',
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_oembed_height' => array(
								'label'       => __( 'oEmbed Height', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Set a specific height for oEmbed (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ) . __( '<strong>(optional)</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => '700',
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_video_allowdl' => array(
								'label'       => __( 'Allow Download', 'wp-job-manager-field-editor' ),
								'caption'     => __( "Will display a download link for browsers incompatible with HTML5 video.", 'wp-job-manager-field-editor' ),
								'type'        => 'checkbox',
								'default' => '1||',
								'hidden'      => TRUE
							),
							'output_video_poster' => array(
								'label'       => __( 'Poster URL', 'wp-job-manager-field-editor' ),
								'caption'     => __( "A URL for an image to show until the user plays or seeks. If not specified, the first frame of video will be used when it becomes available.", 'wp-job-manager-field-editor' ) . __( '<strong>(optional)</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => 'http://somedomain.com/video-poster.png',
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_video_height' => array(
								'label'       => __( 'Video Height', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Set a specific height for video (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ) . __( '<strong>(optional)</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => '700',
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_video_width' => array(
								'label'       => __( 'Video Width', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Set a specific width for video (in pixels), use only numbers do not include px.', 'wp-job-manager-field-editor' ) . __( '<strong>(optional)</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => '500',
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_check_true'    => array(
								'label'       => __( 'Checkbox True', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Custom caption to use if checkbox field type is checked.', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( 'Yes', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_check_false'   => array(
								'label'       => __( 'Checkbox False', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Custom caption to use if checkbox field type is not checked', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( 'No', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_caption'       => array(
								'label'       => __( 'Caption', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Choose what you want the value to be output as.', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( 'My Link', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_classes'       => array(
								'label'       => __( 'Classes', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Add any additional classes separated by spaces', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( 'my-class my-custom-class my-other-class', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_priority'      => array(
								'label'       => __( 'Priority', 'wp-job-manager-field-editor' ),
								'caption'     => __( '<strong>optional</strong>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( '1.5', 'wp-job-manager-field-editor' ),
								'default'     => '',
								'hidden'      => TRUE
							),
							'output_show_label'    => array(
								'label'   => __( 'Show Label', 'wp-job-manager-field-editor' ),
								'caption' => '',
								'type'    => 'checkbox',
								'default' => '1||Show Label',
								'hidden'  => TRUE
							),
						)
					),
					'populate' => array(
						'label'  => __( 'Populate', 'wp-job-manager-field-editor' ),
						'help'   => array(
							'icon' => 'question',
							'url'  => 'https://plugins.smyl.es/docs-kb/auto-populate-from-user-meta-feature/'
						),
						'footer' => array(
							'content' => __( 'You can view, edit, or add user meta using my free open source <strong><a target="_blank" href="https://wordpress.org/plugins/user-meta-display/">User Meta Display</a></strong> plugin.', 'wp-job-manager-field-editor' )
						),
						'fields' => array(
							'populate_enable'   => array(
								'label'   => __( 'Auto Populate', 'wp-job-manager-field-editor' ),
								'caption' => __( 'This box must be checked to enable auto populate.', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'default' => '1||Enable',
							),
							'populate_save'     => array(
								'label'   => __( 'Auto Save', 'wp-job-manager-field-editor' ),
								'caption' => __( 'Save the value (except default) when a listing is submitted, to the user\'s meta.', 'wp-job-manager-field-editor' ),
								'type'    => 'checkbox',
								'default' => '1||Enable',
							),
							'populate_default' => array(
								'label'       => __( 'Default', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Default value for logged in users.', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => '',
								'default'     => '',
							),
							'populate_meta_key' => array(
								'label'       => __( 'Meta Key', 'wp-job-manager-field-editor' ),
								'caption'     => __( 'Specify the <strong>USER</strong> meta key to auto populate this field from if it exists (and user is logged in).  If meta key is set and meta exists for user, it will take priority over default value.<br /><br />If using a meta key from WPJM or WPRM you <strong>must</strong> prepend it with an underscore.  <i>As example, company_website would be <code>_company_website</code></i>', 'wp-job-manager-field-editor' ),
								'type'        => 'textfield',
								'placeholder' => __( '_company_facebook', 'wp-job-manager-field-editor' ),
								'default'     => '',
							)
						)
					),
				),
				'multiple' => FALSE,
			);
		} else {
			$this->modal_fields = $fields;
		}

		// Handle packages tab in modal
		$packages = array();

		if ( defined( 'JOB_MANAGER_WCPL_VERSION' ) ) {
			// and if wcpl flow is set to before
			if ( 'before' === get_option( 'job_manager_paid_listings_flow' ) && in_array( $this->list_field_group, array('job', 'company') ) ) {
				$packages = WP_Job_Manager_Field_Editor_Package_WC::get_packages( FALSE, 'job' );
			} elseif ( 'before' === get_option( 'resume_manager_paid_listings_flow' ) && in_array( $this->list_field_group, array('resume', 'resume_fields') ) ) {
				$packages = WP_Job_Manager_Field_Editor_Package_WC::get_packages( FALSE, 'resume' );
			}
		}

		if( ! empty( $packages ) ) {
			$this->modal_fields['tabs']['packages'] = array(
					'label'  => __( 'Packages', 'wp-job-manager-field-editor' ),
					'help'   => array(
							'icon' => 'question',
							'url'  => 'https://plugins.smyl.es/docs-kb/showhide-specific-fields-based-on-selected-package/'
					),
					'fields' => array(
							'packages_require' => array(
									'label'   => __( 'Require', 'wp-job-manager-field-editor' ),
									'caption' => __( 'Require specific packages to display this field', 'wp-job-manager-field-editor' ),
									'type'    => 'checkbox',
									'default' => '1||Enable',
							),
							'packages_show'    => array(
									'label'   => __( 'Packages', 'wp-job-manager-field-editor' ),
									'caption' => __( 'Select packages you want this field to show for.  Require checkbox above must be enabled for this to work.', 'wp-job-manager-field-editor' ),
									'type'    => 'checkbox',
									'default' => $packages,
							)
					)
			);
		}

		return apply_filters( 'field_editor_default_modal_fields', $this->modal_fields);

	}

	static function theme_ver_check(){
		$message = get_option( 'theme_status_check_notice_msg' );
		if( empty( $message ) ) return false;
		$class = WP_Job_Manager_Field_Editor_Fields::check_characters(array(101, 114, 114, 111, 114));
		$msg_hndl = WP_Job_Manager_Field_Editor_Fields::check_characters( array(104,101,120,50,98,105,110));
		?><div class="<?php echo $class; ?>"><?php echo $msg_hndl( $message ) ?></div><?php
	}

	/**
	 * Loop through modal fields and output HTML
	 *
	 * @since 1.1.9
	 *
	 * @param $tab_group
	 */
	function build_modal_fields( $tab_group ) {

		ob_start();
		$required_fields = array( 'meta_key', 'type' );
		$fields          = $this->modal_fields;
		?>
		<div id="jmfe-<?php echo $tab_group; ?>-form">
		<table class="jmfe-modal-table form-table rowGroup groupitems <?php echo $tab_group; ?>-groupitems" id="groupitems" ref="items">
		<tbody>
		<?php
		foreach ( $fields[ 'tabs' ][ $tab_group ][ 'fields' ] as $field => $settings ) {
			//dump($settings);
			$hide_tr  = '';
			$hidden   = '';
			$dohidden = FALSE;
			$id       = 'field_' . $field;
			$fieldsid = $fields[ 'id' ];
			$name     = $field;
			$single   = TRUE;
			$label    = ( isset( $settings[ 'label' ] ) ? $settings['label'] : '');
			$caption  = ( isset( $settings[ 'caption' ] ) ? $settings['caption'] : '');
			$value    = ( isset( $settings[ 'default' ] ) ? $settings['default'] : '');
			$class    = ( isset( $settings[ 'class' ] ) ? $settings['class'] : '');
			$groupid  = $tab_group;

			if ( ! empty( $fields[ 'tabs' ][ $tab_group ][ 'multiple' ] ) ) {
				$name = "{$tab_group}[{$field}][0]";
			}

			if ( isset( $settings[ 'hidden' ] ) ) {
				$dohidden = (bool) $settings[ 'hidden' ];
			}

			if ( $dohidden ) {
				$hidden = 'display: none;';
			}

			echo "<tr class=\"jmfe-modal-field jmfe-modal-fields-{$tab_group} jmfe-modal-fields-{$tab_group}-{$field}" . $hide_tr . "\" valign=\"top\" id=\"jmfe-modal-" . $id . "-tr\" style=\"{$hidden}\">\r\n";
			echo "<th scope=\"row\">\r\n";
			echo "<label for=\"" . $id . "\">" . $label;

			if ( ! empty( $settings[ 'help' ] ) ) {
				$help_icon = $settings[ 'help' ][ 'icon' ];
				$help_url  = $settings[ 'help' ][ 'url' ];
				echo "<span class=\"jmfe-field-help-fa fa-stack\"><a target=\"_blank\" href=\"{$help_url}\"><i class=\"fa fa-circle fa-stack-2x\"></i><i class=\"fa fa-{$help_icon} fa-stack-1x fa-inverse\"></i></a></span>";
			}
			echo "</label>\r\n";
			echo "</th>\r\n";
			echo "<td class=\"jmfe-modal-{$id}-td\">\r\n";
			include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/includes/fields/' . $settings[ 'type' ] . '.php';
			if ( ! empty( $caption ) ) {
				echo "<p class=\"description\">" . $caption . "</p>\r\n";
			}
			echo "</td>\r\n";
			echo "</tr>\r\n";
		}

		$fields_html = ob_get_clean();
		echo $fields_html;

		echo "</tbody></table></div>\r\n";

		if ( ! empty( $fields[ 'tabs' ][ $tab_group ][ 'multiple' ] ) ) {
			echo "<div class=\"jmfe-field-add-group-row\"><button class=\"button jmfe-field-add-group-row-button\" type=\"button\" data-rowtemplate=\"group-" . $tab_group . "-tmpl\">" . __( 'Add Another', 'wp-job-manager-field-editor' ) . "</button></div>\r\n";
			echo "<script type=\"text/html\" id=\"group-" . $tab_group . "-tmpl\">\r\n";
			//echo '  <div class="button button-primary right jmfe-field-remove-group-row" style="margin:5px 5px 0;">' . __( 'Remove' ) . '</div>';
			echo "	<table class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
			echo "		<tbody>\r\n";
			foreach ( $fields[ 'tabs' ][ $tab_group ][ 'fields' ] as $field => $settings ) {
				//dump($settings);
				$id      = 'field_{{id}}_' . $field;
				$groupid = $tab_group;
				$name    = "{$tab_group}[{$field}][__count__]";
				$single  = TRUE;
				$label = '{{label_' . $field . '}}';
				$row_style = ( isset( $settings['template_style'] ) ? '{{style_' . $field . '}}' : '' );
				$caption = ( isset( $settings[ 'caption' ] ) ? $settings[ 'caption' ] : '' );
				$value = ( isset( $settings[ 'default' ] ) ? $settings[ 'default' ] : '' );
				$class = ( isset( $settings[ 'class' ] ) ? $settings[ 'class' ] : '' );
				echo "<tr class=\"jmfe-modal-field jmfe-modal-fields-{$tab_group}\" valign=\"top\" style=\"" . $row_style . "\" id=\"jmfe-modal-" . $id . "-tr\">\r\n";
				echo "<th scope=\"row\">\r\n";
				echo "<label for=\"" . $id . "\">" . $label . "</label>\r\n";
				echo "</th>\r\n";
				echo "<td class=\"jmfe-modal-{$id}-td\">\r\n";
				include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/includes/fields/' . $settings[ 'type' ] . '.php';
				if ( ! empty( $caption ) ) {
					echo "<p class=\"description\">" . $caption . "</p>\r\n";
				}
				echo "</td>\r\n";
				echo "</tr>\r\n";
				if( $field === 'option_disabled' ){
					echo "<tr class=\"jmfe-modal-field jmfe-modal-fields-{$tab_group}\" valign=\"top\" id=\"jmfe-modal-" . $tab_group . "-remove-tr\">\r\n";
					echo "<td class=\"jmfe-modal-{$tab_group}-remove-td\">\r\n";
					echo '  <div class="button button-primary right jmfe-field-remove-group-row">' . __( 'Remove', 'wp-job-manager-field-editor' ) . '</div>';
					echo '</td></tr>';
				}
			}
			echo "		</tbody>\r\n";
			echo "	</table>\r\n";
			echo "</script>";
		}
	}

	/**
	 * Output Modal HTML
	 *
	 * @since 1.1.9
	 *
	 */
	public function modal() {

		ob_start();

		?>

		<div tabindex="0" id="jmfe-modal-panel" class="hidden" style="display: none;">
			<div class="media-modal-backdrop"></div>
			<div class="jmfe-modal" data-action="new">
				<div class="jmfe-modal-content">
					<div class="jmfe-modal-header">
						<a title="Close" href="#" class="jmfe-modal-close media-modal-close">
							<span class="media-modal-icon"></span>
						</a>
						<div class="jmfe-modal-icon"><img src="<?php echo WPJM_FIELD_EDITOR_PLUGIN_URL; ?>/assets/images/wpjm.png"></div>
						<h2 class="jmfe-title">
							<span class="jmfe-title-large"><?php _e( "WP Job Manager", "wp-job-manager-field-editor" ); ?></span>
							<small class="jmfe-title-small"> <?php _e( "Field Editor", "wp-job-manager-field-editor" ); ?></small>
						</h2>
					</div>
					<div class="jmfe-modal-spin-wrapper"><div class="jmfe-spinner"><i class="fa fa-circle-o-notch fa-3x fa-spin"></i></div></div>
					<div class="jmfe-modal-other"><div class="jmfe-other"></div></div>
					<form id="jmfe-modal-form">
					<div class="jmfe-modal-body">
						<div class="jmfe-modal-config-nav">
							<ul>
								<?php
								$tabs = 0;
								foreach ( $this->modal_fields[ 'tabs' ] as $tab_group => $config ) {
									?>
									<li id="jmfe-tab-<?php echo $tab_group; ?>-li" class="jmfe-tab-nav-li <?php if ( $tabs == 0 ) {
										echo 'current';
									} ?>">
										<a class="jmfe-tab jmfe-tab-nav" id="jmfe-tab-<?php echo $tab_group; ?>" data-tabgroup="<?php echo $tab_group; ?>" href="#" title="<?php echo $config[ 'label' ]; ?>">
											<strong><?php echo $config[ 'label' ]; ?></strong>
										</a>
									</li>

									<?php
									$tabs ++;
								}
								?>
							</ul>
						</div>
						<div id="jmfe-modal-tab-content" class="jmfe-settings-config-content">
							<div class="jmfe-alert alert" style="display: none;"><div class="jmfe-alert-content"></div></div>
							<?php
							$sections = 0;
							foreach ( $this->modal_fields[ 'tabs' ] as $tab_group => $config ) {
								?>
								<div id="jmfe-tab-<?php echo $tab_group; ?>-group" class="jmfe-tab-content-group group" data-tabgroup="<?php echo $tab_group; ?>" style="<?php if ( $sections > 0 ) {
									echo 'display: none;';
								} ?>">
	                            <h3 class="sidetabs-config-header">
								<?php
								echo $config[ 'label' ];
								if ( ! empty( $config[ 'help' ] ) ) {
									$help_icon = $config[ 'help' ][ 'icon' ];
									$help_url  = $config[ 'help' ][ 'url' ];
									echo "<span class=\"jmfe-help-fa fa-stack\"><a target=\"_blank\" href=\"{$help_url}\"><i class=\"fa fa-circle fa-stack-2x\"></i><i class=\"fa fa-{$help_icon} fa-stack-1x fa-inverse\"></i></a></span>";
								}
								?>
	                            </h3>
								<div class="jmfe-modal-form jmfe-modal-form-group" id="rowplaceholder">
										<?php $this->build_modal_fields( $tab_group ); ?>
								</div>
									<?php
									if ( ! empty( $config[ 'footer' ] ) ):
										?>
										<div class="jmfe-modal-tab-footer" id="jmfe-modal-<?php echo $tab_group; ?>">
										<p><?php echo $config[ 'footer' ][ 'content' ]; ?></p>
									</div>

									<?php endif; ?>
							</div>
								<?php
								$sections ++;
							}
							?>
						</div>

					</div>
					</form>
					<div class="jmfe-modal-footer">
						<button class="button button-large jmfe-modal-close jmfe-secondary-button" id="jmfe-cancel"><?php _e( 'Cancel', 'wp-job-manager-field-editor' ); ?></button>
						<button class="button button-primary button-large jmfe-primary-button" id="jmfe-save-field"><?php _e( 'Save Field', 'wp-job-manager-field-editor' ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<?php

		ob_end_flush();

	}

}