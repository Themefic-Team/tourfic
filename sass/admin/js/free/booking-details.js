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

        // Pyment Status Section
        $('.tf-order-payment-status').select2({
            dropdownCssClass: 'tf-booking-filter-modal',
            placeholder: "Payment Status",
            allowClear: true
        });
        // Bulk Section
        $('.tf-filter-bulk-option').select2({
            dropdownCssClass: 'tf-booking-filter-modal',
            placeholder: "Bulk Action",
        });
        // Post Section
        $('.tf-post-id-filter-options').select2({
            dropdownCssClass: 'tf-booking-filter-modal',
            placeholder: "Tour Name",
            allowClear: true
        });
        // Checked Section
        $('.tf-tour-checkinout-options').select2({
            dropdownCssClass: 'tf-booking-checkinout-filter-modal',
            placeholder: "Checked in status",
            allowClear: true
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
         * Ajax Checkinout Status
         *
         * tf_checkinout_details_edit
         */
        $('.tf-order-checkinout-status ul li').click(function() {
            
            let selected_value = $(this).attr('data-value');
            let order_id = $('.tf_single_order_id').val();

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_checkinout_details_edit',
                    order_id: order_id,
                    checkinout: selected_value
                },
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
         * Ajax Order Status Status
         *
         * tf_order_status_edit
         */
        $('.tf-order-ostatus ul li').click(function() {
            
            let selected_value = $(this).attr('data-value');
            let order_id = $('.tf_single_order_id').val();

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_order_status_edit',
                    order_id: order_id,
                    status: selected_value
                },
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

        // Filter Checkbox Selected

        $("#cb #cb-select-all-1").click(function() {
            $('input[name="order_id[]"]').each(function() {
                $(this).prop("checked", true);
            });
        });

        /**
         * Filter Bulk Action
         *
         * tf_order_bulk_action_edit
         */
        $('.tf-order-status-filter-btn').click(function() {
            
            let order_list = [];
            let bulk_action = $('.tf-filter-bulk-option').val();
            $('.tf_booking_details_wrap input[name="order_id[]"]:checked').each(function() {
                order_list.push($(this).val());
            });
            
            if(order_list.length > 0 && bulk_action!==''){
                $.ajax({
                    type: 'post',
                    url: tf_admin_params.ajax_url,
                    data: {
                        action: 'tf_order_bulk_action_edit',
                        orders: order_list,
                        status: bulk_action
                    },
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
            }
        });

        /**
         * Filter Checked Perameter Passing
         *
         */
        $('.tf-tour-checkinout-options').change(function() {
            let changeValue = $(this).val();
            $('.tf-preloader-box').show();
            let currentURL = window.location.href;
            let BaseURL = currentURL.split('?')[0];
            let queryString = currentURL.split('?')[1];

            let currentURLParams= new URLSearchParams(queryString);
            currentURLParams.delete("paged");
            if (currentURLParams.has("checkinout")) {
                currentURLParams.set("checkinout", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }else{
                let updatedUrl = currentURL + "&checkinout=" + changeValue;
                window.location.href = updatedUrl;
            }
        });

        /**
         * Filter Post Perameter Passing
         *
         */
        $('.tf-post-id-filter-options').change(function() {
            let changeValue = $(this).val();
            $('.tf-preloader-box').show();
            let currentURL = window.location.href;
            let BaseURL = currentURL.split('?')[0];
            let queryString = currentURL.split('?')[1];

            let currentURLParams= new URLSearchParams(queryString);
            currentURLParams.delete("paged");
            if (currentURLParams.has("post")) {
                currentURLParams.set("post", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }else{
                let updatedUrl = currentURL + "&post=" + changeValue;
                window.location.href = updatedUrl;
            }
        });

        /**
         * Filter Post Perameter Passing
         *
         */
        $('.tf-order-payment-status').change(function() {
            let changeValue = $(this).val();
            $('.tf-preloader-box').show();
            let currentURL = window.location.href;
            let BaseURL = currentURL.split('?')[0];
            let queryString = currentURL.split('?')[1];

            let currentURLParams= new URLSearchParams(queryString);
            currentURLParams.delete("paged");
            if (currentURLParams.has("payment")) {
                currentURLParams.set("payment", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }else{
                let updatedUrl = currentURL + "&payment=" + changeValue;
                window.location.href = updatedUrl;
            }
        });
        
    });

})(jQuery);