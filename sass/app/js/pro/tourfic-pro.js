(function ($) {
    $(document).ready(function () {

        /**
         * Hotel Details Popup
         *
         */
        $(document).on('click', '.tf-room-detail-qv', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            var post_id = $(this).attr("data-hotel");
            var uniqid_id = $(this).attr("data-uniqid");
            var data = {
                action: 'tf_tour_details_qv',
                post_id: post_id,
                uniqid_id: uniqid_id
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $("#tour_room_details_qv").html(response);

                    $("#tour_room_details_loader").hide();
                    $.fancybox.open({
                        src: '#tour_room_details_qv',
                        type: 'inline',
                    });
                }

            });
        });

        /**
         * Deposit amount toggle
        */
        $(document).on("click", "input[name='make_deposit']", function () {
            let id = $(this).val();
            if ($(this).is(':checked')) {
                $('.tf-deposit-amount-' + id).show();
            } else {
                $('.tf-deposit-amount-' + id).hide();
            }
        });

        /**
         * Airport Service Price
         */
        $(document).on("change", "#airport-service", function (e) {

            $("#tour_room_details_loader").show();
            var service_type = $(this).val();
            var roomid = $('#hotel_roomid').val();
            var hotelid = $("#hotel-post-id").val();
            var hoteladult = $("#adults").val();
            var hotelchildren = $("#children").val();
            var deposit = $("#hotel_room_depo").val();
            var room = $("#hotel_room_number").val();
            var check_in_date = $("input[name=check_in_date]").val();
            var check_out_date = $("input[name=check_out_date]").val();
            var data = {
                action: 'tf_hotel_airport_service_price',
                service_type: service_type,
                roomid: roomid,
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

        $(document).on("click", ".tf_air_service", function (e) {
            e.preventDefault();
            var $this = $(this);
            var roomnumber = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
            var room_id = $(this).closest('.room-submit-wrap').find('input[name=room_id]').val();
            var unique_id = $(this).closest('.room-submit-wrap').find('input[name=unique_id]').val();
            var hotel_deposit = $(this).closest('.room-submit-wrap').find('input[name=make_deposit]').is(':checked');
            if (roomnumber == 0) {
                $(this).closest('.room-submit-wrap').find('.roomselectissue').html('<span style="color:red">' + tf_pro_params.select_room + '</span>');
            } else {
                $(this).closest('.room-submit-wrap').find('.roomselectissue').html('');
                $("#hotel_room_number").val(roomnumber);
                $("#hotel_roomid").val(room_id);
                $("#hotel_room_uniqueid").val(unique_id);
                $("#hotel_room_depo").val(hotel_deposit);
                $.fancybox.open({
                    src: '#tf-hotel-services',
                    type: 'inline',
                    afterClose: function () {
                        $('#airport-service option:first').prop('selected', true);
                        $('.tf-airport-pickup-response').html('');
                    }
                });
            }
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
                        Swal.fire(
                            obj.title,
                            obj.message,
                            'error'
                        )
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
                        Swal.fire(
                            obj.title,
                            obj.message,
                            'error'
                        )
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
                        Swal.fire(
                            obj.title,
                            obj.message,
                            'error'
                        )
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
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                $("body").removeClass("tf-modal-open");
                $(".tf-modal").removeClass("tf-modal-show");
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


        //Tour Expand/Close

        $('#itinerary-switcher').click(function () {
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

        if (tf_params.showitinerarystatus == 1) {
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
        }

        /**
         * Ajax login
         */
        $(document).on('click', '#tf-login .tf-submit', function (e) {
            e.preventDefault();

            var tf_login_nonce = $("input[name=tf_login_nonce]").val();
            var user = $("input[name=tf_log_user]").val();
            var pass = $("input[name=tf_log_pass]").val();

            var data = {
                action: 'tf_login',
                tf_login_nonce: tf_login_nonce,
                user: user,
                pass: pass,
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                beforeSend: function (response) {

                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    $(".tf-login-response").html(obj.data);
                    if (obj.redirect_url) {
                        window.location.href = obj.redirect_url;
                    }
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
        $(document).on('click', '#tf-register .tf-submit', function (e) {
            e.preventDefault();

            var tf_reg_data = $("#tf-register").serializeArray();
            var tf_reg_nonce = $("input[name=tf_reg_nonce]").val();
            var user = $("input[name=tf_user]").val();
            var email = $("input[name=tf_email]").val();
            var pass = $("input[name=tf_pass]").val();
            var pass_confirm = $("input[name=tf_pass_confirm]").val();
            var role = $('input[name="tf_role"]:checked').val();

            var data = {
                action: 'tf_registration',
                tf_reg_nonce: tf_reg_nonce,
                tf_reg_data: tf_reg_data,
                user: user,
                email: email,
                pass: pass,
                pass_confirm: pass_confirm,
                role: role,
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                beforeSend: function (response) {

                },
                success: function (response) {
                    const obj = JSON.parse(response);
                    $(".tf-reg-response").html(obj.data);
                    if (obj.redirect_url) {
                        window.location.href = obj.redirect_url;
                    }
                },
            });

        });

        /**
         * Resend email verification url
         */
        $(document).on('click', '.resend-email-verification', function (e) {
            e.preventDefault();

            var user_id = $(this).attr("data-id");
            console.log(user_id);

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

        // QR Code Scan Open
        $(document).on('click', '.tf_qr_open', function (e) {
            e.preventDefault();
            TFQRSCANER();
        });

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
                tf_qr_code: qr_code,
            };

            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                success: function (data) {
                    var response = JSON.parse(data);
                    if( response.qr_code_response_checked== "true" ){
                        $(".tf-scanner-quick-review").html("");
                        $(".tf-final-submission-form").hide();
                        $(".tf-scanner-preloader").hide();
                        $(".tf-final-submission-feedback").show();
                    }else{
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

    });
})(jQuery);

// QR Code Scan Function
const TFQRSCANER = () => {
    var scanner = new Instascan.Scanner({ video: document.getElementById('tf-video-preview'), scanPeriod: 5, mirror: false });
    scanner.addListener('scan',function(content){
        if(tf_pro_params.tour_qr==2){
            jQuery(".tf-scanner-preloader").show();
            jQuery(".tf_qr_code_number").val(content);
            var data = {
                action: 'tf_qr_code_quick_info',
                tf_qr_code: content,
            };
            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                success: function (data) {
                    var response = JSON.parse(data);
                    if( response.qr_code_result ){
                        jQuery(".tf-scanner-quick-review").html(response.qr_code_result);
                        jQuery(".tf-scanner-preloader").hide();
                        jQuery(".tf-final-submission-form").show();
                        jQuery(".tf-qr-option").hide();
                    }else{
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
        if(tf_pro_params.tour_qr==1){
            jQuery(".tf-qr-option").hide();
            jQuery(".tf-scanner-preloader").show();
            var data = {
                action: 'tf_qr_code_verification',
                tf_qr_code: content,
            };
            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                success: function (data) {
                    var response = JSON.parse(data);
                    if( response.qr_code_response== "true" ){
                        jQuery(".tf-final-submission-form").hide();
                        jQuery(".tf-scanner-preloader").hide();
                        jQuery(".tf-final-submission-feedback").show();
                    }
                    if( response.qr_code_response== "false" ){
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
        //window.location.href=content;
    });
    Instascan.Camera.getCameras().then(function (cameras){
        if(cameras.length>0){
            jQuery(".tf-qr-code-preview").show();
            jQuery(".tf-final-submission-form").hide();
            jQuery(".tf-final-submission-feedback").hide();
            jQuery(".tf-final-error-feedback").hide();
            if(cameras.length==4){
                scanner.start(cameras[2]);
            }else if(cameras.length==2){
                scanner.start(cameras[1]);
            }else{
                scanner.start(cameras[0]);
            }

        }else{
            console.error('No cameras found.');
            alert('No cameras found.');
        }
    }).catch(function(e){
        console.error(e);
        alert(e);

    });
}
