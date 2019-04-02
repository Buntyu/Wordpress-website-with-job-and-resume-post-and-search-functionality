<li id="job_listing-<?php the_ID(); ?>" <?php job_listing_class(); ?> <?php echo apply_filters( 'jobify_listing_data', '' ); ?>>
	<div class="row">
		<a href="<?php the_job_permalink(); ?>" class="job_listing-link">
			<div class="logo col-sm-2 col-md-2 col-lg-2">
				<?php the_company_logo(); ?>
			</div>

			<div class="position col-xs-12 col-sm-10 col-md-5 col-lg-4">
				<h3><?php the_title(); ?></h3>

				<div class="company">
					<?php the_company_name( '<strong>', '</strong> ' ); ?>
					<?php the_company_tagline( '<span class="tagline">', '</span>' ); ?>
				</div>
			</div>


<?php global $post;
 $city= get_post_meta($post->ID,'_job_salary',true);
?>

			<div class="location col-xs-12 col-md-4 col-lg-3">
				<?php echo $city.'&nbsp; &nbsp;' ;
					the_job_location( false ); ?>



			</div>
			<ul class="meta col-lg-2">
				<?php do_action( 'job_listing_meta_start' ); ?>

				<li class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></li>
				<li class="date"><date><?php printf( __( 'Posted %s ago', 'jobify' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?></date></li>

				<?php do_action( 'job_listing_meta_end' ); ?>
			</ul>
		</a>
	</div>
</li>
