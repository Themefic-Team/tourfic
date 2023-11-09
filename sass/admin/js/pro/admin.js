/**
 * Ajax install Tourfic
 * 
 * @since 1.0
 */
 (function($) {
	
	$(document).ready(function(){	

        $(document).on('click', '.tf-install', function(e) {
            e.preventDefault();

            var current = $(this);
            var plugin_slug = current.attr("data-plugin-slug");

            current.addClass('updating-message').text(tf_pro_params.installing);

            var data = {
                action: 'tf_ajax_install_tourfic',
                _ajax_nonce: tf_pro_params.tf_pro_nonce,
                slug: plugin_slug,
            };

            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                current.removeClass('updating-message');
                current.addClass('updated-message').text(tf_pro_params.installed);
                current.attr("href", response.data.activateUrl);
            })
            .fail(function() {
                current.removeClass('updating-message').text(tf_pro_params.install_failed);
            })
            .always(function() {
                current.removeClass('install-now updated-message').addClass('activate-now button-primary').text(tf_pro_params.activating);
                current.unbind(e);
                current[0].click();
            });
        });

        /**
         * License Activate
         * 
         * Ajax
         */
         $(document).on('click', '.tf-license-activate #submit', function(e) {
            e.preventDefault();

            // $('.tf-option-form').submit();

            //after 3 seconds page will be reloaded
            // setTimeout(function() {
            //     location.reload();
            // } , 3000);

            var current = $(this);
            
            var license_key = $("input[name='tf_settings[license-key]']").val();
            var license_email = $("input[name='tf_settings[license-email]']").val();
            
            var data = {
                action: 'tf_act_license',
                license_key: license_key,
                license_email: license_email,
            };
            
            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                //console.log(response);
                //console.log(response.data.activateUrl);
            })
            .success(function(response) {
                //console.log(response);
                location.reload();
            });
        });

        /**
         * License Deactivate
         * 
         * Ajax
         */
        $(document).on('click', '.el-license-container #submit', function(e) {
            e.preventDefault();

            var current = $(this);

            var data = {
                action: 'tf_deact_license',
            };

            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                //console.log(response);
                //console.log(response.data.activateUrl);
            })
            .success(function(response) {
                //console.log(response);
                location.reload();
            });
        });

    });

})(jQuery);