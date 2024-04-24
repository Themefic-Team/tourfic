(function ($) {
    $(document).ready(function () {

        // Create an instance of Notyf
        const notyf = new Notyf({
            ripple: true,
            dismissable: true,
            duration: 3000,
            position: {
                x: 'right',
                y: 'bottom',
            },
        });

        //if body has class .tourfic-settings_page_tf-setup-wizard then add background-color: #ecf5ff; to html
        if ($('body').hasClass('tourfic-settings_page_tf-setup-wizard')) {
            $('html').css('padding', '0');
        }

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            if(tf_admin_params.is_woo_not_active) {
                $('.tf-setup-step-1').fadeIn(600);
            } else {
                $('.tf-setup-step-2').fadeIn(600);
            }
        });

        $(document).on('click', '.tf-setup-next-btn, .tf-setup-skip-btn', function (e) {
            e.preventDefault();
            let form = $('#tf-setup-wizard-form');
            let skipSteps = form.find('input[name="tf-skip-steps"]').val();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let nextStep = step + 1;

            //min one service required
            if (step === 2 && $(this).hasClass('tf-setup-next-btn')) {
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
            if(step === 2 && !tf_admin_params.is_woo_not_active) {
                $('.tf-setup-step-2').fadeOut(300, function () {
                    $('.tf-setup-step-0').fadeIn(300);
                });
            } else {
                $('.tf-setup-step-' + step).fadeOut(300, function () {
                    $('.tf-setup-step-' + prevStep).fadeIn(300);
                });
            }
        });

        /*
        * Setup Wizard form submit
        * @author: Foysal
        */
        $(document).on('click', '.tf-setup-submit-btn', function (e) {
            e.preventDefault();
            let submitBtn = $('.tf-setup-submit-btn.tf-quick-setup-btn');
            let form = $(this).closest('#tf-setup-wizard-form');
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let skipSteps = form.find('input[name="tf-skip-steps"]').val();

            if($(this).hasClass('tf-quick-setup-btn') && skipSteps.indexOf(step) !== -1) {
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
                complete: function () {
                    submitBtn.removeClass('tf-btn-loading');
                },
                error: function (error) {
                    submitBtn.removeClass('tf-btn-loading');
                    console.log(error);
                }
            });
        });

        /*
        * Travelfic Theme Installing
        * @author: Jahid
        */
        let travelfic_toolkit_active_plugins = tf_admin_params.is_travelfic_toolkit_active;

        $(document).on('click', '.tf-setup-travelfic-theme-btn', function (e) {
            e.preventDefault();
            if(tf_admin_params.current_active_theme && "travelfic"!=tf_admin_params.current_active_theme){
                let theme_slug = $(this).attr('data-install');
                $('.tf-setup-travelfic-theme-btn').text("Travelfic Installing...");
                $('.tf-setup-travelfic-theme-btn').addClass('tf-btn-loading');
                var data = {
                    action: "tf_theme_installing",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: theme_slug,
                };
                // Installing Function
                jQuery.post(tf_admin_params.ajax_url, data, function (response) {
                    $('.tf-setup-travelfic-theme-active').click();
                })
            }else{
                $('.tf-setup-travelfic-toolkit-btn').click();
            }
            
        });

        /*
        * Travelfic Theme Activating
        * @author: Jahid
        */

        $(document).on('click', '.tf-setup-travelfic-theme-active', function (e) {

            e.preventDefault();
            let theme_slug = $(this).attr('data-install');
            $('.tf-setup-travelfic-theme-btn').text("Travelfic Activate...");

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: "tf_setup_travelfic_theme_active",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: theme_slug,
                },
                success: function(response) {
                    if ($.inArray("travelfic-toolkit", travelfic_toolkit_active_plugins) !== -1) {
                        $('.tf-setup-travelfic-toolkit-btn').click();
                    }else{
                        window.location.replace(tf_admin_params.toolkit_page_url);
                    }
                },
                error: function(error) {
                    
                }
            });

        });

        /*
        * Travelfic Toolkit Installing
        * @author: Jahid
        */
       
        $(document).on('click', '.tf-setup-travelfic-toolkit-btn', function (e) {
            e.preventDefault();
            if ($.inArray("travelfic-toolkit", travelfic_toolkit_active_plugins) !== -1) {
                let plugin_slug = $(this).attr('data-install');
                $('.tf-setup-travelfic-theme-btn').text("Toolkit Installing...");

                var data = {
                    action: "tf_travelfic_toolkit_installing",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: plugin_slug,
                };
                // Installing Function
                jQuery.post(tf_admin_params.ajax_url, data, function (response) {
                    $('.tf-setup-travelfic-toolkit-active').click();
                })
            }else{
                window.location.replace(tf_admin_params.toolkit_page_url);
            }
        });

        /*
        * Travelfic Toolkit Activating
        * @author: Jahid
        */
        $(document).on('click', '.tf-setup-travelfic-toolkit-active', function (e) {

            e.preventDefault();
            let plugin_slug = $(this).attr('data-install');
            $('.tf-setup-travelfic-theme-btn').text("Toolkit Activate...");

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: "tf_travelfic_toolkit_activate",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: plugin_slug,
                },
                success: function(response) {
                    window.location.replace(tf_admin_params.toolkit_page_url);
                },
                error: function(error) {
                    
                }
            });
        });

        /*
        * WooCommerce Plugin Install
        * @auther: Foysal
        */
        $(document).on('click', '.tf-install-woo-btn', function (e) {
            e.preventDefault();
            let btn = $(this);

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: "tf_ajax_install_woo",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: 'woocommerce',
                },
                beforeSend: function () {
                    btn.text(tf_admin_params.installing)
                    btn.addClass('tf-btn-loading');
                },
                success: function(response) {
                    btn.text(tf_admin_params.activating);
                    $('.tf-active-woo-btn').click();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        /*
        * WooCommerce Plugin Activate
        * @auther: Foysal
        */
        $(document).on('click', '.tf-active-woo-btn', function (e) {
            e.preventDefault();
            let btn = $(this);

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: "tf_ajax_activate_woo",
                    _ajax_nonce: tf_admin_params.tf_nonce,
                    slug: 'woocommerce',
                },
                beforeSend: function () {
                    btn.text(tf_admin_params.activating)
                    btn.addClass('tf-btn-loading');
                },
                success: function(response) {
                    notyf.success(response.data);

                    setTimeout(function(){
                        btn.closest('.tf-setup-step-layout').find('.tf-setup-next-btn').click();
                    }, 500);

                    btn.removeClass('tf-btn-loading');
                    $('.tf-install-woo-btn').removeClass('tf-btn-loading');
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });

})(jQuery);