/**
 * Ajax install
 * 
 * @since 1.0
 */
 (function($) {
	
	$(document).ready(function(){	

        $(document).on('click', '.tf-install', function(e) {
            e.preventDefault();

            var current = $(this);
            var plugin_slug = current.attr("data-plugin-slug");

            current.addClass('updating-message').text('Installing...');

            var data = {
                action: 'tf_ajax_install_plugin',
                _ajax_nonce: tf_params.tf_nonce,
                slug: plugin_slug,
            };

            jQuery.post( tf_params.ajax_url, data, function(response) {
                //console.log(response);
                //console.log(response.data.activateUrl);
                current.removeClass('updating-message');
                current.addClass('updated-message').text('Installed!');
                current.attr("href", response.data.activateUrl);
            })
            .fail(function() {
                current.removeClass('updating-message').text('Failed!');
            })
            .always(function() {
                current.removeClass('install-now updated-message').addClass('activate-now button-primary').text('Activating...');
                current.unbind(e);
                current[0].click();
            });
        });

        /**
         * Pro Feature button link
         */
        $(document).on('click', '.tf-pro', function(e) {
            window.open('https://tourfic.com/');
        });

        $(document).on('click', '.tf-csf-pro', function(e) {
            window.open('https://tourfic.com/');
        });

    });

})(jQuery);