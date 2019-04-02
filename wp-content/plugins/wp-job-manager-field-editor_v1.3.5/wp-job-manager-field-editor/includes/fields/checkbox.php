<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! empty( $settings['default'] ) ) {
	$structure = explode( ',', $settings['default'] );
	$options   = array();
	foreach ( $structure as $key => $part ) {
		$option = explode( '||', $part );
		if ( isset( $option[1] ) ) {
			if ( false !== strpos( $option[0], '*' ) ) {
				if ( empty( $instance ) ) {
					$value = array(
						$key => str_replace( '*', '', $option[0] )
					);
				}
			}
			$options[ $option[0] ] = $option[1];
		} else {
			$options[ $option[0] ] = $option[0];
		}
	}
} else {
	$options = array( '1' => 'Required' );
}

$checkboxindex = 0;

foreach ( $options as $checkboxValue => $checkboxLabel ) {
	$checkboxValue = str_replace( '*', '', $checkboxValue );
	$sel           = NULL;
	if ( $checkboxValue === 1 ) {
		$sel = 'checked="checked"';
	};
	?>
	<p><label style="margin-left: 8px;"><input type="checkbox" class="<?php if( $class ) echo $class; ?>" name="<?php echo $name; if ( ! empty( $options ) && ( count( $options ) > 1 )) echo "[0][" . $checkboxindex . "]";?>" <?php echo $sel; ?> id="<?php echo $id . '_' . $checkboxindex; ?>" value="<?php echo $checkboxValue; ?>"> <?php echo $checkboxLabel; ?></label></p>
	<?php
	$checkboxindex ++;
} ?>
