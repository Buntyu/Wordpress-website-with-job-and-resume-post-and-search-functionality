<?php
	if( ! isset( $admin ) || ! $admin ){
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'jmfe-radio-field' );
	}
?>
<?php
	if( $key ) $field[ 'meta_key' ] = $key;
	$has_selection = false;
	foreach ( $field[ 'options' ] as $key => $value ) :
		$key = str_replace( '*', '', $key, $replace_default );
		$key = str_replace( '~', '', $key, $replace_disabled );
		// Only set default if it's not disabled as well
		if ( $replace_default > 0 && $replace_disabled < 1 ) $field[ 'default' ] = $key;

		$disabled_option = $replace_disabled > 0 ? 'disabled="disabled"' : '';
		if( isset( $field[ 'value' ] ) ? $field[ 'value' ] : $field[ 'default' ] == $key ) $has_selection = true;
?>
		<input type="radio" style="margin-left: 5px; margin-right: 5px; width: auto;" data-meta_key="<?php echo $field[ 'meta_key' ]; ?>" class="jmfe-radio jmfe-radio-<?php echo $field[ 'meta_key' ]; ?> input-radio" name="<?php echo esc_attr( isset( $field[ 'name' ] ) ? $field[ 'name' ] : $field['meta_key'] ); ?>" id="<?php echo $field['meta_key'] . '-' . esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php if ( isset( $field[ 'value' ] ) || isset( $field[ 'default' ] ) ) checked( isset( $field[ 'value' ] ) ? $field[ 'value' ] : $field[ 'default' ], $key, TRUE ); ?> <?php echo $disabled_option; ?>/><?php echo esc_html( $value ); ?>
<?php
	endforeach;
	if( ! isset( $admin ) || ! $admin ):
?>
	<small data-meta_key="<?php echo $field['meta_key']; ?>" alt="<?php _e( 'Clear Selection', 'wp-job-manager-field-editor' ); ?>" class="jmfe-clear-radio jmfe-clear-radio-<?php echo $field[ 'meta_key' ]; ?>" style="<?php if( ! $has_selection ) echo 'display: none;'; ?>margin-left: 5px; cursor: pointer;">
		<span class="dashicons dashicons-dismiss" style="vertical-align: middle;"></span>
	</small>
<?php
	endif;
	if ( ! empty( $field[ 'description' ] ) ) : ?><small class="description"><?php echo $field[ 'description' ]; ?></small><?php endif; ?>