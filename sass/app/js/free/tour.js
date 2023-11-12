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

                    if (response.without_payment == 'false') {
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
                    } else {
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
        var tf_search_page_params = new window.URLSearchParams(window.location.search);
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
        if ($('.tf-tour-booking-box').length > 0) {
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
    });

})(jQuery, window);