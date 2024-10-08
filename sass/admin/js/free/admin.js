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
                action: 'tf_delete_old_review_fields',
                deleteAll: $(this).data('delete-all'),
                _ajax_nonce: tf_admin_params.tf_nonce
            };

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: data,
                beforeSend: function (data) {
                    notyf.success(tf_admin_params.deleting_old_review_fields);
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
                action: 'tf_remove_room_order_ids',
                meta_field: meta_field,
                post_id: post_id,
                _ajax_nonce: tf_admin_params.tf_nonce
            };

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                beforeSend: function (data) {
                    notyf.success(tf_admin_params.deleting_room_order_ids);
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
                notyf.error(tf_admin_params.tour_location_required);
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
                notyf.error(tf_admin_params.hotel_location_required);
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
                notyf.error(tf_admin_params.apartment_location_required);
                return false;
            }
        });

        /**
         * Hotel select in room post type
         *
         * show notyf error
         */
        $(document).on('click', '.post-type-tf_room #publish, .post-type-tf_room #save-post', function (e) {
            if ($('[name="tf_room_opt[tf_hotel]"]').val().length === 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error(tf_admin_params.hotel_required_in_room);
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

            current.addClass('updating-message').text(tf_admin_params.installing);

            var data = {
                action: 'tf_ajax_install_plugin',
                _ajax_nonce: tf_admin_params.tf_nonce,
                slug: plugin_slug,
            };

            jQuery.post(tf_admin_params.ajax_url, data, function (response) {
                current.removeClass('updating-message');
                current.addClass('updated-message').text(tf_admin_params.installed);
                current.attr("href", response.data.activateUrl);
            })
                .fail(function () {
                    current.removeClass('updating-message').text(tf_admin_params.install_failed);
                })
                .always(function () {
                    current.removeClass('install-now updated-message').addClass('activate-now button-primary').text(tf_admin_params.activating);
                    current.unbind(e);
                    current[0].trigger("click");
                });
        });

        /**
         * Pro Feature button link
         */
        $(document).on('click', '.tf-pro', function (e) {
            e.preventDefault();
            window.open('https://tourfic.com/');
        });

        $(window).on('load', function () {
            $('.tf-field-disable').find('input, select, textarea, button, div, span').attr('disabled', 'disabled');
        });

        $(document).on('click', '.tf-field-pro', function (e) {
            e.preventDefault();
            window.open('https://tourfic.com/');
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

        /*
        * Author @Jahid
        * Tour Booking Status
        */

        $('.tf-ticket-status').on("click", function () {
            if ($(this).is(':checked')) {
                var order_unique_id = $(this).val();
                $("#tf-booking-status-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_ticket_status_change',
                        _ajax_nonce: tf_admin_params.tf_nonce,
                        status: "check in",
                        order_unique_id: order_unique_id,
                    },
                    success: function (data) {
                        $("#tf-booking-status-loader").removeClass('show');
                    }
                });
            } else {
                var order_unique_id = $(this).val();
                $("#tf-booking-status-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_ticket_status_change',
                        _ajax_nonce: tf_admin_params.tf_nonce,
                        status: "",
                        order_unique_id: order_unique_id,
                    },
                    success: function (data) {
                        $("#tf-booking-status-loader").removeClass('show');
                    }
                });
            }
        });

        /*
        * active tourfic affiliate plugin
        */
        $(document).on('click', '.tf-affiliate-active', function(e) {
            e.preventDefault();

            var btn = $(this);

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_affiliate_active',
                    nonce: tf_admin_params.tf_nonce,
                },
                beforeSend: function (data) {
                    btn.addClass('tf-btn-loading').css({'pointer-events': 'none'});
                },
                success: function (data) {
                    let response = JSON.parse(data);
                    if( response.status === 'success' ) {
                        notyf.success(response.message);

                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }
                    btn.removeClass('tf-btn-loading').css({'pointer-events': 'auto'});
                },
                error: function (data) {
                    btn.removeClass('tf-btn-loading').css({'pointer-events': 'auto'});
                },
                complete: function (data) {
                    btn.removeClass('tf-btn-loading').css({'pointer-events': 'auto'});
                }
            })
        });

        /*
        * install tourfic affiliate plugin
        */
        $(document).on('click', '.tf-affiliate-install', function(e) {
            e.preventDefault();

            var btn = $(this);

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_affiliate_install',
                    nonce: tf_admin_params.tf_nonce,
                },
                beforeSend: function (data) {
                    btn.addClass('tf-btn-loading').css({'pointer-events': 'none'});
                },
                success: function (data) {
                    let response = JSON.parse(data);
                    if( response.status === 'success' ) {
                        location.reload();
                    }
                    btn.removeClass('tf-btn-loading').css({'pointer-events': 'auto'});
                },
                error: function (data) {
                    btn.removeClass('tf-btn-loading').css({'pointer-events': 'auto'});
                }
            })
        });

        /*
        * Author @Jahid
        * Hotel, Tour, Apartment Duplicator
        */
       
        $('.tf-post-data-duplicate').on('click', function(e) {
            e.preventDefault();
            var postID = $(this).data('postid');
            var postType = $(this).data('posttype');
            var nonce = $(this).data('nonce');
            $('#wpcontent').append('<div class="tf-duplicator-loader"></div>');
            // AJAX request to duplicate post
            $.ajax({
                type: 'POST',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_duplicate_post_data',
                    postID: postID,
                    postType: postType,
                    security: nonce
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(errorThrown) {
                    // Handle errors (if any)
                    console.error('Error duplicating post:', errorThrown);
                }
            });
        });

    });

})(jQuery);