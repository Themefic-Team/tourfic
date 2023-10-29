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

        // Apartment Min and Max Range in Search Result
        var tf_search_page_params = new window.URLSearchParams(window.location.search);
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
    });

})(jQuery, window);