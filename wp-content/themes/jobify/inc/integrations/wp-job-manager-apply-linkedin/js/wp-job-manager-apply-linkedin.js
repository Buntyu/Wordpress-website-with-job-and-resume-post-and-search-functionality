(function($) {
	'use strict';

	var JobifyWPJobManagerLinkedIn = {
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

				if ( ! $( '#submit-resume-form' ).length ) {
					self.initApplyLinkedIn();
				}
			});
		},

		initApplyLinkedIn: function() {
			$( '.apply-with-linkedin-details' ).addClass( 'modal' );

			if ( typeof IN === 'undefined' ) {
				return;
			}

			IN.Event.on(IN, "auth", triggerModal);

			function triggerModal() {
				Jobify.App.popup({
					items : {
						src : $( '.apply-with-linkedin-details' )
					}
				});
			}
		}
	};

	JobifyWPJobManagerLinkedIn.init();

})(jQuery);