<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<input placeholder="<?php echo $settings['placeholder']; ?>" name="<?php echo $name; ?>" class="widefat" type="text" ref="<?php echo $groupid; ?>" id="<?php echo $id; ?>" value="<?php echo htmlentities( $value ); ?>" <?php if ( in_array( $field, $required_fields ) ) echo 'required="required"'; ?>/>
