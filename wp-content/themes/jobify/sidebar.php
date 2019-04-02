<?php
/**
 *
 */

if ( ! is_active_sidebar( 'sidebar-blog' ) )
	return;
?>

<div class="col-md-3 col-xs-12">
	<?php dynamic_sidebar( 'sidebar-blog' ); ?>
</div>