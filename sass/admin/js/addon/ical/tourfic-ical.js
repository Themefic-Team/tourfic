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
        

        /*
        * Check available hotel room from date to date
        * Author @Foysal
        */
        $(document).on('click', '.room-ical-import', function (e) {
            e.preventDefault();

            let btn = $(this);
            let iCalUrl = btn.closest('.tf-fieldset').find('.ical_url_input').val();
            let postId = $('#post_ID').val();
            let roomIndex = btn.data('room-index');
            let pricingBy = btn.data('pricing-by');
            let avail_date = btn.closest('.tf-single-repeater-room').find('.avail_date');

            if (iCalUrl === '') {
                notyf.error('Please enter iCal Url');
                return false;
            }

            $.ajax({
                type: 'post',
                url: tf_ical_params.ajax_url,
                data: {
                    action: 'tf_import_ical',
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
                    } else {
                        notyf.error(response.data.message);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                error: function (response) {
                    btn.removeClass('tf-btn-loading');
                    console.log('error', response);
                },
                complete: function (response) {
                    btn.removeClass('tf-btn-loading');
                }
            })

        });
    });

})(jQuery);