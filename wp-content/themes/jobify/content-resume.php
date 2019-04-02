<?php $category = get_the_resume_category(); ?>

<li id="resume-<?php the_ID(); ?>" <?php resume_class(); ?> <?php echo apply_filters( 'jobify_listing_data', '' ); ?>>
	<div class="row">
		<a href="<?php the_resume_permalink(); ?>">

			<div class="logo col-sm-2 col-md-1 col-lg-1">
				<?php the_candidate_photo( 'large' ); ?>
			</div>

			<div class="position col-xs-12 col-sm-10 col-md-6 col-lg-5">
				<h3><?php the_title(); ?></h3>

				<div class="candidate-title">
					<?php the_candidate_title( '<strong>', '</strong> ' ); ?>
				</div>
			</div>
			<div class="location col-xs-12 col-md-5 col-lg-4">
				<?php the_candidate_location( false ); ?>
			</div>

			<ul class="meta col-lg-2 <?php if ( $category ) : ?>has-category<?php endif; ?>">
				<?php if ( $category ) : ?>
					<li class="resume-category">
						<?php echo $category ?>
					</li>
				<?php endif; ?>

				<li class="date"><?php printf( __( 'Updated %s ago', 'jobify' ), human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) ) ); ?></li>
			</ul>
		</a>
	</div>
</li>