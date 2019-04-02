<?php
/**
 * Share Post/Page/Job
 *
 * @package Jobify
 * @since Jobify 1.0
 */

global $post;
?>
<div class="entry-share">

	<?php do_action( 'jobify_share_before' ); ?>

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=431844826937522";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<div class="fb-share-button" data-href="<?php the_permalink(); ?>" data-type="button_count"></div>

	<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

	<script src="//platform.linkedin.com/in.js" type="text/javascript">
	 lang: en_US
	 <?php if ( defined( 'JOB_MANAGER_APPLY_WITH_LINKEDIN_API_KEY' ) ) : ?>
	 api_key: <?php echo esc_js( JOB_MANAGER_APPLY_WITH_LINKEDIN_API_KEY ); ?>
	 <?php endif; ?>
	</script>
	<script type="IN/Share" data-url="<?php the_permalink(); ?>" data-counter="right"></script>

	<!-- Place this tag where you want the share button to render. -->
	<div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php the_permalink(); ?>"></div>

	<!-- Place this tag after the last share tag. -->
	<script type="text/javascript">
	  (function() {
	    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/platform.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>

	<?php do_action( 'jobify_share_after' ); ?>

	<?php if ( function_exists( 'zilla_likes' ) ) : ?>
	<span class="entry-like">
		<?php zilla_likes(); ?>
	</span>
	<?php endif; ?>
</div>