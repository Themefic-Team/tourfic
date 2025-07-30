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
        
        // Add the classes to the body element
        if (tf_params.body_classes && tf_params.body_classes.length > 0) {
            $.each(tf_params.body_classes, function(index, className) {
                $('body').addClass(className);
            });
        }
        

        /*
         * AJAX load for range filter to prevent performance issue.
         * @author Sunvi
        */
       
        if( $(".widget_tf_price_filters").length > 0 ){

            let urlParams = new URLSearchParams(window.location.search);
            let post_type = urlParams.get('type');
            if(!post_type){
                post_type = $(document).find('input[name="post_id"]').attr("data-post-type");
            }
            if( post_type == 'tf_hotel' || post_type == 'tf_tours' || post_type == 'tf_apartment' || post_type == 'tf_carrental' ){
                $.ajax({
                    type: 'POST',
                    url: tf_params.ajax_url,
                    data: {
                        action: 'tf_get_min_max_price',
                        _nonce: tf_params.nonce,
                        post_type: post_type
                    },
                    success: function (response) {
                        if (response.success) {
                            if( post_type == 'tf_tours' ) {
                                    let tf_tour_range_options = {
                                        range: {
                                            min: parseInt( response.data?.tf_tours?.min ),
                                            max: parseInt( response.data?.tf_tours?.max ),
                                            step: 1
                                        },
                                        initialSelectedValues: {
                                            from: parseInt( response.data?.tf_tours?.min ),
                                            to: parseInt( response.data?.tf_tours?.max )
                                        },
                                        grid: false,
                                        theme: "dark",
                                        onFinish: function () {
                                            if($(".filter-reset-btn").length>0){
                                                $(".filter-reset-btn").show();
                                            }
                                            makeFilter();
                                        }
                                    };
                                    if ( parseInt( response.data?.tf_tours?.min ) != 0 && parseInt( response.data?.tf_tours?.max ) != 0) {
                                        $('.tf-tour-filter-range').alRangeSlider(tf_tour_range_options);
                                    }
                            
                                    // Tours Min and Max Range in Search Result
                                    var tf_search_page_params = new window.URLSearchParams(window.location.search);
                                    let tf_tours_search_range = {
                                        range: {
                                            min: parseInt( response.data?.tf_tours?.min  ),
                                            max: parseInt( response.data?.tf_tours?.max ),
                                            step: 1
                                        },
                                        initialSelectedValues: {
                                            from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt( response.data?.tf_tours?.min ),
                                            to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt( response.data?.tf_tours?.max )
                                        },
                                        grid: false,
                                        theme: "dark",
                                        onFinish: function () {
                                            if($(".filter-reset-btn").length>0){
                                                $(".filter-reset-btn").show();
                                            }
                                            makeFilter();
                                        }
                                    };
                                    if ( parseInt( response.data?.tf_tours?.min ) != 0 && parseInt( response.data?.tf_tours?.max ) != 0) {
                                        $('.tf-tour-result-price-range').alRangeSlider(tf_tours_search_range);
                                    }

                                // Store in global variable or object
                                window.tf_price_ranges = {
                                    min: parseInt(response.data?.tf_tours?.min),
                                    max: parseInt(response.data?.tf_tours?.max)
                                };
                                if(tf_search_page_params.get('from') && tf_search_page_params.get('to')){
                                    window.tf_price_ranges.min = parseInt(tf_search_page_params.get('from'));
                                    window.tf_price_ranges.max = parseInt(tf_search_page_params.get('to'));
                                }
                            } else if( post_type == 'tf_hotel' ) { 
                                        let tf_hotel_range_options = {
                                            range: {
                                                min: parseInt( response.data?.tf_hotel?.min ),
                                                max: parseInt( response.data?.tf_hotel?.max ),
                                                step: 1
                                            },
                                            initialSelectedValues: {
                                                from: parseInt( response.data?.tf_hotel?.min ),
                                                to: parseInt( response.data?.tf_hotel?.max )
                                            },
                                            grid: false,
                                            theme: "dark",
                                            onFinish: function () {
                                                if($(".filter-reset-btn").length>0){
                                                    $(".filter-reset-btn").show();
                                                }
                                                makeFilter();
                                            }
                                        };
                                        if ( response.data?.tf_hotel?.min != 0 && response.data?.tf_hotel?.max != 0) {
                                            $('.tf-hotel-filter-range').alRangeSlider(tf_hotel_range_options);
                                        }
                                
                                        // Hotel Min and Max Range in Search Result
                                        var tf_search_page_params = new window.URLSearchParams(window.location.search);
                                        let tf_hotel_search_range = {
                                            range: {
                                                min: parseInt( response.data?.tf_hotel?.min ),
                                                max: parseInt( response.data?.tf_hotel?.max ),
                                                step: 1
                                            },
                                            initialSelectedValues: {
                                                from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt( response.data?.tf_hotel?.min ),
                                                to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt( response.data?.tf_hotel?.max )
                                            },
                                            grid: false,
                                            theme: "dark",
                                            onFinish: function () {
                                                if($(".filter-reset-btn").length>0){
                                                    $(".filter-reset-btn").show();
                                                }
                                                makeFilter();
                                            }
                                        };
                                        if ( response.data?.tf_hotel?.min != 0 && response.data?.tf_hotel?.max != 0) {
                                            $('.tf-hotel-result-price-range').alRangeSlider(tf_hotel_search_range);
                                        }

                                // Store in global variable or object
                                window.tf_price_ranges = {
                                    min: parseInt(response.data?.tf_hotel?.min),
                                    max: parseInt(response.data?.tf_hotel?.max)
                                };
                                if(tf_search_page_params.get('from') && tf_search_page_params.get('to')){
                                    window.tf_price_ranges.min = parseInt(tf_search_page_params.get('from'));
                                    window.tf_price_ranges.max = parseInt(tf_search_page_params.get('to'));
                                }
                            } else if( post_type == 'tf_apartment' ) {
                                let tf_apartment_range_options = {
                                    range: {
                                        min: parseInt( response.data?.tf_apartment?.min ),
                                        max: parseInt( response.data?.tf_apartment?.max ),
                                        step: 1
                                    },
                                    initialSelectedValues: {
                                        from: parseInt( response.data?.tf_apartment?.min ),
                                        to: parseInt( response.data?.tf_apartment?.max )
                                    },
                                    grid: false,
                                    theme: "dark",
                                };

                                if ( response.data?.tf_apartment?.min != 0 && response.data?.tf_apartment?.max != 0) {
                                    $('.tf-apartment-filter-range').alRangeSlider(tf_apartment_range_options);
                                }

                                var tf_search_page_params = new window.URLSearchParams(window.location.search);
                                let tf_apartment_search_range = {
                                    range: {
                                        min: parseInt( response.data?.tf_apartment?.min ),
                                        max: parseInt( response.data?.tf_apartment?.max ),
                                        step: 1
                                    },
                                    initialSelectedValues: {
                                        from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt( response.data?.tf_apartment?.min ),
                                        to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt( response.data?.tf_apartment?.max )
                                    },
                                    grid: false,
                                    theme: "dark",
                                    onFinish: function () {
                                        if($(".filter-reset-btn").length>0){
                                            $(".filter-reset-btn").show();
                                        }
                                        makeFilter();
                                    }
                                };
                                if ( parseInt(  response.data?.tf_apartment?.min ) != 0 && parseInt( response.data?.tf_apartment?.max ) != 0) {
                                    $('.tf-apartment-result-price-range').alRangeSlider(tf_apartment_search_range);
                                }

                                // Store in global variable or object
                                window.tf_price_ranges = {
                                    min: parseInt(response.data?.tf_apartment?.min),
                                    max: parseInt(response.data?.tf_apartment?.max)
                                };
                                if(tf_search_page_params.get('from') && tf_search_page_params.get('to')){
                                    window.tf_price_ranges.min = parseInt(tf_search_page_params.get('from'));
                                    window.tf_price_ranges.max = parseInt(tf_search_page_params.get('to'));
                                }
                            } else if( post_type == 'tf_carrental' ) {
                                var tf_search_page_params = new window.URLSearchParams(window.location.search);
                                let tf_car_search_range = {
                                    range: {
                                        min: parseInt( response.data?.tf_carrental?.min ),
                                        max: parseInt( response.data?.tf_carrental?.max ),
                                        step: 1
                                    },
                                    initialSelectedValues: {
                                        from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt( response.data?.tf_carrental?.min ),
                                        to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt( response.data?.tf_carrental?.max )
                                    },
                                    grid: false,
                                    theme: "dark",
                                    onFinish: function () {
                                        if($(".filter-reset-btn").length>0){
                                            $(".filter-reset-btn").show();
                                        }
                                        makeFilter();
                                    }
                                };
                                if ( parseInt( response.data?.tf_carrental?.min ) != 0 && parseInt( response.data?.tf_carrental?.max ) != 0) {
                                    $('.tf-car-result-price-range').alRangeSlider(tf_car_search_range);
                                }

                                // Store in global variable or object
                                window.tf_price_ranges = {
                                    min: parseInt(response.data?.tf_carrental?.min),
                                    max: parseInt(response.data?.tf_carrental?.max)
                                };
                                if(tf_search_page_params.get('from') && tf_search_page_params.get('to')){
                                    window.tf_price_ranges.min = parseInt(tf_search_page_params.get('from'));
                                    window.tf_price_ranges.max = parseInt(tf_search_page_params.get('to'));
                                }
                
                                var tf_search_page_params = new window.URLSearchParams(window.location.search);
                                let tf_car_search_seat_range = {
                                    range: {
                                        min: parseInt( response.data?.tf_carrental?.min_seat ),
                                        max: parseInt( response.data?.tf_carrental?.max_seat ),
                                        step: 1
                                    },
                                    initialSelectedValues: {
                                        from: tf_search_page_params.get('from') ? tf_search_page_params.get('from') : parseInt( response.data?.tf_carrental?.min_seat ),
                                        to: tf_search_page_params.get('to') ? tf_search_page_params.get('to') : parseInt( response.data?.tf_carrental?.max_seat )
                                    },
                                    grid: false,
                                    theme: "dark",
                                    onFinish: function () {
                                        if($(".filter-reset-btn").length>0){
                                            $(".filter-reset-btn").show();
                                        }
                                        makeFilter();
                                    }
                                };
                                if ( parseInt( response.data?.tf_carrental?.min_seat ) != 0 && parseInt( response.data?.tf_carrental?.max_seat ) != 0) {
                                    $('.tf-car-result-seat-range').alRangeSlider(tf_car_search_seat_range);
                                }
                                window.tf_price_ranges = {
                                    ...window.tf_price_ranges,
                                    min_seat: parseInt(response.data?.tf_carrental?.min_seat),
                                    max_seat: parseInt(response.data?.tf_carrental?.max_seat)
                                }
                            } 
                        }
                    }
                })
            }
        }

        /**
         * Scroll to Review Section
         */
        $(".tf-top-review a").on("click", function () {
            $('html, body').animate({
                scrollTop: $("#tf-review").offset().top - 32
            }, 1000);
        });

        /**
         * Scroll to Map Section
         */
        $(".tf-map-link a").on("click", function () {
            $('html, body').animate({
                scrollTop: $("#tour-map").offset().top - 32
            }, 1000);
        });

        /**
         * Ajax Search Result
         *
         * by search form submit
         *
         * by feature filter
         */

        var filter_xhr;
        // Creating a function for reuse this filter in any where we needs.
        const makeFilter = (page = 1, mapCoordinates = []) => {
            var dest = $('#tf-place').val();
            var page = page;
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var infant = $('#infant').val();
            var checked = $('#check-in-out-date').val();
            var startprice = $('.widget_tf_price_filters input[name="from"]').val();
            var endprice = $('.widget_tf_price_filters input[name="to"]').val();
            var tf_author = $('#tf_author').val();
            // split date range into dates
            var checkedArr = checked ? checked.split(' - ') : '';
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
            let tf_ordering = $('#tf-orderby').find(":selected").val();

            let category = termIdsByFeildName('car_category');
            let fuel_type = termIdsByFeildName('car_fueltype');
            let engine_year = termIdsByFeildName('car_engine_year');
            let min_seat = $('.widget_tf_seat_filters input[name="from"]').val();
            let max_seat = $('.widget_tf_seat_filters input[name="to"]').val();
            let same_location = $('input[name="same_location"]:checked').val();
            let driver_age = $('input[name="driver_age"]:checked').val();
            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();
            let pickup_slug = $('#tf_pickup_location_id').val();
            let dropoff_slug = $('#tf_dropoff_location_id').val();
            let elSettings = $('#tf-elementor-settings').text();
            
            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('_nonce', tf_params.nonce);
            formData.append('type', posttype);
            formData.append('page', page);
            formData.append('dest', dest);
            formData.append('adults', adults );
            formData.append('room', room);
            formData.append('children', children ? children : 0);
            formData.append('infant', infant ? infant : 0);
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
            formData.append('category', category);
            formData.append('fuel_type', fuel_type);
            formData.append('engine_year', engine_year);
            formData.append('pickup', pickup_slug);
            formData.append('dropoff', dropoff_slug);
            formData.append('pickup_date', pickup_date);
            formData.append('dropoff_date', dropoff_date);
            formData.append('pickup_time', pickup_time);
            formData.append('dropoff_time', dropoff_time);
            formData.append('same_location', same_location);
            formData.append('driver_age', driver_age);
            formData.append('dropoff_time', dropoff_time);
            formData.append("tf_ordering", tf_ordering);
            formData.append("elSettings", elSettings);
            formData.append('page', page);

            if (startprice) {
                formData.append('startprice', startprice);
            }
            if (endprice) {
                formData.append('endprice', endprice);
            }
            if (tf_author) {
                formData.append('tf_author', tf_author);
            }

            if (min_seat) {
                formData.append('min_seat', min_seat);
            }
            if (max_seat) {
                formData.append('max_seat', max_seat);
            }

            if(mapCoordinates.length === 4){
                formData.append('mapCoordinates', mapCoordinates.join(','));
                formData.append('mapFilter', true);
            }
            // abort previous request
            if (filter_xhr && filter_xhr.readyState != 4) {
                filter_xhr.abort();
            }

            wp.hooks.applyFilters('tf_search_filter_ajax_data', formData, {
                posttype: posttype,
            });

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
                        var mapPostsCount = $('.tf-map-posts-count').html();
                        $('.tf-total-results').find('span').html(postsCount);
                        $('.tf-total-results').find('span.tf-map-item-count').html(mapPostsCount);
                    }
                },
                success: function (data, e) {
                    $('.archive_ajax_result').unblock();
                    $('#tf_ajax_searchresult_loader').hide();
                    $('.archive_ajax_result').html(data);
                    // Filter Popup Removed
                    if($('.tf-filter-cars')){
                        $('.tf-filter-cars').removeClass('tf-btn-loading');
                    }
                    if ($('.tf-details-right').length > 0) {
                        $('.tf-details-right').removeClass('tf-filter-show');
                    }
                    if($('#tf-hotel-archive-map').length) {

                        // GOOGLE MAP INITIALIZE
                        var mapLocations = $('#map-datas').html();
                        if ($('#map-datas').length && mapLocations.length) {
                            googleMapInit(mapLocations);
                        } else {
                            googleMapInit('');
                        }
                    }

                    // @KK show notice in every success request
                    if(!mapCoordinates.length) {
                        notyf.success(tf_params.ajax_result_success);
                    }
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
        $(document).on('click', '.tf_search_ajax_pagination a.page-numbers', function (e) {
            e.preventDefault();
            page = tf_page_pagination_number($(this).clone());
            makeFilter(page);
        }); 
        

        // Search Result Ajax pagination
        $(document).on('click', 'tf_tax_posts_navigation a.page-numbers', function (e) {
            e.preventDefault();
            page = tf_page_pagination_number($(this).clone());
            makeFilter(page);
        });

        // Look for submission and change on filter widgets
        $(document).on('submit', '#tf-widget-booking-search', function (e) {
            e.preventDefault();
            makeFilter()
        });
        $(document).on('change', '.widget_tf_price_filters input[name="from"], .widget_tf_price_filters input[name="to"], [name*=tf_filters],[name*=tf_hotel_types],[name*=tf_features],[name*=tour_features],[name*=tf_attractions],[name*=tf_activities],[name*=tf_tour_types],[name*=tf_apartment_features],[name*=tf_apartment_types], [name*=car_category],[name*=car_fueltype],[name*=car_engine_year]', function () {
            if ($(".filter-reset-btn").length > 0) {
                $(".filter-reset-btn").show();
            }
            makeFilter();
        })

        // Archive Page Filter
        $(document).on('submit', '.tf_archive_search_result', function (e) {
            e.preventDefault();

            checked = $('#check-in-out-date').val();
            var checkedArr = checked.split(' - ');
            var checkin = checkedArr[0];
            var checkout = checkedArr[1];
            var posttype = $('.tf-post-type').val();


            if ($.trim(checkin) === '' && tf_params.date_hotel_search && posttype === 'tf_hotel') {

                if ($('#tf-required').length === 0) {
                    if($('.tf_booking-dates .tf_label-row').length === 1){
                        $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                    }else{
                        $("#check-in-out-date").trigger("click");
                    }
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_tour_search && posttype === 'tf_tours') {

                if ($('#tf-required').length === 0) {
                    if($('.tf_booking-dates .tf_label-row').length === 1){
                        $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                    }else{
                        $("#check-in-out-date").trigger("click");
                    }
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_apartment_search && posttype === 'tf_apartment') {

                if ($('#tf-required').length === 0) {
                    if($('.tf_booking-dates .tf_label-row').length === 1){
                        $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                    }else{
                        $("#check-in-out-date").trigger("click");
                    }
                }
                return;
            }

            makeFilter()
        });

        // Archive Page Filter Reset
        $(document).on('click', '.filter-reset-btn', function (e) {
            e.preventDefault();

            // Reset checkboxes
            $('[name*=tf_filters],[name*=tf_hotel_types],[name*=tf_features],[name*=tour_features],[name*=tf_attractions],[name*=tf_activities],[name*=tf_tour_types],[name*=tf_apartment_features],[name*=tf_apartment_types], [name*=car_category],[name*=car_fueltype],[name*=car_engine_year]').prop('checked', false);
            
            // Reset price sliders
            if ($('.tf-hotel-filter-range').length > 0) {
                $('.tf-hotel-filter-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min, to: window.tf_price_ranges.max },
                });
            }
            if ($('.tf-hotel-result-price-range').length > 0) {
                $('.tf-hotel-result-price-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min, to: window.tf_price_ranges.max },
                });
            }

            if ($('.tf-tour-filter-range').length > 0) {
                $('.tf-tour-filter-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min, to: window.tf_price_ranges.max },
                });
            }

            if ($('.tf-apartment-filter-range').length > 0) {
                $('.tf-apartment-filter-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min, to: window.tf_price_ranges.max },
                });
            }

            if ($('.tf-car-result-price-range').length > 0) {
                $('.tf-car-result-price-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min, to: window.tf_price_ranges.max },
                });
            }

            if ($('.tf-car-result-seat-range').length > 0) {
                $('.tf-car-result-seat-range').alRangeSlider('update', {
                    values: { from: window.tf_price_ranges.min_seat, to: window.tf_price_ranges.max_seat },
                });
            }
            
            makeFilter();
            $(".filter-reset-btn").hide();

            //template 4
            if ($(".tf-archive-filter-sidebar").length > 0) {
                $(".tf-archive-filter-sidebar").removeClass('tf-show');
            }
        });

        $(".tf-archive-ordering").on('change', 'select.tf-orderby', function (e) {
            $(this).closest('form').trigger('submit');
        });

        $(".tf-archive-ordering").on('submit', function (e) {
            e.preventDefault();
            makeFilter();

        });

        /*
        * Car Archive Filter
        * @author Jahid
        */
        $(document).on('click', '.tf-filter-cars', function (e) {
            let $this = $(this);
            $this.addClass('tf-btn-loading');

            if(tf_params.location_car_search){
                let same_location = $('input[name="same_location"]:checked').val();
                if('on'==same_location){
                    if ($.trim($('#tf_pickup_location').val()) == '') {
                        if ($('#tf-required').length === 0) {
                            if($('.tf-driver-location').length === 1){
                                $('.tf-driver-location').append('<span id="tf-required" class="required"><b>Select Pickup & Dropoff Location</b></span>');
                            }else{
                                $("#tf_pickup_location").trigger("click");
                            }
                        }
                        $('.tf-filter-cars').removeClass('tf-btn-loading');
                        return;
                    } else {
                        if ($('#tf-required').length === 1) {
                            $('.tf-driver-location .required').remove();
                        }
                    }
                }else{
                    if ($.trim($('#tf_pickup_location').val()) == '' || $.trim($('#tf_dropoff_location').val()) == '') {
                        if ($('#tf-required').length === 0) {
                            if($('.tf-driver-location').length === 1){
                                $('.tf-driver-location').append('<span id="tf-required" class="required"><b>Select Pickup & Dropoff Location</b></span>');
                            }else{
                                $("#tf_pickup_location").trigger("click");
                            }
                        }
                        $('.tf-filter-cars').removeClass('tf-btn-loading');
                        return;
                    } else {
                        if ($('#tf-required').length === 1) {
                            $('.tf-driver-location .required').remove();
                        }
                    }
                }


            }

            if(tf_params.date_car_search){
                if ($.trim($('.tf_pickup_date').val()) == '' || $.trim($('.tf_dropoff_date').val()) == '') {
                    if ($('#tf-required').length === 0) {
                        if($('.tf-driver-location').length === 1){
                            $('.tf-driver-location').append('<span id="tf-required" class="required"><b>Select Pickup & Dropoff Date</b></span>');
                        }else{
                            $(".tf_pickup_date").trigger("click");
                        }
                    }
                    $('.tf-filter-cars').removeClass('tf-btn-loading');
                    return;
                } else {
                    if ($('#tf-required').length === 1) {
                        $('.tf-driver-location .required').remove();
                    }
                }
            }

            if($(".filter-reset-btn").length>0){
                $(".filter-reset-btn").show();
            }

            makeFilter();
        });

        /*
        * Same Location Checkbox
        * @author Jahid
        */
        $(document).on('click', '.tf-driver-location [name="same_location"]', function (e) {
            // Check if the checkbox is checked
            if ($(this).is(':checked')) {
                $('.tf-pick-drop-location').addClass('active');
            } else {
                $('.tf-pick-drop-location').removeClass('active');
            }
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

        $(window).on("load", function () {
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
        $('.share-toggle[data-toggle="true"]').on("click", function (e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).slideToggle('fast');
        });

        // Copy button
        $('button#share_link_button').on("click", function () {
            $(this).addClass('copied');
            $(this).children('span').css('display', 'block');
            setTimeout(function () {
                $('button#share_link_button').removeClass('copied');
                $('button#share_link_button').children('span').css('display', 'none');
            }, 3000);
            const inputElement = $(this).parent().find("#share_link_input");
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(inputElement.val()).then(() => {
                    
                });
            } else {
                const tempInput = document.createElement("textarea");
                tempInput.value = inputElement.val();
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
            }
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


        // Design 2 related tour
        $('.tf-design-2-slider-items-wrapper, .tf-design-3-slider-items-wrapper').slick({
            dots: false,
            arrows: true,
            infinite: true,
            speed: 300,
            autoplaySpeed: 2000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
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

            if (index >= 0) {
                userLists.splice(index, 1)
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
            targetNode.addClass('fas fa-heart');
            targetNode.addClass('tf-text-red');
            targetNode.removeClass('far fa-heart-o');
            targetNode.removeClass('add-wishlist');


        }
        /* blank icon */
        const wishIcon = targetNode => {
            targetNode.addClass('add-wishlist');
            targetNode.addClass('far fa-heart-o');
            targetNode.removeClass('fas fa-heart');
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

        /*
        * Trourfic autocomplete destination
        */
        function tourfic_autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/

            // Executes when some one click in the search form location
            inp.addEventListener("focus", function () {
                if (this.value == '' || !this.value) {
                    // alert("Working....")
                    let a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.classList.add("autocomplete-items")
                    this.parentNode.appendChild(a);
                    for (const [key, value] of Object.entries(arr)) {
                        let b = document.createElement("DIV");
                        b.innerHTML = value;
                        b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                        b.addEventListener("click", function (e) {
                            let source = this.getElementsByTagName("input")[0];
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug
                        });
                        a.appendChild(b);
                    }
                }
            })

            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("keyup", function (e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
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
                        if (x) x[currentFocus].trigger("click");
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
                // closeAllLists(e.target);
                if (e.target.id == "content" || e.target.id == "") {
                    closeAllLists(e.target);
                }
            });
        }

        /**
         * Open/close horizontal search form persons panel
         */
        // Adult, Child, Room Selection toggle
        $(".tf_selectperson-wrap .tf_input-inner,.tf_person-selection-wrap .tf_person-selection-inner").on("click", function () {
            $('.tf_acrselection-wrap').slideToggle('fast');
        });
        // Close
        document.addEventListener("click", function (event) {
            if (!$(event.target).closest(".tf_selectperson-wrap, .tf_acrselection-wrap, .tf-booking-form-guest-and-room").length) {
                $(".tf_acrselection-wrap, .tf_person-selection-wrap").slideUp("fast");
            }
        });

        /**
         * Number/text change horizontal search form
         */
        // Number Increment
        $('.acr-inc, .quanity-acr-inc').on('click', function (e) {
            var input = $(this).parent().find('input');
            var max = input.attr('max') ? input.attr('max') : 999;
            if(input.attr('data-max')){
                max = input.attr('data-max');
            }

            var step = input.attr('step') ? input.attr('step') : 1;
            if (!input.val()) {
                input.val(0);
            }
            if (input.val() < max) {
                input.val(parseInt(input.val()) + parseInt(step)).change();
            }
            if(input.val() == max){
                $(this).addClass('disable');
                $(this).parent().find('.acr-dec').removeClass('disable');
            }else{
                $(this).parent().find('.acr-dec').removeClass('disable');
            }
            // input focus disable
            input.blur();
        });

        // Number Decrement
        $('.acr-dec, .quanity-acr-dec').on('click', function (e) {

            var input = $(this).parent().find('input');
            var min = input.attr('min') ? input.attr('min') : 0;
            if(input.attr('data-min')){
                min = input.attr('data-min');
            }
            var step = input.attr('step') ? input.attr('step') : 1;
            if (!input.val()) {
                input.val(0);
            }
            if (input.val() > min) {
                input.val(input.val() - parseInt(step)).change();
            }
            if(input.val() == min){
                $(this).addClass('disable');
                $(this).parent().find('.acr-inc').removeClass('disable');
            }else{
                $(this).parent().find('.acr-inc').removeClass('disable');
            }
        });

        // Adults change trigger
        $(document).on('change', '#adults', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                $('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult + 's');
            } else {
                $('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult);
            }

        });

        // Children change trigger
        $(document).on('change', '#children', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                $('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            } else {
                $('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            }

        });

        // Infant change trigger
        $(document).on('change', '#infant', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                $('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
            } else {
                $('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
            }

        });

        // Handle all traveler category inputs with a single function
        $(document).on('change', '.tf_hotel-shortcode-design-2 .tf_acrselection input[type="tel"]', function() {
            const $form = $(this).closest('.tf_hotel-shortcode-design-2');
            let totalPeople = 0;
            
            // Sum all traveler inputs in the form
            $form.find('.tf_acrselection input[type="tel"]').each(function() {
                const value = parseInt($(this).val()) || 0;
                totalPeople += value;
            });
            
            if (totalPeople > 0) {
                $form.find(".tf_guest_number .guest").text(totalPeople);
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

        // Shortcode Design 2 Room change trigger
        $(document).on('change', '.rooms-style2', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();
            let total_room = parseInt(thisVal);
            if (total_room > 1) {
                $(".tf_hotel-shortcode-design-2 .tf_guest_number .room").text(total_room);
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

        $(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner").on("click", function () {
            $('.tf-more-info').toggleClass('show');
        });
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner, .tf-more-info").length) {
                $('.tf-more-info').removeClass('show');
            }
        });

        // FAQ Accordion
        $('.tf-faq-title').on("click", function () {
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
        $('.tf-faq-collaps').on("click", function () {
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
        * Booking Form select first tab on load
        * @author: Foysal
        */
        $(window).on('load', function () {
            if ($('.tf-tablinks').length > 0) {
                $('.tf-tablinks').first().trigger('click').addClass('active');
            }
        });

        // Form Tab click
        $('.tf-tablinks').on('click', function (e) {
            e.preventDefault();
            let formId = $(this).data('form-id');
            tfOpenForm(e, formId);
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
            $this.parent('.tf-filter').find('.tf-filter-item').filter(function (index) {
                return index > 3;
            }).removeClass("hidden");
            $this.hide();

            $this.parent('.tf-filter').find('.see-less').show();
        });

        /* see less checkbox filter started */

        $('a.see-less').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-filter').find('.tf-filter-item').filter(function (index) {
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
            $(this).find('.tf-filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");

        });

        $('.tf-category-lists').each(function () {

            var len = $(this).find('ul').children().length;
            $(this).find('.see-more').hide();
            if (len > 4) {
                $(this).find('.see-more').show();
            }
            //hide items if crossed showing limit
            $(this).find('.tf-filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");

        });
        /* see more checkbox filter started */

        $('.tf-category-lists a.see-more').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-category-lists').find('.tf-filter-item').filter(function (index) {
                return index > 3;
            }).removeClass("hidden");
            $this.hide();

            $this.parent('.tf-category-lists').find('.see-less').show();
        });

        /* see less checkbox filter started */

        $('.tf-category-lists a.see-less').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-category-lists').find('.tf-filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");
            $this.hide();
            $this.parent('.tf-category-lists').find('.see-more').show();
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
                    _nonce: tf_params.nonce,
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
        * Without Payment Booking
        * @since 2.9.26
        * @author Jahid
        */
        let tf_hasErrorsFlag = false;
        $('body').on('click', '.tf-traveller-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-single-travel').each(function () {
                $(this).find('input, select').each(function () {
                    if ($(this).attr('data-required') && $(this).attr('data-required') == 1) {
                        if ($(this).val() == "") {
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
                $(this).find('input[type="radio"], input[type="checkbox"]').each(function () {
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
        $('body').on('click', '.tf-book-confirm-error, .tf-hotel-book-confirm-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-confirm-fields').each(function () {
                $(this).find('input, select').each(function () {
                    if ($(this).attr('data-required') && $(this).attr('data-required') == 1 ) {
                        if ($(this).val() == "") {
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
                $(this).find('input[type="radio"], input[type="checkbox"]').each(function () {
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
            let active_steps = $('.tf_popup_stpes').val();
            let stepsArray = active_steps.split(',').map(Number);
            let currentStep = parseInt($(this).attr("data-step"));

            let currentIndex = stepsArray.indexOf(currentStep);
            let step = stepsArray[currentIndex + 1];

            if (step > 1) {
                for (let i = 1; i <= step; i++) {
                    $('.tf-booking-step-' + i).removeClass("active");
                    $('.tf-booking-step-' + (i - 1)).addClass("done");
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
            
            let active_steps = $('.tf_popup_stpes').val();
            let stepsArray = active_steps.split(',').map(Number);
            let currentStep = parseInt($(this).attr("data-step"));

            // Find the previous available step from active_steps
            let currentIndex = stepsArray.indexOf(currentStep);
            let step = (currentIndex > 0) ? stepsArray[currentIndex - 1] : 1;
            
            // let step = $(this).attr("data-step");
            if (step == 1) {
                $('.tf-booking-step').removeClass("active");
                $('.tf-booking-step').removeClass("done");
                $('.tf-booking-step-' + step).addClass("active");
                $('.tf-booking-content').hide();
                $('.tf-booking-content-' + step).fadeIn(300);

                $('.tf-control-pagination').hide();
                $('.tf-pagination-content-' + step).fadeIn(300);
            }
            if (step > 1) {
                let next_step = parseInt(step) + 1;
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
        const tourPopupBooking = () => {
            var $this = $(this);
            let check_in_date = $('#check-in-out-date').val();
            let adults = $('#adults').val();
            let children = $('#children').val();
            let infant = $('#infant').val();
            let post_id = $('input[name=post_id]').val();
            let check_in_time = $('select[name=check-in-time] option').filter(':selected').val();
            var deposit = $('input[name=deposit]').is(':checked');
            var extras = [];
            var quantity = [];
            var selectedPackage = $('.tf-booking-content-package input[name="tf_package"]:checked').val();
            if (selectedPackage !== undefined) {
                var $selectedDiv = $('#package-' + selectedPackage).closest('.tf-single-package');
                adults = $selectedDiv.find('input[name="adults"]').val();
                children = $selectedDiv.find('input[name="childrens"]').val();
                infant = $selectedDiv.find('input[name="infants"]').val();
                check_in_time = $selectedDiv.find('select[name=package_start_time] option').filter(':selected').val();

                $('.tf-single-package').each(function () {
                    var $package = $(this);
                    var currentKey = $package.find('input[name="tf_package"]').val();
                    var isSelected = currentKey === selectedPackage;
            
                    $package.find('input[type="number"]').prop('disabled', !isSelected);
                });
            }
            $('.tour-extra-single').each(function (e) {
                let $this = $(this);

                if ($this.find('input[name="tf-tour-extra"]').is(':checked')) {

                    let tour_extras = $this.find('input[name="tf-tour-extra"]').val();
                    extras.push(tour_extras);

                    if ($this.find('.tf_quantity-acrselection').hasClass('quantity-active')) {
                        let qty = $this.find('input[name="extra-quantity"]').val();

                        quantity.push(qty)
                    } else {
                        quantity.push(1)
                    }
                }
            })

            var extras = extras.join();
            var quantities = quantity.join();
            var data = {
                action: 'tf_tour_booking_popup',
                _nonce: tf_params.nonce,
                post_id: post_id,
                adults: adults,
                children: children,
                infant: infant,
                check_in_date: check_in_date,
                check_in_time: check_in_time,
                tour_extra: extras,
                tour_extra_quantity: quantities,
                deposit: deposit,
                selectedPackage: selectedPackage
            };

            wp.hooks.applyFilters('tf_tour_booking_popup_data', data, selectedPackage);

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
                                $('.tf-traveller-info-box').html(response.traveller_info);
                            } else {
                                $('.tf-traveller-info-box').html(response.traveller_info);
                            }
                        }
                        if ($('.tf-booking-traveller-info').length > 0) {
                            $('.tf-booking-traveller-info').html(response.traveller_summery);
                        }
                        if (response.pacakge_times && typeof response.pacakge_times === 'object') {
                            Object.entries(response.pacakge_times).forEach(([key, times]) => {
                                const wrapper = $(`.tf-package-times-${key}`);
                                wrapper.css('display', 'flex');
                                const select = wrapper.find('select[name="package_start_time"]');
                                if (select.length && select.children('option').length === 0) {                        
                                    // Add placeholder option first
                                    select.append(`<option value="" disabled selected>Time</option>`);

                                    // Then add time options
                                    times.forEach((time) => {
                                        select.append(`<option value="${time}">${time}</option>`);
                                    });
                                }
                            });
                        }
                        
                        $('.tf-withoutpayment-booking').addClass('show');
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });
        }
        $('.tf-booking-popup-btn').on('click', function(e){
            e.preventDefault();
            $(".tf-withoutpayment-booking input[type='text'], .tf-withoutpayment-booking input[type='email'], .tf-withoutpayment-booking input[type='date'], .tf-withoutpayment-booking select, .tf-withoutpayment-booking textarea").val("");

            $('.tf-booking-content-extra input[type="checkbox"]').each(function () {
                if ($(this).prop('checked') == true) {
                    $(this).prop('checked', false);
                }
            });
            tourPopupBooking();
        });

        $(document).on('change', '[name*=tf-tour-extra], input[name="extra-quantity"]', function () {
            tourPopupBooking();
        });
        $(document).on('change', '[name=deposit], [name=tf_package]', function () {
            tourPopupBooking();
        });

        $('.tf-single-person .acr-inc, .tf-single-person .acr-dec').on('click', function (e) {
            tourPopupBooking();
        });

        $('input[name="tf_package"]').on('change', function () {
            var selectedKey = $(this).val();
            $('.tf-single-package').each(function () {
                var $package = $(this);
                if ($package.find('input[name="tf_package"]').val() !== selectedKey) {
                    $package.find('input[type="number"]').prop('disabled', true);
                } else {
                    $package.find('input[type="number"]').prop('disabled', false);
                }
            });
        });        

        // Popup Close
        $('body').on('click touchstart', '.tf-booking-times span', function (e) {
            e.preventDefault();
            $('.tf-withoutpayment-booking').removeClass('show');
            $('.tf-withoutpayment-booking-confirm').removeClass('show');
            // Reset Tabs
            $(".tf-booking-tab-menu ul li").removeClass("active");
            $(".tf-booking-tab-menu ul li").removeClass("done");
            $(".tf-booking-tab-menu ul li:first-child").addClass("active");
            // Reset Content
            $(".tf-booking-content").hide();
            $(".tf-booking-content.show").show();
            // Reset Pagination
            $(".tf-control-pagination").hide();
            $(".tf-control-pagination.show").show();
        });

        /*
        * Custom modal
        * @author: Foysal
        */
        $(document).on('click', '.tf-modal-btn', function (e) {
            e.preventDefault();
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
                _nonce: tf_params.nonce,
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
         * Design 2 Hotel Details Popup
         *
         */
        $(document).on('click', '.tf-room-detail-popup', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            var post_id = $(this).attr("data-hotel");
            var uniqid_id = $(this).attr("data-uniqid");
            var data = {
                action: 'tf_tour_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                uniqid_id: uniqid_id
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $(".tf-room-popup").html(response);
                    $(".tf-room-popup").addClass("tf-show")
                    $("#tour_room_details_loader").hide();

                }

            });
        });

        // Design 3 Toggle share buttons
        $('.tf-single-template__two .tf-share-toggle, .tf-single-template__three .tf-share-toggle').on("click", function (e) {
            e.preventDefault();
            $('.tf-share-toggle').toggleClass('actives');
            $('.tf-off-canvas-share').toggleClass('show');
        });

        // Design 3 Wishlist buttons
        $('.tf-single-template__two .add-wishlist, .tf-single-template__three .add-wishlist').on("click", function (e) {
            e.preventDefault();
            $(this).parents().find('.tf-wishlist-box').addClass('actives');
        });
        $('.tf-single-template__two .remove-wishlist, .tf-single-template__three .remove-wishlist').on("click", function (e) {
            e.preventDefault();
            $(this).parents().find('.tf-wishlist-box').removeClass('actives');
        });

        // Copy button
        $('a#share_link_button').on("click", function (e) {
    
            e.preventDefault();
            $(this).addClass('copied');
           
            setTimeout(function () {
                $('a#share_link_button').removeClass('copied');
            }, 3000);
           // Get the input element
            const inputElement = $(this).parent().find("#share_link_input");


            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(inputElement.val()).then(() => {
                    
                });
            } else {
                const tempInput = document.createElement("textarea");
                tempInput.value = inputElement.val();
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
            }
       
        });

        /*
        * Template 3 Script Start
        * @author: Jahid
        */
        $('.tf-single-template__two .tf-reviews-slider').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 3,
            prevArrow: '<button class="slide-arrow prev-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="49" height="24" viewBox="0 0 49 24" fill="none"><path d="M8.32843 11.0009H44.5V13.0009H8.32843L13.6924 18.3648L12.2782 19.779L4.5 12.0009L12.2782 4.22266L13.6924 5.63687L8.32843 11.0009Z" fill="#B58E53"/></svg></button>',
            nextArrow: '<button class="slide-arrow next-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="49" height="24" viewBox="0 0 49 24" fill="none"><path d="M40.6716 11.0009H4.5V13.0009H40.6716L35.3076 18.3648L36.7218 19.779L44.5 12.0009L36.7218 4.22266L35.3076 5.63687L40.6716 11.0009Z" fill="#B58E53"/></svg></button>',
            responsive: [
                {
                    breakpoint: 993,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ]
        });

        $(".tf-single-template__two .tf-question").on("click", function () {
            var $this = $(this);
            if (!$this.hasClass("tf-active")) {
                $(this).siblings().removeClass("tf-active");
                $(this).siblings().find('.tf-question-desc').slideUp();
                $(this).parents(".tf-questions-col").siblings().find('.tf-question').removeClass("tf-active");
                $(this).parents(".tf-questions-col").siblings().find('.tf-question-desc').slideUp();
            }
            $(this).toggleClass("tf-active");
            $(this).find('.tf-question-desc').slideToggle();
        });

        $(".tf-single-template__two .tf-hero-hotel.tf-popup-buttons").on("click", function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            setTimeout(function () {
                $("#tour_room_details_loader").hide();
                $(".tf-hotel-popup").addClass("tf-show");
            }, 1000);
        });

        $(document).on('click', '.tf-single-template__two .tf-popup-close, .tf-archive-template__two .tf-popup-close', function () {
            $(".tf-popup-wrapper").removeClass("tf-show")
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest(".tf-popup-wrapper .tf-popup-inner").length) {
                $(".tf-popup-wrapper").removeClass('tf-show');
            }
        });

        $('.tf-single-template__two .tf-details-menu a').on('click', function () {
            $(this).addClass('tf-hashlink');
            $(this).closest('li').siblings().find('a').removeClass('tf-hashlink');
        });

        //Room Filter Showing
        $('.tf-single-template__two .tf-available-rooms-head .tf-filter, .tf-archive-template__two .tf-available-rooms-head .tf-filter, .tf-archive-template__three .tf-available-rooms-head .tf-filter').on('click', function () {
            $('.tf-room-filter').toggleClass('tf-filter-show');
        });

        //Archive Filter Showing
        $('.tf-archive-template__two .tf-archive-filter-showing').on('click', function () {
            $('.tf-archive-right').toggleClass('tf-filter-show');
        });

        $(document).on('click touchstart', function (event) {
            if (!$(event.target).closest(".tf-archive-filter-showing, .tf-details-right").length) {
                $(".tf-details-right").removeClass('tf-filter-show');
            }
        });

        //Search Form Showing
        $('.tf-single-template__two .tf-modify-search-btn, .tf-archive-template__two .tf-modify-search-btn').on('click', function () {
            $('.tf-booking-form-wrapper .tf-booking-form').slideDown(300);
            $('.tf-single-template__two .tf-modify-search-btn, .tf-archive-template__two .tf-modify-search-btn').slideUp(300);
        });

        // Full Description Showing
        $('.tf-single-template__two span.tf-see-description, .tf-archive-template__three span.tf-see-description, .single-tf_carrental .tf-single-template__one span.tf-see-description').on('click', function () {
            $('.tf-short-description').slideUp();
            $('.tf-full-description').slideDown();
        });

        // See Less Description Showing
        $('.tf-single-template__two span.tf-see-less-description, .tf-single-template__three span.tf-see-less-description, .single-tf_carrental .tf-single-template__one span.tf-see-less-description').on('click', function () {
            $('.tf-full-description').slideUp();
            $('.tf-short-description').slideDown();
        });

        $('.tf-single-template__two .acr-inc, .tf-archive-booking-form__style-2 .acr-inc, .tf-single-template__two .acr-dec, .tf-archive-booking-form__style-2 .acr-dec').on('click', function () {

            if ($('input#infant').length) {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0) + Number($('input#children').val() ? $('input#children').val() : 0) + Number($('input#infant').val() ? $('input#infant').val() : 0);
            } else {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0) + Number($('input#children').val() ? $('input#children').val() : 0);
            }
            var guest = wp.hooks.applyFilters('tf_guest_count', guest);
            if (guest.toString().length < 2) {
                guest = '0' + guest;
            }
            $('span.tf-guest').html(guest);
            var room = Number($('input#room').val());
            if (room.toString().length < 2) {
                room = '0' + room;
            }
            $('span.tf-room').html(room);
        })

        $(document).ready(function () {
            if ($('input#infant').length) {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0) + Number($('input#children').val() ? $('input#children').val() : 0) + Number($('input#infant').val() ? $('input#infant').val() : 0);
            } else {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0) + Number($('input#children').val() ? $('input#children').val() : 0);
                var adult = Number($('input#adults').val() ? $('input#adults').val() : 0);
                var children = Number($('input#children').val() ? $('input#children').val() : 0);
            }
            var guest = wp.hooks.applyFilters('tf_guest_count', guest);
            if (guest.toString().length < 2) {
                guest = '0' + guest;
            }

            $('span.tf-guest').html(guest);
            $('span.tf-adult').html(adult);
            $('span.tf-children').html(children);
        })

        $(document).on("mouseup", function (e) {
            var container = $(".tf-single-template__two .tf_acrselection-wrap, .tf-archive-booking-form__style-2 .tf_acrselection-wrap");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $(".tf-single-template__two .tf-booking-form-guest-and-room .tf_acrselection-wrap, .tf-archive-booking-form__style-2 .tf-booking-form-guest-and-room .tf_acrselection-wrap").removeClass("tf-show");
            }
        });
        $(".tf-single-template__two .tf-booking-form-guest-and-room, .tf-archive-booking-form__style-2 .tf-booking-form-guest-and-room").on("click", function () {
            $(".tf-single-template__two .tf-booking-form-guest-and-room .tf_acrselection-wrap, .tf-archive-booking-form__style-2 .tf-booking-form-guest-and-room .tf_acrselection-wrap").addClass("tf-show");
        });

        $(".tf-single-template__two .tf-review-open").on("click", function () {
            $(".tf-single-template__two .tf-sitebar-widgets .tf-review-form-wrapper").toggleClass("tf-review-show");
        });

        /**
         * Design 2 Archive Hotel Room Gallery & Archive Tour Gallery Popup
         *
         */
        $(document).on('click', '.tf-hotel-room-popup', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            var post_id = $(this).attr("data-id");
            var post_type = $(this).attr("data-type");
            var data = {
                action: 'tf_hotel_archive_popup_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                post_type: post_type
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $(".tf-popup-body").html(response);
                    $(".tf-hotel-popup").addClass("tf-show")
                    $("#tour_room_details_loader").hide();

                }
            });
        });

        /*
        * Template 2 Script End
        * @author: Jahid
        */

        /*
        * Template 4 Script Start
        * @author: Foysal
        */
        $('.tf-archive-booking-form__style-3 .acr-inc , .tf-archive-booking-form__style-3 .acr-dec').on('click', function () {

            if ($('input#infant').length) {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0 ) + Number($('input#children').val() ? $('input#children').val() : 0) + Number($('input#infant').val() ? $('input#infant').val() : 0);
            } else {
                var guest = Number($('input#adults').val() ? $('input#adults').val() : 0 ) + Number($('input#children').val() ? $('input#children').val() : 0);
                var adult = Number($('input#adults').val() ? $('input#adults').val() : 0 );
                var children = Number($('input#children').val() ? $('input#children').val() : 0);
            }
            var guest = wp.hooks.applyFilters('tf_guest_count', guest);
            if (guest.toString().length < 2) {
                guest = '0' + guest;
            }
            $('span.tf-guest').html(guest);
            $('span.tf-adult').html(adult);
            $('span.tf-children').html(children);
            var room = Number($('input#room').val());
            if (room.toString().length < 2) {
                room = '0' + room;
            }
            $('span.tf-room').html(room);
        })

        $(document).mouseup(function (e) {
            var container = $(".tf-archive-booking-form__style-3 .tf_acrselection-wrap");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $(".tf-archive-booking-form__style-3 .tf-search-guest-and-room .tf_acrselection-wrap").removeClass("tf-show");
            }
        });
        $(".tf-archive-booking-form__style-3 .tf-search-guest-and-room").click(function () {
            $(".tf-archive-booking-form__style-3 .tf-search-guest-and-room .tf_acrselection-wrap").addClass("tf-show");
        });

        /*
         * Grid/List
         */
        $(document).on('click', '.tf-archive-view li.tf-archive-view-item', function (e) {
            e.preventDefault();
            $('.tf-archive-view li.tf-archive-view-item').removeClass('active');
            $(this).addClass('active');

            let dataId = $(this).data('id');
            let hotelContainer = $('.tf-archive-hotels');

            if (dataId === 'grid-view') {
                hotelContainer.addClass('tf-layout-grid');
                hotelContainer.removeClass('tf-layout-list');
            } else {
                hotelContainer.addClass('tf-layout-list');
                hotelContainer.removeClass('tf-layout-grid');
            }
            adjustPadding();
        });

        /*
        * Template 4 hotel, tour, apartment archive scrollbar
        */
        function adjustPadding() {
            var hotelsContainer = $('.tf-archive-template__three .tf-archive-hotels, .tf-archive-details-wrap .tf-archive-hotels');
        
            if (window.innerWidth > 768) {
                if (hotelsContainer[0].scrollHeight > hotelsContainer.height()) {
                    hotelsContainer.css('padding-right', '16px');
                } else {
                    hotelsContainer.css('padding-right', '0px');
                }
            } else {
                hotelsContainer.css('padding-right', '0px');
            }
        }
        
        if ($('.tf-archive-template__three .tf-archive-hotels').length) {
            adjustPadding();
            $(window).on('resize', adjustPadding);
        }

        function adjustSidebarPadding() {
            var sidebar = $('.tf-archive-template__three #tf__booking_sidebar, #tf_map_popup_sidebar');

            if (sidebar[0].scrollHeight > sidebar.height()) {
                sidebar.css('padding-right', '16px');
            } else {
                sidebar.css('padding-right', '0px');
            }
        }

        if($('.tf-archive-template__three #tf__booking_sidebar').length) {
            adjustSidebarPadding();
            $(window).on('resize', adjustSidebarPadding);
        }

        /*
        * Filter btn
        */
        $(document).on('click', '.tf-archive-filter-btn', function () {
            $('.tf-archive-filter-sidebar').toggleClass('tf-show');
        });
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf-archive-filter-sidebar, .tf-archive-filter-btn").length) {
                $('.tf-archive-filter-sidebar').removeClass("tf-show");
            }
        });

        /**
         * Hotel single room gallery modal
         */
        $(document).on('click', '.tf-room-modal-btn', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            var post_id = $(this).attr("data-hotel");
            var uniqid_id = $(this).attr("data-uniqid");
            var data = {
                action: 'tf_tour_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                uniqid_id: uniqid_id
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $(".tf-room-modal .tf-modal-body").html(response);
                    $(".tf-room-modal").addClass("tf-modal-show");
                    $('body').addClass('tf-modal-open');
                    $("#tour_room_details_loader").hide();
                }

            });
        });

        // Template 4 section toggle
        $('.tf-section-toggle-icon').on("click", function () {
            var $this = $(this);
            var parent = $this.closest('.tf-template-section');
            if (!$this.hasClass("active")) {
                parent.find(".tf-section-toggle").slideUp(500);
                $this.removeClass("active");
                parent.find('.tf-toggle-icon-down').removeClass("active");
            }
            $this.toggleClass("active");
            parent.find(".tf-section-toggle").slideToggle();
        });

        function onePageNav( switchName ) {
            const navSwitch = $(switchName);
            const deductHeight = 60;
            let navArr = [];

            navSwitch.each(function(i){
                let navSwitchHref = $(this).attr('href');
                let tgtOff = $(navSwitchHref).offset().top - deductHeight;
                navArr.push([]);
                navArr[i].switch = $(this);
                navArr[i].tgtOff = tgtOff;
            });

            $(window).scroll(function () {
                for( let i = 0; i < navArr.length; i++ ){
                    let scroll = $(window).scrollTop();
                    let tgtKey = navArr[i];
                    let tgtSwitch = tgtKey.switch;
                    let tgtOff = tgtKey.tgtOff;
                    if ( scroll >= tgtOff ) {
                        navSwitch.removeClass('active');
                        tgtSwitch.addClass('active');
                    } else {
                        tgtSwitch.removeClass('active');
                    }
                }
            });
        }
        $(window).on('load resize',function(){
            onePageNav('.tf-details-menu-item');
        });

        var zoomLvl = 5;
        var zoomChangeEnabled = false;
        var centerLvl = new google.maps.LatLng(23.8697847, 90.4219536);
        var markersById = {};
        var markers = [];
        var mapChanged = false;
        var hotelMap;

        const googleMapInit = (mapLocations, mapLat = 23.8697847, mapLng = 90.4219536) => {
            // Clear existing markers
            clearMarkers();

            var locations = mapLocations ? JSON.parse(mapLocations) : [];

            if(!hotelMap){
                hotelMap = new google.maps.Map(document.getElementById("tf-hotel-archive-map"), {
                    zoom: zoomLvl,
                    minZoom: 3,
                    maxZoom: 18,
                    center: new google.maps.LatLng(mapLat, mapLng),
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        {elementType: 'labels.text.fill', stylers: [{color: '#44348F'}]},
                    ],
                    fullscreenControl: false
                });
            }

            var infowindow = new google.maps.InfoWindow({
                maxWidth: 262,
                disableAutoPan: true,
            });

            var bounds = new google.maps.LatLngBounds();
            locations.map(function (location, i) {
                var marker = new MarkerWithLabel({
                    position: new google.maps.LatLng(location['lat'], location['lng']),
                    map: hotelMap,
                    icon: {
                        url: document.getElementById('map-marker').dataset.marker,
                        scaledSize: new google.maps.Size(tf_params.map_marker_width, tf_params.map_marker_height),
                    },
                    labelContent: '<div class="tf_price_inner" data-post-id="' + location['id'] + '">' + window.atob(location['price']) + '</div>',
                    labelAnchor: new google.maps.Point(0, 0),
                    labelClass: "tf_map_price",
                });

                markersById[location['id']] = marker;
                markers.push(marker);
                bounds.extend(marker.position);

                // Define an OverlayView to use the projection for pixel calculation
                const overlay = new google.maps.OverlayView();
                overlay.draw = function () {};
                overlay.setMap(hotelMap);

                google.maps.event.addListener(marker, 'mouseover', function () {
                    infowindow.setContent(window.atob(location['content']));

                    // Convert LatLng to pixel coordinates
                    const markerPosition = marker.getPosition();
                    const markerProjection = overlay.getProjection();
                    const markerPixel = markerProjection.fromLatLngToDivPixel(markerPosition);

                    // Infowindow dimensions
                    const infoWindowHeight = 265;
                    const infoWindowWidth = 262;

                    // Check each edge
                    const isNearLeftEdge = markerPixel.x <= -120;
                    const isNearRightEdge = markerPixel.x >= 120;
                    const isNearTopEdge = (markerPixel.y - (infoWindowHeight+40)) <= -infoWindowHeight;

                    let anchorX = 0.5;
                    let anchorY = 0;

                    if (isNearLeftEdge) {
                        anchorX = 0.9;
                    } else if (isNearRightEdge) {
                        anchorX = 0.1;
                    }

                    if (isNearTopEdge) {
                        anchorY = infoWindowHeight+90
                    }

                    infowindow.setOptions({
                        pixelOffset: new google.maps.Size((anchorX - 0.5) * infoWindowWidth, anchorY)
                    });

                    infowindow.open(hotelMap, marker);
                });

                // Hide the infowindow on mouse leave
                google.maps.event.addListener(marker, 'mouseout', function () {
                    infowindow.close();
                });

                google.maps.event.addListener(marker, 'click', function () {
                    window.open(location?.url, '_blank')
                });
            });

            // Trigger filter on map drag
            google.maps.event.addListener(hotelMap, "dragend", function () {
                zoomLvl = hotelMap.getZoom();
                centerLvl = hotelMap.getCenter();
                mapChanged = true;

                filterVisibleHotels(hotelMap);
            });

            google.maps.event.addListener(hotelMap, "zoom_changed", function () {
                if (zoomChangeEnabled) return;

                zoomLvl = hotelMap.getZoom();
                centerLvl = hotelMap.getCenter();
                mapChanged = true;

                filterVisibleHotels(hotelMap);

            });

            var listener = google.maps.event.addListener(hotelMap, "idle", function() {
                zoomChangeEnabled = true;
                if (!mapChanged) {
                    hotelMap.fitBounds(bounds);
                    centerLvl = bounds.getCenter();
                    hotelMap.setCenter(centerLvl);

                } else {
                    hotelMap.setZoom(zoomLvl);
                    hotelMap.setCenter({lat: centerLvl.lat(), lng: centerLvl.lng()});
                    google.maps.event.removeListener(listener);
                }
                zoomChangeEnabled = false;
            });
        }

        function filterVisibleHotels(map) {
            var bounds = map.getBounds();

            if (bounds) {
                var sw = bounds.getSouthWest();
                var ne = bounds.getNorthEast();
            }

            makeFilter('', [sw.lat(), sw.lng(), ne.lat(), ne.lng()]);
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null)); // Remove each marker from the map
            markers = []; // Clear the array to prevent duplication
        }

        // GOOGLE MAP INITIALIZE
        var mapLocations = $('#map-datas').html();
        if ($('#map-datas').length && mapLocations.length) {
            googleMapInit(mapLocations);
        }

        /*
        * Hotel hover effect on map marker
        * */
        $(document).on('mouseover', '.tf-archive-template__three .tf-archive-hotel', function () {
            let id = $(this).data('id');
            $('.tf_map_price .tf_price_inner[data-post-id="' + id + '"]').addClass('active');

            if (markersById[id]) {
                markersById[id].setAnimation(google.maps.Animation.BOUNCE);
            }
        });
        $(document).on('mouseleave', '.tf-archive-template__three .tf-archive-hotel', function () {
            let id = $(this).data('id');
            $('.tf_map_price .tf_price_inner[data-post-id="' + id + '"]').removeClass('active');

            if (markersById[id]) {
                markersById[id].setAnimation(null);
            }
        });

        /*
        * Map toggle btn for mobile
        */
        $(document).on('click', '.tf-archive-template__three .tf-mobile-map-btn, .tf-archive-listing__three .tf-mobile-map-btn', function (e) {
            e.preventDefault();
            $('.tf-archive-template__three .tf-details-right').css('display', 'block');
            $('.tf-archive-listing__three .tf-details-right').css('display', 'block');
        });
        $(document).on('click', '.tf-archive-template__three .tf-mobile-list-btn, .tf-archive-listing__three .tf-mobile-list-btn', function (e) {
            e.preventDefault();
            $('.tf-archive-template__three .tf-details-right').css('display', 'none');
            $('.tf-archive-listing__three .tf-details-right').css('display', 'none');
        });

        /*
        * Map Popup
        * */
        $(document).on('click', '.tf-map-modal-btn', function (e) {
            e.preventDefault();

            //template 4
            if ($(".tf-archive-filter-sidebar").length > 0) {
                $(".tf-archive-filter-sidebar").removeClass('tf-show');
            }

            $.fancybox.open({
                src: '.tf-archive-details-wrap',
                type: 'inline',
                touch: false,
                afterClose: function () {
                    $('.tf_template_4_hotel_archive .tf-archive-details-wrap, .tf_template_4_tour_archive .tf-archive-details-wrap, .tf_template_4_apartment_archive .tf-archive-details-wrap').css('display', 'block');
                },
                afterShow: function(instance, current) {
                    // Add a class to the parent fancybox container
                    instance.$refs.container.addClass('tf-archive-details-fancy');

                    if($('.tf-archive-details-wrap .tf-archive-hotels').length) {
                        adjustPadding();
                        $(window).on('resize', adjustPadding);
                    }

                    if($('#tf_map_popup_sidebar').length) {
                        adjustSidebarPadding();
                        $(window).on('resize', adjustSidebarPadding);
                    }
                }
            });
        });


         /*
        * Car Search Form Pickup & Dropoff Time
        * @author Mofazzal Hossain
        */

        // Open time options
        $('body').on('click' , '.selected-pickup-time, .selected-dropoff-time', function () {
            const $infoSelect = $(this).closest('.info-select');
            const $dropdown = $infoSelect.find('.time-options-list');
            const isOpen = $dropdown.is(':visible');

            $('.time-options-list').slideUp(200);
            $('.selected-dropoff-time, .selected-pickup-time').removeClass('active');

            if (!isOpen) {
                $dropdown.slideDown(200);
                $(this).addClass('active');
            }
        });

        // Select time
        $('.tf-pickup-time li, .tf-dropoff-time li').on('click', function () {
            const selected = $(this).attr('value');
            const $infoSelect = $(this).closest('.info-select');

            if ($(this).closest('ul').hasClass('tf-pickup-time')) {
                $('.tf_pickup_time').val(selected);
                $('.tf_dropoff_time').val(selected);
                $('.selected-pickup-time .text').text(selected);
                $('.selected-dropoff-time .text').text(selected);
            } else {
                $('.tf_dropoff_time').val(selected);
                $('.selected-dropoff-time .text').text(selected);
            }

            $('.time-options-list').slideUp(200);
            $('.selected-dropoff-time, .selected-pickup-time').removeClass('active');
        });

        // Click outside to close dropdown
        $('body').on('click', function (e) {
            if (!$(e.target).closest('.info-select').length) {
                $('.time-options-list').slideUp(200);
                $('.selected-dropoff-time, .selected-pickup-time').removeClass('active');
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


/**
 * Update Max Width of Search Field
 */
function updateMaxWidth(inputField) {
    let inputValue = inputField.val().trim();
    let characterCount = inputValue.length;

    // Get the window width
    let windowWidth = jQuery(window).width();
    
    // Adjust max width based on window width
    let newMaxWidth;
    if (windowWidth < 1025) {
        newMaxWidth = 100 + (Math.max(characterCount - 1, 0) * 20); // Mobile: 100px + 20px per character
    } else {
        newMaxWidth = 132 + (Math.max(characterCount - 1, 0) * 40); // Desktop: 132px + 40px per character
    }

    // Apply the new max-width
    inputField.closest(".tf-search__form__field.tf-mx-width").css("max-width", newMaxWidth + "px");
}

// Input change
jQuery(".tf-search__form__field__input").on("input", function() {
    updateMaxWidth(jQuery(this));
});

// Increment button
jQuery(".acr-inc").on("click", function() {
    let inputField = jQuery(".tf-search__form__field__input");
    inputField.trigger("input");
});

// Decrement button
jQuery(".acr-dec").on("click", function() {
    let inputField = jQuery(".tf-search__form__field__input");
    inputField.trigger("input");
});