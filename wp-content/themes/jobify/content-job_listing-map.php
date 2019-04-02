<?php
/**
 *
 */
?>

<div class="<?php echo $type ?>-map-wrapper">
	<?php do_action( 'jobify_map_before' ); ?>

	<div class="<?php echo $type ?>-map">
		<div id="<?php echo $type ?>-map-canvas"></div>
	</div>

	<?php do_action( 'jobify_map_after' ); ?>
</div>

<script>
	jQuery(document).on( 'ready', function($) {
		new cLocator.Stage( '<?php echo $type ?>', jobifyMapSettings );
	});
</script>