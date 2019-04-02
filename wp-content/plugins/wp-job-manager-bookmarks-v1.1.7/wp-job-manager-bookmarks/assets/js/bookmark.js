jQuery(document).ready(function($) {
	$('.bookmark-notice').click(function() {
		$('.bookmark-details').slideToggle();
		$(this).toggleClass('open');
		return false;
	});
	$('.job-manager-bookmark-action-delete').click(function() {
		var answer = confirm( job_manager_bookmarks.i18n_confirm_delete );
		if (answer) {
			return true;
		}
		return false;
	});
});