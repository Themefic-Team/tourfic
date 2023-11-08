(function ($) {
    $(document).ready(function () {

        /*
        * Author @Jahid
        * Multivendor Bulk Action
        */

        $('.vendor-actions input#submit').click(function () {
            var balkaction = $('#tf_vendor_bulk').find(":selected").val();
            var vendorlist = $('input[name="vendor_id"]:checked').serializeArray();
            if (balkaction !== "" && vendorlist.length > 0) {
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
        * Author @Jahid, @Foysal
        * Vendor Status
        */
        $('.vendor-status-switcher').click(function () {
            let status = $(this).is(':checked') ? 'enabled' : 'disabled';
            var vendorid = $(this).val();
            $("#tf-report-loader").addClass('show');
            jQuery.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: {
                    action: 'tf_vendor_activation',
                    status: status,
                    vendorid: vendorid,
                },
                success: function (data) {
                    $("#tf-report-loader").removeClass('show');
                }
            });
        });

        /*
        * Author @Jahid, @Foysal
        * Admin Vendor Registration
        */
        $(document).on('click', '#tf-vendor-register .tf-save-user', function (e) {
            e.preventDefault();

            let form = $(this).closest('#tf-vendor-register');
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_vendor_registration');

            $("#tf-report-loader").addClass('show');

            $.ajax({
                url: tf_vendor_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (response) {
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
        * Author @Jahid, @Foysal
        * Admin Vendor Update
        */
        $(document).on('click', '#tf-vendor-update .tf-save-user', function (e) {
            e.preventDefault();

            let form = $(this).closest('#tf-vendor-update');
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_vendor_update');
            $("#tf-report-loader").addClass('show');

            $.ajax({
                url: tf_vendor_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (response) {
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
            var withdraw_id = $(this).attr("data-id");
            var data = {
                action: 'tf_vendor_payouts',
                withdraw_id: withdraw_id
            };
            var admin_confirmation = confirm("Are you sure to Approved?");
            if (admin_confirmation) {
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
            var withdraw_id = $(this).attr("data-id");
            var data = {
                action: 'tf_vendor_payouts_decline',
                withdraw_id: withdraw_id
            };
            var admin_confirmation = confirm("Are you sure to Decline?");
            if (admin_confirmation) {
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
        * vendor cover pic
        * @author Foysal
        */
        $('body').on('click', '.tf-cover-pic-edit', function (e) {
            openMediaUploader('.tf_cover_photo', 'full', '.tf-cover-pic');
        });
        $('body').on('click', '.tf-cover-pic-delete', function (e) {
            removeImage('.tf_cover_photo', '.tf-cover-pic');
        });

        /*
        * vendor profile pic
        * @author Foysal
        */
        $('body').on('click', '.tf-profile-pic-edit', function (e) {
            openMediaUploader('.tf_user_image', 'thumbnail', '.tf-profile-image');
        });
        $('body').on('click', '.tf-profile-pic-delete', function (e) {
            removeImage('.tf_user_image', '.tf-profile-image');
        });

        /*
        * Media Uploader
        * @author Foysal
        */
        const openMediaUploader = (name, size, selector) => {

            if (typeof wp !== 'undefined' && wp.media) {
                const mediaFrame = wp.media({
                    title: 'Select an image',
                    button: {
                        text: 'Use this image',
                    },
                    multiple: false,
                });

                mediaFrame.on('open', () => {
                    if (mediaFrame.content.mode() !== 'browse') {
                        mediaFrame.content.mode('browse'); // set the mode to browse (library tab)
                    }
                });

                mediaFrame.on('select', () => {
                    let attachmentUrl;
                    const selected = mediaFrame.state().get('selection').first().toJSON();
                    if (size) {
                        const attachment = mediaFrame.state().get('selection').first().toJSON();
                        attachmentUrl = attachment.sizes[size] ? attachment.sizes[size].url : attachment.url;
                    } else {
                        attachmentUrl = selected.url;
                    }

                    $(selector).attr('src', attachmentUrl);
                    $(name).val(attachmentUrl);

                });

                mediaFrame.open();
            } else {
                console.log('wp.media is not available');
            }
        }

        /*
        * Media Remove
        * @author Foysal
        */
        const removeImage = (name, selector) => {
            let defaultVal = $(selector).data('default');
            $(name).val('');
            $(selector).attr('src', defaultVal);
        }

        /*
        * Author @Jahid
        * Vendor Dashboard Chart
        */

        if (tf_vendor_params.tf_vendor_chart_enable == 1) {
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
                        label: "Total Sales",
                        borderColor: '#003C79',
                        tension: 0.1,
                        data: tf_vendor_params.tf_total_amount,
                        fill: false
                    },
                        {
                            label: "Vendor Amount",
                            borderColor: 'red',
                            tension: 0.1,
                            data: tf_vendor_params.tf_total_vendor_amount,
                            fill: false
                        },
                        {
                            label: "Admin Commission",
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
            var year = $(".tf-year").val();
            var chartval = [];
            $.each($("input[name='tf-chart-report']:checked"), function () {
                chartval.push($(this).val());
            });
            if (chartval.length == 0 || !month.length) {
                alert("Both Fields are Required !");
                return;
            }
            if (chartval.length > 0) {
                $("#tf-report-loader").addClass('show');
                $('.tf-vendor-order-cart').find('iframe').remove();
            }
            jQuery.ajax({
                type: 'post',
                url: tf_options.ajax_url,
                data: {
                    action: 'tf_vendor_month_reports',
                    month: month,
                    year: year,
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
                                label: response.tf_total_amount ? "Total Sales" : '',
                                borderColor: '#003C79',
                                tension: 0.1,
                                data: response.tf_total_amount,
                                fill: false
                            },
                                {
                                    label: response.tf_total_vendor_amount ? "Vendor Amount" : '',
                                    borderColor: 'red',
                                    tension: 0.1,
                                    data: response.tf_total_vendor_amount,
                                    fill: false
                                },
                                {
                                    label: response.tf_total_commission ? "Admin Commission" : '',
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
                                    filter: function (legendItem, chartData) {
                                        if (legendItem.datasetIndex === 0) {
                                            return response.tf_total_amount ? true : false;
                                        }
                                        if (legendItem.datasetIndex === 1) {
                                            return response.tf_total_vendor_amount ? true : false;
                                        }
                                        if (legendItem.datasetIndex === 2) {
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
                    if (response.vendor_amount == 0) {
                        $(".tf-payout-submit-button").hide();
                    } else {
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

            if (!vendor.length || !amount.length || !payment_date.length || !payment_release_date.length || !payment_method.length || !payment_note.length) {
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
            var withdraw_id = $(this).attr("data-id");
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

            if (!vendor.length || !amount.length || !payment_date.length || !payment_release_date.length || !payment_method.length || !payment_note.length) {
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