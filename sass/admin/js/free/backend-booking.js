(function ($) {
    $(document).ready(function () {

        // Create an instance of Notyf
        const notyf = new Notyf({
            ripple: true,
            duration: 3000,
            dismissable: true,
            position: {
                x: 'right',
                y: 'bottom',
            },
        });

        const alert_popup = {
            success: function (message) {
                $.confirm({
                    icon: 'fa fa-check',
                    theme: 'modern',
                    title: 'Success!',
                    content: message,
                    type: 'green',
                    animat6ionSpeed: 300,
                    animation: 'scale',
                    closeAnimation: 'scale',
                    animateFromElement: false,
                    typeAnimated: true,
                    boxWidth: '500px',
                    useBootstrap: false,
                    closeIcon: true,
                    buttons: {
                        OK: {
                            btnClass: 'btn-blue',
                        }
                    }
                })
            },

            error: function (message) { 
                $.confirm({
                    icon: 'fa fa-times',
                    theme: 'modern',
                    title: 'Error!',
                    content: message,
                    type: 'red',
                    typeAnimated: true,
                    animationSpeed: 300,
                    animation: 'scale',
                    closeAnimation: 'scale',
                    animateFromElement: false,
                    boxWidth: '500px',
                    useBootstrap: false,
                    closeIcon: true,
                    buttons: {
                        OK: {
                            btnClass: 'btn-red',
                        }
                    }
                })
            }
        }

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
                        _nonce: tf_admin_params.tf_nonce,
                        from: from,
                        to: to,
                    },
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(!response.success){
                            notyf.error(response.data)
                        } else {
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
                            $('[name="tf_hotel_rooms_number"]').removeAttr('disabled');
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    complete: function (response) {
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
            var from = $('[name="tf_hotel_date[from]"]').val();
            var to = $('[name="tf_hotel_date[to]"]').val();

            if (hotel_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_room',
                        _nonce: tf_admin_params.tf_nonce,
                        hotel_id: hotel_id,
                        from: from,
                        to: to,
                    },
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(!response.success){
                            notyf.error(response.data)
                        } else {
                            var select2 = $('[name="tf_available_rooms"]');
                            var serviceSelect = $('[name="tf_hotel_service_type"]');

                            select2.removeAttr('disabled');
                            select2.empty();
                            select2.append('<option value="">' + tf_admin_params.select_room + '</option>');
                            $.each(response.data.rooms, function (key, value) {
                                select2.append('<option value="' + key + '">' + value + '</option>');
                            });
                            select2.select2();
                            //auto select the first option
                            select2.val(select2.find('option:eq(1)').val()).trigger('change');

                            //service type select
                            serviceSelect.empty();
                            $.each(response.data.services, function (key, value) {
                                serviceSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            $('#tf-backend-hotel-book-btn').removeAttr('disabled');
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    complete: function (response) {
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
            var from = $('[name="tf_hotel_date[from]"]').val();
            var to = $('[name="tf_hotel_date[to]"]').val();

            if (room_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_update_room_fields',
                        _nonce: tf_admin_params.tf_nonce,
                        hotel_id: hotel_id,
                        room_id: room_id,
                        from: from,
                        to: to,
                    },
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(!response.success){
                            notyf.error(response.data)
                        } else {
                            var select = $('[name="tf_hotel_rooms_number"]');
                            select.empty();
                            
                            if(response.data.rooms > 0 ){
                                for (var i = 1; i <= response.data.rooms; i++) {
                                    if (i === 1) {
                                        select.append('<option value="' + i + '" selected>' + i + ' Room</option>');
                                    } else {
                                        select.append('<option value="' + i + '">' + i + ' Rooms</option>');
                                    }
                                }
                                
                                $('#tf-backend-hotel-book-btn').removeAttr('disabled');
                            } else {
                                select.append('<option value="" selected>No Room Available</option>');
                                select.attr('disabled', 'disabled');
                            }

                            $('[name="tf_hotel_adults_number"]').val(response.data.adults).attr('max', response.data.adults * response.data.rooms);
                            $('[name="tf_hotel_children_number"]').val(response.data.children).attr('max', response.data.children * response.data.rooms);
                        }
                    },
                    error: function (response) {
                        console.log(response);
                        $('#tf-backend-hotel-book-btn').removeAttr('disabled');
                    },
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
            let requiredFields = [
                'tf_hotel_booked_by', 'tf_customer_first_name', 'tf_customer_email', 'tf_customer_phone', 'tf_customer_country', 'tf_customer_address', 'tf_customer_city', 'tf_customer_state', 'tf_customer_zip', 'tf_hotel_date[from]', 'tf_hotel_date[to]', 'tf_available_hotels', 'tf_available_rooms', 'tf_hotel_rooms_number', 'tf_hotel_adults_number', 'tf_hotel_children_number'];

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
                            alert_popup.error(obj.message);

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
                        alert_popup.success(obj.message)

                        alert_popup.success(obj.message)

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
                error: function (response) {
                    console.log(response);
                },
                complete: function (response) {
                    btn.removeClass('tf-btn-loading');
                }
            })

        });


        /*
        * Check available tour by date
        * Author @Foysal
        */
        /*$(document).on('change', '[name="tf_tour_date"], [name="tf_tour_adults_number"], [name="tf_tour_children_number"]', function (e) {
            e.preventDefault();

            var tourDate = $('[name="tf_tour_date"]').val();
            var adults = $('[name="tf_tour_adults_number"]').val();
            var children = $('[name="tf_tour_children_number"]').val();

            if (tourDate.length > 0 && adults.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_tour',
                        tourDate: tourDate,
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
        });*/

        /*
        * Tour time and extra fields update
        * Author @Foysal
        */
        $(document).on('change', '[name="tf_available_tours"]', function (e) {
            e.preventDefault();

            var tourId = $('[name="tf_available_tours"]').val();

            if (tourId) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_tour_date_time_update',
                        _nonce: tf_admin_params.tf_nonce,
                        tour_id: tourId,
                    },
                    beforeSend: function () {
                        $('#tf-backend-hotel-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(response){
                            const obj = JSON.parse(response);

                            if (obj.custom_avail !== '1') {
                                populateObjectTimeSelect(obj.allowed_times)
                            }

                            let flatpickerObj = {
                                enableTime: false,
                                dateFormat: "Y/m/d",
                            };

                            if (obj.tour_availability) {
                                flatpickerObj.minDate = "today";
                                flatpickerObj.disableMobile = "true";
                                flatpickerObj.enable = Object.entries(obj.tour_availability)
                                .filter(([dateRange, data]) => data.status === "available")
                                .map(([dateRange, data]) => {
                                    const [fromRaw, toRaw] = dateRange.split(' - ').map(str => str.trim());
                    
                                    const today = new Date();
                                    const formattedToday = today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate();
                                    let fromDate = fromRaw;
                    
                                    return {
                                        from: fromDate,
                                        to: toRaw
                                    };
                                });
                            }

                            flatpickerObj.onChange = function (selectedDates, dateStr, instance) {
                                // Initialize empty object for times
                                let times = {};
                                const selectedDate = selectedDates[0];
                                const timestamp = selectedDate.getTime();

                                const tourAvailability = obj.tour_availability;

                                for (const key in tourAvailability) {
                                    const availability = tourAvailability[key];

                                    if (availability.status !== 'available') continue;

                                    const from = new Date(availability.check_in.trim()).getTime();
                                    const to   = new Date(availability.check_out.trim()).getTime();

                                    if (timestamp >= from && timestamp <= to) {
                                        const allowedTime = availability.allowed_time?.time || [];

                                        allowedTime.forEach((t) => {
                                            if (t && t.trim() !== '') {
                                                times[t] = t;
                                            }
                                        });

                                        break; // stop after first match
                                    }
                                }

                                populateTimeSelect(times);
                                
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            }

                            $("[name='tf_tour_date']").flatpickr(flatpickerObj);

                            if (obj.tour_extras_array && Object.keys(obj.tour_extras_array).length > 0) {
                                let extras = $('[name="tf_tour_extras[]"]');
                                extras.removeAttr('disabled');
                                extras.empty();
                            
                                $.each(obj.tour_extras_array, function (key, value) {
                                    extras.append($('<option>', {
                                        value: key,
                                        html: value // Use html to parse entities like &#36;
                                    }));
                                });
                            
                                extras.select2();
                            } else {
                                let extras = $('[name="tf_tour_extras[]"]');
                                extras.empty().attr('disabled', 'disabled');
                            }

                            $('#tf-backend-hotel-book-btn').removeAttr('disabled');
                        }
                    }
                });
            }
        });

        function populateObjectTimeSelect(times) {
            let timeSelect = $('[name="tf_tour_time"]');
            timeSelect.empty();

            if (Object.keys(times).length > 0) {
                // Use the keys and values from the object to populate the options
                $.each(times, function (key, value) {
                    timeSelect.append(`<option value="${key}">${value}</option>`);
                });
                timeSelect.attr('disabled', false);
            } else {
                timeSelect.append(`<option value="" selected>No Time Available</option>`);
                timeSelect.attr('disabled', 'disabled');
            }

        }

        function populateTimeSelect(times) {
            let timeSelect = $('[name="tf_tour_time"]');
            timeSelect.empty();

            if (Object.keys(times).length > 0) {
                // Use the keys and values from the object to populate the options
                $.each(times, function (key, value) {
                    timeSelect.append(`<option value="${key}">${value}</option>`);
                });
            } else {
                timeSelect.append(`<option value="" selected>No Time Available</option>`);
                timeSelect.attr('disabled', 'disabled');
            }

        }

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
            let requiredFields = ['tf_tours_booked_by', 'tf_customer_first_name', 'tf_customer_email', 'tf_customer_phone', 'tf_customer_country', 'tf_customer_address', 'tf_customer_city', 'tf_customer_state', 'tf_customer_zip', 'tf_tour_date', 'tf_available_tours', 'tf_tour_adults_number', 'tf_tour_children_number'];

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
                        alert_popup.error(obj.message)

                        if (obj.errors) {
                            obj.errors.forEach(function (text) {
                                notyf.error(text);
                            });
                        }

                        form.find('input').removeClass('error-input');
                        form.find('select').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('select').closest('.tf-fieldset').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-fieldset').find('small.text-danger').remove();

                        if (obj['fieldErrors']) {
                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name="' + requiredField + '"]').removeClass('error-input');
                                form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').find('small.text-danger').remove();
                                if (errorField) {
                                    form.find('[name="' + requiredField + '"]').addClass('error-input');
                                    form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').append('<small class="text-danger">' + errorField + '</small>');
                                }
                            }
                        }
                    } else {
                        alert_popup.success(obj.message)

                        alert_popup.success(obj.message)

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

        /**
         * Backend Apartments Booking
        */
        
        // Chekck Available Apartment by Date

        $(document).on('change', '[name="tf_apartment_date[from]"], [name="tf_apartment_date[to]"]', function (e) {
            e.preventDefault();

            var fromValue = $('[name="tf_apartment_date[from]"]').val();
            var toValue = $('[name="tf_apartment_date[to]"]').val();
            var apartment_id = $('[name="tf_available_apartments"]').val()

            if (fromValue.length > 0 && toValue.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_available_apartment',
                        _nonce: tf_admin_params.tf_nonce,
                        from: fromValue,
                        to: toValue,
                        apartment_id: apartment_id
                    },
                    beforeSend: function () {
                        $('#tf-backend-apartment-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(!response.success){
                            notyf.error(response.data)
                        } else {
                            var select2 = $('[name="tf_available_apartments"]');
                            select2.empty();
                            select2.append('<option value="">' + 'Select Apartment' + '</option>');
                            $.each(response.data.apartments, function (key, value) {
                                select2.append('<option value="' + key + '">' + value + '</option>');
                            });
                            // select2.select2();

                            //select the first option
                            select2.val(select2.find('option:eq(1)').val()).trigger('change');
                            $('#tf-backend-apartment-book-btn').removeAttr('disabled');
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    complete: function (response) {
                        $('#tf-backend-apartment-book-btn').removeAttr('disabled');
                    }
                });
            }
        })
        
        // Available Additional Fees

        $(document).on('change', '[name="tf_available_apartments"]', function (e) {
            e.preventDefault();

            var apartment_id = $('[name="tf_available_apartments"]').val();
            var from = $('[name="tf_apartment_date[from]"]').val();
            var to = $('[name="tf_apartment_date[to]"]').val();

            if (apartment_id.length > 0) {
                jQuery.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_check_apartment_aditional_fees',
                        _nonce: tf_admin_params.tf_nonce,
                        apartment_id: apartment_id,
                        from: from,
                        to: to,
                    },
                    beforeSend: function () {
                        $('#tf-backend-apartment-book-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if(!response.success){
                            notyf.error(response.data)
                        } else {
                            var serviceSelect = $('[name="tf_apartment_additional_fees"]');

                            serviceSelect.select2({multiple: true});

                            //Additional fees auto selection
                            serviceSelect.empty();

                            if (response.data.additional_fees.length > 0) {
                                $.each(response.data.additional_fees, function (key, value) {
                                    serviceSelect.append('<option value="' + key + '">' + value.label + ' - ' + value.price + '</option>');
                                });
                            } else {
                                serviceSelect.append('<option value="' + 1 + '">' + 'There are no additional fees' + '</option>');
                            }

                            serviceSelect.find('option').prop('selected', true).trigger('change');

                            $('#tf-backend-apartment-book-btn').removeAttr('disabled');
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    complete: function (response) {
                        $('#tf-backend-apartment-book-btn').removeAttr('disabled');
                    }
                });
            }
        });

        /*
        * Backend Apartment Booking
        */
        $(document).on('click', '#tf-backend-apartment-book-btn', function (e) {
            e.preventDefault();

            let btn = $(this);
            let form = btn.closest('form.tf-backend-apartment-booking');
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_backend_apartment_booking');
            let requiredFields = [
                'tf_apartment_booked_by',
                'tf_customer_first_name',
                'tf_customer_email',
                'tf_customer_phone',
                'tf_customer_country',
                'tf_customer_address',
                'tf_customer_city',
                'tf_customer_state',
                'tf_customer_zip',
                'tf_apartment_date[from]',
                'tf_apartment_date[to]',
                'tf_available_apartments',
                'tf_apartment_adults_number',
                'tf_apartment_children_number',
                'tf_apartment_infant_number',
            ];

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
                            alert_popup.error(obj.message)

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
                                if (requiredField === 'tf_apartment_date[from]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-from').find('small.text-danger').remove();
                                } else if (requiredField === 'tf_apartment_date[to]') {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-date-to').find('small.text-danger').remove();
                                } else {
                                    form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').find('small.text-danger').remove();
                                }
                                if (errorField) {
                                    form.find('[name="' + requiredField + '"]').addClass('error-input');
                                    if (requiredField === 'tf_apartment_date[from]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-from').append('<small class="text-danger">' + errorField + '</small>');
                                    } else if (requiredField === 'tf_apartment_date[to]') {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-date-to').append('<small class="text-danger">' + errorField + '</small>');
                                    } else {
                                        form.find('[name="' + requiredField + '"]').closest('.tf-fieldset').append('<small class="text-danger">' + errorField + '</small>');
                                    }
                                }
                            }
                        }
                    } else {
                        alert_popup.success(obj.message)

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
                error: function (response) {
                    console.log(response);
                },
                complete: function (response) {
                    btn.removeClass('tf-btn-loading');
                }
            })
        });
    });

})(jQuery);