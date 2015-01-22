(function($) {
	var $methodSelect = $('#fw-option-mailer-method'),
		$smtpGroup = $('#fw-backend-option-fw-option-mailer-smtp');

	$methodSelect.on('change', function() {
		if ('smtp' === this.value) {
			$smtpGroup.show();
		} else {
			$smtpGroup.hide();
		}
	}).trigger('change');

})(jQuery);
