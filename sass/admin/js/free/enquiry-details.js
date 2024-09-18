;(function($) {

    "use strict";

    $(document).ready(function() {

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

        $(".tf-enquiry-details-wrap .tf-order-status-filter-btn").on("click", function(e) {

            e.preventDefault();

            let $this = $(this);
            let selected_items = [];
            let actions = $this.closest(".tf-enquiry-details-wrap").find(".tf-filter-bulk-option-enquiry").val();
            let hotelIdFilter = $this.closest(".tf-enquiry-details-wrap").find(".tf-enquiry-filter-hotel-name").val();
            let mainWrap = $this.closest(".tf-enquiry-details-wrap");

            mainWrap.find(".tf-checkbox-listing #tf-enquiry-name-checkbox:checked").each(function() {
                selected_items.push($(this).val());
            });

            $.ajax({
                url: tf_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_enquiry_bulk_action',
                    selected_items: selected_items,
                    bulk_action: actions,
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function() {
                    $this.addClass("loading");
                },
                success: function(response) {
                    let data = $.parseJSON(response);
                    $this.removeClass("loading");
                    if(data.status == "success") {
                        notyf.success(data.msg);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else if (data.status == "error") {
                        notyf.error(data.msg);
                    }
                    
                }
            });
            
        });

        $(".tf-enquiry-filter-hotel-name, .tf-enquiry-filter-tour-name, .tf-enquiry-filter-apartment-name").on('change', function() {
            let $this = $(this);
            let post_id = $this.val() ? $this.val() : '';
            let post_type = $(".enquiry-post-type").val();

            $.ajax({
                url: tf_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_enquiry_filter_post',
                    post_id: post_id,
                    post_type: post_type,
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function() {
                    $this.addClass("loading");
                },
                success: function(response) {

                    if( response.status == "error" ) {
                        $(".tf-enquiry-details-wrap").append(response.msg);
                    } else {
                        $(".tf-enquiry-table").remove();
                        $(".tf-enquiry-details-wrap").append(response);
                    }
                    console.log(response);
                }
            });
        });

    });

})(jQuery);