(function ($) {
    $(document).ready(function () {
        // Select Form
        $('.tf-filter-selection').click(function() {
            $(this).toggleClass('active');
        });

        // Form Selection
        $('.tf-filter-selection ul li').click(function() {
            let $this = $(this).closest('.tf-filter-selection');
            // let selected_value = $(this).attr('data-id');
            let selected_label = $(this).text();
            $this.find('label span').text(selected_label);
        });

        // Tour Filter Section
        $('.tf-tour-filter-options').select2({
            dropdownCssClass: 'tf-booking-filter-modal'
        });
        $('.tf-tour-checkinout-options').select2({
            dropdownCssClass: 'tf-booking-checkinout-filter-modal'
        });
    });

})(jQuery);