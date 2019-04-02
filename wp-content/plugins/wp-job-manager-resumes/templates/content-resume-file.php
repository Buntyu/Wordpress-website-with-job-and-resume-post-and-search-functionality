<?php
if ( ( $resume_files = get_resume_files() ) && apply_filters( 'resume_manager_user_can_download_resume_file', true, $post->ID ) ) : ?>
	<?php foreach ( $resume_files as $key => $resume_file ) : ?>
		<li class="resume-file resume-file-<?php echo substr( strrchr( $resume_file, '.' ), 1 ); ?>">
			<a rel="nofollow" target="_blank" href="<?php echo esc_url( get_resume_file_download_url( null, $key ) ); ?>"><?php echo basename( $resume_file ); ?></a>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
