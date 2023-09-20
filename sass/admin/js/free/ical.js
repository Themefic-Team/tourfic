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
        $(document).on('click', '#room-ical-import', function (e) {
            e.preventDefault();

            let btn = $(this);
            let iCalUrl = btn.closest('.tf-fieldset').find('.ical_url_input').val();

            if (iCalUrl === '') {
                notyf.error('Please enter iCal Url');
                return false;
            }

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_import_ical',
                    ical_url: iCalUrl,
                },
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    console.log('obj', obj);

                    btn.removeClass('tf-btn-loading');
                },
                error: function (response) {
                    btn.removeClass('tf-btn-loading');
                    console.log('error', response);
                }
            })

        });
    });

})(jQuery);