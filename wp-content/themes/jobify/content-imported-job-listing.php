<li id="job_listing-<?php echo md5( $job->title ); ?>" class="<?php echo esc_attr( $source ); ?>_job_listing job_listing type-job_listing" data-longitude="<?php echo esc_attr( $job->longitude ); ?>" data-latitude="<?php echo esc_attr( $job->latitude ); ?>" data-title="<?php echo $job->title; ?>" data-href="<?php echo $job->url; ?>">
	<div class="row">
		<a href="<?php echo esc_url( $job->url ); ?>" target="_blank" <?php echo $link_attributes; ?>>
			<div class="logo col-sm-2 col-md-1 col-lg-1">
				<img class="company_logo" src="<?php echo esc_url( $logo ); ?>" alt="Logo" />
			</div>

			<div class="position col-xs-12 col-sm-10 col-md-6 col-lg-5">
				<h3><?php echo esc_html( $job->title ); ?></h3>
				<div class="company">
					<strong><?php echo esc_html( $job->company ); ?></strong>
					<small class="tagline"><?php echo esc_html( $job->tagline ); ?></small>
				</div>
			</div>

			<div class="location col-xs-12 col-md-5 col-lg-4">
				<?php echo esc_html( $job->location ); ?>
			</div>

			<ul class="meta col-lg-2">
				<li class="job-type <?php echo esc_attr( $job->type_slug ); ?>"><?php echo esc_html( $job->type ); ?></li>
				<li class="date"><date><?php printf( __( 'Posted %s ago', 'jobify' ), human_time_diff( $job->timestamp, current_time( 'timestamp' ) ) ); ?></date></li>
			</ul>
		</a>
	</div>
</li>