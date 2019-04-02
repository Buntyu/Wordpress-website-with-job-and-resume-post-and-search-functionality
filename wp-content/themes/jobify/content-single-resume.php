<?php
/**
 *
 */

global $post;

$skills     = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) );
$education  = get_post_meta( $post->ID, '_candidate_education', true );
$experience = get_post_meta( $post->ID, '_candidate_experience', true );
$emailform = get_post_meta( $post->ID, '_candidate_email', true );
$info            = jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' );

$has_local_info  = is_array( $skills ) || $education || $experience;

$col_description = 'top' == $info ? '12' : ( $has_local_info ? '6' : '10' );
$col_info        = 'top' == $info ? '12' : ( 'side' == $info ? '4' : '6' );
?>

<div class="single-resume-content row">

	<?php if ( resume_manager_user_can_view_resume( $post->ID ) ) : ?>

		<?php do_action( 'single_resume_start' ); ?>

		<?php locate_template( array( 'sidebar-single-resume-top.php' ), true, false ); ?>

		<div class="resume_description col-md-<?php echo $col_description; ?> col-sm-12">
			<h2 class="job-overview-title"><?php _e( 'Description', 'jobify' ); ?></h2>

			<?php echo apply_filters( 'the_resume_description', get_the_content() ); ?>
		</div>

		<?php if ( $has_local_info ) : ?>

		<div class="resume-info col-md-<?php echo $col_info; ?> col-sm-8 col-xs-12">

			<?php if ( $skills && is_array( $skills ) && 'side' == $info ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Skills', 'jobify' ); ?></h2>

				<ul class="resume-manager-skills">
					<?php echo '<li>' . implode( '</li><li>', $skills ) . '</li>'; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $education ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Education', 'jobify' ); ?></h2>

				<dl class="resume-manager-education">
				<?php
					foreach( $education as $item ) : ?>

						<dt>
							<h3><?php echo esc_html( $item['location'] ); ?></h3>
						</dt>
						<dd>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong class="qualification"><?php echo esc_html( $item['qualification'] ); ?></strong>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>

			<?php if ( $experience ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Experience', 'jobify' ); ?></h2>

				<dl class="resume-manager-experience">
				<?php
					foreach( $experience as $item ) : ?>

						<dt>
							<h3><?php echo esc_html( $item['employer'] ); ?></h3>
						</dt>
						<dd>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong class="job_title"><?php echo esc_html( $item['job_title'] ); ?></strong>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>
		</div>

		<?php endif; ?>

		<?php locate_template( array( 'sidebar-single-resume.php' ), true, false ); ?>

		<?php do_action( 'single_resume_end' ); ?>

	<?php else : ?>

		<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'resume_manager', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

	<?php endif; ?>
<?php global $emailform; ?>
</div>