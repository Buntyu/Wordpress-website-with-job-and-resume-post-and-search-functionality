(function($) {
	'use strict';

	var JobifyWPJobManager = {
		cache: {
			$document: $(document),
			$window: $(window)
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.initApply();
				self.initIndeed();
				self.avoidSubmission();
				self.initContact();
			});
		},

		initApply: function() {
			var $details = $( '.application_details, .resume_contact_details' );
			var $button = $( '.application_button, .resume_contact_button' );

			if ( ! $details.length ) {
				return;
			}

			$details
				.addClass( 'modal' )
				.attr( 'id', 'apply-overlay' );

			$button
				.addClass( 'popup-trigger' )
				.attr( 'href', '#apply-overlay' );
		},

		initIndeed: function() {
			$( '.job_listings' ).on( 'update_results', function() {
				$( '.indeed_job_listing' ).addClass( 'type-job_listing' );
			});
		},

		initContact: function() {
			$( '.resume_contact_button' ).click(function(e) {
				e.preventDefault();

				Jobify.App.popup({
					items : {
						src : $( '.resume_contact_details' )
					}
				});

				return false;
			});
		},

		avoidSubmission: function() {
			$( '.job_filters, .resume_filters' ).submit(function(e) {
				return false;
			});
		}
	};

	JobifyWPJobManager.init();

})(jQuery);