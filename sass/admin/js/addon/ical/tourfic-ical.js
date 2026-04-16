(function ($) {
    $(document).ready(function () {
        const getErrorMessage = function (response, fallbackMessage) {
            if (response && response.responseJSON && response.responseJSON.data && response.responseJSON.data.message) {
                return response.responseJSON.data.message;
            }

            return fallbackMessage;
        };

        const ensureStatusWrap = function (btn) {
            let fieldset = btn.closest('.tf-fieldset');
            let statusWrap = fieldset.find('.tf-ical-status-wrap');

            if (!statusWrap.length) {
                statusWrap = $('<div class="tf-ical-status-wrap"></div>');
                fieldset.append(statusWrap);
            }

            return statusWrap;
        };

        const loadIcalStatus = function () {
            let postId = $('#post_ID').val();
            let importButton = $('.room-ical-import, .apt-ical-import').first();

            if (!postId || !importButton.length) {
                return;
            }

            $.ajax({
                type: 'post',
                url: tf_ical_params.ajax_url,
                data: {
                    action: 'tf_ical_sync_status',
                    tf_nonce: tf_ical_params.nonce,
                    post_id: postId,
                },
                success: function (response) {
                    if (response.data.status === true && response.data.status_html) {
                        ensureStatusWrap(importButton).html(response.data.status_html);
                    }
                },
            });
        };

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
        

        /*
        * Hotel iCal Import
        * Author @Foysal
        */
        $(document).on('click', '.room-ical-import', function (e) {
            e.preventDefault();

            let btn = $(this);
            let iCalUrl = btn.closest('.tf-fieldset').find('.ical_url_input').val();
            let postId = $('#post_ID').val();
            let roomIndex = btn.data('room-index');
            let pricingBy = btn.data('pricing-type');
            let avail_date = btn.closest('.tf-single-repeater-room').find('.avail_date');

            if (iCalUrl === '') {
                notyf.error('Please enter iCal Url');
                return false;
            }

            $.ajax({
                type: 'post',
                url: tf_ical_params.ajax_url,
                data: {
                    action: 'tf_hotel_ical_import',
                    tf_nonce: tf_ical_params.nonce,
                    ical_url: iCalUrl,
                    post_id: postId,
                    room_index: roomIndex,
                    pricing_by: pricingBy,
                },
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    if (response.data.status === true) {
                        notyf.success(response.data.message);
                        avail_date.val(response.data.avail_date);
                        if (response.data.status_html) {
                            ensureStatusWrap(btn).html(response.data.status_html);
                        }
                    } else {
                        notyf.error(response.data.message);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                error: function (response) {
                    btn.removeClass('tf-btn-loading');
                    notyf.error(getErrorMessage(response, 'iCal import failed'));
                    console.log('error', response);
                },
                complete: function (response) {
                    btn.removeClass('tf-btn-loading');
                }
            })

        });

        /*
        * Apartment iCal Import
        * Author @Foysal
        */
        $(document).on('click', '.apt-ical-import', function (e) {
            e.preventDefault();

            let btn = $(this);
            let iCalUrl = btn.closest('.tf-fieldset').find('.ical_url_input').val();
            let postId = $('#post_ID').val();
            let pricingType = btn.data('pricing-type');

            if (iCalUrl === '') {
                notyf.error('Please enter iCal Url');
                return false;
            }

            $.ajax({
                type: 'post',
                url: tf_ical_params.ajax_url,
                data: {
                    action: 'tf_apartment_ical_import',
                    tf_nonce: tf_ical_params.nonce,
                    ical_url: iCalUrl,
                    post_id: postId,
                    pricing_by: pricingType,
                },
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    if (response.data.status === true) {
                        notyf.success(response.data.message);
                        $('.apt_availability').val(response.data.apt_availability);
                        if (response.data.status_html) {
                            ensureStatusWrap(btn).html(response.data.status_html);
                        }
                    } else {
                        notyf.error(response.data.message);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                error: function (response) {
                    btn.removeClass('tf-btn-loading');
                    notyf.error(getErrorMessage(response, 'iCal import failed'));
                    console.log('error', response);
                },
                complete: function (response) {
                    btn.removeClass('tf-btn-loading');
                }
            })

        });

        loadIcalStatus();


    });

})(jQuery);
