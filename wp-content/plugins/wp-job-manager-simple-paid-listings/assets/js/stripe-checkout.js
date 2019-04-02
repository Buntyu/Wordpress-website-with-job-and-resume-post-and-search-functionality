jQuery( function() {

	jQuery(document).on( 'click', '#job_preview_submit_button', function(){

		var $form  = jQuery(this).closest('form');
		var $token = $form.find('input.stripe_token');

		if ( $token.val() )
			return true;

		var token_action = function( res ) {
			$form.find('input.stripe_token').remove();
			$form.append("<input type='hidden' class='stripe_token' name='stripe_token' value='" + res.id + "'/>");
			$form.find('#job_preview_submit_button').trigger( "click" );
		};

		StripeCheckout.open({
			key:         stripe_checkout_params.key,
			address:     false,
			name:        stripe_checkout_params.name,
			amount:      stripe_checkout_params.amount,
			panelLabel:  stripe_checkout_params.label,
			currency:    stripe_checkout_params.currency,
			email:       stripe_checkout_params.email,
			token:       token_action
		});

		return false;
    });

} );