(function ($) {
    $(document).ready(function () {

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
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        var select2 = $('[name="tf_available_hotels"]');
                        select2.empty();
                        select2.append('<option value="">' + tf_admin_params.select_hotel + '</option>');
                        $.each(response.data.hotels, function (key, value) {
                            select2.append('<option value="' + key + '">' + value + '</option>');
                        });
                        select2.select2();
                        //select the first option
                        select2.val(select2.find('option:eq(1)').val()).trigger('change');
                        $('#tf-backend-hotel-book-btn').removeAttr('disabled');
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
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
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
                        $('#tf-backend-hotel-book-btn').removeAttr('disabled');
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
            console.log('room_id', room_id)

            if (room_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_update_room_fields',
                        hotel_id: hotel_id,
                        room_id: room_id,
                    },
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        var select = $('[name="tf_hotel_rooms_number"]');
                        select.empty();
                        for (var i = 1; i <= response.data.rooms; i++) {
                            if(i === 1){
                                select.append('<option value="' + i + '" selected>' + i + ' Room</option>');
                            } else {
                                select.append('<option value="' + i + '">' + i + ' Rooms</option>');
                            }
                        }

                        $('[name="tf_hotel_adults_number"]').val(response.data.adults).attr('max', response.data.adults * response.data.rooms);
                        $('[name="tf_hotel_children_number"]').val(response.data.children).attr('max', response.data.children * response.data.rooms);

                        $('#tf-backend-hotel-book-btn').removeAttr('disabled');
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
            let requiredFields = ['tf_hotel_booked_by', 'tf_customer_first_name', 'tf_customer_last_name', 'tf_customer_email', 'tf_customer_phone', 'tf_customer_country', 'tf_customer_address', 'tf_customer_city', 'tf_customer_state', 'tf_customer_zip', 'tf_hotel_date[from]', 'tf_hotel_date[to]', 'tf_available_hotels', 'tf_available_rooms', 'tf_hotel_rooms_number', 'tf_hotel_adults_number', 'tf_hotel_children_number'];

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
                            Swal.fire(
                                'Error!',
                                obj.message,
                                'error'
                            )
                            form.find('input').removeClass('error-input');
                            form.find('select').removeClass('error-input');
                            form.find('textarea').removeClass('error-input');
                            form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                            form.find('select').closest('.tf-fieldset').find('small.text-danger').remove();
                            form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                        } else {

                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name="' + requiredField + '"]').removeClass('error-input');
                                if (requiredField === 'tf_hotel_date[from]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-from').find('small.text-danger').remove();
                                } else if (requiredField === 'tf_hotel_date[to]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-to').find('small.text-danger').remove();
                                } else {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').find('small.text-danger').remove();
                                }
                                if (errorField) {
                                    form.find('[name="' + requiredField + '"]').addClass('error-input');
                                    if (requiredField === 'tf_hotel_date[from]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-from').append('<small class="text-danger">' + errorField + '</small>');
                                    } else if (requiredField === 'tf_hotel_date[to]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-to').append('<small class="text-danger">' + errorField + '</small>');
                                    } else {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').append('<small class="text-danger">' + errorField + '</small>');
                                    }
                                }
                            }
                        }
                    } else {
                        Swal.fire(
                            'Success!',
                            obj.message,
                            'success'
                        )
                        form[0].reset();
                        form.find('input').removeClass('error-input');
                        form.find('select').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('select').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                    }
                    btn.removeClass('tf-btn-loading');
                },

            })

        });


        /*
        * Check available tour by date
        * Author @Foysal
        */
        $(document).on('change', '[name="tf_tour_date[from]"], [name="tf_tour_date[to]"], [name="tf_tour_adults_number"], [name="tf_tour_children_number"]', function (e) {
            e.preventDefault();

            var from = $('[name="tf_tour_date[from]"]').val();
            var to = $('[name="tf_tour_date[to]"]').val();
            var adults = $('[name="tf_tour_adults_number"]').val();
            var children = $('[name="tf_tour_children_number"]').val();

            if (from.length > 0 && to.length > 0 && adults.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_tour',
                        from: from,
                        to: to,
                        adults: adults,
                        children: children
                    },
                    beforeSend: function () {
                        $('#tf-backend-tour-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        var select2 = $('[name="tf_available_tours"]');
                        select2.empty();
                        select2.append('<option value="">' + tf_admin_params.select_tour + '</option>');
                        $.each(response.data.tours, function (key, value) {
                            select2.append('<option value="' + key + '">' + value + '</option>');
                        });
                        select2.select2();
                        //select the first option
                        select2.val(select2.find('option:eq(1)').val()).trigger('change');
                        $('#tf-backend-tour-book-btn').removeAttr('disabled');
                    }
                });
            }
        });

        /*
        * Backend Tour Booking
        * Author @Foysal
        */
        $(document).on('click', '#tf-backend-tour-book-btn', function (e) {
            e.preventDefault();

            let btn = $(this);
            let form = btn.closest('form.tf-backend-tour-booking');
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_backend_tour_booking');
            let requiredFields = ['tf_tour_booked_by', 'tf_customer_first_name', 'tf_customer_last_name', 'tf_customer_email', 'tf_customer_phone', 'tf_customer_country', 'tf_customer_address', 'tf_customer_city', 'tf_customer_state', 'tf_customer_zip', 'tf_tour_date[from]', 'tf_tour_date[to]', 'tf_available_tours', 'tf_tour_adults_number', 'tf_tour_children_number'];

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
                            Swal.fire(
                                'Error!',
                                obj.message,
                                'error'
                            )
                            form.find('input').removeClass('error-input');
                            form.find('select').removeClass('error-input');
                            form.find('textarea').removeClass('error-input');
                            form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                            form.find('select').closest('.tf-fieldset').find('small.text-danger').remove();
                            form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                        } else {

                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name="' + requiredField + '"]').removeClass('error-input');
                                if (requiredField === 'tf_tour_date[from]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-from').find('small.text-danger').remove();
                                } else if (requiredField === 'tf_tour_date[to]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-to').find('small.text-danger').remove();
                                } else {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').find('small.text-danger').remove();
                                }
                                if (errorField) {
                                    form.find('[name="' + requiredField + '"]').addClass('error-input');
                                    if (requiredField === 'tf_tour_date[from]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-from').append('<small class="text-danger">' + errorField + '</small>');
                                    } else if (requiredField === 'tf_tour_date[to]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-to').append('<small class="text-danger">' + errorField + '</small>');
                                    } else {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').append('<small class="text-danger">' + errorField + '</small>');
                                    }
                                }
                            }
                        }
                    } else {
                        Swal.fire(
                            'Success!',
                            obj.message,
                            'success'
                        )
                        form[0].reset();
                        form.find('input').removeClass('error-input');
                        form.find('select').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('select').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();
                    }
                    btn.removeClass('tf-btn-loading');
                },

            })

        });
    });

})(jQuery);