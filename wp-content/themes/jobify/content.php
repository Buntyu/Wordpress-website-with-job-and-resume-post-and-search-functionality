<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package Jobify
 * @since Jobify 1.0
 */
 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'row' ); ?>>
	<header class="entry-header col-sm-3 col-xs-12">
		<div class="entry-author">
			<?php echo get_avatar( '', 100 ); ?> 
<!-- <img src="//archipro.com/wp-content/uploads/2016/06/Logo-Square-e1490988998133.jpg"> -->
		<!--	<?php printf( __( 'Written by <a class="author-link" href="%s" rel="author">%s</a>', 'jobify' ), esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), get_the_author() ); ?> -->

<?php echo 'Sponsored By <a class="author-link" href="//archipro.com/" rel="author">Archipro</a>'; ?>

		</div>

		<div class="entry-meta">
			<data itemprop="datePublished" value="<?php echo get_the_date(); ?>"><?php echo get_the_date(); ?></data>

			<?php if ( comments_open() ) : ?>
				<span class="comments-link">
					<?php comments_popup_link( __( '0 Comments', 'jobify' ), __( '1 Comment', 'jobify' ), __( '% Comments', 'jobify' ) ); ?>
				</span><!-- .comments-link -->
			<?php endif; ?>

			<?php if ( is_singular() ) : ?>
				<?php do_action( 'jobify_share_object' ); ?>
			<?php endif; ?>

		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry col-sm-9 col-xs-12">
		

		<?php if ( is_single() ) : ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php else : ?>
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
		<?php endif; ?>

<?php if (  has_post_thumbnail() ) : ?>
			<div class="entry-feature">
				<?php the_post_thumbnail( 'fullsize' ); ?>
			</div>
		<?php endif; ?>

		<div class="entry-summary">
			<?php if ( is_singular() ) : ?>
				<?php the_content(); ?>

				<?php if ( is_singular() ) : ?>
				<?php the_tags( '<p class="entry-tags"><i class="icon-tag"></i> ' . __( 'Tags:', 'jobify' ) . ' ', ', ', '</p>' ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'jobify' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
				<?php endif; ?>
			<?php else : ?>
				<?php the_excerpt(); ?>

				<p><a href="<?php the_permalink(); ?>" rel="bookmark" class="button button-medium"><?php _e( 'Continue Reading', 'jobify' ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post -->
