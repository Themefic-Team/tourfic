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


        /**
         * Delete old review fields
         * @author kabir, fida
         */
        $(document).on('click', '.tf-del-old-review-fields', function (e) {
            e.preventDefault();
            var $this = $(this);
            var data = {
                action: 'tourfic_delete_old_review_fields',
                deleteAll: $(this).data('delete-all'),
                _ajax_nonce: tourficAdminParams.tf_nonce
            };

            $.ajax({
                type: 'post',
                url: tourficAdminParams.ajax_url,
                data: data,
                beforeSend: function (data) {
                    notyf.success(tourficAdminParams.deleting_old_review_fields);
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
            var post_id = $("#post_ID").val();
            var meta_field = $this.closest('.tf-repeater-content-wrap').find('.tf-order_id input').attr('name');
            var data = {
                action: 'tourfic_remove_room_order_ids',
                meta_field: meta_field,
                post_id: post_id,
                _ajax_nonce: tourficAdminParams.tf_nonce
            };

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                beforeSend: function (data) {
                    notyf.success(tourficAdminParams.deleting_room_order_ids);
                },
                success: function (response) {
                    notyf.success(response.data.message);
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
        $(document).on('click', '.post-type-tf_tours #publish, .post-type-tf_tours #save-post', function (e) {
            if ($('input[name="tf_tours_opt[location][address]"]').val().length === 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error(tourficAdminParams.tour_location_required);
                return false;
            }
        });

        /**
         * Hotel location required
         *
         * show notyf error
         */
        $(document).on('click', '.post-type-tf_hotel #publish, .post-type-tf_hotel #save-post', function (e) {
            if ($('input[name="tf_hotels_opt[map][address]"]').val().length === 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error(tourficAdminParams.hotel_location_required);
                return false;
            }
        });

        /**
         * Apartment location required
         *
         * show notyf error
         */
        $(document).on('click', '.post-type-tf_apartment #publish, .post-type-tf_apartment #save-post', function (e) {
            if ($('[name="tf_apartment_opt[map][address]"]').val().length === 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error(tourficAdminParams.apartment_location_required);
                return false;
            }
        });

        /**
         * Ajax install
         *
         * @since 1.0
         */
        $(document).on('click', '.tf-install', function (e) {
            e.preventDefault();

            var current = $(this);
            var plugin_slug = current.attr("data-plugin-slug");

            current.addClass('updating-message').text(tourficAdminParams.installing);

            var data = {
                action: 'tourfic_ajax_install_plugin',
                _ajax_nonce: tourficAdminParams.tf_nonce,
                slug: plugin_slug,
            };

            jQuery.post(tourficAdminParams.ajax_url, data, function (response) {
                current.removeClass('updating-message');
                current.addClass('updated-message').text(tourficAdminParams.installed);
                current.attr("href", response.data.activateUrl);
            })
                .fail(function () {
                    current.removeClass('updating-message').text(tourficAdminParams.install_failed);
                })
                .always(function () {
                    current.removeClass('install-now updated-message').addClass('activate-now button-primary').text(tourficAdminParams.activating);
                    current.unbind(e);
                    current[0].trigger("click");
                });
        });

        /**
         * Generate & set unique id for hotel rooms
         */
        $(document).on('click', '.room-repeater > div.csf-fieldset > a.csf-repeater-add', function (e) {

            var repeaterNumber = $('.room-repeater .csf-repeater-wrapper [data-depend-id="room"]').length - 2;

            $('.room-repeater .unique-id input').each(function () {
                repeaterNumber++;
                if ($('.room-repeater [data-depend-id="room"] [data-depend-id="unique_id"]').val().length === 0) {
                    $('.room-repeater [name="tf_hotel[room][' + repeaterNumber + '][unique_id]"]').val(new Date().valueOf() + repeaterNumber);
                }
            });

        });

        //documentation link open in new tab
        $('.tf-go-docs').parent().attr('target', '_blank');

        //pricing link open in new tab
        $('#toplevel_page_tourfic_settings a[href*="tourfic.com/pricing"]').attr('target', '_blank');

        /*
        * Author @Jahid
        * Hotel, Tour, Apartment Duplicator
        */
       
        $('.tf-post-data-duplicate').on('click', function(e) {
            e.preventDefault();
            var postID = $(this).data('postid');
            var nonce = $(this).data('nonce');
            $('#wpcontent').append('<div class="tf-duplicator-loader"></div>');
            // AJAX request to duplicate post
            $.ajax({
                type: 'POST',
                url: tourficAdminParams.ajax_url,
                data: {
                    action: 'tourfic_duplicate_post_data',
                    postID: postID,
                    security: nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                        return;
                    }

                    $('.tf-duplicator-loader').remove();
                    notyf.error(response.data && response.data.message ? response.data.message : 'Unable to duplicate this post.');
                },
                error: function(xhr) {
                    $('.tf-duplicator-loader').remove();
                    var message = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                        ? xhr.responseJSON.data.message
                        : 'Unable to duplicate this post.';
                    notyf.error(message);
                }
            });
        });

    });

})(jQuery);
