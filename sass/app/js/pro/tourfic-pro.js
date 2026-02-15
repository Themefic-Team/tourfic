(function ($) {
    $(document).ready(function () {
        /**
         * Deposit amount toggle
         */
        $(document).on("click", "input[name='make_deposit']", function () {
            let id = $(this).val();
            if ($(this).is(':checked')) {
                $('.tf-deposit-amount-' + id).removeClass("tf-hotel-deposit-hide");
            } else {
                $('.tf-deposit-amount-' + id).addClass("tf-hotel-deposit-hide");
            }
        });

        const alert_popup = {
            success: function (title, message) {
                $.confirm({
                    icon: 'fa fa-check',
                    theme: 'modern',
                    title: title,
                    content: message,
                    type: 'green',
                    typeAnimated: true,
                    boxWidth: '500px',
                    animationSpeed: 300,
                    animation: 'scale',
                    closeAnimation: 'scale',
                    animateFromElement: false,
                    useBootstrap: false,
                    closeIcon: true,
                    buttons: {
                        OK: {
                            btnClass: 'btn-blue',
                            
                        }
                    }
                })
            },

            error: function (title, message) {
                $.confirm({
                    icon: 'fa fa-times',
                    theme: 'modern',
                    title: title,
                    content: message,
                    type: 'red',
                    typeAnimated: true,
                    animationSpeed: 300,
                    animation: 'scale',
                    closeAnimation: 'scale',
                    animateFromElement: false,
                    boxWidth: '500px',
                    useBootstrap: false,
                    closeIcon: true,
                    buttons: {
                        OK: {
                            btnClass: 'btn-red',
                        }
                    }
                })
            }
        }

        /**
         * Airport Service Price
         */
        $(document).on("change", "#airport-service", function (e) {

            $("#tour_room_details_loader").show();
            var service_type = $(this).val();
            var roomid = $(this).closest('.fancybox-slide').find('input[name=room_id]').val();
            var hotelid = $("#hotel-post-id").val();
            var hoteladult = $("#adults").val();
            var hotelchildren = $("#children").val();
            var deposit = $("input[name=hotel_room_depo]").val();
            var room = $("#hotel_room_number").val();
            var check_in_date = $("input[name=check_in_date]").val();
            var check_out_date = $("input[name=check_out_date]").val();
            var option_id = $(this).closest('.fancybox-slide').find("input[name=option_id]").val();
            var data = {
                action: 'tf_hotel_airport_service_price',
                _nonce: tf_params.nonce,
                service_type: service_type,
                roomid: roomid,
                option_id: option_id,
                id: hotelid,
                hoteladult: hoteladult,
                hotelchildren: hotelchildren,
                room: room,
                check_in_date: check_in_date,
                check_out_date: check_out_date,
                deposit: deposit
            };
            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {

                    $("#tour_room_details_loader").hide();
                    $(".tf-airport-pickup-response").html(response);
                },
            });
        });

        /*
        * Affiliate booking form ajax
        * @author Foysal
        */
        $(document).on('submit', '#tf_affiliate_booking_form', function (e) {
            e.preventDefault();

            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                data = form.serializeArray();

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: {
                    action: 'tf_affiliate_booking_form',
                    formData: data,
                },
                beforeSend: function () {
                    form.css({'opacity': '0.5', 'pointer-events': 'none'});
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');

                    if (obj.status === 'success') {
                        $('#tf-affiliate-booking-modal').remove();
                        let modal = obj.modalHtml;
                        $('body').append(modal).addClass('tf-modal-open');
                        $('#tf-affiliate-booking-modal').addClass('tf-modal-show');
                    } else {
                        alert_popup.error(obj.title, obj.message);
                    }
                },
                error: function () {

                }
            })
        });

        /*
        * TravelPayouts Flight ajax
        * @author Foysal
        */
        $(document).on('submit', '#tf_travelpayouts_flight_form', function (e) {
            e.preventDefault();

            let form = $(this),
                data = form.serializeArray(),
                submitBtn = form.find('.tf-submit');

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: {
                    action: 'tf_pro_travelpayouts_flight_search',
                    formData: data,
                },
                beforeSend: function () {
                    form.css({'opacity': '0.5', 'pointer-events': 'none'});
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');

                    if (obj.status === 'success') {
                        if (obj.white_label) {
                            $('#tf-travelpayouts-flight-modal').remove();
                            let modal = obj.modalHtml;
                            $('body').append(modal).addClass('tf-modal-open');
                            $('#tf-travelpayouts-flight-modal').addClass('tf-modal-show');
                        } else {
                            window.open(obj.url, '_blank');
                        }
                    } else {
                        alert_popup.error(obj.title, obj.message);
                    }
                },
                error: function () {

                }
            })
        })

        /*
        * TravelPayouts Hotel ajax
        * @author Foysal
        */
        $(document).on('submit', '#tf_travelpayouts_hotel_form', function (e) {
            e.preventDefault();

            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                data = form.serializeArray();

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: {
                    action: 'tf_pro_travelpayouts_hotel_search',
                    formData: data,
                },
                beforeSend: function () {
                    form.css({'opacity': '0.5', 'pointer-events': 'none'});
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');

                    if (obj.status === 'success') {
                        if (obj.white_label) {
                            $('#tf-travelpayouts-hotel-modal').remove();
                            let modal = obj.modalHtml;
                            $('body').append(modal).addClass('tf-modal-open');
                            $('#tf-travelpayouts-hotel-modal').addClass('tf-modal-show');
                        } else {
                            window.open(obj.url, '_blank');
                        }
                    } else {
                        alert_popup.error(obj.title, obj.message);
                    }
                },
                error: function () {

                }
            })
        })

        /*
        * Custom Modal
        * @author Foysal
        */
        $(document).on('click', '.tf-modal-btn', function () {
            var dataTarget = $(this).attr('data-target');
            $(dataTarget).addClass('tf-modal-show');
            $('body').addClass('tf-modal-open');
        });
        $(document).on("click", '.tf-modal-close', function () {
            $('.tf-modal').removeClass('tf-modal-show');
            $('body').removeClass('tf-modal-open');
        });
        $(document).on("click", function (event) {
            if(!$('.tf-map-modal').length) {
                if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                    $("body").removeClass("tf-modal-open");
                    $(".tf-modal").removeClass("tf-modal-show");
                }
            }
        });

        /*
        * Trip Class checkbox
        * @author Foysal
        */
        $(document).on('click', 'input[name=trip-class]', function () {
            let checked = $(this).is(':checked');

            if (checked) {
                $('.trip-class-text').text(tf_pro_params.business_class);
            } else {
                $('.trip-class-text').text(tf_pro_params.economy_class);
            }

        });

        /*
        * DeBounce
        * @author Foysal
        */
        const deBounce = (fn, delay) => {
            let timer;
            return function () {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, arguments);
                }, delay);
            }
        }

        /*
        * TravelPayouts Flight Autocomplete
        * @author Foysal
        */
        $('.tf_travelpayouts_location').each(function () {
            let thisElm = $(this),
                parent = thisElm.closest('.tf_label-row');

            $(this).on('keyup', function (event) {
                let name = thisElm.data('name'),
                    locale = thisElm.data('locale'),
                    val = thisElm.val();

                if (val.length >= 2) {
                    $.getJSON("https://autocomplete.travelpayouts.com/jravia?locale=" + locale + "&with_countries=false&q=" + val, function (data) {
                        if (typeof data == 'object') {
                            if (data.length > 0) {
                                $.each(data, function (key, value) {
                                    if (key === 0) {
                                        parent.find('input[name=' + name + ']').val(value.code);
                                    }
                                });
                            }
                        }
                    })
                } else {
                    parent.find('input[name=' + name + ']').val('');
                }
            });

            $(this).on('keyup', deBounce(function (event) {
                let name = thisElm.data('name'),
                    locale = thisElm.data('locale'),
                    val = thisElm.val();

                $('.tf_travelpayouts_location_list').remove();

                if (val.length >= 2) {
                    $.getJSON("https://autocomplete.travelpayouts.com/jravia?locale=" + locale + "&with_countries=false&q=" + val, function (data) {
                        if (typeof data == 'object') {
                            var html = '';
                            html += '<div class="tf_travelpayouts_location_list">';
                            html += '<ul>';
                            if (data.length > 0) {
                                $.each(data, function (key, value) {
                                    var f_name = value.name != null ? '(' + value.name + ')' : '';
                                    html += '<li data-value="' + value.code + '" data-text="' + value.city_fullname + ' - ' + value.code + '"><span>' + get_highlight(value.city_fullname, val) + ' ' + f_name + '</span><strong class="tf_travelpayouts_location_code">' + value.code + '</strong></li>';
                                });
                            } else {
                                html += '<li class="tf-no-result">' + tf_pro_params.no_result + '</li>';
                            }
                            html += '</ul>';
                            html += '</div>';
                            parent.append(html);
                        }
                    })
                }
            }, 300));
        });

        /*
        * TravelPayouts Hotel Autocomplete
        * @author Foysal
        */
        $('.tf_travelpayouts_hotel_location').each(function () {
            let thisElm = $(this),
                parent = thisElm.closest('.tf_label-row');

            $(this).on('keyup', deBounce(function (event) {
                let name = thisElm.data('name'),
                    locale = thisElm.data('locale'),
                    val = thisElm.val();

                $('.tf_travelpayouts_location_list').remove();

                if (val.length >= 2) {
                    $.getJSON("https://engine.hotellook.com/api/v2/lookup.json?query=" + val + "&lang=" + locale + "&limit=5", function (data) {

                        if (typeof data == 'object') {
                            var html = '';
                            html += '<div class="tf_travelpayouts_location_list">';
                            html += '<ul>';
                            if (data.status === 'ok') {
                                $.each(data.results.locations, function (key, value) {
                                    html += '<li data-type="location" data-value="' + value.fullName + '" data-text="' + value.fullName + '"><span>' + get_highlight(value.fullName, val) + ' - <strong>' + value.hotelsCount + ' ' + thisElm.data('text') + '</strong></span></li>'
                                });
                                $.each(data.results.hotels, function (key, value) {
                                    html += '<li data-type="hotel" data-value="' + value.fullName + '" data-text="' + value.fullName + '"><span>' + get_highlight(value.fullName, val) + '</span></li>'
                                });
                            } else {
                                html += '<li class="tf-no-result">' + tf_pro_params.no_result + '</li>';
                            }
                            html += '</ul>';
                            html += '</div>';
                            parent.append(html);
                        }
                    })
                }
            }, 300));
        });


        function get_highlight(text, val) {
            return text.replace(new RegExp(val, 'gi'), '<strong>$&</strong>');
        }

        $(document).on('click focus', function (event) {
            if (!$(event.target).closest(".tf_travelpayouts_location,.tf_travelpayouts_location_list").length) {
                $(".tf_travelpayouts_location_list").remove();
            }
        });

        /*
        * Select Location
        * @author Foysal
        */
        $(document).on('click', '.tf_travelpayouts_location_list li', function () {
            let text = $(this).data('text');
            let value = $(this).data('value');
            let parent = $(this).closest('.tf_label-row');
            let input = parent.find('.tf_tp_autocomplete');
            let name = input.data('name');
            input.val(text);
            parent.find('input[name=' + name + ']').val(value);
            $('.tf_travelpayouts_location_list').remove();
        })

        //   Tour chart preview
        if (tf_params.showitinerarychart == 1) {
            if ($('#tour-itinerary-chart').length > 0) {
                var ctx = document.getElementById('tour-itinerary-chart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: tf_params.itinerarayday,
                        // Information about the dataset
                        datasets: [{
                            label: tf_params.elevvationmode,
                            backgroundColor: 'lightblue',
                            borderColor: 'royalblue',
                            tension: 0.1,
                            data: tf_params.itineraraymeter,
                        }]
                    },

                    // Configuration options
                    options: {
                        tooltips: {
                            enabled: true,
                            mode: 'label',
                            callbacks: {
                                label: function (tooltipItems, data) {
                                    return tooltipItems.yLabel + ' ' + tf_params.elevvationmode;
                                }
                            }
                        },
                        layout: {
                            padding: 10,
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: tf_params.elevvationmode
                        },
                        scales: {
                            yAxes: [{
                                scaleLabel: {
                                    display: false,
                                    labelString: ''
                                },
                                ticks: {
                                    display: tf_params.showyaxis == 1 ? true : false
                                },
                                gridLines: {
                                    display: tf_params.showlinegraph == 1 ? true : false,
                                    drawBorder: tf_params.showlinegraph == 1 ? true : false,
                                }
                            }],
                            xAxes: [{
                                scaleLabel: {
                                    display: false,
                                    labelString: ''
                                },
                                ticks: {
                                    display: tf_params.showxaxis == 1 ? true : false
                                },
                                gridLines: {
                                    display: tf_params.showlinegraph == 1 ? true : false
                                }
                            }]
                        }
                    }

                });
            }
        }


        //Tour Expand/Close
        $('#itinerary-switcher').on("click", function () {
            if ($(this).is(':checked')) {
                $(".tf-ininerary-content").show();
                $('.arrow').addClass('arrow-animate');
                $('.ininerary-other-gallery').slick({
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    arrows: true,
                    fade: false,
                    adaptiveHeight: true,
                    infinite: true,
                    useTransform: true,
                    speed: 400,
                    cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                    responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1,
                        }
                    }, {
                        breakpoint: 640,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    }, {
                        breakpoint: 420,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    }]
                });
            } else {
                $(".tf-ininerary-content").hide();
                $('.arrow').removeClass('arrow-animate');
            }
        });

        if(tf_params.showitinerarystatus == 1) {
            if($(".tf-accordion-content")) {
                $(".tf-accordion-content").find('.ininerary-other-gallery').slick({
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    arrows: true,
                    fade: false,
                    adaptiveHeight: true,
                    infinite: true,
                    useTransform: true,
                    speed: 400,
                    cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                    responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1,
                        }
                    }, {
                        breakpoint: 640,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    }, {
                        breakpoint: 420,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    }]
                });
            }
            

            if( $('.tf-itinerary-content-wrap')) {
                $(document).find( ".tf-itinerary-content-wrap" ).show()
            }
            $(document).find( ".tf-itinerary-content-box" ).show()
        }

        /**
         * Ajax login
         */
        $(document).on('submit', '#tf-login', function (e) {
            e.preventDefault();

            let btn = $(this).find('.tf-submit');
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_login');
            let requiredFields = ['tf_log_user', 'tf_log_pass'];

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (!obj.success) {
                        if (obj.message) {
                            alert_popup.error(obj.title, obj.message);

                            form.find('input').removeClass('error-input');
                            form.find('textarea').removeClass('error-input');
                            form.find('input').closest('.tf-reg-field').find('small.text-danger').remove();
                            form.find('textarea').closest('.tf-reg-field').find('small.text-danger').remove();
                        } else {
                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name=' + requiredField + ']').removeClass('error-input');
                                form.find('[name=' + requiredField + ']').closest('.tf-reg-field').find('small.text-danger').remove();
                                if (errorField) {
                                    form.find('[name=' + requiredField + ']').addClass('error-input');
                                    form.find('[name=' + requiredField + ']').closest('.tf-reg-field').append('<small class="text-danger">' + errorField + '</small>');
                                }
                            }
                        }
                    } else {
                         alert_popup.success("Success!", obj.message);

                        form[0].reset();
                        form.find('input').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-reg-field').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-reg-field').find('small.text-danger').remove();
                    }
                    if (obj.redirect_url) {
                        window.location.href = obj.redirect_url;
                    }
                    btn.removeClass('tf-btn-loading');
                },
            });

        });

        /**
         * Open login popup
         *
         * add class "tf-login-popup" in button/link
         */
        $(document).on('click', '.tf-login-popup', function (e) {
            e.preventDefault();

            $.fancybox.open({
                src: '#tf-login-popup',
                type: 'inline',
            });

        });

        /**
         * Ajax registration
         */
        $(document).on('submit', '#tf-register', function (e) {
            e.preventDefault();

            let btn = $(this).find('.tf-submit');
            let wishlistItems = getWish();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action', 'tf_registration');
            formData.append('wishlist', JSON.stringify(wishlistItems));
            let requiredFields = ['tf_user', 'tf_email', 'tf_pass', 'tf_pass_confirm'];
            let extra_register_fields = form.find('[name=extra_register_fields]').val();
            let vendor_reg = form.find('[name=vendor_reg]').val();

            if (extra_register_fields) {
                let extra_register_fields_obj = JSON.parse(extra_register_fields);
                for (const extra_register_field of extra_register_fields_obj) {
                    requiredFields.push(extra_register_field);
                }
            }
            if (vendor_reg == 1) {
                requiredFields.push('tf_role');
            }

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (!obj.success) {
                        if (obj.message) {
                            alert_popup.error("Error!", obj.message);

                            form.find('input').removeClass('error-input');
                            form.find('textarea').removeClass('error-input');
                            form.find('input').closest('.tf-reg-field').find('small.text-danger').remove();
                            form.find('textarea').closest('.tf-reg-field').find('small.text-danger').remove();
                        } else {

                            for (const requiredField of requiredFields) {
                                const errorField = obj['fieldErrors'][requiredField + '_error'];

                                form.find('[name=' + requiredField + ']').removeClass('error-input');
                                form.find('[name=' + requiredField + ']').closest('.tf-reg-field').find('small.text-danger').remove();
                                if (errorField) {
                                    form.find('[name=' + requiredField + ']').addClass('error-input');
                                    form.find('[name=' + requiredField + ']').closest('.tf-reg-field').append('<small class="text-danger">' + errorField + '</small>');
                                }
                            }
                        }
                    } else {
                         $.confirm({
                            icon: 'fa fa-check',
                            theme: 'modern',
                            title: "Success!",
                            content: obj.message,
                            type: 'green',
                            typeAnimated: true,
                            boxWidth: '500px',
                            useBootstrap: false,
                            animation: 'scale',
                            closeAnimation: 'scale',
                            animateFromElement: false,
                            closeIcon: true,
                            animationSpeed: 300,
                            buttons: {
                                OK: {
                                    btnClass: 'btn-blue',
                                    action: function() {
                                        if (obj.redirect_url) {
                                            window.location.href = obj.redirect_url;
                                        }
                                    },

                                }
                            }
                        })
                        form[0].reset();
                        form.find('input').removeClass('error-input');
                        form.find('textarea').removeClass('error-input');
                        form.find('input').closest('.tf-reg-field').find('small.text-danger').remove();
                        form.find('textarea').closest('.tf-reg-field').find('small.text-danger').remove();
                    }
                    if (obj.redirect_url) {
                        //window.location.href = obj.redirect_url;
                    }
                    btn.removeClass('tf-btn-loading');
                },
            });

        });

        if($('[name=tf_role]').length > 0) {
            $(document).on('change', '[name=tf_role]', function () {
                let role = $(this).val();
                if (role == 'tf_vendor') {
                    $('.tf-reg-extra-fields').show();
                } else {
                    $('.tf-reg-extra-fields').hide();
                }
            });
        }

        /* get wishlist from localstorage  */
        const wishKey = 'wishlist_item';
        const getWish = () => {
            let userLists = localStorage.getItem(wishKey);
            // if list is null then init list else make array from json string
            return (userLists === null) ? [] : JSON.parse(userLists);
        }

        /**
         * Resend email verification url
         */
        $(document).on('click', '.resend-email-verification', function (e) {
            e.preventDefault();

            var user_id = $(this).attr("data-id");

            var data = {
                action: 'tf_resend_verification',
                user_id: user_id,
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $(".tf-verification-msg").html(tf_pro_params.email_sent_success);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        /**
         * Open registration popup
         *
         * add class "tf-reg-popup" in button/link
         */
        $(document).on('click', '.tf-reg-popup', function (e) {
            e.preventDefault();

            $.fancybox.open({
                src: '#tf-reg-popup',
                type: 'inline',
            });

        });

        // // QR Code Scan Open
        // $(document).on('click', '.tf_qr_open', function (e) {
        //     e.preventDefault();
        //     TFQRSCANER();
        // });

        // QR Code Scan Another
        $(document).on('click', '.tf_scan_another', function (e) {
            e.preventDefault();
            $(".tf-final-submission-feedback").hide();
            $(".tf-final-error-feedback").hide();
        });


        // QR Code Scan Back
        $(document).on('click', '.tf_scan_back', function (e) {
            e.preventDefault();
            location.reload();
        });

        // QR Code Scan Verify
        $(document).on('click', '.tf_qr_verify', function (e) {
            e.preventDefault();
            var qr_code = $(".tf_qr_code_number").val();
            $(".tf-scanner-preloader").show();
            var data = {
                action: 'tf_qr_code_verification',
                _nonce: tf_params.nonce,
                tf_qr_code: qr_code,
            };

            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                success: function (data) {
                    var response = JSON.parse(data);
                    if (response.qr_code_response_checked == "true") {
                        $(".tf-scanner-quick-review").html("");
                        $(".tf-final-submission-form").hide();
                        $(".tf-scanner-preloader").hide();
                        $(".tf-final-submission-feedback").show();
                        $(".tf-tour-infos").html(response.tf_qr_code_result);
                    } else {
                        $(".tf-scanner-quick-review").html("");
                        $(".tf-final-submission-form").hide();
                        $(".tf-scanner-preloader").hide();
                        $(".tf-final-error-feedback").show();
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        // Itinerary Map Popup Slider Arrow Hide and Show

        $(document).on("mouseenter", '#itn-infowindow', function(e) {
            if($(".itinerary-map-popup-img").length > 1 ) {
                $(e.currentTarget).find(".itinerary-map-popup-img-icons").removeClass("popup-arrow-active")

            } else {
                $(e.currentTarget).find(".itinerary-map-popup-img-icons").addClass("popup-arrow-active")
            }
        }) 
        $(document).on("mouseleave", '#itn-infowindow', function(e) {
            $(".itinerary-map-popup-img-icons").addClass("popup-arrow-active")
        });

        /*
        * Car Add Extra
        * @author Jahid
        */
        $(document).on('submit', '.tf-car-extra-infos', function (e) {
            e.preventDefault();
            let form = $(this);
            const formData = new FormData(e.target);
            submitBtn = form.find('.tf-extra-submit'),
            formData.append('action', 'tf_extra_add_to_booking');
            formData.append('_nonce', tf_params.nonce);

            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            formData.append('pickup_date', pickup_date);
            formData.append('dropoff_date', dropoff_date);
            formData.append('pickup_time', pickup_time);
            formData.append('dropoff_time', dropoff_time);

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    form.css({'opacity': '0.5', 'pointer-events': 'none'});
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');
                    if(response.total_price){
                        $('.tf-price-header h2').html(response.total_price);
                    }
                    $('.tf-added-extra').html(response.extra);
                    if(response.extra){
                        $('.tf-extra-added-info').fadeIn();
                    }
                }
            });

        });

        /*
        * Car Delete Extra
        * @author Jahid
        */
        $(document).on('click', '.tf-single-added-extra .delete', function (e) {
            e.preventDefault();
            let $this = $(this);
            $this.closest('.tf-single-added-extra').remove();
            var count = $('.tf-added-extra .tf-single-added-extra').length;
            if(count==0){
                $('.tf-extra-added-info').hide();
            }
        });

        // Customer refund from customer profile
        $(document).on('click', '.tf-refund-processed', function (e) {
            e.preventDefault();
            let $this = $(this);
            
            var data = {
                action: 'tf_customer_refund_request',
                _nonce: tf_params.nonce,
                order: $this.closest('.tf-refund-box-content').find('#tf_order_id').val()
            };
            
            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $this.addClass('tf-btn-loading');
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);
                    if (response.status == 'error') {

                    } else {
                        if (response.redirect_to) {
                            window.location.replace(response.redirect_to);
                        }
                    }
                }
            });

        });

        // Refund Confirmation Popup
        $(document).on('click', '.tf_refund_request', function (e) {
            e.preventDefault();
            let $this = $(this);
            let href = $this.attr('href');  // Get the URL from the href attribute

            // Create a URL object to easily extract query parameters
            let url = new URL(href);
            let order = url.searchParams.get("order");         // Get the 'order' parameter
            let orderType = url.searchParams.get("order-type");

            var data = {
                action: 'tf_customer_refund_message',
                _nonce: tf_params.nonce,
                order: order,
                orderType: orderType
            };

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $this.addClass('tf-btn-loading');
                },
                success: function (data) {
                    $(".tf-refund-message").html(data);
                    $(".tf-refund-confirmation-box").css('display', 'flex');
                    $this.removeClass('tf-btn-loading');
                }
            });

        });

        // Refund Confirmation Popup close
        $(document).on('click', '.tf-refund-cancel', function (e) {
            e.preventDefault();
            $(".tf-refund-confirmation-box").hide();
            $(".tf-refund-message").html('');
        });

        // Cancellation showing
        $(document).on('click', '.tf-cancelltion-popup-btn a', function (e) {
            e.preventDefault();
            $('.tf-car-cancellation-popup').css('display', 'flex');
        });  

        // Cancellation Popup close
        $(document).on('click', '.tf-cancellation-popup-header .tf-close-popup', function (e) {
            e.preventDefault();
            $('.tf-car-cancellation-popup').hide();
        });
    });

})(jQuery);

// QR Code Scan Function
document.addEventListener('DOMContentLoaded', (event) => {
    TFQRSCANER();
})

const TFQRSCANER = () => {
    const video = document.querySelector('#tf-video-preview');
    if(video) {
        const qrScanner = new QrScanner(
            video,
            result => afterScanSuccess(result),
            { 
                onDecodeError: error => { },
                returnDetailedScanResult: true, 
                highlightScanRegion: true, 
                highlightCodeOutline: true, 
                preferredCamera: 'environment'
            }
        );
    
        checkCamera(qrScanner);
    
        // After  Successfully Scan QR code
        const afterScanSuccess = function (result) {
            if (result.data) {
                qrScanner.stop();
                if (tf_pro_params.tour_qr == 2) {
                    jQuery(".tf-scanner-preloader").show();
                    document.querySelector('.tf_qr_code_number').value = result.data;
                    var data = {
                        action: 'tf_qr_code_quick_info',
                        _nonce: tf_params.nonce,
                        tf_qr_code: result.data,
                    };
                    jQuery.ajax({
                        url: tf_params.ajax_url,
                        type: 'post',
                        data: data,
                        success: function (data) {
                            var response = JSON.parse(data);
                            if (response.qr_code_result) {
                                jQuery(".tf-scanner-quick-review").html(response.qr_code_result);
                                jQuery(".tf-scanner-preloader").hide();
                                jQuery(".tf-final-submission-form").show();
                                jQuery(".tf-qr-option").hide();
                            } else {
                                jQuery(".tf-scanner-quick-review").html("");
                                jQuery(".tf-scanner-preloader").hide();
                                jQuery(".tf-final-error-feedback").show();
                                jQuery(".tf-qr-option").hide();
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
                if (tf_pro_params.tour_qr == 1) {
                    jQuery(".tf-qr-option").hide();
                    jQuery(".tf-scanner-preloader").show();
                    var data = {
                        action: 'tf_qr_code_verification',
                        _nonce: tf_params.nonce,
                        tf_qr_code: result.data,
                    };
                    jQuery.ajax({
                        url: tf_params.ajax_url,
                        type: 'post',
                        data: data,
                        success: function (data) {
                            var response = JSON.parse(data);
                            if (response.qr_code_response == "true") {
                                jQuery(".tf-final-submission-form").hide();
                                jQuery(".tf-scanner-preloader").hide();
                                jQuery(".tf-final-submission-feedback").show();
                                jQuery(".tf-tour-infos").html(response.tf_qr_code_result);
                            }
                            if (response.qr_code_response == "false") {
                                jQuery(".tf-final-submission-form").hide();
                                jQuery(".tf-scanner-preloader").hide();
                                jQuery(".tf-final-error-feedback").show();
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
            }
        }
    
        jQuery(".tf_scan_another").on("click", function (e) {
            e.preventDefault();
            qrScanner.start();
        })

        jQuery(".tf_scan_back").on("click", function (e) {
            qrScanner.destroy();
        });

            // Check if device has camera
        function checkCamera(scanner) {
            QrScanner.hasCamera().then(hasCamera => {
                if (!hasCamera) {
                    alert(tf_params.no_camera_msg);
                    jQuery('.camera-warning').show();
                    jQuery('#tf-video-preview').hide();
                } else {
                    scanner.start();
                }
            });
        }
    }
}