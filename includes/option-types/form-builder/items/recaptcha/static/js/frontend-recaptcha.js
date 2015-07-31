function fw_forms_builder_item_recaptcha_init() {
    jQuery('.form-builder-item-recaptcha').each(function () {
        grecaptcha.render(this, {
            sitekey : form_builder_item_recaptcha.site_key
        });
    });
}