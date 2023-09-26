(function ($) {
    $(document).ready(function () {

        //if body has class .admin_page_tf-setup-wizard then add background-color: #ecf5ff; to html
        if ($('body').hasClass('admin_page_tf-setup-wizard')) {
            $('html').css('padding', '0');
        }

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            $('.tf-setup-step-1').show();
        });

        $(document).on('click', '.tf-setup-next-btn, .tf-setup-skip-btn', function (e) {
            e.preventDefault();
            let form = $('#tf-setup-wizard-form');
            let skipSteps = form.find('input[name="tf-skip-steps"]').val();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let nextStep = step + 1;

            //min one service required
            if (step === 1 && $(this).hasClass('tf-setup-next-btn')) {
                let services = $('input[name="tf-services[]"]:checked').length;

                if (!services) {
                    alert(tf_admin_params.i18n.no_services_selected);
                    return false;
                }

                //if hotel service not checked, hide hotel settings
                if (!$('input[name="tf-services[]"][value="hotel"]').is(':checked')) {
                    $('.tf-hotel-setup-wizard').hide();
                    $('.tf-add-new-hotel').hide();
                } else {
                    $('.tf-hotel-setup-wizard').show();
                    $('.tf-add-new-hotel').show();
                }

                //if tour service not checked, hide tour settings
                if (!$('input[name="tf-services[]"][value="tour"]').is(':checked')) {
                    $('.tf-tour-setup-wizard').hide();
                    $('.tf-add-new-tour').hide();
                } else {
                    $('.tf-tour-setup-wizard').show();
                    $('.tf-add-new-tour').show();
                }

                //if apartment service not checked, hide apartment settings
                if (!$('input[name="tf-services[]"][value="apartment"]').is(':checked')) {
                    $('.tf-apartment-setup-wizard').hide();
                    $('.tf-add-new-apartment').hide();
                } else {
                    $('.tf-apartment-setup-wizard').show();
                    $('.tf-add-new-apartment').show();
                }
            }

            //skip steps add to input[name="tf-skip-steps"]
            if ($(this).hasClass('tf-setup-skip-btn')) {
                skipSteps = !skipSteps ? step : skipSteps.indexOf(step) === -1 ? skipSteps + ',' + step : skipSteps;
                form.find('input[name="tf-skip-steps"]').val(skipSteps);

                if(step === 1){
                    $('.tf-hotel-setup-wizard').show();
                    $('.tf-tour-setup-wizard').show();
                }
            }

            //remove skip steps from input[name="tf-skip-steps"] if user back to step and go to next step
            if($(this).hasClass('tf-setup-next-btn') && skipSteps.indexOf(step) !== -1) {
                skipSteps = skipSteps.replace(step, '');
                form.find('input[name="tf-skip-steps"]').val(skipSteps);
            }

            //hide current step and show next step (if not last step)
            if(!$(this).hasClass('tf-setup-submit-btn')) {
                $('.tf-setup-step-' + step).fadeOut(300, function () {
                    $('.tf-setup-step-' + nextStep).fadeIn(300);
                });
            }
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
        $(document).on('click', '.tf-setup-submit-btn', function (e) {
            e.preventDefault();
            let submitBtn = $('.tf-setup-submit-btn.tf-admin-btn');
            let form = $(this).closest('#tf-setup-wizard-form');
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let skipSteps = form.find('input[name="tf-skip-steps"]').val();

            if($(this).hasClass('tf-admin-btn') && skipSteps.indexOf(step) !== -1) {
                skipSteps = skipSteps.replace(step, '');
                form.find('input[name="tf-skip-steps"]').val(skipSteps);
            }

            let formData = new FormData(form[0]);
            formData.append('action', 'tf_setup_wizard_submit');

            $.ajax({
                url: tf_admin_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    submitBtn.removeClass('tf-btn-loading');
                    if (data.success) {
                        $('.tf-finish-step').show();
                        $('.tf-setup-step-' + step).hide();
                    }
                },
                error: function (error) {
                    submitBtn.removeClass('tf-btn-loading');
                    console.log(error);
                }
            });
        });

    });

})(jQuery);