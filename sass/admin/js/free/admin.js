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
                deleteAll: $(this).data('delete-all')
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
            };

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                beforeSend: function (data) {
                    notyf.success(tf_admin_params.deleting_room_order_ids);
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
        $(document).on('click', '.post-type-tf_tours #publish, .post-type-tf_tours #save-post', function (e) {
            if ($('textarea[name="tf_tours_opt[text_location]"]').val().length === 0) {
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
            if ($('textarea[name="tf_hotels_opt[address]"]').val().length === 0) {
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
            if ($('[name="tf_apartment_opt[address]"]').val().length === 0) {
                e.preventDefault;
                e.stopImmediatePropagation();
                notyf.error(tf_admin_params.apartment_location_required);
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
                    current[0].click();
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

        $('.tf-ticket-status').click(function () {
            if ($(this).is(':checked')) {
                var order_unique_id = $(this).val();
                $("#tf-booking-status-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_ticket_status_change',
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
        * Check available hotel room from date to date
        * Author @Foysal
        */
        $(document).on('change', '[name="tf_hotel_date[from]"], [name="tf_hotel_date[to]"]', function (e) {
            e.preventDefault();

            var from = $('[name="tf_hotel_date[from]"]').val();
            var to = $('[name="tf_hotel_date[to]"]').val();

            if (from.length > 0 && to.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_hotel',
                        from: from,
                        to: to,
                    },
                    success: function (response) {
                        console.log(response.data.hotels)
                        var select2 = $('[name="tf_available_hotels"]');
                        select2.empty();
                        select2.append('<option value="">' + tf_admin_params.select_hotel + '</option>');
                        $.each(response.data.hotels, function (key, value) {
                            select2.append('<option value="' + key + '">' + value + '</option>');
                        });
                        select2.select2();
                        //select the first option
                        select2.val(select2.find('option:eq(1)').val()).trigger('change');
                    }
                });
            }
        });

        /*
        * Room filter on hotel change
        * Author @Foysal
        */
        $(document).on('change', '[name="tf_available_hotels"]', function (e) {
            e.preventDefault();

            var hotel_id = $('[name="tf_available_hotels"]').val();

            if (hotel_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_room',
                        hotel_id: hotel_id,
                    },
                    success: function (response) {
                        var select2 = $('[name="tf_available_rooms"]');
                        //remove disabled attr
                        select2.removeAttr('disabled');
                        select2.empty();
                        select2.append('<option value="">' + tf_admin_params.select_room + '</option>');
                        $.each(response.data.rooms, function (key, value) {
                            select2.append('<option value="' + key + '">' + value + '</option>');
                        });
                        select2.select2();
                        //auto select the first option
                        select2.val(select2.find('option:eq(1)').val()).trigger('change');
                    }
                });
            }
        });

        /*
        * Room adults, children, infants fields update on room change
        * Author @Foysal
        */
        $(document).on('change', '[name="tf_available_rooms"]', function (e) {
            e.preventDefault();

            let hotel_id = $('[name="tf_available_hotels"]').val();
            let room_id = $('[name="tf_available_rooms"]').val();

            if (room_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_update_room_fields',
                        hotel_id: hotel_id,
                        room_id: room_id,
                    },
                    success: function (response) {
                        $('[name="tf_hotel_rooms_number"]').val(response.data.rooms).attr('max', response.data.rooms);
                        $('[name="tf_hotel_adults_number"]').val(response.data.adults).attr('max', response.data.adults);
                        $('[name="tf_hotel_children_number"]').val(response.data.children).attr('max', response.data.children);
                    }
                });
            }
        });

        /*
        * Backend Hotel Booking
        * Author @Foysal
        */
        $(document).on('click', '#tf-backend-hotel-book-btn', function (e) {
            e.preventDefault();

            let btn = $(this);
            let form = btn.closest('form.tf-backend-hotel-booking');
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_backend_hotel_booking');
            let requiredFields = ['tf_hotel_booked_by', 'tf_customer_first_name', 'tf_customer_last_name', 'tf_customer_email', 'tf_customer_phone', 'tf_customer_address', 'tf_customer_city', 'tf_customer_state', 'tf_customer_zip', 'tf_hotel_date[from]', 'tf_hotel_date[to]', 'tf_available_hotels', 'tf_available_rooms', 'tf_hotel_rooms_number', 'tf_hotel_adults_number'];

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (!obj.success) {
                        if (obj.message) {
                            // Swal.fire(
                            //     'Error!',
                            //     obj.message,
                            //     'error'
                            // )
                            form.find('input').removeClass('error-input');
                            form.find('textarea').removeClass('error-input');
                            form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                            form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                        } else {

                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name="' + requiredField + '"]').removeClass('error-input');
                                form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').find('small.text-danger').remove();
                                if (errorField) {
                                    form.find('[name="' + requiredField + '"]').addClass('error-input');
                                    if (requiredField === 'tf_hotel_date[from]') {
                                        form.find('[name="' + requiredField + '"]').closest('div').append('<small class="text-danger">' + errorField + '</small>');
                                    } else if (requiredField === 'tf_hotel_date[to]') {
                                        form.find('[name="' + requiredField + '"]').closest('div').append('<small class="text-danger">' + errorField + '</small>');
                                    } else {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').append('<small class="text-danger">' + errorField + '</small>');
                                    }
                                }
                            }
                        }
                    } else {
                        // Swal.fire(
                        //     'Success!',
                        //     obj.message,
                        //     'success'
                        // )
                        form[0].reset();
                        form.find('input').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                    }
                    // if (obj.redirect_url) {
                    //window.location.href = obj.redirect_url;
                    // }
                    btn.removeClass('tf-btn-loading');
                },

            })

        });
    });

})(jQuery);