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

        /**
         * Visitor Details Popup Open
         *
         */
        $(document).on('click', '.visitor_edit span', function (e) {
            e.preventDefault();
            $(".visitor-details-edit-form").show();
        });

        /**
         * Visitor Details Popup Close
         *
         */
        $(document).on('click', '.visitor-details-edit-popup .tf-booking-times span', function (e) {
            e.preventDefault();
            $(".visitor-details-edit-form").hide();
        });

        /**
         * Ajax tour booking
         *
         * tf_visitor_details_edit
         */
        $(document).on('submit', 'form.visitor-details-edit-popup', function (e) {
            e.preventDefault();
            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_visitor_details_edit');
            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $('.tf-preloader-box').show();
                },
                complete: function (data) {
                    
                },
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                },

            });
        });

        /**
         * Vouchers Popup Open
         *
         */
        $(document).on('click', '.tf-voucher-preview .tf-preview-btn a', function (e) {
            e.preventDefault();
            $(".tf-voucher-quick-view-box").show();
        });

        /**
         * Vouchers Popup Close
         *
         */
        $(document).on('click', '.tf-voucher-quick-view-box .tf-quick-view-times span', function (e) {
            e.preventDefault();
            $(".tf-voucher-quick-view-box").hide();
        });
        
    });

})(jQuery);