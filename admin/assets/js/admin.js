(function($) {
	
	$(document).ready(function(){	

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

        /**
         * Delete old review fields
         * @author kabir, fida
         */
        $(document).on('click', '.tf-del-old-review-fields', function (e) {
            e.preventDefault();
            var $this = $(this);
            var data = {
                action: 'tf_delete_old_review_fields',
                deleteAll: $(this).data('delete-all')
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                beforeSend: function (data) {
                    notyf.success('Deleting old review fields...')
                },
                success: function (data) {
                    notyf.success(data.data);
                },
                error: function (data) {
                    notyf.error(data.data);
                },

            });

        });

        /**
         * Delete room order ids
         * @author fida
         */
         $(document).on('click', '.remove-order-ids', function (e) {

            e.preventDefault();
            
            var $this = $(this);
            var meta_field = $this.closest( '.csf-repeater-content' ).find('.tf-order_id input').attr('name');

            var data = {
                action: 'tf_remove_room_order_ids',
                meta_field: meta_field,
                post_id: post_id,
            };

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                beforeSend: function (data) {
                    notyf.success('Deleting order ids...')
                },
                success: function (data) {
                    notyf.success(data.data);
                    location.reload();
                },
                error: function (data) {
                    notyf.error(data.data);
                },
            });

        });

        /**
         * Tour location required
         * 
         * show notyf error
         */
        $(document).on('click', '.post-type-tf_tours #publish, .post-type-tf_tours #save-post', function(e) {
            if( $('textarea[name="tf_tours_option[text_location]"]').val().length === 0 ) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error('Tour Location is a required field!');
                return false;
            }
        });

        /**
         * Hotel and Tour feature images required
         * 
         * show notyf error
         */
        
        $(document).on('click', '.post-type-tf_tours #publish, .post-type-tf_tours #save-post', function(e) {
            if( $('#set-post-thumbnail').find('img').size() == 0) {
            e.preventDefault;
            e.stopImmediatePropagation();
            notyf.error('Tour image is a required!');
            return false;
            }
        });

        $(document).on('click', '.post-type-tf_hotel #publish, .post-type-tf_hotel #save-post', function(e) {
            if( $('#set-post-thumbnail').find('img').size() == 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error('Hotel image is a required!');
                return false;
            }
        });
        
        /**
         * Ajax install
         * 
         * @since 1.0
         */
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

        /**
         * Generate & set unique id for hotel rooms
         */
        $(document).on('click', '.room-repeater > div.csf-fieldset > a.csf-repeater-add', function(e) {

            var repeaterNumber = $('.room-repeater .csf-repeater-wrapper [data-depend-id="room"]').length - 2;

            $('.room-repeater .unique-id input').each(function() {
                repeaterNumber++;
                if( $('.room-repeater [data-depend-id="room"] [data-depend-id="unique_id"]').val().length === 0 ) {
                    $('.room-repeater [name="tf_hotel[room]['+repeaterNumber+'][unique_id]"]').val(new Date().valueOf() + repeaterNumber);
                }
            });

        });

    });

})(jQuery);