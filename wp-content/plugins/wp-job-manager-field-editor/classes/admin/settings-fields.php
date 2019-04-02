<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Field_Editor_Settings_Fields {

	/**
	 * CheckBox Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function checkbox_field( $a ) {

		$o       = $a[ 'option' ];
		$checked = checked( $a[ 'value' ], 1, FALSE );

		echo "<label><input id=\"{$o['name']}\" type=\"checkbox\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" value=\"1\"  {$a['attributes']} {$checked} /> {$o['cb_label']} </label>";
		$this->description( $o );

	}

	/**
	 * CheckBoxes Field
	 *
	 *
	 * @since 1.3.6
	 *
	 * @param $a
	 */
	function checkboxes_field( $a ) {

		$o       = $a['option'];

		$boxnum = 0;
		foreach( $o['options'] as $key => $config ){
			$default_checked = isset( $config['std'] ) && $a['value'] === FALSE ? ! empty( $config['std'] ) : false;
			$checked = is_array( $a['value'] ) ? checked( in_array( $key, $a['value'] ), TRUE, FALSE ) : checked( $default_checked, TRUE, FALSE );
			echo "<label style=\"margin-right: 5px;\"><input id=\"{$o['name']}_{$key}\" type=\"checkbox\" class=\"{$a['field_class']}\" name=\"{$o['name']}[]\" value=\"{$key}\"  {$a['attributes']} {$checked} /> {$config['label']} </label>";
			$boxnum++;
		}

		$this->description( $o );

	}

	/**
	 * Dump Debug Data
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function debug_dump_field( $a ) {

		$this->fields()->dump_array( $this->field_data() );

	}

	/**
	 * Default Header
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $args
	 */
	function default_header( $args ) {

	}

	/**
	 * About Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function about_field( $a ){
	?>

		<p><strong>Version:</strong> <?php echo WPJM_FIELD_EDITOR_VERSION; ?></p>
		<p><strong>Author:</strong> Myles McNamara</p>
		<br>
		<p>Did you know im also the Founder and CEO of Host Tornado?</p>
		<p><a href="https://plugins.smyl.es/contact/" target="_blank">Contact me</a> for an exclusive sMyles Plugins customer promo code discount for any shared SSD (Solid State Drive) hosting packages!  Data centers in Florida USA, Arizona USA, Montreal Canada, and France. Your site will run faster than it ever has, or your money back!</p>

	<?php
	}

	/**
	 * Support Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function support_field( $a ) {

		?>

		<p>
			Currently the best way to report any issues you are having or get support with this plugin is to submit a support ticket via<br />
			your <a href="https://plugins.smyl.es/" target="_blank">sMyles Plugins</a> account.  This will get you the quickest support possible and will allow me to track any support issues.
			<br /><br />
			You can submit a new support ticket here:<br />
			<a href="https://plugins.smyl.es/support/new/" target="_blank">https://plugins.smyl.es/support/new/</a> <small>( opens in new window )</small>
		</p>
		<br />
		<p>
			You can also view any documentation available for this plugin on my website as well:<br />
			<a href="https://plugins.smyl.es/docs/" target="_blank">https://plugins.smyl.es/docs/</a>
		</p>

	<?php
	}

	/**
	 * Backup Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function backup_field( $a ) {

		$o = $a[ 'option' ];
		$url = get_admin_url();

		echo "<input type=\"hidden\" name=\"content\" value=\"jmfe_custom_fields\" />";
		echo "<input type=\"hidden\" name=\"download\" value=\"true\" />";
		echo "<button formmethod=\"GET\" formaction=\"{$url}export.php\" id=\"{$o['name']}\" name=\"button_submit\" value=\"{$o['action']}\" type=\"submit\" class=\"button {$a['field_class']}\" {$a['attributes']}>{$o['caption']}</button>";
		$this->description( $o );

	}

	/**
	 * Button Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function button_field( $a ) {

		$o = $a[ 'option' ];
		if ( ! $this->field_data() && $o['name'] == "jmfe_remove_all" ){
			_e( 'No fields to remove!  A button will appear here when there is field data you can remove.', 'wp-job-manager-field-editor' );
			return;
		}

		echo "<button id=\"{$o['name']}\" name=\"button_submit\" value=\"{$o['action']}\" type=\"submit\" class=\"button {$a['field_class']}\" {$a['attributes']}>{$o['caption']}</button>";
		$this->description( $o );

	}

	/**
	 * Link Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function link_field( $a ) {

		$o = $a[ 'option' ];

		echo "<a id=\"{$o['name']}\" href=\"{$o['href']}\" class=\"{$a['field_class']}\" {$a['attributes']}>{$o['caption']}</a>";
		$this->description( $o );

	}

	/**
	 * Select Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function select_field( $a ) {

		$o = $a[ 'option' ];

		echo "<select id=\"{$o['name']}\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" {$a['attributes']}>";

		foreach ( $o[ 'options' ] as $key => $name ) {
			$value    = esc_attr( $key );
			$label    = esc_attr( $name );
			$selected = isset($a['value']) ? selected( $a[ 'value' ], $key, FALSE ) : false;

			echo "<option value=\"{$value}\" {$selected}> {$label} </option>";
		}

		echo "</select>";
		$this->description( $o );

	}

	/**
	 * Textarea Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function textarea_field( $a ) {

		$o = $a[ 'option' ];

		echo "<textarea cols=\"50\" rows=\"3\" id=\"{$o['name']}\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" {$a['attributes']}>";
		echo esc_textarea( $o[ 'value' ] );
		echo "</textarea>";
		$this->description( $o );

	}

	/**
	 * Textbox Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $a
	 */
	function textbox_field( $a ) {

		$o = $a[ 'option' ];

		echo "<input id=\"{$o['name']}\" type=\"text\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" value=\"{$a['value']}\" {$a['placeholder']} {$a['attributes']} />";
		$this->description( $o );

	}

	/**
	 * License Key
	 *
	 *
	 * @since @@since
	 *
	 * @param $a
	 */
	function license_email_field( $a ) {

		$o = $a[ 'option' ];

		//delete_option( 'wp-job-manager-field-editor_email' );

		if( empty( $a['value'] ) ){
			echo "<input id=\"{$o['name']}\" type=\"text\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" value=\"{$a['value']}\" {$a['placeholder']} {$a['attributes']} />";
			$this->description( $o );
		} else {
			echo $a['value'];
		}

	}

	/**
	 * License Key
	 *
	 *
	 * @since @@since
	 *
	 * @param $a
	 */
	function license_key_field( $a ) {

		$o = $a['option'];

		if( empty($a['value']) ) {
			echo "<input id=\"{$o['name']}\" type=\"text\" class=\"{$a['field_class']}\" name=\"{$o['name']}\" value=\"{$a['value']}\" {$a['placeholder']} {$a['attributes']} />";
			$this->description( $o );
		} else {
			$url = get_admin_url();
			echo "<button formmethod=\"GET\" formaction=\"{$url}edit.php?post_type=job_listing&page=field-editor-settings\" id=\"wp-job-manager-field-editor_deactivate_licence\" name=\"wp-job-manager-field-editor_deactivate_licence\" value=\"true\" type=\"submit\" class=\"button {$a['field_class']}\" {$a['attributes']}>" . __('Deactivate License', 'wp-job-manager-field-editor') . "</button>";
		}
	}

	/**
	 * Description Field
	 *
	 *
	 * @since 1.1.9
	 *
	 * @param $o
	 */
	function description( $o ) {

		if ( ! empty( $o[ 'desc' ] ) ) echo "<p class=\"description\">{$o['desc']}</p>";

	}

}