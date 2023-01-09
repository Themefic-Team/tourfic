(function($) {

    $(document).ready(function () {

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            $('.tf-setup-step-1').show();
        });

        $(document).on('click', '.tf-setup-next-btn, .tf-setup-skip-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let nextStep = step + 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + nextStep).fadeIn(300);
            });
        });

        $(document).on('click', '.tf-setup-prev-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let prevStep = step - 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + prevStep).fadeIn(300);
            });
        });

        /*
        * Setup Wizard form submit
        * @author: Foysal
        */
        $(document).on('submit', '#tf-setup-wizard-form', function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            formData.append('action', 'tf_setup_wizard_submit');

            $.ajax({
                url: tf_setup_wizard.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    form.find('.tf-setup-submit-btn').addClass('tf-btn-loading');
                },
                success: function (response) {
                    form.find('.tf-setup-submit-btn').removeClass('tf-btn-loading');
                    if (response.success) {
                        $('.tf-setup-step-3').fadeOut(300, function () {
                            $('.tf-setup-step-4').fadeIn(300);
                        });
                    }
                },
                error: function (error) {
                    form.find('.tf-setup-submit-btn').removeClass('tf-btn-loading');
                    console.log(error);
                }
            });
        });

    });

})(jQuery);