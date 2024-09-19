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
                }
            });
        });

        $("#tf-single-enquiry-reply-form").on('submit', function(e) {
            e.preventDefault();

            let $this = $(this);
            let reply_mail = $this.find(".tf-enquiry-reply-email").val();
            let reply_message = $this.find(".tf-enquiry-reply-textarea").val();
            let userName = $this.find(".tf-enquiry-reply-name").val();
            let subject = $this.find(".tf-enquiry-reply-subject").val();
            let post_id = $this.find(".tf-enquiry-reply-post-id").val();
            let enquiry_id = $this.find(".tf-enquiry-reply-id").val();


            $.ajax({
                url: tf_admin_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_enquiry_reply_email',
                    reply_mail: reply_mail,
                    reply_message: reply_message,
                    user_name: userName,
                    subject: subject,
                    post_id: post_id,
                    enquiry_id: enquiry_id,
                    _ajax_nonce: tf_admin_params.tf_nonce
                },
                beforeSend: function() {
                    $("#tf-enquiry-status-loader").addClass("show");
                },
                success: function(response) {
                    $("#tf-enquiry-status-loader").removeClass("show");

                    let data = JSON.parse(response);
                    
                    if(data.status == "success") {
                        notyf.success(data.msg);
                    } else if (data.status == "error") {
                        notyf.error(data.msg);
                    }
                },
                error: function(data) {
                    console.log(data);
                },
            });
        })

        $(document).on("click", ".tf-single-enquiry-copy-btn", function (e) {
            let $this = $(this),
                copy_text = $this.closest(".tf-single-enquiry-details-value").data("enquiry-copy-text"),
                $temp = $("<input>"),
                copy_ip_addr = $(this).parent().parent().find(".tf-single-enquiry-log-details-single-value").data("enquiry-copy-text");
            
            copy_text = copy_text ? copy_text : copy_ip_addr;

            $("body").append($temp);
            $temp.val(copy_text).select();
            document.execCommand("copy");
            $temp.remove();
            notyf.success("Copied to clipboard");
        });
    });

})(jQuery);