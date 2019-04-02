<?php if ( ! is_tax( 'job_listing_type' ) && empty( $job_types ) ) : ?>
	<ul class="job_types">
		<?php foreach ( get_job_listing_types() as $type ) : 
                    $selected = '';
                    if($type->slug == 'full-time'){
                        $selected = checked(true,true,FALSE);
                    }
                    ?>
            <li><label for="job_type_<?php echo $type->slug; ?>" class="<?php echo sanitize_title( $type->name ); ?>"><input type="radio" name="filter_job_type[]" value="<?php echo $type->slug; ?>" <?php  echo $selected; ?> id="job_type_<?php echo $type->slug; ?>" /> <?php echo $type->name; ?></label></li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="filter_job_type[]" value="" />
<?php elseif ( $job_types ) : ?>
	<?php foreach ( $job_types as $job_type ) : ?>
		<input type="hidden" name="filter_job_type[]" value="<?php echo sanitize_title( $job_type ); ?>" />
	<?php endforeach; ?>
<?php endif; ?>