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

        //###############################
        //         Hotel                #
        //###############################

        /**
         * Hotel room availability
         *
         * Ajax room filter
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
         * Recent Hotel - Tour
         *
         * Slick
         */


        /**
         * Scroll to room reserve table
         */
        $(".reserve-button a").click(function () {
            $('html, body').animate({
                scrollTop: $("#rooms").offset().top - 32
            }, 1000);
        });

        //###############################
        //         Tour                 #
        //###############################

        /**
         * Scroll to Tour Review Section
         */
        $(".tf-top-review a").click(function () {
            $('html, body').animate({
                scrollTop: $("#tf-review").offset().top - 32
            }, 1000);
        });

        /**
         * Scroll to Map Section
         */
        $(".tf-map-link a").click(function () {
            $('html, body').animate({
                scrollTop: $("#tour-map").offset().top - 32
            }, 1000);
        });

        /**
         * Ajax tour booking
         *
         * tf_tours_booking
         */
        $(document).on('submit', 'form.tf_tours_booking', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_tours_booking');


            // Tour Extra
            var tour_extra_total = [];
            jQuery('.tour-extra-single input:checkbox:checked').each(function () {
                tour_extra_total.push(jQuery(this).val());
            });
            formData.append('tour_extra', tour_extra_total);

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $this.block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });
                    $('#tour_room_details_loader').show();
                    $('.tf-notice-wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if(response.without_payment == 'false'){
                        if (response.status == 'error') {
                            $.fancybox.close();
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
                    }else{
                        $('#tour_room_details_loader').hide();
                        $('.tf-withoutpayment-booking').removeClass('show');
                        $('.tf-withoutpayment-booking-confirm').addClass('show');
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });
        });

        /**
         * Single Tour Gallery
         *
         * Fancybox
         */
        $('[data-fancybox="tour-gallery"]').fancybox({
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
         * Itinerary gallery init
         */
        $('.tf-itinerary-gallery').fancybox({
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "close"
            ]
        });

        /**
         * Single Tour price change
         *
         * adult, child, infant
         */
        $(document).on('click', '.tf-single-tour-pricing .tf-price-tab li', function () {
            var t = $(this).attr('id');

            $(this).addClass('active').siblings().removeClass('active');
            $('.tf-price').addClass('tf-d-n');
            $('.' + t + '-price').removeClass('tf-d-n');
        });
        //first li click
        $('.tf-single-tour-pricing .tf-price-tab li:first-child').trigger('click');

        /**
         * Single Tour price change Template 1
         *
         * adult, child, infant
         */
        $(document).on('click', '.tf-trip-person-info ul li', function () {
            var t = $(this).attr('data');

            $(this).addClass('active').siblings().removeClass('active');
            $('.tf-trip-pricing').removeClass('active');
            $('.tf-' + t).addClass('active');
        });

        //###############################
        //     Apartment                #
        //###############################

        /**
         * Ajax apartment booking
         * @author Foysal
         */
        $(document).on('submit', 'form#tf-apartment-booking', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_apartment_booking');

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $this.block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });

                    $('.tf-notice-wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status === 'error') {
                        $.fancybox.close();
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
         * Apartment location autocomplete
         * @author Foysal
         */
        var apartment_location_input = document.getElementById("tf-apartment-location");
        var apartment_locations = tf_params.apartment_locations;
        if (apartment_location_input) {
            tourfic_autocomplete(apartment_location_input, apartment_locations);
        }

        /**
         * Apartment Min and Max Range
         * @author Foysal
         */
        if (tf_params.tf_apartment_min_price >= 0 && tf_params.tf_apartment_max_price > 0) {
            $('.tf-apartment-filter-range').alRangeSlider({
                range: {
                    min: parseInt(tf_params.tf_apartment_min_price),
                    max: parseInt(tf_params.tf_apartment_max_price),
                    step: 1
                },
                initialSelectedValues: {
                    from: parseInt(tf_params.tf_apartment_min_price),
                    to: parseInt(tf_params.tf_apartment_max_price)
                },
                grid: false,
                theme: "dark",
            });
        }

        /**
         * Apartment highlights slider
         * @author Foysal
         */
        $('.tf-apt-highlights-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        /**
         * Apartment room slider
         * @author Foysal
         */
        $('.tf-apartment-room-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        /**
         * Related Apartment slider
         * @author Foysal
         */
        $('.tf-related-apartment-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 3000,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        //###############################
        //        Search                #
        //###############################

        /**
         * Ajax Search Result
         *
         * by search form submit
         *
         * by feature filter
         */

        var filter_xhr;
        // Creating a function for reuse this filter in any where we needs.
        const makeFilter = () => {
            var dest = $('#tf-place').val();
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var infant = $('#infant').val();
            var checked = $('#check-in-out-date').val();
            var startprice = $('.widget_tf_price_filters input[name="from"]').val();
            var endprice = $('.widget_tf_price_filters input[name="to"]').val();
            var tf_author = $('#tf_author').val();
            // split date range into dates
            var checkedArr = checked.split(' - ');
            var checkin = checkedArr[0];
            var checkout = checkedArr[1];
            var posttype = $('.tf-post-type').val();

            if ($.trim(checkin) === '' && tf_params.date_hotel_search && posttype === 'tf_hotel') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_tour_search && posttype === 'tf_tours') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_apartment_search && posttype === 'tf_apartment') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            let filters = termIdsByFeildName('tf_filters');
            let tfHotelTypes = termIdsByFeildName('tf_hotel_types');
            let features = termIdsByFeildName('tf_features');
            let tour_features = termIdsByFeildName('tour_features');
            let attractions = termIdsByFeildName('tf_attractions');
            let activities = termIdsByFeildName('tf_activities');
            let tfTourTypes = termIdsByFeildName('tf_tour_types');
            let tfApartmentFeatures = termIdsByFeildName('tf_apartment_features');
            let tfApartmentTypes = termIdsByFeildName('tf_apartment_types');

            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('type', posttype);
            formData.append('dest', dest);
            formData.append('adults', adults);
            formData.append('room', room);
            formData.append('children', children);
            formData.append('infant', infant);
            formData.append('checkin', checkin);
            formData.append('checkout', checkout);
            formData.append('filters', filters);
            formData.append('features', features);
            formData.append('tf_hotel_types', tfHotelTypes);
            formData.append('tour_features', tour_features);
            formData.append('attractions', attractions);
            formData.append('activities', activities);
            formData.append('tf_tour_types', tfTourTypes);
            formData.append('tf_apartment_features', tfApartmentFeatures);
            formData.append('tf_apartment_types', tfApartmentTypes);
            formData.append('checked', checked);
            if (startprice) {
                formData.append('startprice', startprice);
            }
            if (endprice) {
                formData.append('endprice', endprice);
            }
            if (tf_author) {
                formData.append('tf_author', tf_author);
            }
            // abort previous request
            if (filter_xhr && filter_xhr.readyState != 4) {
                filter_xhr.abort();
            }


            //var pagination_url = '/?place=' + dest + '&adults=' + adults + '&children=' + children + '&type=' + posttype;
            //formData.append('pagination_url', pagination_url);
            filter_xhr = $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $('.archive_ajax_result').block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });
                    $('#tf_ajax_searchresult_loader').show();
                    if ($.trim(checkin) !== '') {
                        $('.tf_booking-dates .tf_label-row').find('#tf-required').remove();
                    }
                },
                complete: function (data) {
                    $('.archive_ajax_result').unblock();
                    $('#tf_ajax_searchresult_loader').hide();

                    // total posts 0 if not found by @hena
                    if ($('.tf-nothing-found')[0]) {
                        $('.tf_posts_navigation').hide();
                        var foundPosts = $('.tf-nothing-found').data('post-count');
                        $('.tf-total-results').find('span').html(foundPosts);
                    } else {
                        $('.tf_posts_navigation').show();
                        var postsCount = $('.tf-posts-count').html();
                        $('.tf-total-results').find('span').html(postsCount);
                    }

                },
                success: function (data, e) {
                    $('.archive_ajax_result').unblock();
                    $('#tf_ajax_searchresult_loader').hide();
                    $('.archive_ajax_result').html(data);
                    // @KK show notice in every success request
                    notyf.success(tf_params.ajax_result_success);
                },
                error: function (data) {
                    console.log(data);
                },

            });
        };

        // Search Result Ajax page number
        function tf_page_pagination_number(element) {
            element.find('span').remove();
            return parseInt(element.html());
        }

        // Search Result Ajax pagination
        $(document).on('click', '.tf_posts_ajax_navigation a.page-numbers', function (e) {
            e.preventDefault();
            page = tf_page_pagination_number($(this).clone());
            PaginationmakeFilter(page);
        });

        // Creating a function for reuse this filter in any where we needs.
        const PaginationmakeFilter = (page) => {
            var dest = $('#tf-place').val();
            var page = page;
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var checked = $('#check-in-out-date').val();
            var startprice = $('.widget_tf_price_filters input[name="from"]').val();
            var endprice = $('.widget_tf_price_filters input[name="to"]').val();
            var tf_author = $('#tf_author').val();
            // split date range into dates
            var checkedArr = checked.split(' - ');
            var checkin = checkedArr[0];
            var checkout = checkedArr[1];
            var posttype = $('.tf-post-type').val();

            let filters = termIdsByFeildName('tf_filters');
            let tfHotelTypes = termIdsByFeildName('tf_hotel_types');
            let features = termIdsByFeildName('tf_features');
            let tour_features = termIdsByFeildName('tour_features');
            let attractions = termIdsByFeildName('tf_attractions');
            let activities = termIdsByFeildName('tf_activities');
            let tfTourTypes = termIdsByFeildName('tf_tour_types');
            let tfApartmentFeatures = termIdsByFeildName('tf_apartment_features');
            let tfApartmentTypes = termIdsByFeildName('tf_apartment_types');

            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('type', posttype);
            formData.append('page', page);
            formData.append('dest', dest);
            formData.append('adults', adults);
            formData.append('room', room);
            formData.append('children', children);
            formData.append('checkin', checkin);
            formData.append('checkout', checkout);
            formData.append('filters', filters);
            formData.append('tf_hotel_types', tfHotelTypes);
            formData.append('features', features);
            formData.append('tour_features', tour_features);
            formData.append('attractions', attractions);
            formData.append('activities', activities);
            formData.append('tf_tour_types', tfTourTypes);
            formData.append('tf_apartment_features', tfApartmentFeatures);
            formData.append('tf_apartment_types', tfApartmentTypes);
            formData.append('checked', checked);
            if (startprice) {
                formData.append('startprice', startprice);
            }
            if (endprice) {
                formData.append('endprice', endprice);
            }
            if (tf_author) {
                formData.append('tf_author', tf_author);
            }
            // abort previous request
            if (filter_xhr && filter_xhr.readyState != 4) {
                filter_xhr.abort();
            }

            filter_xhr = $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $('.archive_ajax_result').block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });

                    if ($.trim(checkin) !== '') {
                        $('.tf_booking-dates .tf_label-row').find('#tf-required').remove();
                    }
                },
                complete: function (data) {
                    $('.archive_ajax_result').unblock();

                    // total posts 0 if not found by @hena
                    if ($('.tf-nothing-found')[0]) {
                        $('.tf_posts_navigation').hide();
                        var foundPosts = $('.tf-nothing-found').data('post-count');
                        $('.tf-total-results').find('span').html(foundPosts);
                    } else {
                        $('.tf_posts_navigation').show();
                        var postsCount = $('.tf-posts-count').html();
                        $('.tf-total-results').find('span').html(postsCount);
                    }

                },
                success: function (data, e) {
                    $('.archive_ajax_result').unblock();
                    $('.archive_ajax_result').html(data);
                    // @KK show notice in every success request
                    notyf.success(tf_params.ajax_result_success);
                },
                error: function (data) {
                    console.log(data);
                },

            });
        };

        // Look for submission and change on filter widgets
        $(document).on('submit', '#tf-widget-booking-search', function (e) {
            e.preventDefault();
            makeFilter()
        });
        $(document).on('change', '[name*=tf_filters],[name*=tf_hotel_types],[name*=tf_features],[name*=tour_features],[name*=tf_attractions],[name*=tf_activities],[name*=tf_tour_types],[name*=tf_apartment_features],[name*=tf_apartment_types]', function () {
            makeFilter();
        })

        // Archive Page Filter
        $(document).on('submit', '.tf_archive_search_result', function (e) {
            e.preventDefault();
            makeFilter()
        });

        /*
        * Get term ids by field name
        * @auther Foysal
        */
        const termIdsByFeildName = (fieldName) => {
            let termIds = [];
            $(`[name*=${fieldName}]`).each(function () {
                if ($(this).is(':checked')) {
                    termIds.push($(this).val());
                }
            });
            return termIds.join();
        }

        //###############################
        //        Common Functions      #
        //###############################

        /**
         * Rating bar
         */
        $.fn.inViewport = function (cb) {
            return this.each(function (i, el) {
                function visPx() {
                    var H = $(this).height(),
                        r = el.getBoundingClientRect(), t = r.top, b = r.bottom;
                    return cb.call(el, Math.max(0, t > 0 ? H - t : (b < H ? b : H)));
                }

                visPx();
                $(win).on("resize scroll", visPx);
            });
        };

        $(window).load(function () {
            // Trigger Animation
            jQuery('[data-width]').each(function () {
                var $this = jQuery(this);
                var width = $this.attr('data-width');

                $this.inViewport(function (px) {
                    if (px > 0) {
                        $this.css('width', +width + '%');
                    } else {
                        $this.css('width', '0%');
                    }
                });
            });
        });

        /**
         * Full Width JS
         */
        function fullwidthInit(selector) {
            function fullWidth(selector) {
                $(selector).each(function () {
                    $(this).width("100%").css({marginLeft: "-0px"});

                    var window_width = $(window).width();

                    var left_margin = "-" + $(this).offset().left + "px";

                    $(this).width(window_width).css({marginLeft: left_margin});
                });
            }

            $(window).on("resize load", function () {
                fullWidth(selector);
            });
        }


        /**
         * Share buttons
         */
        // Toggle share buttons
        $('.share-toggle[data-toggle="true"]').click(function (e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).slideToggle('fast');
        });

        // Copy button
        $('button#share_link_button').click(function () {

            $(this).addClass('copied');
            setTimeout(function () {
                $('button#share_link_button').removeClass('copied');
            }, 3000);
            $(this).parent().find("#share_link_input").select();
            document.execCommand("copy");
        });


        /**
         * Related Tour
         *
         * Slick
         */
        $('.tf-slider-items-wrapper,.tf-slider-activated').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            //autoplay: true,
            autoplaySpeed: 2000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        /**
         * Customer Reviews
         *
         * Slick
         */
        $('.tf-review-items-wrapper').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 2000,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        /**
         * Wishlist Functionality
         *
         */
        /* get wishlist from localstorage  */
        const wishKey = 'wishlist_item';
        const getWish = () => {
            let userLists = localStorage.getItem(wishKey);
            // if list is null then init list else make array from json string
            return (userLists === null) ? [] : JSON.parse(userLists);
        }

        /* store item in wishlist for loggedin and visitor */
        const addWish = item => {
            let userLists = getWish()
            // Look if item is not already is in list
            if (userLists.filter(i => i.post == item.post).length === 0) {
                // push to list
                userLists.push(item)
                // save list
                localStorage.setItem(wishKey, JSON.stringify(userLists));
                return true;
            }
            return false;
        }
        /* get all wishlist items */
        const getAllWish = () => {
            let nodes = $('.tf-wishlist-holder');
            $.each(nodes, function (index, element) {
                let type = $(element).data('type');
                type = type ? type.split(',') : undefined;
                let userLists = getWish();
                if (type !== undefined) userLists = userLists.filter(e => type.includes(e.type));
                let ids = userLists.map(e => e.post);
                let data = {
                    nonce: $(element).data('nonce'),
                    action: 'tf_generate_table',
                    ids
                }
                $.post(tf_params.ajax_url, data,
                    function (data) {
                        if (data.success) {
                            $(element).html(data.data);
                        }
                    },
                );
            });


        }
        /* delete item from wishlist */
        const removeWish = id => {
            let userLists = getWish()
            let index = userLists.findIndex(x => x.post == id);
            console.log(index, id, userLists);
            if (index >= 0) {
                userLists.splice(index, 1)
                console.log(userLists);
                localStorage.setItem(wishKey, JSON.stringify(userLists));
                if (tf_params.single != '1') getAllWish()
                return true;
            } else return false;

        }

        /* toggle icon for the wish list */
        const wishIconToggleForGuest = () => {
            if (!$(document).hasClass('logged-in') && $(document).find('.add-wishlist')) {
                let targetNode = $('.add-wishlist');
                let id = targetNode.data('id');
                let userLists = getWish()
                var index = userLists.findIndex(x => x.post == id);
                if (index >= 0) {
                    wishIconFill(targetNode);
                } else {
                    wishIcon(targetNode);
                }
            }
        }

        /* fill icon class */
        const wishIconFill = targetNode => {
            targetNode.addClass('remove-wishlist');
            targetNode.addClass('fa-heart');
            targetNode.addClass('tf-text-red');
            targetNode.removeClass('fa-heart-o');
            targetNode.removeClass('add-wishlist');


        }
        /* blank icon */
        const wishIcon = targetNode => {
            targetNode.addClass('add-wishlist');
            targetNode.addClass('fa-heart-o');
            targetNode.removeClass('fa-heart');
            targetNode.removeClass('tf-text-red');
            targetNode.removeClass('remove-wishlist');
        }
        /* send request to wp-admin for storing request */
        $(document).on('click', '.add-wishlist', function () {

            let targetNode = $('.add-wishlist');
            let data = {
                type: targetNode.data('type'),
                post: targetNode.data('id'),
            }
            let wishPageTitle = targetNode.data('page-title');
            let wishPageUrl = targetNode.data('page-url');
            let wishlistpage = wishPageUrl !== undefined ? '<a class="wish-button" href="' + wishPageUrl + '">' + wishPageTitle + '</a>' : '';

            /* For logged in user */
            if ($('body').hasClass('logged-in')) {
                data.action = 'tf_add_to_wishlists';
                data.nonce = targetNode.data('nonce');
                $.ajax({
                    type: "post",
                    url: tf_params.ajax_url,
                    data: data,
                    beforeSend: function (data) {
                        notyf.success(tf_params.wishlist_add)
                    },
                    success: function (response) {
                        if (response.success) {
                            wishIconFill(targetNode);
                            notyf.success({
                                message: response.data,
                                duration: 4e3
                            });
                        }
                    }
                });

            } else {
                /* For guest */
                if (addWish(data) === true) {
                    notyf.success(tf_params.wishlist_add)
                    wishIconFill(targetNode);
                    notyf.success({
                        message: tf_params.wishlist_added,
                        duration: 4e3
                    });
                } else notyf.error(tf_params.wishlist_add_error);

            }

            return false;

        });
        /* populate wishlist table */
        if ($('body').find('.tf-wishlist-holder').length) {
            getAllWish()
        }
        /* trigger remove wish function */
        $(document).on('click', '.remove-wishlist', function () {
            let targetNode = $('.remove-wishlist');
            let id = targetNode.data('id');
            /* For logged in user */
            if ($('body').hasClass('logged-in')) {
                let tableNode = targetNode.closest('table');
                let type = tableNode.data('type');
                let data = {id, action: 'tf_remove_wishlist', type, nonce: targetNode.data('nonce')}
                $.get(tf_params.ajax_url, data,
                    function (data) {
                        if (data.success) {
                            if (tf_params.single != '1') {
                                tableNode.closest('.tf-wishlists').html(data.data);
                            }
                            wishIcon(targetNode);
                            notyf.success(tf_params.wishlist_removed);
                        }
                    }
                );

            } else {
                /* For guest */
                if (removeWish(id) == true) {
                    wishIcon(targetNode);
                    notyf.success(tf_params.wishlist_removed);
                } else {
                    notyf.error(tf_params.wishlist_remove_error);
                }
            }

        });

        /* toggle icon for guest */
        wishIconToggleForGuest();

        //###############################
        //      Reusable Functions      #
        //###############################

        /*
        * Trourfic autocomplete destination
        */
        function tourfic_autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function (e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                var $notfound = [];
                /*for each item in the array...*/
                for (const [key, value] of Object.entries(arr)) {
                    if (value.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        $notfound.push('found');
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = "<strong>" + value.substr(0, val.length) + "</strong>";
                        b.innerHTML += value.substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'> `;
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function (e) {
                            let source = this.getElementsByTagName("input")[0];
                            console.log(source.dataset.slug);
                            /*insert the value for the autocomplete text field:*/
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug //source.dataset.slug
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);

                    } else {
                        $notfound.push('notfound');
                    }
                }

                if ($notfound.indexOf('found') == -1) {
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("DIV");
                    /*make the matching letters bold:*/

                    b.innerHTML += tf_params.no_found;
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += "<input type='hidden' value=''>";
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function (e) {
                        /*insert the value for the autocomplete text field:*/
                        inp.value = this.getElementsByTagName("input")[0].value;
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }

            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        /**
         * Initiate autocomplete on inputs
         */

            // Hotel location autocomplete
        var hotel_location_input = document.getElementById("tf-location");
        var hotel_locations = tf_params.locations;
        if (hotel_location_input) {
            tourfic_autocomplete(hotel_location_input, hotel_locations);
        }
        // Tour destination autocomplete
        var tour_destination_input = document.getElementById("tf-destination");
        var tour_destinations = tf_params.tour_destinations;
        if (tour_destination_input) {
            tourfic_autocomplete(tour_destination_input, tour_destinations);
        }

        /**
         * Single tour sticky booking bar position fixed
         */
        $(window).scroll(function () {
            var sticky = $('.tf-tour-booking-wrap'),
                scroll = $(window).scrollTop();

            if (scroll >= 800) sticky.addClass('tf-tours-fixed');
            else sticky.removeClass('tf-tours-fixed');
        });

        /**
         * Single tour sticky booking bar - template 1
         * @author Foysal
         */
        if($('.tf-tour-booking-box').length > 0) {
            $(window).scroll(function () {
                let bookingBox = $('.tf-tour-booking-box');
                let bottomBar = $('.tf-bottom-booking-bar');
                let boxOffset = bookingBox.offset().top + bookingBox.outerHeight();

                var scrollTop = $(window).scrollTop();

                if (scrollTop > boxOffset) {
                    bottomBar.addClass('active'); // Add your class name here
                } else {
                    bottomBar.removeClass('active'); // Remove the class if scrolling back up
                }
            });
        }

        $(document).on('click', '.tf-booking-mobile-btn', function (e) {
            e.preventDefault();
            $(this).closest('.tf-bottom-booking-bar').toggleClass('mobile-active');
        });

        /**
         * Open/close horizontal search form persons panel
         */
        // Adult, Child, Room Selection toggle
        $(document).on('click', '.tf_selectperson-wrap .tf_input-inner,.tf_person-selection-wrap .tf_person-selection-inner', function () {
            $('.tf_acrselection-wrap').slideToggle('fast');
        });
        // Close
        jQuery(document).on("click", function (event) {
            if (!jQuery(event.target).closest(".tf_selectperson-wrap").length) {
                jQuery(".tf_acrselection-wrap").slideUp("fast");
            }
        });

        /**
         * Number/text change horizontal search form
         */
        // Number Increment
        $('.acr-inc').on('click', function (e) {
            var input = $(this).parent().find('input');
            var max = input.attr('max') ? input.attr('max') : 999;
            var step = input.attr('step') ? input.attr('step') : 1;

            if (input.val() < max) {
                input.val(parseInt(input.val()) + parseInt(step)).change();
            }
            // input focus disable
            input.blur();
        });

        // Number Decrement
        $('.acr-dec').on('click', function (e) {

            var input = $(this).parent().find('input');
            var min = input.attr('min');
            var step = input.attr('step') ? input.attr('step') : 1;

            if (input.val() > min) {
                input.val(input.val() - parseInt(step)).change();
            }
        });

        // Adults change trigger
        $(document).on('change', '#adults', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult);
            }

        });

        // Children change trigger
        $(document).on('change', '#children', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            }

        });

        // Infant change trigger
        $(document).on('change', '#infant', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
            }

        });

        // Room change trigger
        $(document).on('change', '#room', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.room-text').text(thisVal + " " + tf_params.room);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.room-text').text(thisVal + " " + tf_params.room);
            }
        });

        /**
         * Review submit form toggle
         */
        $(document).on('click', '#reply-title', function () {
            var $this = $(this);
            $('#commentform').slideToggle('fast', 'swing', function () {
                $this.parent().toggleClass('active');
            });
        });

        /**
         * Ask question
         */
        $(document).on('click', '#tf-ask-question-trigger', function (e) {
            e.preventDefault();
            $('#tf-ask-question').fadeIn().find('.response').html("");
        });

        // Close Ask question
        $(document).on('click', 'span.close-aq', function () {
            $('#tf-ask-question').fadeOut();
        });

        // Ajax Ask question submit
        $(document).on('submit', 'form#ask-question', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_ask_question');

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function (data) {
                    $this.block({
                        message: null,
                        overlayCSS: {
                            background: "#fff",
                            opacity: .5
                        }
                    });

                    $this.find('.response').html(tf_params.sending_ques);
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status == 'sent') {
                        $this.find('.response').html(response.msg);

                        $this.find('[type="reset"]').trigger('click');
                    } else {
                        $this.find('.response').html(response.msg);
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });

        });

        /**
         * Change archive item
         *
         * Grid/List
         */
        $(document).on('click', '.change-view', function (e) {
            e.preventDefault();
            $('.change-view').removeClass('active');
            $(this).addClass('active');

            var dataid = $(this).data('id');
            if (dataid == 'grid-view') {
                $('.archive_ajax_result').addClass('tours-grid');
            } else {
                $('.archive_ajax_result').removeClass('tours-grid');
            }

        });

        /**
         * Design 1 archive item
         *
         * Grid/List
         */
        $(document).on('click', '.tf-grid-list-layout', function (e) {
            e.preventDefault();
            $('.tf-grid-list-layout').removeClass('active');
            $(this).addClass('active');

            var dataid = $(this).data('id');
            if (dataid == 'grid-view') {
                $('.tf-item-cards').addClass('tf-layout-grid');
                $('.tf-item-cards').removeClass('tf-layout-list');
            } else {
                $('.tf-item-cards').addClass('tf-layout-list');
                $('.tf-item-cards').removeClass('tf-layout-grid');
            }

        });

        /**
         * ajax tour load pagination
         */
        var flag = false;
        var main_xhr;

        var amPushAjax = function (url) {
            if (main_xhr && main_xhr.readyState != 4) {
                main_xhr.abort();
            }

            main_xhr = $.ajax({
                url: url,
                contentType: false, // Not to set any content header
                processData: false, // Not to process data
                asynch: true,
                beforeSend: function () {

                    $(document).find('.tf_posts_navigation').addClass('loading');
                    $(document).find('.archive_ajax_result').addClass('loading');
                    flag = true;
                },
                success: function (data) {
                    $('.archive_ajax_result').html($('.archive_ajax_result', data).html());

                    $('.tf_posts_navigation').html($('.tf_posts_navigation', data).html());

                    //document.title = $(data).filter('title').text();

                    flag = false;

                    $(document).find('.tf_posts_navigation').removeClass('loading');
                    $(document).find('.archive_ajax_result').removeClass('loading');

                }
            });
        };

        // Feed Ajax Trigger
        $(document).on('click', '.tf_posts_page_navigation a.page-numbers', function (e) {
            e.preventDefault();
            var targetUrl = (e.target.href) ? e.target.href : $(this).context.href;
            amPushAjax(targetUrl);
            window.history.pushState({url: "" + targetUrl + ""}, "", targetUrl);
        });
        // End Feed Ajax Trigger

        // Hotel and Tour Advance Search form

        $(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner").click(function () {
            $('.tf-more-info').toggleClass('show');
        });
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner, .tf-more-info").length) {
                $('.tf-more-info').removeClass('show');
            }
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

        // Tour Min and Max Range
        let tf_tour_range_options = {
            range: {
                min: parseInt(tf_params.tf_tour_min_price),
                max: parseInt(tf_params.tf_tour_max_price),
                step: 1
            },
            initialSelectedValues: {
                from: parseInt(tf_params.tf_tour_min_price),
                to: parseInt(tf_params.tf_tour_max_price) / 2
            },
            grid: false,
            theme: "dark",
        };
        if (tf_params.tf_tour_min_price != 0 && tf_params.tf_tour_max_price != 0) {
            $('.tf-tour-filter-range').alRangeSlider(tf_tour_range_options);
        }

        // Tours Min and Max Range in Search Result
        let tf_tours_search_range = {
            range: {
                min: parseInt(tf_params.tf_tour_min_price),
                max: parseInt(tf_params.tf_tour_max_price),
                step: 1
            },
            initialSelectedValues: {
                from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt(tf_params.tf_tour_min_price),
                to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt(tf_params.tf_tour_max_price) / 2
            },
            grid: false,
            theme: "dark",
            onFinish: function () {
                makeFilter();
            }
        };
        if (tf_params.tf_tour_min_price != 0 && tf_params.tf_tour_max_price != 0) {
            $('.tf-tour-result-price-range').alRangeSlider(tf_tours_search_range);
        }

        // Apartment Min and Max Range in Search Result
        let tf_apartment_search_range = {
            range: {
                min: parseInt(tf_params.tf_apartment_min_price),
                max: parseInt(tf_params.tf_apartment_max_price),
                step: 1
            },
            initialSelectedValues: {
                from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt(tf_params.tf_apartment_min_price),
                to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt(tf_params.tf_apartment_max_price) / 2
            },
            grid: false,
            theme: "dark",
            onFinish: function () {
                makeFilter();
            }
        };
        if (tf_params.tf_apartment_min_price != 0 && tf_params.tf_apartment_max_price != 0) {
            $('.tf-apartment-result-price-range').alRangeSlider(tf_apartment_search_range);
        }

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

        // Tour Destination

        $('#tf-tour-location-adv').click(function (e) {
            var location = $(this).val();
            if (location) {
                $(".tf-tour-results").removeClass('tf-destination-show');
            } else {
                $(".tf-tour-results").addClass('tf-destination-show');
            }
        });
        $('#tf-tour-location-adv').keyup(function (e) {
            var location = $(this).val();
            $("#tf-tour-place").val(location);
        });
        $('#tf-destination').keyup(function (e) {
            var tf_location = $(this).val();
            $("#tf-search-tour").val(tf_location);
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest("#tf-tour-location-adv").length) {
                $(".tf-tour-results").removeClass('tf-destination-show');
            }
        });
        $('#ui-id-2 li').click(function (e) {
            var dest_name = $(this).attr("data-name");
            var dest_slug = $(this).attr("data-slug");
            $(".tf-tour-preview-place").val(dest_name);
            $("#tf-tour-place").val(dest_slug);
            $(".tf-tour-results").removeClass('tf-destination-show');
        });


        // Itinerary Accordion
        $('.tf-accordion-head').click(function () {
            $(this).toggleClass('active');
            $(this).parent().find('.arrow').toggleClass('arrow-animate');
            $(this).parent().find('.tf-accordion-content').slideToggle();
            //$(this).parents('#tf-accordion-wrapper').siblings().find('.tf-accordion-content').slideUp();
            $(this).siblings().find('.ininerary-other-gallery').slick({
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
        });


        // FAQ Accordion
        $('.tf-faq-title').click(function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-faq-desc").slideUp(400);
                $(".tf-faq-title").removeClass("active");
                $('.arrow').removeClass('arrow-animate');
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
            $('.arrow', this).toggleClass('arrow-animate');
        });

        /*
        * New Template FAQ Accordion
        * @author: Jahid
        */
        $('.tf-faq-collaps').click(function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-faq-content").slideUp(400);
                $(".tf-faq-collaps").removeClass("active");
                $('.tf-faq-single').removeClass('active');
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
            $(this).closest('.tf-faq-single').toggleClass('active');
        });

        /*
        * New Template Itinerary Accordion
        * @author: Jahid
        */
        $('.tf-itinerary-title').click(function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-itinerary-content-box").slideUp(400);
                $(".tf-itinerary-title").removeClass("active");
                $('.tf-single-itinerary-item').removeClass('active');
            }
            $this.toggleClass("active");
            $(this).closest('.tf-single-itinerary-item').toggleClass('active');
            $this.next().slideToggle();
        });

        /*
        * New Template Tour Extra
        * @author: Jahid
        */
        $('.tf-form-title.tf-tour-extra').click(function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-tour-extra-box").slideUp(400);
                $(".tf-form-title.tf-tour-extra").removeClass("active");
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
        });

        /*
        * Booking Form select first tab on load
        * @author: Foysal
        */
        $(window).on('load', function () {
            if ($('.tf-tablinks').length > 0) {
                $('.tf-tablinks').first().trigger('click').addClass('active');
            }
        });

        // Form Tab click
        $(document).on('click', '.tf-tablinks', function (e) {
            let formId = $(this).data('form-id');
            tfOpenForm(event, formId);
        });

        /*
        * Booking Form tab mobile version
        * @author: Foysal
        */
        $(document).on('change', 'select[name="tf-booking-form-tab-select"]', function () {
            var tabId = $(this).val();
            tfOpenForm(event, tabId);
        });

        /*
         * Location Search
         * @author: Foysal
         */
        $(document).on('keyup', '.tf-hotel-side-booking #tf-location, .tf-hotel-side-booking #tf-destination', function () {
            let search = $(this).val();
            $(this).next('input[name=place]').val(search);
        })


        /**
         * Children age field add when children added in search field
         * @since 2.8.6
         * @author Abu Hena
         */

        if ($('.child-age-limited')[0]) {
            $('.acr-select .child-inc').on('click', function () {
                var first_element = $('div[id^="tf-age-field-0"]');
                var ch_element = $('div[id^="tf-age-field-"]:last');
                if (ch_element.length != 0) {
                    var num = parseInt(ch_element.prop("id").match(/\d+/g), 10) + 1;
                }
                var elements = ch_element.clone().prop('id', 'tf-age-field-' + num);
                elements.find("label").html('Child age ' + num);
                //elements.find("select").attr('name','children_'+num+'_age');
                elements.find("select").attr('name', 'children_ages[]');
                ch_element.after(elements);
                elements.show();
                first_element.hide();

            })

            $('.acr-select .child-dec').on('click', function () {
                var total_age_input = $('.tf-children-age').length;
                var ch_element = $('div[id^="tf-age-field-"]:last');
                if (total_age_input != 1) {
                    ch_element.remove();
                }
            })
        }
        var postsCount = $('.tf-posts-count').html();
        $('.tf-total-results').find('span').html(postsCount);

//Sidebar widget js
        $('.tf-widget-title').on('click', function () {
            $(this).find('i').toggleClass('collapsed');
            $(this).siblings('.tf-filter').slideToggle('medium');
        })

        /* see more checkbox filter started */

        $('a.see-more').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-filter').find('.filter-item').filter(function (index) {
                return index > 3;
            }).removeClass("hidden");
            $this.hide();

            $this.parent('.tf-filter').find('.see-less').show();
        });

        /* see Sless checkbox filter started */

        $('a.see-less').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-filter').find('.filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");
            $this.hide();
            $this.parent('.tf-filter').find('.see-more').show();
        });

        $('.tf-filter').each(function () {

            var len = $(this).find('ul').children().length;
            $(this).find('.see-more').hide();
            if (len > 4) {
                $(this).find('.see-more').show();
            }
            //hide items if crossed showing limit
            $(this).find('.filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");

        });

        /* see more checkbox filter end */

        //active checkbox bg
        $('.tf_widget input').on('click', function () {
            $(this).parent().parent().toggleClass('active');
        });

        /**
         * Cart item remove from checkout page
         * @since 2.9.7
         * @author Foysal
         */
        $('form.checkout').on('click', '.cart_item a.remove', function (e) {
            e.preventDefault();

            var cart_item_key = $(this).attr("data-cart_item_key");

            $.ajax({
                type: 'POST',
                url: tf_params.ajax_url,
                data: {
                    action: 'tf_checkout_cart_item_remove',
                    cart_item_key: cart_item_key,
                },
                beforeSend: function () {
                    $('body').trigger('update_checkout');
                },
                success: function (result) {
                    $('body').trigger('update_checkout');
                },
                error: function (error) {

                }
            });
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

        /*
        * Tour Search submit
        * @since 2.9.7
        * @author Foysal
        */
        $(document).on('submit', '#tf_tour_aval_check', function (e) {
            e.preventDefault();
            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                formData = new FormData(form[0]);

            formData.append('action', 'tf_tour_search');

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

        /*
        * Single Hotel Gallery
        * @author Jahid
        */
        $(document).on('click', '#featured-gallery', function (e) {
            e.preventDefault();
            $("#tour-gallery").trigger("click");
        });

        /*
        * Without Payment Booking
        * @since 2.9.26
        * @author Jahid
        */
        let tf_hasErrorsFlag = false; 
        $(document).on('click', '.tf-traveller-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-single-travel').each(function() {
                $(this).find('input, select').each(function() {
                    if($(this).attr('data-required')){
                        if($(this).val() == ""){
                            hasErrors.push(true);
                            const errorContainer = $(this).siblings('.error-text');
                            errorContainer.text('This field is required.');
                            if (errorContainer.text() !== '') {
                                errorContainer.addClass('error-visible');
                            } else {
                                errorContainer.removeClass('error-visible');
                            }
                        }
                    }
                });
                $(this).find('input[type="radio"], input[type="checkbox"]').each(function() {
                    if ($(this).attr('data-required')) {
                        const radioName = $(this).attr('name');
                        const isChecked = $('input[name="' + radioName + '"]:checked').length > 0;
                
                        if (!isChecked) {
                            hasErrors.push(true);
                            const errorContainer = $(this).parent().siblings('.error-text');
                            errorContainer.text('This field is required.');
                            if (errorContainer.text() !== '') {
                                errorContainer.addClass('error-visible');
                            } else {
                                errorContainer.removeClass('error-visible');
                            }
                        }
                    }
                });

            });
            if (hasErrors.includes(true)) {
                tf_hasErrorsFlag = true;
                return false;
            }
            tf_hasErrorsFlag = false;
        });

        // Booking Confirmation Form Validation
        $(document).on('click', '.tf-book-confirm-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-confirm-fields').each(function() {
                $(this).find('input, select').each(function() {
                    if($(this).attr('data-required')){
                        if($(this).val() == ""){
                            hasErrors.push(true);
                            const errorContainer = $(this).siblings('.error-text');
                            errorContainer.text('This field is required.');
                            if (errorContainer.text() !== '') {
                                errorContainer.addClass('error-visible');
                            } else {
                                errorContainer.removeClass('error-visible');
                            }
                        }
                    }
                });
                $(this).find('input[type="radio"], input[type="checkbox"]').each(function() {
                    if ($(this).attr('data-required')) {
                        const radioName = $(this).attr('name');
                        const isChecked = $('input[name="' + radioName + '"]:checked').length > 0;
                
                        if (!isChecked) {
                            hasErrors.push(true);
                            const errorContainer = $(this).parent().siblings('.error-text');
                            errorContainer.text('This field is required.');
                            if (errorContainer.text() !== '') {
                                errorContainer.addClass('error-visible');
                            } else {
                                errorContainer.removeClass('error-visible');
                            }
                        }
                    }
                });
            });
            if (hasErrors.includes(true)) {
                tf_hasErrorsFlag = true;
                return false;
            }
        });

        // Navigation Next
        $(document).on('click', '.tf-tabs-control', function (e) {
            e.preventDefault();
            if (tf_hasErrorsFlag) {
                return false; 
            }
            let step = $(this).attr("data-step"); 
            if(step>1){
                for(let i = 1; i <= step; i++){
                    $('.tf-booking-step-' + i).removeClass("active");
                    $('.tf-booking-step-' + i).addClass("done");
                }
                $('.tf-booking-step-' + step).addClass("active");
                $('.tf-booking-content').hide();
                $('.tf-booking-content-' + step).fadeIn(300);

                
                $('.tf-control-pagination').hide();
                $('.tf-pagination-content-' + step).fadeIn(300);
            }
        });

        // Navigation Back
        $(document).on('click', '.tf-step-back', function (e) {
            e.preventDefault();
            let step = $(this).attr("data-step"); 
            if(step==1){
                $('.tf-booking-step').removeClass("active");
                $('.tf-booking-step').removeClass("done");
                $('.tf-booking-step-' + step).addClass("active");
                $('.tf-booking-content').hide();
                $('.tf-booking-content-' + step).fadeIn(300);

                $('.tf-control-pagination').hide();
                $('.tf-pagination-content-' + step).fadeIn(300);
            }
            if(step>1){
                let next_step = parseInt(step)+1;
                $('.tf-booking-step-' + next_step).removeClass("active");
                $('.tf-booking-step-' + step).addClass("active");
                $('.tf-booking-step-' + step).removeClass("done");
                $('.tf-booking-step-' + next_step).removeClass("done");

                $('.tf-booking-content').hide();
                $('.tf-booking-content-' + step).fadeIn(300);
                $('.tf-control-pagination').hide();
                $('.tf-pagination-content-' + step).fadeIn(300);
            }
        });

        // Popup Open
        const makeBooking = () => {
            var $this = $(this);
            let check_in_date = $('#check-in-out-date').val();
            let adults = $('#adults').val();
            let children = $('#children').val();
            let infant = $('#infant').val();
            let post_id = $('input[name=post_id]').val();
            let check_in_time = $('select[name=check-in-time] option').filter(':selected').val();
            var deposit = $('input[name=deposit]').is(':checked');
            var extras = [];
            $('[name*=tf-tour-extra]').each(function () {
                if ($(this).is(':checked')) {
                    extras.push($(this).val());
                }
            });
            var extras = extras.join();
            var data = {
                action: 'tf_tour_booking_popup',
                post_id: post_id,
                adults: adults,
                children: children,
                infant: infant,
                check_in_date: check_in_date,
                check_in_time: check_in_time,
                tour_extra: extras,
                deposit: deposit
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
                            if($(".tf-traveller-info-box").html().trim() == ""){
                                $('.tf-traveller-info-box').html(response.traveller_info);
                            }else{
                                $('.tf-traveller-info-box').html(response.traveller_info);
                            }
                        }
                        if ($('.tf-booking-traveller-info').length > 0) {
                            $('.tf-booking-traveller-info').html(response.traveller_summery);
                        }
                        $('.tf-withoutpayment-booking').addClass('show');
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });
        }
        $(document).on('click', '.tf-booking-popup-btn', function (e) {
            e.preventDefault();
            $(".tf-withoutpayment-booking input[type='text'], .tf-withoutpayment-booking input[type='email'], .tf-withoutpayment-booking input[type='date'], .tf-withoutpayment-booking select, .tf-withoutpayment-booking textarea").val("");

            $('.tf-booking-content-extra input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')==true){ 
                    $(this).prop('checked',false);
                }
            });
            makeBooking();
        });

        $(document).on('change', '[name*=tf-tour-extra]', function () {
            makeBooking();
        });
        $(document).on('change', '[name=deposit]', function () {
            makeBooking();
        });

        // Popup Close
        $(document).on('click', '.tf-booking-times span', function (e) {
            $('.tf-withoutpayment-booking').removeClass('show');
            $('.tf-withoutpayment-booking-confirm').removeClass('show');
            // Reset Tabs
            $(".tf-booking-tab-menu ul li").removeClass("active");
            $(".tf-booking-tab-menu ul li").removeClass("done");
            $(".tf-booking-tab-menu ul li:first-child").addClass("active");
            // Reset Content
            $(".tf-booking-content").hide();
            $(".tf-booking-content:first").show();
            // Reset Pagination
            $(".tf-control-pagination").hide();
            $(".tf-control-pagination:first").show();
        });

        /*
        * Custom modal
        * @author: Foysal
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
    });

})(jQuery, window);

/**
 * Horizontal Search Form Tab Control
 */
function tfOpenForm(evt, formName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tf-tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tf-tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(formName).style.display = "block";
    document.getElementById(formName).style.transition = "all 0.2s";
    evt.target.className += " active";
}

/**
 * Show review form on popup
 */
function tf_load_rating() {
    jQuery('#commentform').show();
}