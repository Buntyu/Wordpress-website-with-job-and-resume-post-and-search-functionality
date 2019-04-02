<?php

if ( ! defined( 'ABSPATH' ) ) exit;

ob_start();
$structure     = explode( ',', $settings['default'] );
$defaultSelect = NULL;
$options       = array();

foreach ( $structure as & $option ) {
	if ( false !== strpos( $option, '||' ) ) {
		$parts                = explode( '||', $option );
		$options[ $parts[0] ] = $parts[1];
		if ( false !== stripos( $option, '*' ) ) {
			$defaultSelect = $parts[0];
		}
	} else {
		$options[ $option ] = ucwords( $option );
		if ( false !== stripos( $option, '*' ) ) {
			$defaultSelect = $option;
		}
	}
}
echo '<div id="jmfe-modal-unknown-' . $id . '" style="display: none;"></div>';
echo '<select class="widefat" name="' . $name . '" id="' . $id . '" >\r\n';
if( ! empty( $settings[ 'placeholder' ] ) ) echo ' <option value="none" disabled selected>' . $settings['placeholder'] . '</option>';
if ( $settings['default'] === $value && ! empty( $defaultSelect ) ) {
	$value = $defaultSelect;
} else if ( empty( $defaultSelect ) ) {
	?>



<?php
}

$dropdownindex = 0;
foreach ( $options as $dropdownValue => $dropdownLabel ) {
	$dropdownValue = str_replace( '*', '', $dropdownValue );
	if( FALSE !== strpos( $dropdownLabel, '---' ) ){
		$dropdownLabel = str_replace( '---', '', $dropdownLabel );
		echo "<option style='text-indent: 5%;' class='disabled' disabled>--- {$dropdownLabel} ---</option>";
	} else {
		?>
		<option <?php if ( $value == $dropdownValue ) {
			echo 'selected="selected"';
		}; ?> value="<?php echo $dropdownValue; ?>"> <?php echo str_replace( '*', '', $dropdownLabel ); ?></option>
	<?php
	}
} ?>
</select>

<?php ob_end_flush(); ?>