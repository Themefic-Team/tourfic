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

        // Select Form
        $('.tf-filter-selection').on("click", function() {
            $(this).toggleClass('active');
        });

        // Form Selection
        $('.tf-filter-selection ul li').on("click", function() {
            let $this = $(this).closest('.tf-filter-selection');
            // let selected_value = $(this).attr('data-id');
            let selected_label = $(this).text();
            $this.find('label span').text(selected_label);
        });

        // Pyment Status Section
        if ($('.tf-order-payment-status, .tf-calendar-order-payment-status').length > 0 ) {
            $('.tf-order-payment-status, .tf-calendar-order-payment-status').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Order Status",
                allowClear: true
            });
        }
        // Bulk Section
        if ( $('.tf-filter-bulk-option').length > 0 ) {
            $('.tf-filter-bulk-option').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Bulk Action",
            });
        }
        
        if ( $('.tf-filter-bulk-option-enquiry').length > 0 ) {
            $('.tf-filter-bulk-option-enquiry').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Bulk Action",
                minimumResultsForSearch: -1,
            });
        }
        if ( $('.tf-enquiry-filter-hotel-name').length > 0 ) {
            $('.tf-enquiry-filter-hotel-name').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Hotel Name",
                allowClear: true
            });
        }
        
        if ( $('.tf-enquiry-filter-apartment-name').length > 0 ) {
            $('.tf-enquiry-filter-apartment-name').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Apartment Name",
                allowClear: true
            });
        }
        if ( $('.tf-enquiry-filter-tour-name ').length > 0 ) {
            $('.tf-enquiry-filter-tour-name').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Tours Name",
                allowClear: true
            });
        }
        if ( $('.tf-filter-mail-option-enquiry').length > 0 ) {
            $('.tf-filter-mail-option-enquiry').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Filters",
                allowClear: true,
                minimumResultsForSearch: -1,
            });
        }
        
        // Tour Post Section
        if( $('.tf-post-id-filter-options, .tf-booking-post-id-filter-options').length > 0 ) {
            $('.tf-post-id-filter-options, .tf-booking-post-id-filter-options').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Tour Name",
                allowClear: true
            });
        }
        
        // Hotel Post Section
        if ( $('.tf-hotel-id-filter-options, .tf-booking-hotel-id-filter-options').length > 0 ) {
            $('.tf-hotel-id-filter-options, .tf-booking-hotel-id-filter-options').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Hotel Name",
                allowClear: true
            });
        }
        
        // Apartment Post Section
        if ( $('.tf-apartment-id-filter-options, .tf-booking-apartment-id-filter-options').length > 0 ) {
            $('.tf-apartment-id-filter-options, .tf-booking-apartment-id-filter-options').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Apartment Name",
                allowClear: true
            });
        }

        // Car Post Section
        if ( $('.tf-car-id-filter-options').length > 0 ) {
            $('.tf-car-id-filter-options').select2({
                dropdownCssClass: 'tf-booking-filter-modal',
                placeholder: "Car Name",
                allowClear: true
            });
        }
        

        // Checked Section
        if ( $('.tf-tour-checkinout-options, .tf-booking-checkinout-options').length > 0 ) {
            $('.tf-tour-checkinout-options, .tf-booking-checkinout-options').select2({
                dropdownCssClass: 'tf-booking-checkinout-filter-modal',
                placeholder: "Checked in status",
                allowClear: true
            });
        }

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
            formData.append('_ajax_nonce', tf_admin_params.tf_nonce);
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
        $('.tf-order-checkinout-status ul li').on("click", function() {
            
            let selected_value = $(this).attr('data-value');
            let order_id = $('.tf_single_order_id').val();

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_checkinout_details_edit',
                    order_id: order_id,
                    checkinout: selected_value,
                    _ajax_nonce: tf_admin_params.tf_nonce
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
        $('.tf-order-ostatus ul li').on("click", function() {
            
            let selected_value = $(this).attr('data-value');
            let order_id = $('.tf_single_order_id').val();

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_order_status_edit',
                    order_id: order_id,
                    status: selected_value,
                    _ajax_nonce: tf_admin_params.tf_nonce
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
         * Ajax Order Status Email Resend
         *
         * tf_order_status_email_resend
         */
        $('.tf-order-email-resend ul li').on("click", function() {
            
            let selected_value = $(this).attr('data-value');
            let order_id = $('#tf_email_order_id').val();
            let db_id = $('.tf_single_order_id').val();

            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_order_status_email_resend',
                    order_id: order_id,
                    status: selected_value,
                    id : db_id,
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function (data) {
                    $('.tf-preloader-box').show();
                },
                complete: function (data) {
                    
                },
                success: function (data) {
                    $('.tf-preloader-box').hide();
                    notyf.success("Email Sucessfully Resend!");
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

        $("#cb #cb-select-all-1").on("click", function() {
            $('input[name="order_id[]"]').each(function() {
                $(this).prop("checked", !$(this).prop("checked"));
            });
        });

        /**
         * Filter Bulk Action
         *
         * tf_order_bulk_action_edit
         */
        $('.tf-order-status-filter-btn').on("click", function() {
            
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
                        status: bulk_action,
                        _ajax_nonce: tf_admin_params.tf_nonce
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
         * Search Filter BY ID boly
         *
         * tf-right-search-filter
         */
        $(document).on('submit', 'form.tf-right-search-filter', function (e) {
            e.preventDefault();

            let id = $("#tf-searching-key").val();
            if(id!==""){
                $('.tf-preloader-box').show();
                let currentURL = window.location.href;
                let BaseURL = currentURL.split('?')[0];
                let queryString = currentURL.split('?')[1];

                let currentURLParams= new URLSearchParams(queryString);
                currentURLParams.delete("paged");
                if (currentURLParams.has("post")) {
                    currentURLParams.set("post", id);
                    let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                    window.location.href = updatedUrl;
                }else{
                    let updatedUrl = currentURL + "&post=" + id;
                    window.location.href = updatedUrl;
                }
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
                currentURLParams.set("checkinout", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }

            //Nonce
            if (!currentURLParams.has("nonce")) {
                currentURLParams.set("nonce", tf_admin_params.tf_nonce);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }
        });

        /**
         * Filter Post Perameter Passing
         *
         */
        $('.tf-post-id-filter-options, .tf-hotel-id-filter-options, .tf-apartment-id-filter-options').change(function() {
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
                currentURLParams.set("post", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }

            //Nonce
            if (!currentURLParams.has("nonce")) {
                currentURLParams.set("nonce", tf_admin_params.tf_nonce);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
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
                currentURLParams.set("payment", changeValue);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }
            
            //Nonce
            if (!currentURLParams.has("nonce")) {
                currentURLParams.set("nonce", tf_admin_params.tf_nonce);
                let updatedUrl = BaseURL.split('?')[0] + '?' + currentURLParams.toString();
                window.location.href = updatedUrl;
            }
        });

        // Booking View Change

        $(".tf_booking_views_button ul li").on("click", function() {
            $(".tf_booking_views_button ul li").removeClass('active');
            let $this =  $(this);
            let view = $this.attr('data-view');
            $this.addClass('active');
            if('calendar'==view){
                $('.tf-booking-header-filter').hide();
                $('.tf-order-table-responsive').hide();
                $('#tf-booking-calendar').css('padding', '24px');
                $('.tf-calendar-booking-header-filter').css('display', 'flex');
                $('#tf-booking-calendar').show();
                
                // Re-render the calendar
                initializeCalendar();
                
            }
            if('list'==view){
                $('#tf-booking-calendar').hide();
                $('.tf-calendar-booking-header-filter').hide();
                $('.tf-booking-header-filter').css('display','flex');
                $('.tf-order-table-responsive').show();
            }
        });

        // Booking Details Popup close
        $(document).on('click', '.tf-booking-calendar-popup-box .tf-popup-header .tf-close', function (e) {
            $(".tf-booking-calendar-popup-box").hide();
        });

        // Booking Details Popup
        $(document).on('click', '.tf-booking-single-popup', function (e) {
            e.preventDefault();
            let $this = $(this);
            $('.tf-calendar-popup-box').html('');
            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_booking_details_popup',
                    id: $this.attr('data-id'),
                    type: $this.attr('data-type'),
                    page: $this.attr('data-page'),
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function (data) {
                    $('.tf-preloader-box').show();
                },
                complete: function (data) {
                    
                },
                success: function (data) {
                    $('.tf-calendar-popup-box').html(data);
                    $('.tf-preloader-box').hide();
                    $('.tf-booking-calendar-popup-box').css('display', 'flex');
                },
                error: function (data) {
                    console.log(data);
                },

            });
        });

        // Booking Calendar Filter
        $(document).on('change', '.tf-calendar-order-payment-status, .tf-booking-checkinout-options, .tf-filter-by-post', function (e) {
            e.preventDefault();
            let ostatus = $('.tf-calendar-order-payment-status').val();
            let checkinout = $('.tf-booking-checkinout-options').val();
            let post_id = $('.tf-filter-by-post').val();
            
            $.ajax({
                type: 'post',
                url: tf_admin_params.ajax_url,
                data: {
                    action: 'tf_booking_calendar_filter',
                    ostatus: ostatus,
                    checkinout: checkinout,
                    post_id: post_id,
                    post_type: $('#tf_booking_post_type').val(),
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function (data) {
                    $('.tf-preloader-box').show();
                },
                complete: function (data) {
                    
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    $('.tf-preloader-box').hide();
                    initializeCalendar(data.events);
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });

    });

})(jQuery);

// Booking Calendar
function initializeCalendar(eventsSource) {
    var calendarEl = document.getElementById('tf-booking-calendar');
    var currentPageUrl = window.location.href;

    // Set the events based on the page URL
    if (!eventsSource) {
        if (currentPageUrl.includes('post_type=tf_tours&page=tf_tours_booking')) {
            eventsSource = tf_options.tf_tours_orders;
        } else if (currentPageUrl.includes('post_type=tf_hotel&page=tf_hotel_booking')) {
            eventsSource = tf_options.tf_hotels_orders;
        } else if (currentPageUrl.includes('post_type=tf_apartment&page=tf_apartment_booking')) {
            eventsSource = tf_options.tf_apartments_orders;
        } else if (currentPageUrl.includes('post_type=tf_carrental&page=tf_carrental_booking')) {
            eventsSource = tf_options.tf_cars_orders;
        } else {
            eventsSource = []; // Fallback option if none of the conditions match
        }
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        editable: false,
        selectable: true,
        dayMaxEvents: 3,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: eventsSource,
        eventContent: function(info) {
            var customEl = document.createElement('div');
            customEl.classList.add('tf-booking-single-popup');
            customEl.setAttribute('data-id', info.event.id);
            customEl.setAttribute('data-type', info.event.extendedProps.post_type);
            customEl.setAttribute('data-page', info.event.extendedProps.page);
            customEl.innerHTML = `<span>${info.event.title}</span>`;
            return { domNodes: [customEl] };
        }
    });

    calendar.render();
}

document.addEventListener('DOMContentLoaded', function() {
    var tfcalendarEl = document.getElementById('tf-booking-calendar');

    if (tfcalendarEl) {
        var eventsSource;
        
        if (tfcalendarEl.dataset.set) {
            initializeCalendar(eventsSource);
            tfcalendarEl.style.display = 'none';
        } else {
            initializeCalendar(eventsSource);
        }
    } 
});
