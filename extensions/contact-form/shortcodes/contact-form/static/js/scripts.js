"use strict";
(function($, _, fwe){
	fwe.on('fw:options:init', function (data) {
		$('a#fw-ext-contact-form-get-mailer-page').on('click', function (e) {
			e.preventDefault();

			var main = $(this).parents('#fw-options-box-main');

			main.find('.fw-options-tabs-wrapper').find('a[href="#fw-options-tab-settings"]').trigger('click');
			main.find('.fw-options-tabs-wrapper').find('#fw-options-tab-settings').find('a[href="#fw-options-tab-mailer-options"]').trigger('click');
		})
	});
})(jQuery, _, fwEvents);