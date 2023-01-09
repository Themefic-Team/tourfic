(function($) {

    $(document).ready(function () {

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            $('.tf-setup-step-1').show();

            //add step parameter to url if not exists and if exists replace it
            /*var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_1");
            } else {
                url.searchParams.set("step", "step_1");
            }
            window.history.pushState({}, '', url);*/

        });

        $(document).on('click', '.tf-setup-next-btn, .tf-setup-skip-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let nextStep = step + 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + nextStep).fadeIn(300);
            });

            //add step parameter to url if not exists and if exists replace it
            /*var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_" + nextStep);
            } else {
                url.searchParams.set("step", "step_" + nextStep);
            }
            window.history.pushState({}, '', url);*/
        });

        $(document).on('click', '.tf-setup-prev-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let prevStep = step - 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + prevStep).fadeIn(300);
            });

            //add step parameter to url if not exists and if exists replace it
            /*var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_" + prevStep);
            } else {
                url.searchParams.set("step", "step_" + prevStep);
            }
            window.history.pushState({}, '', url);*/
        });

        //on window history change
        /*window.onpopstate = function (event) {
            var url = new URL(window.location.href);
            var step = url.searchParams.get("step");
            step.replace('step_', '');
            if (step !== null) {
                $('.tf-setup-step-' + step).fadeOut(300, function () {
                    $('.tf-setup-step-' + step).fadeIn(300);
                });
            }
        }*/

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