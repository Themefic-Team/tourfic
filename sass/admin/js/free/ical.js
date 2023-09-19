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

            let $this = $(this);
            let iCalUrl = $this.closest('.tf-fieldset').

            console.log('clicked');
        });
    });

})(jQuery);