<tr>
	<td class="sort-column">&nbsp;</td>
	<td>
		<input type="text" class="input-text" name="field_label[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $field['label'] ); ?>" />
	</td>
	<td class="field-type">
		<select name="field_type[<?php echo esc_attr( $index ); ?>]" class="field_type">
			<?php
			foreach ( $field_types as $key => $type ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
			}
			?>
		</select>
	</td>
	<td>
		<input type="text" class="input-text" name="field_description[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( isset( $field['description'] ) ? $field['description'] : '' ); ?>" placeholder="<?php _e( 'N/A', 'wp-job-manager-applications' ); ?>" />
	</td>
	<td class="field-options">
		<input type="text" class="input-text placeholder" name="field_placeholder[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( isset($field['placeholder']) ? $field['placeholder'] : '' ); ?>" placeholder="<?php _e( 'N/A', 'wp-job-manager-applications' ); ?>" />

		<input type="text" class="input-text options" name="field_options[<?php echo esc_attr( $index ); ?>]" placeholder="<?php _e( 'Pipe (|) separate options.', 'wp-job-manager-applications' ); ?>" value="<?php echo esc_attr( isset( $field['options'] ) ? implode( ' | ', $field['options'] ) : '' ); ?>" />

		<div class="file-options">
			<label class="multiple-files"><input type="checkbox" class="input-text" name="field_multiple_files[<?php echo esc_attr( $index ); ?>]" value="1" <?php checked( ! empty( $field['multiple'] ), true ); ?> /> <?php _e( 'Multiple Files?', 'wp-job-manager-applications' ); ?></label>
		</div>

		<span class="na">&ndash;</span>
	</td>
	<td class="field-rules">
		<div class="rules">
			<select name="field_rules[<?php echo esc_attr( $index ); ?>][]" multiple="multiple" style="width: 250px;">
				<?php
					$field['rules'] = array_filter( (array) ( isset( $field['rules'] ) ? $field['rules'] : array() ) );

					foreach( $field_rules as $optgroup => $rules ) {
						echo '<optgroup label="' . esc_attr( $optgroup ) . '">';
						foreach ( $rules as $key => $rule ) {
							if ( 'required' === $key ) {
								$selected = selected( ! empty( $field['required'] ), true, false );
							} else {
								$selected = selected( in_array( $key, $field['rules'] ), true, false );
							}
							echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $rule ) . '</option>';
						}
						echo '</optgroup>';
					}
				?>
			</select>
		</div>
		<span class="na">&ndash;</span>
	</td>
	<td class="field-actions">
		<a class="delete-field" href='#'>X</a>
	</td>
</tr>
