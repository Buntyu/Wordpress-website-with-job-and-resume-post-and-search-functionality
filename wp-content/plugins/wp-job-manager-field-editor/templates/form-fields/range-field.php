<?php
	wp_enqueue_script( 'jmfe-range-field' );
	$name = esc_attr( isset($field['name']) ? $field['name'] : $key );
	$min = isset( $field['min'] ) && is_numeric( $field['min'] ) ? intval($field['min']) : 0;
	$max = isset( $field['max'] ) && is_numeric( $field['max'] ) ? intval($field['max']) : 10;
	$step = isset( $field['step'] ) && is_numeric( $field['step'] ) ? intval($field['step']) : 1;
	$prepend = isset( $field['prepend'] ) ? esc_attr( $field['prepend'] ) : '';
	$append = isset( $field['append'] ) ? esc_attr( $field['append'] ) : '';
	$value = isset( $field['value'] ) ? esc_attr($field['value']) : '';

	// Set value to default if there is no value, or if the value is not a number
	if( isset( $field['default'] ) && ! is_numeric( $value ) ) $value = intval( $field['default'] );
	// If you want to show min/max before and after slider, need to use this filter to do so
	$show_min_max = apply_filters( 'field_editor_range_input_show_min_max', FALSE );
?>
<div class="jmfe-input-range-wrapper">

	<?php if( $show_min_max ): ?><span id="<?php echo esc_attr( $key ); ?>-min" class="jmfe-input-range-value-min"><?php echo $min; ?></span><?php endif; ?>
	<input type="range" data-prepend="<?php echo $prepend; ?>" data-append="<?php echo $append; ?>" class="input-range jmfe-input-range" name="<?php echo $name; ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo intval($value); ?>" title="<?php echo isset($field['title']) ? esc_attr( $field['title'] ) : ''; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> />
	<?php if($show_min_max): ?><span class="jmfe-input-range-value-max" id="<?php echo esc_attr( $key ); ?>-max"><?php echo $max; ?></span><?php endif; ?>

	<output for="<?php echo $name; ?>" id="<?php echo esc_attr( $key ); ?>-output" class="jmfe-input-range-value" style="position: relative; display: inline-block; margin-left: 10px; vertical-align: top;">
	</output>

</div>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>
