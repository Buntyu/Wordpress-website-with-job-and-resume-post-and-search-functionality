<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>
	<ul class="resume_packages">
		<?php if ( $user_packages ) : ?>
			<li class="package-section"><?php _e( 'Your Packages:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $user_packages as $key => $package ) :
				$package = wc_paid_listings_get_package( $package );
				?>
				<li class="user-resume-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="resume_package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
					<label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?></label><br/>
					<?php
						if ( $package->get_limit() ) {
							printf( _n( '%s resume posted out of %d', '%s resumes posted out of %s', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count(), $package->get_limit() );
						} else {
							printf( _n( '%s resume posted', '%s resumes posted', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count() );
						}

						if ( $package->get_duration() ) {
							printf( ' ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'wp-job-manager-wc-paid-listings' ), $package->get_duration() );
						}

						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ( $packages ) : ?>
			<li class="package-section"><?php _e( 'Purchase Package:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $packages as $key => $package ) :
				$product = get_product( $package );
				if ( ! $product->is_type( array( 'resume_package', 'resume_package_subscription' ) ) ) {
					continue;
				}
				?>
				<li class="resume-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="resume_package" value="<?php echo $product->id; ?>" id="package-<?php echo $product->id; ?>" />
					<label for="package-<?php echo $product->id; ?>"><?php echo $product->get_title(); ?></label><br/>
					<?php
						printf( _n( '%s to post %d resume', '%s to post %s resumes', $product->get_limit(), 'wp-job-manager-wc-paid-listings' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'wp-job-manager-wc-paid-listings' ) );

						if ( $product->get_duration() ) {
							printf( ' ' . _n( 'listed for %s day', 'listed for %s days', $product->get_duration(), 'wp-job-manager-wc-paid-listings' ), $product->get_duration() );
						}

						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
<?php else : ?>

	<p><?php _e( 'No packages found', 'wp-job-manager-wc-paid-listings' ); ?></p>

<?php endif; ?>