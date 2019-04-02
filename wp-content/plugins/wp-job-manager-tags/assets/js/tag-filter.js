jQuery( document ).ready( function ( $ ) {
	$( '.job_listings' )

		.on( 'click', '.filter_by_tag a', function() {
			var tag = $(this).text();
			var existing_tag = $('.filter_by_tag').find('input[value="' + tag + '"]');

			if ( existing_tag.size() > 0 ) {
				$(existing_tag).remove();
				$(this).removeClass('active');
			} else {
				$('.filter_by_tag').append('<input type="hidden" name="job_tag[]" value="' + tag + '" />');
				$(this).addClass('active');
			}

			var target = $(this).closest( 'div.job_listings' );

			target.trigger( 'update_results', [ 1, false ] );

			return false;
		})

		.on( 'reset', function() {
			$('.filter_by_tag a.active', this).removeClass('active');
			$('.filter_by_tag input', this).remove();
		})

		.on( 'updated_results', function( event, results ) {
			if ( results.tag_filter ) {
				var $target = $(this);
				$target.find( '.filter_by_tag_cloud' ).html( results.tag_filter );
				$target.find( '.filter_by_tag' ).show();
				$target.find( '.filter_by_tag input' ).each(function(){
					var tag = $(this).val();
					$target.find('.filter_by_tag a').each(function(){
						if ( $(this).text() === tag ) {
							$(this).addClass('active');
						}
					});
				});
			} else {
				$(this).find( '.filter_by_tag' ).hide();
			}
		})

		.on( 'change', '#search_categories', function() {
			var target = $( this ).closest( 'div.job_listings' );
			target.find('.filter_by_tag input').remove();
			target.trigger( 'update_results', [ 1, false ] );
		});
});