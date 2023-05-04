
(function ($) {
    $(document).ready(function () {

        /*
        * Author @Jahid
        * Multivendor Bulk Action
        */

        $('.vendor-actions input#submit').click(function () {
            var balkaction = $('#tf_vendor_bulk').find(":selected").val();
            var vendorlist = $('input[name="vendor_id"]:checked').serializeArray();
            if (balkaction !== "" && vendorlist.length > 0){
                $("#tf-report-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_vendor_params.ajax_url,
                    data: {
                        action: 'tf_vendor_bulk',
                        balkaction: balkaction,
                        vendorlist: vendorlist,
                    },
                    success: function (data) {
                        $("#tf-report-loader").removeClass('show');
                        location.reload();
                    }
                });
            }
        });

        /*
        * Author @Jahid
        * Vendor Status
        */

        $('.vendor-status-switcher').click(function(){
            if ($(this).is(':checked')) {
                var vendorid= $(this).val();
                $("#tf-report-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_vendor_params.ajax_url,
                    data: {
                        action: 'tf_vendor_activation',
                        status: "enabled",
                        vendorid: vendorid,
                    },
                    success: function (data) {
                        $("#tf-report-loader").removeClass('show');
                    }
                });
            }else{
                var vendorid= $(this).val();
                $("#tf-report-loader").addClass('show');
                jQuery.ajax({
                    type: 'post',
                    url: tf_vendor_params.ajax_url,
                    data: {
                        action: 'tf_vendor_activation',
                        status: "disabled",
                        vendorid: vendorid,
                    },
                    success: function (data) {
                        $("#tf-report-loader").removeClass('show');
                    }
                });
            }
        });

        /*
        * Author @Jahid
        * Admin Vendor Registration
        */
        $(document).on('click', '#tf-vendor-register .tf-save-user', function (e) {
            e.preventDefault();
            var tf_reg_nonce = $("input[name=tf_reg_nonce]").val();
            var user = $("input[name=tf_username]").val();
            var first_name = $("input[name=tf_first_name]").val();
            var last_name = $("input[name=tf_last_name]").val();
            var email = $("input[name=tf_user_email]").val();
            var phone = $("input[name=tf_user_phone]").val();
            var bio = $("textarea[name=tf_user_bio]").val();
            var vendor_image = $("input[name=tf_vendor_image]").val();
            var pass = $("input[name=tf_user_password]").val();
            var commission = $("input[name=tf_user_commission]").val();
            var vendor_status = $('input[name="tf_vendor_enabled"]:checked');
            var data = {
                action: 'tf_vendor_registration',
                tf_reg_nonce: tf_reg_nonce,
                user: user,
                email: email,
                pass: pass,
                commission: commission,
                first_name: first_name,
                last_name: last_name,
                phone: phone,
                bio: bio,
                vendor_image: vendor_image,
                vendor_status: vendor_status.length,
            };
            $("#tf-report-loader").addClass('show');

            $.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: data,
                beforeSend: function (response) {
                    //alert(response);
                },
                complete: function (response) {
                    //alert(response);
                },
                success: function (response) {
                    $("#tf-report-loader").removeClass('show');
                    $(".tf-vendor-reg-response").html(response);
                },
                error: function (data) {
                    console.log(data);
                }
            });

        });

        /*
        * Author @Jahid
        * Admin Vendor Update
        */
        $(document).on('click', '#tf-vendor-update .tf-save-user', function (e) {
            e.preventDefault();
            var tf_reg_nonce = $("input[name=tf_reg_nonce]").val();
            var tf_reg_data = $("#tf-vendor-update").serializeArray();
            var vendor_id = $("input[name=tf_vendor_id]").val();
            var first_name = $("input[name=tf_first_name]").val();
            var last_name = $("input[name=tf_last_name]").val();
            var email = $("input[name=tf_user_email]").val();
            var phone = $("input[name=tf_user_phone]").val();
            var pass = $("input[name=tf_user_password]").val();
            var commission = $("input[name=tf_user_commission]").val();
            var vendor_status = $('input[name="tf_vendor_enabled"]:checked');
            var vendor_posts = $('input[name="tf_vendor_posts"]:checked');
            var bio = $("textarea[name=tf_user_bio]").val();
            var vendor_image = $("input[name=tf_vendor_image]").val();
            var data = {
                action: 'tf_vendor_update',
                tf_reg_nonce: tf_reg_nonce,
                tf_reg_data: tf_reg_data,
                vendor_id: vendor_id,
                email: email,
                pass: pass,
                commission: commission,
                first_name: first_name,
                last_name: last_name,
                phone: phone,
                bio: bio,
                vendor_image: vendor_image,
                vendor_status: vendor_status.length,
                vendor_posts: vendor_posts.length,
            };
            $("#tf-report-loader").addClass('show');

            $.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: data,
                beforeSend: function (response) {
                    //alert(response);
                },
                complete: function (response) {
                    //alert(response);
                },
                success: function (response) {
                    $("#tf-report-loader").removeClass('show');
                    $(".tf-vendor-reg-response").html(response);
                },
                error: function (data) {
                    console.log(data);
                }
            });

        });

        /*
        * Author @Jahid
        * Admin Vendor Payout approval
        */
        $(document).on('click', '.tf-payout-pending', function (e) {
            e.preventDefault();
            var withdraw_id =$(this).attr("data-id");
            var data = {
                action: 'tf_vendor_payouts',
                withdraw_id: withdraw_id
            };
            var admin_confirmation = confirm("Are you sure to Approved?");
            if(admin_confirmation){
                $("#tf-report-loader").addClass('show');
                $.ajax({
                    type: 'post',
                    url: tf_vendor_params.ajax_url,
                    data: data,
                    beforeSend: function (response) {
                        //alert(response);
                    },
                    complete: function (response) {
                        //alert(response);
                    },
                    success: function (response) {
                        $("#tf-report-loader").removeClass('show');
                        location.reload();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        });

        /*
        * Author @Jahid
        * Admin Vendor Payout Decline
        */
        $(document).on('click', '.tf-payout-decline', function (e) {
            e.preventDefault();
            var withdraw_id =$(this).attr("data-id");
            var data = {
                action: 'tf_vendor_payouts_decline',
                withdraw_id: withdraw_id
            };
            var admin_confirmation = confirm("Are you sure to Decline?");
            if(admin_confirmation){
                $("#tf-report-loader").addClass('show');
                $.ajax({
                    type: 'post',
                    url: tf_vendor_params.ajax_url,
                    data: data,
                    beforeSend: function (response) {
                        //alert(response);
                    },
                    complete: function (response) {
                        //alert(response);
                    },
                    success: function (response) {
                        $("#tf-report-loader").removeClass('show');
                        location.reload();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        });

        /*
        * Author @Jahid
        * Admin Vendor Image Upload
        */

        $('body').on('click', '.tf-update-vendor-img', function (e) {
            frame = wp.media({
                title: "Select Image",
                button: {
                    text: "Insert Image"
                },
                multiple: false
            });
            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                $('.tf_vendor_image').val(attachment.url);
                $('.profile-pic').attr('src', attachment.url);
            });
            frame.open();
            return false;
        });

        /*
        * Author @Jahid
        * Vendor Dashboard Chart
        */

        if(tf_vendor_params.tf_vendor_chart_enable==1){       
            var ctx = document.getElementById('tf_month_vendor_info'); // node
            var ctx = document.getElementById('tf_month_vendor_info').getContext('2d'); // 2d context
            var ctx = $('#tf_month_vendor_info'); // jQuery instance
            var ctx = 'tf_month_vendor_info'; // element id

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: tf_vendor_params.tf_current_months_days,
                    // Information about the dataset
                    datasets: [{
                            label : "Total Sales",
                            borderColor: '#003C79',
                            tension: 0.1,
                            data: tf_vendor_params.tf_total_amount,       
                            fill: false
                        },
                        {
                            label : "Vendor Amount",
                            borderColor: 'red',
                            tension: 0.1,
                            data: tf_vendor_params.tf_total_vendor_amount,       
                            fill: false
                        },
                        {
                            label : "Admin Commission",
                            borderColor: '#73A724',
                            tension: 0.1,
                            data: tf_vendor_params.tf_total_commission, 
                            fill: false,
                        }
                    ]
                },

                // Configuration options
                options: {
                layout: {
                padding: 10,
                },
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: tf_vendor_params.tf_current_month
                    }
                }

            });
        }

        /*
        * Author @Jahid
        * Vendor Dashboard Chart Filter
        */

        $(document).on('click', '.tf-vendor-submission', function () {
            var month = $(".tf-month").val();
            var chartval=[];
            $.each($("input[name='tf-chart-report']:checked"), function(){            
                chartval.push($(this).val());
            });
            if(chartval.length == 0 || !month.length){
                alert("Both Fields are Required !");
                return;
            }
            if(chartval.length > 0){
                $("#tf-report-loader").addClass('show');
                $('.tf-vendor-order-cart').find('iframe').remove();
            }
            jQuery.ajax({
                type: 'post',
                url: tf_options.ajax_url,
                data: {
                    action: 'tf_vendor_month_reports',
                    month: month,
                    chartval: chartval,
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    var ctx = document.getElementById('tf_month_vendor_info'); // node
                    var ctx = document.getElementById('tf_month_vendor_info').getContext('2d'); // 2d context
                    var ctx = $('#tf_month_vendor_info'); // jQuery instance
                    var ctx = 'tf_month_vendor_info'; // element id

                    var chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.tf_current_months_days,
                            // Information about the dataset
                            datasets: [{
                                    label : response.tf_total_amount ? "Total Sales" : '',
                                    borderColor: '#003C79',
                                    tension: 0.1,
                                    data: response.tf_total_amount,       
                                    fill: false
                                },
                                {
                                    label : response.tf_total_vendor_amount ? "Vendor Amount" : '',
                                    borderColor: 'red',
                                    tension: 0.1,
                                    data: response.tf_total_vendor_amount,       
                                    fill: false
                                },
                                {
                                    label : response.tf_total_commission ? "Admin Commission" : '',
                                    borderColor: '#73A724',
                                    tension: 0.1,
                                    data: response.tf_total_commission, 
                                    fill: false
                                }
                            ]
                        },

                        // Configuration options
                        options: {
                        layout: {
                        padding: 10,
                        },
                            legend: {
                                labels: {
                                    filter: function(legendItem, chartData) {
                                        if (legendItem.datasetIndex === 0 ) {
                                            return response.tf_total_amount ? true : false;
                                        }
                                        if (legendItem.datasetIndex === 1 ) {
                                            return response.tf_total_vendor_amount ? true : false;
                                        }
                                        if (legendItem.datasetIndex === 2 ) {
                                            return response.tf_total_commission ? true : false;
                                        }
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: response.tf_current_month
                            }
                        }

                    });

                    $("#tf-report-loader").removeClass('show');
                }
            })
        });


        /*
        * Author @Jahid
        * Vendor Payout Modal Show/Hide
        */

        $(document).on('click', '.tf-create-payout', function (e) {
            e.preventDefault();
            $(".tf-vendor-payout-modals").show();
        });

        $(document).on('click', '.tf-payout-times', function (e) {
            e.preventDefault();
            $(".tf-vendor-payout-modals").hide();
            $(".tf-vendor-payout-modals-update").hide();
        });

        /*
        * Author @Jahid
        * Payout Added
        */

        $(document).on('change', '.tf-vendor-unique-id', function () {
            var vendor_id = $(this).val();
 
            $("#tf-report-loader").addClass('show');
            jQuery.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: {
                    action: 'tf_vendor_payouts_amount_preview',
                    vendor_id: vendor_id
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    // console.log(response.vendor_amount);
                    $(".tf-unique-vendor-amount").val(response.vendor_amount);
                    if(response.vendor_amount==0){
                        $(".tf-payout-submit-button").hide();
                    }else{
                        $(".tf-payout-submit-button").show();
                    }
                    $("#tf-report-loader").removeClass('show');
                }
            })
        });

        $(document).on('click', '.tf-payout-submit-button', function () {
            var vendor = $("#tf-vendor-id").val();
            var amount = $("#tf-payment-amount").val();
            var payment_date = $("#tf-payment-date").val();
            var payment_release_date = $("#tf-payment-release-date").val();
            var payment_method = $("#tf-vendor-payment-method").val();
            var payment_note = $("#tf-payment-note").val();
           
            if( !vendor.length || !amount.length || !payment_date.length || !payment_release_date.length || !payment_method.length || !payment_note.length ){
                alert("Please Fillup the Fields !");
                return;
            }
            
            $("#tf-report-loader").addClass('show');
            
            jQuery.ajax({
                type: 'post',
                url: tf_options.ajax_url,
                data: {
                    action: 'tf_vendor_payouts_create',
                    vendor: vendor,
                    amount: amount,
                    payment_date: payment_date,
                    payment_release_date: payment_release_date,
                    payment_method: payment_method,
                    payment_note: payment_note
                },
                success: function (data) {
                    $("#tf-report-loader").removeClass('show');
                    $("#tf-vendor-id").val('');
                    $("#tf-payment-amount").val('');
                    $("#tf-payment-date").val('');
                    $("#tf-payment-release-date").val('');
                    $("#tf-vendor-payment-method").val('');
                    $("#tf-payment-note").val('');
                    location.reload();
                }
            })
        });

        
        /*
        * Author @Jahid
        * Payout Update
        */

        $(document).on('click', '.tf-payout-edit', function () {
            var withdraw_id =$(this).attr("data-id");
            $("#tf-report-loader").addClass('show');
            jQuery.ajax({
                type: 'post',
                url: tf_options.ajax_url,
                data: {
                    action: 'tf_vendor_payouts_update',
                    withdraw_id: withdraw_id,
                },
                success: function (data) {
                    $("#tf-report-loader").removeClass('show');
                    $(".tf-vendor-payout-modals-update").show();
                    $("#payout-field-form").html(data);
                }
            })
        });

        $(document).on('click', '.tf-payout-update-button', function () {
            var vendor = $("#tf-uvendor-id").val();
            var amount = $("#tf-upayment-amount").val();
            var payment_date = $("#tf-upayment-date").val();
            var payment_release_date = $("#tf-upayment-release-date").val();
            var payment_method = $("#tf-uvendor-payment-method").val();
            var payment_note = $("#tf-upayment-note").val();
            var payment_id = $("#tf-payment-id").val();
           
            if( !vendor.length || !amount.length || !payment_date.length || !payment_release_date.length || !payment_method.length || !payment_note.length ){
                alert("Please Fillup the Fields !");
                return;
            }
            
            $("#tf-report-loader").addClass('show');
            
            jQuery.ajax({
                type: 'post',
                url: tf_options.ajax_url,
                data: {
                    action: 'tf_vendor_payouts_info_update',
                    vendor: vendor,
                    amount: amount,
                    payment_date: payment_date,
                    payment_release_date: payment_release_date,
                    payment_method: payment_method,
                    payment_note: payment_note,
                    payment_id: payment_id
                },
                success: function (data) {
                    $("#tf-report-loader").removeClass('show');
                    location.reload();
                }
            })
        });

    });
})(jQuery);