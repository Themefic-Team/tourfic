(function ($, win) {
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

        /**
         * Hotel room availability ajax filter
         * @author Fida
         */
        const tfRoomFilter = () => {
            // Child & Child Type
            var adult_field_type = $("#adults").attr('type');
            var child_field_type = $("#children").attr('type');

            if ($.trim($('input[name=check-in-out-date]').val()) == '') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            } else {
                if ($('#tf-required').length === 1) {
                    $('.tf_booking-dates .tf_label-row .required').html('');
                }
            }
            //get the checked values of features
            var features = [];
            $('.tf-room-checkbox :checkbox:checked').each(function (i) {
                features[i] = $(this).val();
            });
            var tf_room_avail_nonce = $("input[name=tf_room_avail_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            if (adult_field_type == "number") {
                var adult = $('#adults').val();
            } else {
                var adult = $('select[name=adults] option').filter(':selected').val();
            }
            if (child_field_type == "number") {
                var child = $('#children').val();
            } else {
                var child = $('select[name=children] option').filter(':selected').val();
            }
            //var features = $('input[name=features]').filter(':checked').val();
            var children_ages = $('input[name=children_ages]').val();
            var check_in_out = $('input[name=check-in-out-date]').val();

            var data = {
                action: 'tf_room_availability',
                tf_room_avail_nonce: tf_room_avail_nonce,
                post_id: post_id,
                adult: adult,
                child: child,
                features: features,
                children_ages: children_ages,
                check_in_out: check_in_out,
            };

            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                beforeSend: function () {
                    $("#tf-single-hotel-avail .btn-primary.tf-submit").addClass('tf-btn-booking-loading');
                },
                success: function (data) {
                    $('html, body').animate({
                        scrollTop: $("#rooms").offset().top
                    }, 500);
                    $("#rooms").html(data);
                    $('.tf-room-filter').show();
                    $("#tf-single-hotel-avail .btn-primary.tf-submit").removeClass('tf-btn-booking-loading');
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        $(document).on('click', '#tf-single-hotel-avail .tf-submit', function (e) {
            e.preventDefault();
            tfRoomFilter();
        });

        $(document).on('change', '.tf-room-checkbox :checkbox', function () {
            tfRoomFilter();
        });
        /**
         * Click to go back to hotel availability form
         */
        $(document).on('click', '.hotel-room-availability', function (e) {
            e.preventDefault();

            $('html, body').animate({
                scrollTop: $("#tf-single-hotel-avail").offset().top
            }, 500);
        });

        /**
         * Ajax hotel booking
         *
         * tf_hotel_booking
         */
        $(document).on('click', '.hotel-room-book', function (e) {
            e.preventDefault();

            var $this = $(this);

            var tf_room_booking_nonce = $("input[name=tf_room_booking_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            if ($(this).closest('.room-submit-wrap').find('input[name=room_id]').val()) {
                var room_id = $(this).closest('.room-submit-wrap').find('input[name=room_id]').val();
            } else {
                var room_id = $("#hotel_roomid").val();
            }
            if ($(this).closest('.room-submit-wrap').find('input[name=unique_id]').val()) {
                var unique_id = $(this).closest('.room-submit-wrap').find('input[name=unique_id]').val();
            } else {
                var unique_id = $("#hotel_room_uniqueid").val();
            }
            var location = $('input[name=place]').val();
            var adult = $('input[name=adult]').val();
            var child = $('input[name=child]').val();
            var children_ages = $('input[name=children_ages]').val();
            var check_in_date = $('input[name=check_in_date]').val();
            var check_out_date = $('input[name=check_out_date]').val();
            if ($(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val()) {
                var room = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
                var deposit = $(this).closest('.room-submit-wrap').find('input[name=make_deposit]').is(':checked');
            } else {
                var room = $("#hotel_room_number").val();
                var deposit = $("#hotel_room_depo").val();
            }
            var airport_service = $('.fancybox-slide #airport-service').val();

            var data = {
                action: 'tf_hotel_booking',
                tf_room_booking_nonce: tf_room_booking_nonce,
                post_id: post_id,
                room_id: room_id,
                unique_id: unique_id,
                location: location,
                adult: adult,
                child: child,
                children_ages: children_ages,
                check_in_date: check_in_date,
                check_out_date: check_out_date,
                room: room,
                deposit: deposit,
                airport_service: airport_service
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                beforeSend: function (data) {
                    $this.block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });

                    $('.tf_notice_wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status == 'error') {

                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                notyf.error(text);
                            });
                        }

                        return false;
                    } else {

                        if (response.redirect_to) {
                            window.location.replace(response.redirect_to);
                        } else {
                            jQuery(document.body).trigger('added_to_cart');
                        }

                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });

        });

        /**
         * Single hotel Gallery
         *
         * Fancybox
         */
        $('[data-fancybox="hotel-gallery"]').fancybox({
            loop: true,
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "close"
            ],
            hash: false,
        });

        /**
         * Hotel slider
         *
         * Slick
         */

        var sbp = $('.swiper-button-prev'),
            sbn = $('.swiper-button-next');

        $('.single-slider-wrapper .tf_slider-for').slick({
            slide: '.slick-slide-item',
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            dots: false,
            centerMode: false,
            variableWidth: false,
            adaptiveHeight: true
        });

        sbp.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickPrev');
        });

        sbn.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickNext');
        });

        /**
         * Scroll to room reserve table
         */
        $(".reserve-button a").click(function () {
            $('html, body').animate({
                scrollTop: $("#rooms").offset().top - 32
            }, 1000);
        });

        /*
        * Single Hotel Gallery
        * @author Jahid
        */
        $(document).on('click', '#featured-gallery', function (e) {
            e.preventDefault();
            $("#tour-gallery").trigger("click");
        });

        /*
        * Hotel Search submit
        * @since 2.9.7
        * @author Foysal
        */
        $(document).on('submit', '#tf_hotel_aval_check', function (e) {
            e.preventDefault();
            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                formData = new FormData(form[0]);

            formData.append('action', 'tf_hotel_search');

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
                    let obj = JSON.parse(response);
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');
                    if (obj.status === 'error') {
                        notyf.error(obj.message);
                    }
                    if (obj.status === 'success') {
                        //location redirect to form action url with query string
                        location.href = form.attr('action') + '?' + obj.query_string;
                    }
                }
            });
        });

        // Hotel Location

        $('#tf-destination-adv').click(function (e) {
            var location = $(this).val();
            if (location) {
                $(".tf-hotel-locations").removeClass('tf-locations-show');
            } else {
                $(".tf-hotel-locations").addClass('tf-locations-show');
            }
        });
        $('#tf-destination-adv').keyup(function (e) {
            var location = $(this).val();
            $("#tf-place-destination").val(location);
        });
        $('#tf-location').keyup(function (e) {
            var tf_location = $(this).val();
            $("#tf-search-hotel").val(tf_location);
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest("#tf-destination-adv").length) {
                $(".tf-hotel-locations").removeClass('tf-locations-show');
            }
        });
        $('#ui-id-1 li').click(function (e) {
            var dest_name = $(this).attr("data-name");
            var dest_slug = $(this).attr("data-slug");
            $(".tf-preview-destination").val(dest_name);
            $("#tf-place-destination").val(dest_slug);
            $(".tf-hotel-locations").removeClass('tf-locations-show');
        });

        // Hotel Min and Max Range
        let tf_hotel_range_options = {
            range: {
                min: parseInt(tf_params.tf_hotel_min_price),
                max: parseInt(tf_params.tf_hotel_max_price),
                step: 1
            },
            initialSelectedValues: {
                from: parseInt(tf_params.tf_hotel_min_price),
                to: parseInt(tf_params.tf_hotel_max_price) / 2
            },
            grid: false,
            theme: "dark",
        };
        if (tf_params.tf_hotel_min_price != 0 && tf_params.tf_hotel_max_price != 0) {
            $('.tf-hotel-filter-range').alRangeSlider(tf_hotel_range_options);
        }

        // Hotel Min and Max Range in Search Result
        var tf_search_page_params = new window.URLSearchParams(window.location.search);
        let tf_hotel_search_range = {
            range: {
                min: parseInt(tf_params.tf_hotel_min_price),
                max: parseInt(tf_params.tf_hotel_max_price),
                step: 1
            },
            initialSelectedValues: {
                from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt(tf_params.tf_hotel_min_price),
                to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt(tf_params.tf_hotel_max_price) / 2
            },
            grid: false,
            theme: "dark",
            onFinish: function () {
                makeFilter();
            }
        };
        if (tf_params.tf_hotel_min_price != 0 && tf_params.tf_hotel_max_price != 0) {
            $('.tf-hotel-result-price-range').alRangeSlider(tf_hotel_search_range);
        }

        // Hotel location autocomplete
        var hotel_location_input = document.getElementById("tf-location");
        var hotel_locations = tf_params.locations;
        if (hotel_location_input) {
            tourfic_autocomplete(hotel_location_input, hotel_locations);
        }

        /*
        * Hotel without payment booking
        * @since 2.10.3
        * @Author Foysal
        */
        const hotelPopupBooking = ($this) => {

            var tf_room_booking_nonce = $("input[name=tf_room_booking_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            var roomnumber = $this.closest('.reserve').find('select[name=hotel_room_selected]').val();
            var room_id = $this.closest('.tf-room').find('input[name=room_id]').val();
            var unique_id = $this.closest('.tf-room').find('input[name=unique_id]').val();
            var hotel_deposit = $this.closest('.tf-room').find('input[name=make_deposit]').is(':checked');
            if (roomnumber == 0) {
                $this.closest('.tf-room').find('.roomselectissue').html('<span style="color:red">' + tf_pro_params.select_room + '</span>');
            } else {
                $this.closest('.tf-room').find('.roomselectissue').html('');
                $("#hotel_room_number").val(roomnumber);
                $("#hotel_roomid").val(room_id);
                $("#hotel_room_uniqueid").val(unique_id);
                $("#hotel_room_depo").val(hotel_deposit);
            }

            /*if ($(this).closest('.room-submit-wrap').find('input[name=room_id]').val()) {
                var room_id = $(this).closest('.room-submit-wrap').find('input[name=room_id]').val();
            } else {
                var room_id = $("#hotel_roomid").val();
            }
            if ($(this).closest('.room-submit-wrap').find('input[name=unique_id]').val()) {
                var unique_id = $(this).closest('.room-submit-wrap').find('input[name=unique_id]').val();
            } else {
                var unique_id = $("#hotel_room_uniqueid").val();
            }*/
            var location = $('input[name=place]').val();
            var adult = $('input[name=adult]').val();
            var child = $('input[name=child]').val();
            var children_ages = $('input[name=children_ages]').val();
            var check_in_date = $('input[name=check_in_date]').val();
            var check_out_date = $('input[name=check_out_date]').val();
            if ($this.closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val()) {
                var room = $this.closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
                var deposit = $this.closest('.tf-room').find('input[name=make_deposit]').is(':checked');
            } else {
                var room = $("#hotel_room_number").val();
                var deposit = $("#hotel_room_depo").val();
            }
            var airport_service = $('[name="tf_airport_service"]:checked').val();

            var data = {
                action: 'tf_hotel_booking_popup',
                tf_room_booking_nonce: tf_room_booking_nonce,
                post_id: post_id,
                room_id: room_id,
                unique_id: unique_id,
                location: location,
                adult: adult,
                child: child,
                children_ages: children_ages,
                check_in_date: check_in_date,
                check_out_date: check_out_date,
                room: room,
                deposit: deposit,
                airport_service: airport_service
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                beforeSend: function (data) {
                    $('#tour_room_details_loader').show();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status == 'error') {

                        $('#tour_room_details_loader').hide();
                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                notyf.error(text);
                            });
                        }

                        return false;
                    } else {
                        $('#tour_room_details_loader').hide();
                        if ($('.tf-traveller-info-box').length > 0) {
                            if ($(".tf-traveller-info-box").html().trim() == "") {
                                $('.tf-traveller-info-box').html(response.guest_info);
                            } else {
                                $('.tf-traveller-info-box').html(response.guest_info);
                            }
                        }
                        if ($('.tf-booking-traveller-info').length > 0) {
                            $('.tf-booking-traveller-info').html(response.hotel_booking_summery);
                        }
                        $this.closest('form.tf-room').find('.tf-withoutpayment-booking').addClass('show');
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });
        }
        $(document).on('click', '.tf-hotel-booking-popup-btn', function (e) {
            e.preventDefault();
            var $this = $(this);

            hotelPopupBooking($this);
        });


        $(document).on("change", "[name='tf_airport_service']", function (e) {
            var $this = $(this);

            hotelPopupBooking($this);
        });
    });

})(jQuery, window);