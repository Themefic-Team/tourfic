// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
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

        // Apartment Location Autocomplete
        function tourfic_autocomplete(inp, arr) {
            var currentFocus;
        
            // Show autocomplete suggestions on focus
            inp.addEventListener("focus", function () {
                closeAllLists();
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
                a.classList.add("autocomplete-items");
                this.parentNode.appendChild(a);
        
                for (const [key, value] of Object.entries(arr)) {
                    let b = document.createElement("DIV");
                    b.innerHTML = value;
                    b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                    b.addEventListener("click", function () {
                        let source = this.getElementsByTagName("input")[0];
                        inp.value = source.value;
                        inp.closest('input').nextElementSibling.value = source.dataset.slug;
                        setTimeout(() => {
                            closeAllLists();
                        },100);
                    });
                    a.appendChild(b);
                }
            });
        
            // Filter suggestions on keyup
            inp.addEventListener("keyup", function (e) {
                var val = this.value.toLowerCase();
                closeAllLists();
                currentFocus = -1;
                
                if (!val) return false;
        
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
        
                var found = false;
                for (const [key, value] of Object.entries(arr)) {
                    if (value.toLowerCase().startsWith(val)) {
                        found = true;
                        let b = document.createElement("DIV");
                        b.innerHTML = `<strong>${value.substr(0, val.length)}</strong>${value.substr(val.length)}`;
                        b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                        b.addEventListener("click", function (e) {
                            let source = this.getElementsByTagName("input")[0];
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug;
                
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
        
                // If no match found, show "No results found"
                if (!found) {
                    let b = document.createElement("DIV");
                    b.innerHTML = `<span>${tf_params.no_found}</span>`;
                    b.innerHTML += `<input type='hidden' value="">`;
                    b.addEventListener("click", function () {
                        inp.value = "";
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            });
        
            // Handle keyboard navigation
            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "-autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
        
                if (e.keyCode == 40) {
                    // Arrow DOWN
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    // Arrow UP
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    // ENTER key
                    e.preventDefault();
                    if (currentFocus > -1 && x) {
                        x[currentFocus].click();
                    }
                }
            });
        
            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = x.length - 1;
                x[currentFocus].classList.add("autocomplete-active");
            }
        
            function removeActive(x) {
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }
        
            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt !== x[i] && elmnt !== inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
        
            // Close when clicking outside
            $(document).on('click', function (event) {
                if (!$(event.target).closest("#tf-apartment-location").length) {
                    $("#tf-apartment-location-autocomplete-list").hide();
                }
            });
        }
        
        jQuery('.tf-details-qc-slider-single').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: false,
            adaptiveHeight: true,
            infinite: true,
            useTransform: true,
            speed: 400,
            cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
        });

        jQuery('.tf-details-qc-slider-nav')
            .on('init', function (event, slick) {
                jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
            })
            .slick({
                slidesToShow: 7,
                slidesToScroll: 7,
                dots: false,
                focusOnSelect: false,
                infinite: false,
                responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5,
                    }
                }, {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                }, {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                }]
            });

        jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
            jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
            var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
            jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
            jQuery(currrentNavSlideElem).addClass('is-active');
        });

        jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
            event.preventDefault();
            var goToSingleSlide = jQuery(this).data('slick-index');

            jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
        });

        $(".tf_apartment_check_in_out_date").on("click", function(){
            $(".tf-apartment-check-in-out-date").trigger("click");
        });
        $(".tf-apartment-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFields(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFields(selectedDates, instance);
            },
        });

        function dateSetToFields(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if(selectedDates[0]){
                    const startDate = selectedDates[0];
                    $(".tf_apartment_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf_apartment_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                }
                if(selectedDates[1]){
                    const endDate = selectedDates[1];
                    $(".tf_apartment_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf_apartment_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $(".tf_apt_check_in_out_date").on("click", function() {
            $(".tf-apt-check-in-out-date").trigger("click");
        });
        $(".tf-apt-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            onReady: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            },
            onChange: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            }
        });

        function dateSetToFieldsTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
                    $(".tf_checkin_dates span.year").html(startDate.getFullYear());
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
                    $(".tf_checkout_dates span.year").html(endDate.getFullYear());
                }
            }
        }

        $("#tf_apartment_booking #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            minDate: "today",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            }
        });

        const regexMap = {
            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
        };
        const dateRegex = regexMap[tf_params.date_format_for_users];

        $("#tf_apartment_booking #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            minDate: "today",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace(dateRegex, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            }
        });

        if ($('#apartment-location').length) {
            const map = L.map('apartment-location').setView([tf_params.single_apartment_data.address_latitude, tf_params.single_apartment_data.address_longitude], tf_params.single_apartment_data.address_zoom);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.single_apartment_data.address_latitude, tf_params.single_apartment_data.address_longitude], {alt: tf_params.single_apartment_data.address}).addTo(map)
                .bindPopup(tf_params.single_apartment_data.address);
        }

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


        /*
        * Apartment Search submit
        * @since 2.9.7
        * @author Jahid
        */
        $(document).on('submit', '#tf_apartment_booking', function (e) {
            e.preventDefault();
            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                formData = new FormData(form[0]);

            formData.append('action', 'tf_apartments_search');
            formData.append('_nonce', tf_params.nonce);

            if (formData.get('from') == null || formData.get('to') == null) {
                formData.append('from', tf_params.tf_apartment_min_price);
                formData.append('to', tf_params.tf_apartment_max_price);
            }

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
        * Apartment room quick view
        * */
        $(document).on('click', '.tf-apt-room-qv', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            let post_id = $(this).data("post-id");
            let id = $(this).data("id");
            let data = {
                action: 'tf_apt_room_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                id: id
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $("#tf_apt_room_details_qv").html(response);

                    $("#tour_room_details_loader").hide();
                    $.fancybox.open({
                        src: '#tf_apt_room_details_qv',
                        type: 'inline',
                    });
                }

            });
        });

        /**
         * Design 1 Apartment Room Popup
         *
         */
        $(document).on('click', '.tf-apt-room-qv-desgin-1', function (e) {

            e.preventDefault();
            $("#tour_room_details_loader").show();
            let post_id = $(this).data("post-id");
            let id = $(this).data("id");
            let data = {
                action: 'tf_apt_room_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                id: id
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

        // Design Default Room Slider

        $('.tf-apartment-default-design-room-slider').slick({
            arrows: true,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow:"<button type='button' class='slick-prev'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\">\n" +
                "  <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M16.2071 5.29289C16.5976 5.68342 16.5976 6.31658 16.2071 6.70711L10.9142 12L16.2071 17.2929C16.5976 17.6834 16.5976 18.3166 16.2071 18.7071C15.8166 19.0976 15.1834 19.0976 14.7929 18.7071L8.79289 12.7071C8.40237 12.3166 8.40237 11.6834 8.79289 11.2929L14.7929 5.29289C15.1834 4.90237 15.8166 4.90237 16.2071 5.29289Z\" fill=\"#2A3343\"/>\n" +
                "</svg></button>",
            nextArrow:"<button type='button' class='slick-next'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\">\n" +
                "  <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M8.79289 5.29289C9.18342 4.90237 9.81658 4.90237 10.2071 5.29289L16.2071 11.2929C16.5976 11.6834 16.5976 12.3166 16.2071 12.7071L10.2071 18.7071C9.81658 19.0976 9.18342 19.0976 8.79289 18.7071C8.40237 18.3166 8.40237 17.6834 8.79289 17.2929L14.0858 12L8.79289 6.70711C8.40237 6.31658 8.40237 5.68342 8.79289 5.29289Z\" fill=\"#2A3343\"/>\n" +
                "</svg></button>",

            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
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

        /**
         * Apartment 2 design Highlights sliers
         *
         * Slick
         */
        $('.tf-features-block-slides').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 2000,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
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

        $(document).on('click', '.tf-apartment-show-more', function (e) {
            if ($(this).siblings('.tf-full-description')) {
                $(this).siblings('.tf-full-description').show();
                $(this).siblings('.tf-description').hide();
                $(this).text("Show Less");
                $(this).addClass('tf-apartment-show-less');
            }
        });
        
        $(document).on('click', '.tf-apartment-show-less', function (e) {
            if ($(this).siblings('.tf-full-description')) {
                $(this).siblings('.tf-full-description').hide();
                $(this).siblings('.tf-description').show();
                $(this).text("Show More");
                $(this).removeClass('tf-apartment-show-less');
            }
        });
        
        $('.tf-single-review.tf_apartment .tf-single-details').each(function (index, val) {
            if (index > 1) {
                $(this).hide();
            }
        });

        $(".tf-apaartment-show-all").on('click', function (e) { 
            $('.tf-single-review.tf_apartment .tf-single-details').each(function (index, val) {
                $(val).show();
            });

            $('.show-all-review-wrap').hide();
        });

    });

})(jQuery, window);
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
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

        $(".tf-car-search-dropoff-date").on("click", function() {
            $(".tf-car-search-pickup-date").trigger("click");
        });
        var pickupFlatpickr = $(".tf-car-search-pickup-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFields(selectedDates, instance);
            },

            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFields(selectedDates, instance);
            },
        });

        function dateSetToFields(selectedDates, instance) {
            const monthNames = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];
            if (selectedDates[0]) {
                const startDate = selectedDates[0];
                $(".tf-car-search-pickup-date span.date").html(startDate.getDate());
                $(".tf-car-search-pickup-date span.month span").html(monthNames[startDate.getMonth()]);
                $('.tf_search_pickup_date').val(flatpickr.formatDate(startDate, "Y/m/d"));
            }
            if (selectedDates[1]) {
                const endDate = selectedDates[1];
                $(".tf-car-search-dropoff-date span.date").html(endDate.getDate());
                $(".tf-car-search-dropoff-date span.month span").html(monthNames[endDate.getMonth()]);
                $(".tf_search_dropoff_date").val(flatpickr.formatDate(endDate, "Y/m/d"));
            }
        }

        $(".tf_dropoff_date").on("click", function () {
            $(".tf_pickup_date").trigger("click");
        });
        $(".tf_pickup_date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFieldsTwo(selectedDates, instance);
            },

            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFieldsTwo(selectedDates, instance);
            },
        });

        function dateSetToFieldsTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                if (selectedDates[0]) {
                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                    $(".tf_pickup_date").val(startDate);
                }
                if (selectedDates[1]) {
                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                    $(".tf-select-date .tf_dropoff_date").val(endDate);
                }
            }
        }

        $(".tf-shortcode-design-4 .tf_dropoff_date").on("click", function() {
            $(".tf-shortcode-design-4 .tf_pickup_date").trigger("click");
        });

        var pickupFlatpickr = $(".tf-shortcode-design-4 .tf_pickup_date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFieldsFour(selectedDates, instance);
            },

            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFieldsFour(selectedDates, instance);
            },
        });

        function dateSetToFieldsFour(selectedDates, instance) {
            const monthNames = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];
            if (selectedDates[0]) {
                const startDate = selectedDates[0];
                $(".tf-shortcode-design-4 .tf_pickup_date .date").html(startDate.getDate());
                $(".tf-shortcode-design-4 .tf_pickup_date .month").html(monthNames[startDate.getMonth()]);
                $(".tf-shortcode-design-4 .tf_pickup_date .year").html(startDate.getFullYear());
                $(".tf-shortcode-design-4 .tf_pickup_date_input").val(flatpickr.formatDate(startDate, "Y/m/d"));
            }
            if (selectedDates[1]) {
                const endDate = selectedDates[1];
                $(".tf-shortcode-design-4 .tf_dropoff_date .date").html(endDate.getDate());
                $(".tf-shortcode-design-4 .tf_dropoff_date .month").html(monthNames[endDate.getMonth()]);
                $(".tf-shortcode-design-4 .tf_dropoff_date .year").html(endDate.getFullYear());
                $(".tf-shortcode-design-4 .tf_dropoff_date_input").val(flatpickr.formatDate(endDate, "Y/m/d"));
            }
        }

        $(".tf_booking-widget .tf_dropoff_date").on("click", function () {
            $(".tf_booking-widget .tf_pickup_date").trigger("click");
        });
        // Initialize the pickup date picker
        var pickupFlatpickr = $(".tf_booking-widget .tf_pickup_date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFieldsFive(selectedDates, instance);
            },

            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFieldsFive(selectedDates, instance);
            },
        });

        function dateSetToFieldsFive(selectedDates, instance) {
            if (selectedDates.length === 2) {
                if (selectedDates[0]) {
                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                    $("#tf-car-booking-form .tf_pickup_date").val(startDate);
                }
                if (selectedDates[1]) {
                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                    $("#tf-car-booking-form .tf_dropoff_date").val(endDate);
                }
            }
        }

        $(".tf-single-template__one #tf_dropoff_date").on("click", function () {
            $(".tf-single-template__one #tf_pickup_date").trigger("click");
        });
        $(".tf-single-template__one #tf_pickup_date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFieldsSingle(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFieldsSingle(selectedDates, instance);
            },
        });

        function dateSetToFieldsSingle(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const startDay = flatpickr.formatDate(selectedDates[0], "l");
                const endDay = flatpickr.formatDate(selectedDates[1], "l");
                if (selectedDates[0]) {
                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                    $(".tf-single-template__one #tf_pickup_date").val(startDate);
                }
                if (selectedDates[1]) {
                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                    $(".tf-single-template__one #tf_dropoff_date").val(endDate);
                }

                $.ajax({
                    url: tf_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_car_time_slots',
                        pickup_day: startDay,
                        drop_day: endDay
                    },
                    success: function(response) {
                    }
                });
            }
        }

        if ($('#car-location').length) {
            const map = L.map('car-location').setView([tf_params.single_car_data.address_latitude, tf_params.single_car_data.address_longitude], tf_params.single_car_data.address_zoom);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.single_car_data.address_latitude, tf_params.single_car_data.address_longitude], {alt: tf_params.single_car_data.address}).addTo(map)
                .bindPopup(tf_params.single_car_data.address);
        }
        
        // FAQ Accordion
        $('.tf-car-faq-section .tf-faq-head').on("click", function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-question-desc").slideUp(400);
                $(".tf-faq-head").removeClass("active");
                $('.tf-faq-col').removeClass('active');
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
            $(this).closest('.tf-faq-col').toggleClass('active');
        });

        // Tabs Section
        $('.tf-details-menu ul li').on("click", function () {
            var $this = $(this);
            $currentmenu = $this.attr('data-menu');
            $('.tf-details-menu ul li').removeClass('active');

            $('.tf-details-menu ul li[data-menu="' + $currentmenu + '"]').addClass('active');
        });

       
        
        
        
        // Car Location Autocomplete

        function tourfic_car_autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/

            // Executes when some one click in the search form location
            inp.addEventListener("focus", function () {
                    closeAllLists();
                    let a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "-autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    this.parentNode.appendChild(a);
                    for (const [key, value] of Object.entries(arr)) {
                        let b = document.createElement("DIV");
                        b.innerHTML = value;
                        b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                        b.addEventListener("click", function (e) {
                            let source = this.getElementsByTagName("input")[0];
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug;
                            setTimeout(() => {
                                closeAllLists();
                            },100);
                        });
                        a.appendChild(b);
                    }
                // }
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
                        if (x) x[currentFocus].trigger("click");;
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
            $(document).on('click', function (event) {
                if (!$(event.target).closest("#tf_dropoff_location, #tf_pickup_location").length) {
                    $("#tf_pickup_location-autocomplete-list,#tf_dropoff_location-autocomplete-list").hide();
                }
            });
        }

        // Car location autocomplete
        var car_pickup_input = document.getElementById("tf_pickup_location");
        var car_locations = tf_params.car_locations;
        if (car_pickup_input) {
            tourfic_car_autocomplete(car_pickup_input, car_locations);
        }

        var car_dropoff_input = document.getElementById("tf_dropoff_location");
        if (car_dropoff_input) {
            tourfic_car_autocomplete(car_dropoff_input, car_locations);
        }
        
        $(".tf-booking-popup-header .tf-close-popup").on("click", function (e) {
            e.preventDefault();
            $('.tf-car-booking-popup').hide();
        });

       


        /*
        * Car Booking Popup
        * @author Jahid
        */
        $(".tf-car-booking").on('click', function (e) {
            e.preventDefault();
            $this = $(this);

            $('.tf-booking-content-wraper').html("");
            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            pickup_time = convertTo24HourFormat(pickup_time);
            dropoff_time = convertTo24HourFormat(dropoff_time);

            let post_id = $('#post_id').val();

            if( !pickup || !dropoff || !pickup_date || !dropoff_date || !pickup_time || !dropoff_time ){
                $('.error-notice').show();
                $('.error-notice').text('Fill up the all fields');
                return;
            }

            if($this.attr('data-partial')){
                $('#tf_partial_payment').val($this.attr('data-partial'));
            }

            var data = {
                action: 'tf_car_booking_pupup',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup_date: pickup_date,
                pickup_time: pickup_time,
                dropoff_date: dropoff_date,
                dropoff_time: dropoff_time
            };

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $this.addClass('tf-btn-loading');
                },
                success: function (data) {
                    $('.tf-booking-content-wraper').html(data);
                    $('.error-notice').hide();
                    $('.tf-car-booking-popup').css('display', 'flex');
                    $this.removeClass('tf-btn-loading');
                    if($(window).width() < 768){
                        $(".tf-date-select-box").hide();
                        $(".tf-mobile-booking-btn").hide();
                    }
                }
            });

        });

        /*
        * Car Archive Booking Popup
        * @author Jahid
        */
        $('body').on('click', '.tf-car-quick-booking', function (e) {
            e.preventDefault();
            $this = $(this);
            $('.tf-booking-content-wraper').html("");
            let post_id = $this.closest('.tf-booking-btn').find('#post_id').val();
            let pickup_date = $this.closest('.tf-booking-btn').find('#pickup_date').val();
            let dropoff_date = $this.closest('.tf-booking-btn').find('#dropoff_date').val();
            let pickup_time = $this.closest('.tf-booking-btn').find('#pickup_time').val();
            let dropoff_time = $this.closest('.tf-booking-btn').find('#dropoff_time').val();

            var data = {
                action: 'tf_car_booking_pupup',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup_date: pickup_date,
                pickup_time: pickup_time,
                dropoff_date: dropoff_date,
                dropoff_time: dropoff_time
            };

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $this.addClass('tf-btn-loading');
                },
                success: function (data) {
                    $this.closest('.tf-booking-btn').find('.tf-booking-content-wraper').html(data);
                    $this.closest('.tf-booking-btn').find('.tf-car-booking-popup').css('display', 'flex');
                    $this.removeClass('tf-btn-loading');
                }
            });

        });

        $(document).on('click touchstart', '.tf-booking-content-wraper .booking-next', function (e) {
            e.preventDefault();
            let $this = $(this);

            let protections = $('input[name="protections[]"]');


            let validationProtections = protectionValidation(protections);

            if( validationProtections ){
                return;
            }else{
                $('.tf-booking-tabs ul li').removeClass('active');
                $('.tf-booking-tabs ul li.booking').addClass('active');

                $('.tf-protection-content').hide();
                $('.tf-booking-bar').hide();

                $('.tf-booking-form-fields').show();
            }
        });

        /*
        * Car Booking
        * @author Jahid
        */

        const BookingVallidation = (booking) => {
            let hasErrors = [];
            
            $('.error-text').text("");
            booking.find('.tf-single-field').each(function () {
                $(this).find('input, select').each(function () {
                    if ($(this).attr('data-required')) {
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
                return true;
            }
        }

        const protectionValidation = (protections) => {

            var attrCount = 0;
            var response = [];

            $(protections).each(function() {
                attrCount += Array.from(this.attributes).filter( function(a) {
                    return a.nodeName.startsWith('data-required');
                }).length
            })
            
            protections.each(function (i, protection) {

                if ( $(protection).data("required") ) {
                    
                    if (! $(protection).is(':checked')) {
                        response.push(true);
                    } else {
                       response.push(false);
                    }
                }
            });

            if( response.includes(true) ){
                if( attrCount > 1 ){
                    notyf.error('( * ) fields are required');
                } else {
                    notyf.error('( * ) field is required');
                }
                return true;
            }
        };
        $(document).on('click touchstart', '.booking-process', function (e) {
            
            if (e.type === 'touchstart') {
                $(this).off('click');
            }
            let $this = $(this);
            
            let extra_ids = $("input[name='selected_extra[]']").map(function() {
                return $(this).val();
            }).get();

            let extra_qty = $("input[name='selected_qty[]']").map(function() {
                return $(this).val();
            }).get();

            var travellerData = {};
            if($this.hasClass('tf-offline-booking')){
                let booking = $(this).closest('.tf-booking-form-fields');
                let Validation_response = BookingVallidation(booking);
                if(Validation_response){
                    return;
                }
                // Text, email, date inputs
                $("input[name^='traveller[']").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    travellerData[name] = $(this).val();
                });

                // Select dropdowns
                $("select[name^='traveller[']").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    travellerData[name] = $(this).val();
                });

                // Checkbox and Radio buttons
                $("input[type='checkbox'][name^='traveller[']:checked, input[type='radio'][name^='traveller[']:checked").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    if (!travellerData[name]) {
                        travellerData[name] = [];
                    }
                    travellerData[name].push($(this).val());
                });

            }
    
            if($this.hasClass('tf-final-step')){
                var pickup = $('#tf_pickup_location').val();
                let dropoff = $('#tf_dropoff_location').val();
                let pickup_date = $('.tf_pickup_date').val();
                let dropoff_date = $('.tf_dropoff_date').val();
                let pickup_time = $('.tf_pickup_time').val();
                let dropoff_time = $('.tf_dropoff_time').val();

                pickup_time = convertTo24HourFormat(pickup_time);
                dropoff_time = convertTo24HourFormat(dropoff_time);

                if( !pickup || !dropoff || !pickup_date || !dropoff_date || !pickup_time || !dropoff_time ){
                    $('.error-notice').show();
                    $('.error-notice').text('Fill up the all fields');
                    return;
                }
            }

            $('.error-notice').hide();

            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            pickup_time = convertTo24HourFormat(pickup_time);
            dropoff_time = convertTo24HourFormat(dropoff_time);

            let post_id = $('#post_id').val();
            var protection = $('input[name="protections[]"]:checked').map(function() {
                return $(this).val();  // Get the value of each checked checkbox
            }).get();

            let protections = $('input[name="protections[]"]');

            
            let validationProtections = protectionValidation(protections);

            if( validationProtections ){
                return;
            }

            let partial_payment = $('#tf_partial_payment').val();

            var data = {
                action: 'tf_car_booking',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup: pickup,
                dropoff: dropoff,
                pickup_date: pickup_date,
                dropoff_date: dropoff_date,
                pickup_time: pickup_time,
                dropoff_time: dropoff_time,
                protection: protection,
                partial_payment: partial_payment,
                extra_ids: extra_ids,
                extra_qty: extra_qty,
                travellerData: travellerData
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

                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                notyf.error(text);
                            });
                        }

                        $('.tf-car-booking-popup').hide();
                        $this.removeClass('tf-btn-loading');
                        if($('.tf-protection-content')){
                            $('.tf-protection-content').show();
                            $('.tf-booking-bar').show();
                            $('.tf-booking-form-fields').hide();

                            $('.tf-booking-tabs ul li').removeClass('active');
                            $('.tf-booking-tabs ul li.protection').addClass('active');
                        }else{
                            $('.tf-booking-form-fields').show();
                            $('.tf-booking-tabs ul li').removeClass('active');
                            $('.tf-booking-tabs ul li.booking').addClass('active');
                        }
                        return false;
                    } else {
                        if (response.without_payment == 'false') {
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
                        }else{
                            $('.tf-car-booking-popup').hide();
                            $('.tf-withoutpayment-booking-confirm').addClass('show');
                            $this.removeClass('tf-btn-loading');
                            $('#tf_pickup_location').val('');
                            $('#tf_dropoff_location').val('');
                            $('.tf_pickup_date').val('');
                            $('.tf_dropoff_date').val('');
                            $('.tf_pickup_time').val('');
                            $('.tf_dropoff_time').val('');
                            if($('.tf-protection-content')){
                                $('.tf-protection-content').show();
                                $('.tf-booking-bar').show();
                                $('.tf-booking-form-fields').hide();

                                $('.tf-booking-tabs ul li').removeClass('active');
                                $('.tf-booking-tabs ul li.protection').addClass('active');
                            }else{
                                $('.tf-booking-form-fields').show();
                                $('.tf-booking-tabs ul li').removeClass('active');
                                $('.tf-booking-tabs ul li.booking').addClass('active');
                            }
                        }
                    }
                }
            });

        });

        /*
        * Car Search submit
        * @author Jahid
        */
        $(document).on('submit', '#tf_car_booking', function (e) {
            e.preventDefault();
            let form = $(this),
                submitBtn = form.find('.tf-submit'),
                formData = new FormData(form[0]);
            formData.append('action', 'tf_car_search');
            formData.append('_nonce', tf_params.nonce);

            if (formData.get('from') == null || formData.get('to') == null) {
                formData.append('from', tf_params.tf_car_min_price);
                formData.append('to', tf_params.tf_car_max_price);
            }

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
        * Car Quick Booking
        * @author Jahid
        */

        $(".quick-booking").on('click', function (e) {
            let $this = $(this);

            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let pickup_date = $this.closest('.tf-booking-btn').find('#pickup_date').val();
            let dropoff_date = $this.closest('.tf-booking-btn').find('#dropoff_date').val();
            let pickup_time = $this.closest('.tf-booking-btn').find('#pickup_time').val();
            let dropoff_time = $this.closest('.tf-booking-btn').find('#dropoff_time').val();
            let post_id = $this.closest('.tf-booking-btn').find('#post_id').val();

            var data = {
                action: 'tf_car_booking',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup: pickup,
                dropoff: dropoff,
                pickup_date: pickup_date,
                dropoff_date: dropoff_date,
                pickup_time: pickup_time,
                dropoff_time: dropoff_time
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
                    if (response.without_payment == 'false') {
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
                    }
                }
            });

        });

        $(".tf-booking-btn .booking-process").on("click touchstart", function (e) {
            if(e.type === 'touchstart'){
                $(this).off('click');
            }
            let $this = $(this);

            var travellerData = {};
            if($this.hasClass('tf-offline-booking')){
                let booking = $(this).closest('.tf-booking-form-fields');
                let Validation_response = BookingVallidation(booking);
                if(Validation_response){
                    return;
                }
                // Text, email, date inputs
                $("input[name^='traveller[']").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    travellerData[name] = $(this).val();
                });

                // Select dropdowns
                $("select[name^='traveller[']").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    travellerData[name] = $(this).val();
                });

                // Checkbox and Radio buttons
                $("input[type='checkbox'][name^='traveller[']:checked, input[type='radio'][name^='traveller[']:checked").each(function() {
                    var name = $(this).attr('name').replace(/^traveller\[(.*)\]$/, '$1'); // Remove 'traveller_' prefix
                    if (!travellerData[name]) {
                        travellerData[name] = [];
                    }
                    travellerData[name].push($(this).val());
                });
            }
    
            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let partial_payment = $('#tf_partial_payment').val();
            let pickup_date = $this.closest('.tf-booking-btn').find('#pickup_date').val();
            let dropoff_date = $this.closest('.tf-booking-btn').find('#dropoff_date').val();
            let pickup_time = $this.closest('.tf-booking-btn').find('#pickup_time').val();
            let dropoff_time = $this.closest('.tf-booking-btn').find('#dropoff_time').val();
            let post_id = $this.closest('.tf-booking-btn').find('#post_id').val();

            var protection = $('input[name="protections[]"]:checked').map(function() {
                return $(this).val();  // Get the value of each checked checkbox
            }).get();

            let protections = $('input[name="protections[]"]');
            
            let validationProtections = protectionValidation(protections);

            if( validationProtections ){
                return;
            }

            var data = {
                action: 'tf_car_booking',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup: pickup,
                dropoff: dropoff,
                pickup_date: pickup_date,
                dropoff_date: dropoff_date,
                pickup_time: pickup_time,
                dropoff_time: dropoff_time,
                protection: protection,
                partial_payment: partial_payment,
                travellerData: travellerData
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

                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                notyf.error(text);
                            });
                        }

                        $('.tf-car-booking-popup').hide();
                        $this.removeClass('tf-btn-loading');
                        return false;
                    } else {
                        if (response.without_payment == 'false') {
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
                        }else{
                            $('.tf-car-booking-popup').hide();
                            $('.tf-withoutpayment-booking-confirm').addClass('show');
                            $this.removeClass('tf-btn-loading');
                        }
                    }
                }
            });

        });


        /*
        * Car Single Price Calculation
        * @author Jahid
        */
        $('body').on('change', '.tf-car-booking-form .tf_pickup_date, .tf-car-booking-form .tf_dropoff_date', function (e) {
            handleBookingInputChange();
        });

        $('body').on('click', '.tf-car-booking-form .tf-pickup-time li, .tf-car-booking-form .tf-dropoff-time li', function (e) {
            handleBookingInputChange();
        });

        function handleBookingInputChange() {
            let extra_ids = $("input[name='selected_extra[]']").map(function() {
                return $(this).val();
            }).get();

            let extra_qty = $("input[name='selected_qty[]']").map(function() {
                return $(this).val();
            }).get();

            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            pickup_time = convertTo24HourFormat(pickup_time);
            dropoff_time = convertTo24HourFormat(dropoff_time);

            let post_id = $('#post_id').val();

            if( !pickup_date || !dropoff_date || !pickup_time || !dropoff_time ){
                return;
            }
            var data = {
                action: 'tf_car_price_calculation',
                _nonce: tf_params.nonce,
                post_id: post_id,
                pickup_date: pickup_date,
                dropoff_date: dropoff_date,
                pickup_time: pickup_time,
                dropoff_time: dropoff_time,
                extra_ids: extra_ids,
                extra_qty: extra_qty,
            };

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $('.tf-date-select-box').addClass('tf-box-loading');
                },
                success: function (response) {
                    $('.tf-cancellation-box').html('');
                    $('.tf-cancellation-box').hide();
                    if(response){
                        if(response.data.total_price){
                            $('.tf-price-header h2').html(response.data.total_price);
                        }
                        if(response.data.cancellation){
                            $('.tf-cancellation-box').html(response.data.cancellation);
                            $('.tf-cancellation-box').show();
                        }
                        $('.tf-date-select-box').removeClass('tf-box-loading');
                    }
                }
            });
        };


        /*
        * Car Archive View
        * @author Jahid
        */
         $(document).on('click', '.tf-archive-header .tf-archive-view ul li', function (e) {
            $('.tf-archive-header .tf-archive-view ul li').removeClass('active');
            let $this = $(this);
            $this.addClass('active');
            let view = $this.attr('data-view');
            if(view=='grid'){
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').removeClass('list-view');
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').addClass('grid-view');
            }else{
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').addClass('list-view');
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').removeClass('grid-view');
            }
        });

        if($('.tf-single-car-details-warper .tf-details-menu').length){
            // Booking Bar Show
            $(window).scroll(function() {
                // Check the position of the target div
                var targetOffset = $('.tf-single-car-details-warper .tf-details-menu').offset().top;
                var targetHeight = $('.tf-single-car-details-warper .tf-details-menu').outerHeight(); // Get the full height of the div including padding
                var targetBottom = targetOffset + targetHeight;

                var scrollPosition = $(window).scrollTop();
        
                // If the user has scrolled past the target div, show the other div
                if (scrollPosition > targetBottom) {
                    $('.tf-single-booking-bar').fadeIn(); // You can change this to show() or add animations
                } else {
                    $('.tf-single-booking-bar').fadeOut();
                }
            });
        }

        // Back to Booking Form
        $(document).on('click', '.tf-back-to-booking', function (e) {
            e.preventDefault(); 
            $('.tf-single-booking-bar').fadeOut();
            var bookingBarHeight = $('.tf-single-booking-bar').outerHeight() || 0;
            $('html, body').animate({
                scrollTop: $('.tf-date-select-box').offset().top - bookingBarHeight
            }); 
        });        

        // Social Share
        $('.single-tf_carrental .tf-single-template__one .tf-share-toggle').on("click", function (e) {
            e.preventDefault();
            $('.tf-share-toggle').toggleClass('actives');
            $('.share-car-content').toggleClass('show');
        });

        // Instructions showing
        $(document).on('click', '.tf-instraction-showing', function (e) {
            $('.tf-car-instraction-popup').css('display', 'flex');
        });   

        // Instructions Popup Close
        $(".tf-instraction-popup-header .tf-close-popup").on("click touchstart", function (e) {
            e.preventDefault();
            $('.tf-car-instraction-popup').hide();
        });

        $(".tf-confirm-popup .tf-booking-times").on("click touchstart", function (e) {
            e.preventDefault();
            $('.tf-withoutpayment-booking-confirm').removeClass('show');
        })

        // Showing Total into a protections
        $(document).on('change', '.protection-checkbox', function (e) {
            let total_price = 0;
            let prev_total = parseFloat($("#tf_total_proteciton_price").val()) || 0; // Parse as float, default to 0 if empty
            let single_price = parseFloat($(this).parent().parent().find('#tf_single_protection_price').val()) || 0; // Parse as float
        
            if ($(this).is(':checked')) {
                total_price = prev_total + single_price;
            } else {
                total_price = prev_total - single_price;
            }
        
            // Update total and display it
            $('#tf_total_proteciton_price').val(total_price.toFixed(2)); // Format as float with 2 decimal places
            $('#tf_proteciton_subtotal').text(total_price.toFixed(2)); // Display formatted total
        });

        /*
        * Mobile Booking button
        * @author Jahid
        */
        $(".tf-mobile-booking-btn button").on("click", function (e) {
            e.preventDefault();
            var $button = $(this);

            // Toggle the visibility of .tf-date-select-box
            $('.tf-date-select-box').slideToggle( function () {
                // Check visibility after the toggle animation completes
                if ($(this).is(':visible')) {
                    $button.text('Hide');
                } else {
                    $button.text('Book Now');
                }
            });
        });

    
    });

})(jQuery, window);


function convertTo24HourFormat(timeStr) {
    const date = new Date("1970-01-01T" + timeStr);
    if (!isNaN(date.getTime())) {
        return date.toTimeString().split(' ')[0].substring(0, 5); 
    }

    const parts = timeStr.match(/(\d{1,2}):(\d{2})\s?(AM|PM)/i);
    if (!parts) return timeStr; 

    let hour = parseInt(parts[1], 10);
    const minute = parts[2];
    const period = parts[3].toUpperCase();

    if (period === "PM" && hour !== 12) hour += 12;
    if (period === "AM" && hour === 12) hour = 0;

    return `${hour.toString().padStart(2, '0')}:${minute}`;
}

})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
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

        const regexMap = {
            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
        };
        const dateRegex = regexMap[tf_params.date_format_for_users];

        $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            }
        });

        $(".tf_check_inout_dates").on("click", function () {
            $(".tf-check-in-out-date").trigger("click");
        });
        $(".tf-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFields(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFields(selectedDates, instance);
            }
        });

        function dateSetToFields(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf_hotel_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf_hotel_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf_hotel_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf_hotel_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            }
        });

        $(".tf-shortcode-design-4 .tf_check_inout_dates").on("click", function() {
            $(".tf-shortcode-design-4 .tf-check-in-out-date").trigger("click");
        });
        $(".tf-shortcode-design-4 .tf-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            },
            onChange: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            }
        });

        function dateSetToFieldsTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.year").html(startDate.getFullYear());
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.year").html(endDate.getFullYear());
                }
            }
        }


        var selectedTemplate = 'design-2';
        var month = 1;
        if ($(window).width() >= 1240) {
            month = 2;
        }

        $(".tf-single-template__two .tf-booking-date-wrap").on("click", function () {
            $(".tf-check-in-out-date").trigger("click");
        });
        $(".tf-single-template__two .tf-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: selectedTemplate == "design-2" ? month : 1,

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsThree(selectedDates, instance);
            },
        });

        function dateSetToFieldsThree(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-single-template__two .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                    $(".tf-single-template__two .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-single-template__two .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                    $(".tf-single-template__two .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        const checkinoutdateange = flatpickr(".tf-hotel-booking-sidebar #check-in-out-date", {
            enableTime: false,
            mode: "range",
            minDate: "today",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            dateFormat: "Y/m/d",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
        });

        jQuery('.tf-details-qc-slider-single').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            adaptiveHeight: true,
            infinite: true,
            useTransform: true,
            speed: 400,
            cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
        });

        jQuery('.tf-details-qc-slider-nav')
            .on('init', function (event, slick) {
                jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
            })
            .slick({
                slidesToShow: 7,
                slidesToScroll: 7,
                dots: false,
                focusOnSelect: false,
                infinite: false,
                responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 5,
                    }
                }, {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                }, {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                }]
            });

        jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
            jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
            var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
            jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
            jQuery(currrentNavSlideElem).addClass('is-active');
        });

        jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
            event.preventDefault();
            var goToSingleSlide = jQuery(this).data('slick-index');

            jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
        });

        jQuery('.tf-room-gallery-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            adaptiveHeight: true,
            infinite: true,
            useTransform: true,
            speed: 400,
            cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
        });

        jQuery('.tf-room-gallery-slider-nav')
            .on('init', function (event, slick) {
                jQuery('.tf-room-gallery-slider-nav .slick-slide.slick-current').addClass('is-active');
            })
            .slick({
                slidesToShow: 5,
                slidesToScroll: 5,
                dots: false,
                focusOnSelect: false,
                infinite: false,
                centerMode: false,
                responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                    }
                }, {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                }, {
                    breakpoint: 420,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    }
                }]
            });

        jQuery('.tf-room-gallery-slider').on('afterChange', function (event, slick, currentSlide) {
            jQuery('.tf-room-gallery-slider-nav').slick('slickGoTo', currentSlide);
            var currrentNavSlideElem = '.tf-room-gallery-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
            jQuery('.tf-room-gallery-slider-nav .slick-slide.is-active').removeClass('is-active');
            jQuery(currrentNavSlideElem).addClass('is-active');
        });

        jQuery('.tf-room-gallery-slider-nav').on('click', '.slick-slide', function (event) {
            event.preventDefault();
            var goToSingleSlide = jQuery(this).data('slick-index');

            jQuery('.tf-room-gallery-slider').slick('slickGoTo', goToSingleSlide);
        });

        if ($('#hotel-location').length) {
            const map = L.map('hotel-location').setView([tf_params.single_hotel_data.address_latitude, tf_params.single_hotel_data.address_longitude], tf_params.single_hotel_data.address_zoom);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.single_hotel_data.address_latitude, tf_params.single_hotel_data.address_longitude], {alt: tf_params.single_hotel_data.address}).addTo(map)
                .bindPopup(tf_params.single_hotel_data.address);
        }

        if ($('#mobile-hotel-location').length) {
            const mapMobile = L.map('mobile-hotel-location').setView([tf_params.single_hotel_data.address_latitude, tf_params.single_hotel_data.address_longitude], tf_params.single_hotel_data.address_zoom);

            const tilesMobile = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapMobile);

            const markerMobile = L.marker([tf_params.single_hotel_data.address_latitude, tf_params.single_hotel_data.address_longitude], {alt: tf_params.single_hotel_data.address}).addTo(map)
                .bindPopup(tf_params.single_hotel_data.address);
        }

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
                    if($('.tf_booking-dates .tf_label-row').length === 1){
                        if(tf_params.hotel_single_template == 'design-3'){
                            $('.tf_booking-dates .tf_label-row').addClass('tf-date-required');
                            $('.tf-hotel-error-msg').show();
                        } else {
                            $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required"><b>' + tf_params.field_required + '</b></span>');
                        }
                    }else{
                        $(".tf-check-in-out-date").trigger("click");
                    }
                }
                return;
            } else {
                if ($('#tf-required').length === 1) {
                    if(tf_params.hotel_single_template == 'design-3'){
                        $('.tf_booking-dates .tf_label-row').removeClass('tf-date-required');
                        $('.tf-hotel-error-msg').hide();
                    } else {
                        $('.tf_booking-dates .tf_label-row .required').html('');
                    }
                }
            }
            //get the checked values of features
            var features = [];
            $('.tf-room-checkbox :checkbox:checked').each(function (i) {
                features[i] = $(this).val();
            });
            var tf_room_avail_nonce = $("input[name=tf_room_avail_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            if (adult_field_type == "number" || adult_field_type == "tel") {
                var adult = $('#adults').val();
            } else {
                var adult = $('select[name=adults] option').filter(':selected').val();
            }
            if (child_field_type == "number" || child_field_type == "tel") {
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
                    $("#tf-single-hotel-avail .tf-submit").addClass('tf-btn-loading');
                },
                success: function (data) {
                    if( $("#rooms").length > 0){
                        $('html, body').animate({
                            scrollTop: $("#rooms").offset().top
                        }, 500);
                        $("#rooms").html(data);
                        $('.tf-room-filter').show();
                        $("#tf-single-hotel-avail .tf-submit").removeClass('tf-btn-loading');
                     } else {
                         notyf.error(tf_params.no_room_found);
                         $("#tf-single-hotel-avail .tf-submit").removeClass('tf-btn-loading');
                     }
                 },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        $(document).on('change', 'input[name=check-in-out-date]', function () {
            if(tf_params.hotel_single_template == 'design-3'){
                if($.trim($('input[name=check-in-out-date]').val()) !== '') {
                    $('.tf_booking-dates .tf_label-row').removeClass('tf-date-required');
                    $('.tf-hotel-error-msg').hide();
                } else {
                    $('.tf_booking-dates .tf_label-row').addClass('tf-date-required');
                    $('.tf-hotel-error-msg').show();
                }
            }
        });

        $('#tf-single-hotel-avail .tf-submit').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
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
        let TfHasErrorsFlag = false;
        $('body').on('click', '.tf-withoutpayment-booking .tf-traveller-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-single-travel').each(function () {
                $(this).find('input, select').each(function () {
                    if ($(this).attr('data-required') && $(this).attr('data-required') == 1) {
                        if ($(this).val() == "") {
                            hasErrors.push(true);
                        }
                    }
                });
                $(this).find('input[type="radio"], input[type="checkbox"]').each(function () {
                    if ($(this).attr('data-required')) {
                        const radioName = $(this).attr('name');
                        const isChecked = $('input[name="' + radioName + '"]:checked').length > 0;
                        if (!isChecked) {
                            hasErrors.push(true);
                        }
                    }
                });
            });
            if (hasErrors.includes(true)) {
                TfHasErrorsFlag = true;
                return false;
            }
            TfHasErrorsFlag = false;
        });

        $('body').on('click', '.hotel-room-book', function (e) {
            e.preventDefault();
            if (TfHasErrorsFlag) {
                return false;
            }
            var $this = $(this);

            var tf_room_booking_nonce = $("input[name=tf_room_booking_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            var unique_id = $this.closest('.tf-room').find('input[name=unique_id]').val();
            var room_id = $this.closest('.tf-room').find('input[name=room_id]').val();
            var option_id = $this.closest('.tf-room').find('input[name=option_id]').val();

            var location = $('input[name=place]').val();
            var adult = $('input[name=adult]').val();
            var child = $('input[name=child]').val();
            var children_ages = $('input[name=children_ages]').val();
            var check_in_date = $('input[name=check_in_date]').val();
            var check_out_date = $('input[name=check_out_date]').val();
            if ($(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val()) {
                var room = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
                var deposit = $(this).closest('.tf-room').find('input[name=make_deposit]').is(':checked');
            } else {
                var room = $("#hotel_room_number").val();
                var deposit = $this.closest('.tf-room').find('input[name=make_deposit]').is(':checked');
            }
            var airport_service = $this.closest('.tf-withoutpayment-popup').find('[name="airport_service"]:checked').val();

            var data = {
                action: 'tf_hotel_booking',
                tf_room_booking_nonce: tf_room_booking_nonce,
                post_id: post_id,
                room_id: room_id,
                unique_id: unique_id,
                option_id: option_id,
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
            $this.closest(".tf-booking-pagination").siblings(".tf-booking-content-summery").find( '.traveller-single-info input' ).each(function (index, element) {
                var element_name = $(element).attr("name");
                data[ element_name ] = $(element).val();
           })

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
                            $.fancybox.close();
                        }

                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });

        });

        /**
         * Single Hotel Video
         *
         * Fancybox
         */

   
        $('[data-fancybox="hotel-vide"]').fancybox({
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
         * Single Map
         *
         * Fancybox
         */
        $('[data-fancybox="hotel-gallery"]').fancybox({
            loop: true,
            touch: false
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
        $(".reserve-button a").on("click", function () {
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
            formData.append('_nonce', tf_params.nonce);

            if (formData.get('from') == null || formData.get('to') == null) {
                formData.append('from', tf_params.tf_hotel_min_price);
                formData.append('to', tf_params.tf_hotel_max_price);
            }

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

        // Hotel Locations Autocomplete
        function tourfic_autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/

            // Executes when some one click in the search form location
            inp.addEventListener("focus", function () {

                closeAllLists();

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
                        inp.closest('input').nextElementSibling.value = source.dataset.slug;
                        setTimeout(() => {
                            closeAllLists();
                        },100);
                    });
                    a.appendChild(b);
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

            function closeAllLists(elmnt = null) {
                const lists = document.querySelectorAll(".autocomplete-items");
                lists.forEach(list => {
                    if (list !== elmnt && list !== inp && list.parentNode) {
                        list.parentNode.removeChild(list);
                    }
                });
            }

            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function (e) {
                // closeAllLists(e.target);
                if (e.target.id == "content" || e.target.id == "") {
                    closeAllLists(e.target);
                }
            });

            // Close when clicking outside
            $(document).on('click', function (event) {
                if (!$(event.target).closest("#tf-location").length) {
                    $("#tf-locationautocomplete-list").hide();
                }
            });
        }

        // Hotel Location

        $('#tf-destination-adv').on("click", function (e) {
            $(".tf-hotel-locations").addClass('tf-locations-show');
        });
    
        $('#tf-destination-adv').on("keyup", function (e) {
            var location = $(this).val();
            $("#tf-place-destination").val(location);
        });
    
        $('#tf-location').on("keyup", function (e) {
            var tf_location = $(this).val();
            $("#tf-search-hotel").val(tf_location);
        });

    
        $('#ui-id-1').on("click", "li", function (e) {
    
            var dest_name = $(this).attr("data-name");
            var dest_slug = $(this).attr("data-slug");
    
            $(".tf-preview-destination").val(dest_name);
            $("#tf-place-destination").val(dest_slug);
    
            setTimeout(function () {
                $(".tf-hotel-locations").removeClass('tf-locations-show');
            }, 100); 
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest("#tf-destination-adv, #ui-id-1").length) {
                $(".tf-hotel-locations").removeClass('tf-locations-show');
            }
        });

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
                $this.closest('.tf-room').find("input[name=hotel_room_depo]").val(hotel_deposit);
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
                var deposit = $this.closest('.tf-room').find("input[name=hotel_room_depo]").val();
            }
            var airport_service = $this.closest('[name="airport_service"]:checked').val();

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
                    $this.closest(".room-submit-wrap").siblings(".tf-withoutpayment-booking").find('.tf-hotel-booking-content').show()
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
                        if( ! $this.closest('form.tf-room').find('.tf-withoutpayment-booking').hasClass('show') ){
                            $this.closest('form.tf-room').find('.tf-withoutpayment-booking').addClass('show');
                        }
                        $this.closest(".room-submit-wrap").siblings(".tf-withoutpayment-booking").find('.tf-control-pagination:first-child').show()
                    }
                },
                error: function (data) {
                    console.log(data);
                },

            });
        }
        $('body').on('click', '.tf-hotel-booking-popup-btn', function (e) {
            e.preventDefault();
            var $this = $(this);

            hotelPopupBooking($this);
        });

        $('body').on('submit', 'form.tf-room', function (e) {
            e.preventDefault();

            var $this = $(this);
            var formData = new FormData(this);

            // if ($(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val()) {
            //     var room = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
            //     var deposit = $(this).closest('.room-submit-wrap').find('input[name=make_deposit]').is(':checked');
            // } else {
            //     var room = $("#hotel_room_number").val();
            // }
            var deposit = $this.find("input[name=hotel_room_depo]").val();
            var airport_service = $this.find('[name="airport_service"]:checked').val();

            formData.append('action', 'tf_hotel_booking');
            formData.append('_ajax_nonce', tf_params.nonce);
            formData.append('deposit', deposit);
            formData.append('airport_service', airport_service);


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
                error: function (data) {
                    console.log(data);
                },
                complete: function (data) {
                    $this.unblock()
                    $('#tour_room_details_loader').hide();
                    $('.tf-withoutpayment-booking').removeClass('show');
                    $this.find('.tf-withoutpayment-booking-confirm').addClass('show');
                },
            })
        });


        $(document).on("change", "[name='airport_service']", function (e) {
            var $this = $(this);

            hotelPopupBooking($this);
        });

        // Design 1 - hotel Facilities
        $('.tf-hotel-facilities-title-area').on("click", function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-hotel-facilities-content-area").slideUp(400);
                $(".tf-hotel-facilities-title-area").removeClass("active");
                $('.hotel-facilities-icon-down').removeClass("active");
            }
            $this.toggleClass("active");
            $(this).closest('.tf-hotel-facilities-content-area').toggleClass('active');
            $(this).find('.hotel-facilities-icon-down').toggle();
            $(this).find('.hotel-facilities-icon-up').toggleClass('active');
            $this.next().slideToggle();
        });
    });

})(jQuery, window);
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
(function ($, win) {
    $(document).ready(function () {


        function tf_flatpickr_locale() {
            let locale = tf_params.tour_form_data.flatpickr_locale;
            let allowed_locales = ['ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' ];

            if( jQuery.inArray(locale, allowed_locales) !== -1 ) {
                
                switch (locale) {
                    case "bn_BD":
                        locale = 'bn';
                        break;
                    case "de_DE":
                        locale = 'de';
                        break;
                    case "es_ES":
                        locale = 'es';
                        break;
                    case "fr_FR":
                        locale = 'fr';
                        break;
                    case "hi_IN":
                        locale = 'hi';
                        break;
                    case "it_IT":
                        locale = 'it';
                        break;
                    case "nl_NL":
                        locale = 'nl';
                        break;
                    case "ru_RU":
                        locale = 'ru';
                        break;
                    case "zh_CN":
                        locale = 'zh';
                        break;
                }
            } else {
                locale = 'default';
            }

            return locale;
        }

        // let locale_zone = tf_flatpickr_locale();

        window.flatpickr.l10ns[tf_flatpickr_locale()].firstDayOfWeek = tf_params.tour_form_data.first_day_of_week;

        $(".tf_tour_check_in_out_date").on("click", function () {
            $(".tf-tour-check-in-out-date").trigger("click");
        });
        $(".tf-tour-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTwo(selectedDates, instance);
            },
        });

        function dateSetToFieldsTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf_tour_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf_tour_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf_tour_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf_tour_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $("#tf_tour_aval_check #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            altInput: true,
            dateFormat: "Y/m/d",
            altFormat: tf_params.date_format_for_users,
            minDate: "today",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            },
        });

        $(".tf-shortcode-design-4 .tf_tour_check_in_out_date").on("click", function() {
            $(".tf-shortcode-design-4 .tf-tour-check-in-out-date").trigger("click");
        });
        $(".tf-shortcode-design-4 .tf-tour-check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            onReady: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsThree(selectedDates, instance);
            },
            onChange: function(selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsThree(selectedDates, instance);
            }
        });

        function dateSetToFieldsThree(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.date").html(startDate.getDate());
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
                    $(".tf-shortcode-design-4 .tf_checkin_dates span.year").html(startDate.getFullYear());
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.date").html(endDate.getDate());
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
                    $(".tf-shortcode-design-4 .tf_checkout_dates span.year").html(endDate.getFullYear());
                }
            }
        }

        const regexMap = {
            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
        };
        const dateRegex = regexMap[tf_params.date_format_for_users];

        $("#tf_tour_aval_check #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            altInput: true,
            dateFormat: "Y/m/d",
            altFormat: tf_params.date_format_for_users,
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
        });

        if ($('#tour-location').length) {
            const map = L.map('tour-location').setView([tf_params.tour_form_data.location_latitude, tf_params.tour_form_data.location_longitude], tf_params.tour_form_data.location_zoom);
            
            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.tour_form_data.location_latitude, tf_params.tour_form_data.location_longitude], {alt: tf_params.tour_form_data.location}).addTo(map)
                .bindPopup(tf_params.tour_form_data.location);
        }

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
        $('body').on('submit', 'form.tf_tours_booking', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_tours_booking');
            formData.append('_ajax_nonce', tf_params.nonce);


            // Tour Extra
            var tour_extra_total = [];
            var tour_extra_quantity = [];

            /*
            jQuery('.tour-extra-single input:checkbox:checked').each(function () {
                tour_extra_total.push(jQuery(this).val());

                    if ($this.find('.tf_quantity-acrselection').hasClass('quantity-active')) {
                        let qty = $this.find('input[name="extra-quantity"]').val();

                        tour_extra_quantity.push(qty)
                    } else {
                        tour_extra_quantity.push(1)
                    }
                }
            });
            */

            jQuery('.tour-extra-single').each(function(e) {
                let $this = jQuery(this);

                if($this.find('input[name="tf-tour-extra"]').is(':checked')){

                   let tour_extras = $this.find('input[name="tf-tour-extra"]').val();
                   tour_extra_total.push(tour_extras);

                   if($this.find('.tf_quantity-acrselection').hasClass('quantity-active')){
                       let qty = $this.find('input[name="extra-quantity"]').val();

                       tour_extra_quantity.push(qty)
                   }else{
                    tour_extra_quantity.push(1)
                   }
               }
           });

            formData.append('tour_extra', tour_extra_total);
            formData.append('tour_extra_quantity', tour_extra_quantity);

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
                                $('#tour_room_details_loader').hide();
                                $('.tf-withoutpayment-booking').removeClass('show');
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

        $('input[name="tf-tour-extra"]').on("change", function (e) {

            let parent = $(this).parent().parent().parent()

            if ($(this).is(':checked')) {

                parent.find(".tf_quantity-acrselection").addClass('quantity-active')

            } else {

                parent.find(".tf_quantity-acrselection").removeClass('quantity-active')

            }
        })

        $(".tf-itinerary-single-meta li .fa-info-circle, .ininerary-other-info li .fa-info-circle").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).parent().attr("id");
            $(".tour-itinerary-sleep").each(function () {
                var elementId = $(this).attr("id"); 
                if (id === elementId) {
                    $(this).fadeIn();
                } else {
                    $(this).fadeOut();
                }
            });
        });
        
    
        // Hide when clicking outside
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".tour-itinerary-sleep, .ininerary-other-info li .fa-info-circle, .tf-itinerary-single-meta li .fa-info-circle").length) {
                $(".tour-itinerary-sleep").fadeOut();
            }
        });


        /**
         * Single Tour Video
         *
         * Fancybox
         */

        $('[data-fancybox="tour-video"]').fancybox({
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
            formData.append('_nonce', tf_params.nonce);

            if (formData.get('from') == null || formData.get('to') == null) {
                formData.append('from', tf_params.tf_tour_min_price);
                formData.append('to', tf_params.tf_tour_max_price);
            }

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

        // Tour destination Autocomplete

        function tourfic_autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/

            // Executes when some one click in the search form location
            inp.addEventListener("focus", function () {
                // if (this.value == '' || !this.value) {
                    closeAllLists();
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
                            inp.closest('input').nextElementSibling.value = source.dataset.slug;
                            setTimeout(() => {
                                closeAllLists();
                            },100);
                        });
                        a.appendChild(b);
                    }
                // }
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
                        if (x) x[currentFocus].trigger("click");;
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

             // Close when clicking outside
             $(document).on('click', function (event) {
                if (!$(event.target).closest("#tf-destination").length) {
                    $("#tf-destinationautocomplete-list").hide();
                }
            });

        }

        /*
        * New Template Itinerary Accordion
        * @author: Jahid
        */
        $('.tf-itinerary-title').on("click", function () {
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
        $('.tf-form-title.tf-tour-extra').on("click", function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-tour-extra-box").slideUp(400);
                $(".tf-form-title.tf-tour-extra").removeClass("active");
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
        });

        // Itinerary Accordion
        $('.tf-accordion-head').on("click", function () {
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

        $('#tf-tour-location-adv').on("click", function (e) {
            var location = $(this).val();
            $(".tf-tour-results").addClass('tf-destination-show');
        });
        $('#tf-tour-location-adv').on("keyup", function (e) {
            var location = $(this).val();
            $("#tf-tour-place").val(location);
        });
        $('#tf-destination').on("keyup", function (e) {
            var tf_location = $(this).val();
            $("#tf-search-tour").val(tf_location);
        });
    
        $('#ui-id-2 li').on("click", function (e) {
            var dest_name = $(this).attr("data-name");
            var dest_slug = $(this).attr("data-slug");
            $(".tf-tour-preview-place").val(dest_name);
            $("#tf-tour-place").val(dest_slug);
            setTimeout(function () {
                $(".tf-tour-results").removeClass('tf-destination-show');
            }, 100);
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest("#tf-tour-location-adv").length) {
                $(".tf-tour-results").removeClass('tf-destination-show');
            }
        });

        // Tour destination autocomplete
        var tour_destination_input = document.getElementById("tf-destination");
        var tour_destinations = tf_params.tour_destinations;
        if (tour_destination_input) {
            tourfic_autocomplete(tour_destination_input, tour_destinations);
        }

        /**
         * Single tour sticky booking bar position fixed
         */
        $(window).on("scroll", function () {
            var sticky = $('.tf-tour-booking-wrap'),
                scroll = $(window).scrollTop(),
                footer = $('footer');
        
            if (footer.length === 0) {
                return; 
            }
        
            var footerOffset = footer.offset().top,
                windowHeight = $(window).height();
        
            if (scroll >= 800) {
                if (scroll + windowHeight >= footerOffset) {
                    sticky.removeClass('tf-tours-fixed'); 
                } else {
                    sticky.addClass('tf-tours-fixed');
                }
            } else {
                sticky.removeClass('tf-tours-fixed');
            }
        });

        /**
         * Single tour sticky booking bar - template 1
         * @author Foysal
         */
        if ($('.tf-single-template__one .tf-booking-form').length > 0) {
            $(window).on("scroll", function () {
                let bookingBox = $('.tf-single-template__one .tf_tours_main_booking');
                var sticky = $('.tf-single-template__one .tf_tours_bottom_booking .tf-bottom-booking-bar'),
                    scroll = $(window).scrollTop(),
                    footer = $('footer');
            
                if (footer.length === 0) {
                    return; 
                }
                let boxOffset = bookingBox.offset().top + bookingBox.outerHeight();
                var footerOffset = footer.offset().top,
                    windowHeight = $(window).height();
            
                if (scroll >= boxOffset) {
                    if (scroll + windowHeight >= footerOffset) {
                        sticky.removeClass('active'); 
                    } else {
                        sticky.addClass('active');
                    }
                } else {
                    sticky.removeClass('active');
                }
            });
        }
        /**
         * Single tour sticky booking bar - template 2
         * @author Foysal
         */
        if ($('.tf-single-template__two .tf_tours_main_booking').length > 0) {
            $(window).on("scroll", function () {
                let bookingBox = $('.tf-single-template__two .tf_tours_main_booking');
                var sticky = $('.tf-single-template__two .tf_tours_bottom_booking .tf-bottom-booking-bar'),
                    scroll = $(window).scrollTop(),
                    footer = $('footer');
            
                if (footer.length === 0) {
                    return; 
                }
                let boxOffset = bookingBox.offset().top + bookingBox.outerHeight();
                var footerOffset = footer.offset().top,
                    windowHeight = $(window).height();
            
                if (scroll >= boxOffset) {
                    if (scroll + windowHeight >= footerOffset) {
                        sticky.removeClass('active'); 
                    } else {
                        sticky.addClass('active');
                    }
                } else {
                    sticky.removeClass('active');
                }
            });
        }

        //Template Legacy Mobile Booking Btn
        $('.tf-single-template__legacy .tf-booking-mobile-btn .tf_btn').on('click', function (e) {
            e.preventDefault();
            $('.tf-single-template__legacy .tf-booking-mobile-btn').hide();
            $('.tf-single-template__legacy .tf-tour-booking-wrap .tf_tours_booking').addClass('show');
        });

        function applyResponsiveClass() {
            if($('.tf-single-template__legacy .tf-tour-booking-wrap').length > 0){
                if ($(window).width() <= 768) {
                $('.tf-single-template__legacy .tf-tour-booking-wrap').addClass('tf-tours-fixed-default');
                } else {
                $('.tf-single-template__legacy .tf-tour-booking-wrap').removeClass('tf-tours-fixed-default');
                }
            }
          }
        
          // Run on page load
          applyResponsiveClass();
        
          // Run on window resize
          $(window).resize(function () {
            applyResponsiveClass();
          });

        //Template 2 Mobile Booking Btn
        $('.tf-single-template__one .tf-booking-mobile-btn').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.tf-bottom-booking-bar').toggleClass('mobile-active');
        });

        //Template 3 Mobile Booking Btn
        $('.tf-single-template__two .tf-mobile-booking-btn').on('click', function () {
            $('.tf-bottom-booking-bar').addClass('tf-mobile-booking-form');
            $('.tf-single-template__two .tf-mobile-booking-btn').slideUp(300);
        });

        $(document).on('click touchstart', function (e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('.tf-bottom-booking-bar, .tf-mobile-booking-btn, .flatpickr-calendar').length) {
                    $('.tf-bottom-booking-bar').removeClass('tf-mobile-booking-form');
                    $('.tf-single-template__two .tf-mobile-booking-btn').slideDown(300);
                }
            }
        });

        /**
         * Single tour booking form
         */
        const allowed_times = tf_params.tour_form_data.allowed_times ? JSON.parse(tf_params.tour_form_data.allowed_times) : [];
        const custom_avail = tf_params.tour_form_data.custom_avail;
        if (custom_avail == false && Object.keys(allowed_times).length > 0) {
            populateTimeSelect(allowed_times); // Pass the entire object, not just the values
        }

        // First Day of Week
        //const first_day_of_week = tf_params.tour_form_data.flatpickr_locale;

        function populateTimeSelect(times) {
            let timeSelect = $('select[name="check-in-time"]');
            let timeSelectDiv = $(".check-in-time-div");
            timeSelect.empty();

            if (Object.keys(times).length > 0) {
                timeSelect.append(`<option value="" selected hidden>${tf_params.tour_form_data.select_time_text}</option>`);
                // Use the keys and values from the object to populate the options
                $.each(times, function (key, value) {
                    timeSelect.append(`<option value="${key}">${value}</option>`);
                });
                timeSelectDiv.css('display', 'flex');
            } else timeSelectDiv.hide();
        }

        var tour_date_options = {
            enableTime: false,
            dateFormat: "Y/m/d",
            altInput: true,
            altFormat: tf_params.tour_form_data.date_format,
            locale: tf_flatpickr_locale(),
            
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
            },

            onChange: function (selectedDates, dateStr, instance) {

                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                $(".tours-check-in-out").val(instance.altInput.value);
                $('.tours-check-in-out[type="hidden"]').val(dateStr.replace(/[a-z]+/g, '-'));
                if (custom_avail == true) {
                    let times = Object.values(allowed_times).filter((v) => {
                        let date_str = Date.parse(dateStr);
                        let start_date = Date.parse(v.date.from);
                        let end_date = Date.parse(v.date.to);
                        return start_date <= date_str && end_date >= date_str;
                    });
                    times = times.length > 0 && times[0].times ? times[0].times : null;
                    populateTimeSelect(times);
                }
                
                if(tf_params.tour_form_data.tf_tour_selected_template === 'design-2') {
                    dateSetToFields(selectedDates, instance);
                }
            },

        };

        if(tf_params.tour_form_data.tour_type == 'fixed'){
            tour_date_options.defaultDate= tf_params.tour_form_data.defaultDate;
            tour_date_options.enable= tf_params.tour_form_data.enable;
        }

        if(tf_params.tour_form_data.tour_type == 'continuous'){
            tour_date_options.minDate = "today";
            tour_date_options.disableMobile = "true";

            if (custom_avail == true) {
                tour_date_options.enable = Object.values(tf_params.tour_form_data.cont_custom_date).map((v) => {

                    let today = new Date();
                    let from_date = '';
                    let formattedDate = today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate();

                    if( tf_params.tour_form_data.disable_same_day ) {
                        if (v.date.from == formattedDate) {
                            let date = new Date( v.date.from );
                            let nextDay = new Date(date.setDate(date.getDate() + 1));
                            from_date = nextDay.getFullYear() + '/' + (nextDay.getMonth() + 1) + '/' + nextDay.getDate();
                        }  else {
                            from_date = v.date.from;
                        }
                    } else {
                        from_date = v.date.from;
                    }

                    return {
                        from: from_date,
                        to: v.date.to
                    }
                });
            }

            if (custom_avail == false) {
                if (tf_params.tour_form_data.disabled_day || tf_params.tour_form_data.disable_range || tf_params.tour_form_data.disable_specific || tf_params.tour_form_data.disable_same_day) {
                    tour_date_options.disable = [];
                    if (tf_params.tour_form_data.disabled_day) {
                        var disabledDays = tf_params.tour_form_data.disabled_day.map(Number);
                        tour_date_options.disable.push(
                            function (date) {
                            return (date.getDay() === 8 || disabledDays.includes(date.getDay()));
                        }
                        );
                    }
                    if (tf_params.tour_form_data.disable_range) {
                        Object.values(tf_params.tour_form_data.disable_range).forEach((d_item) => {
                            tour_date_options.disable.push({
                                from: d_item.date.from,
                                to: d_item.date.to
                            });
                        });
                    }
                    if (tf_params.tour_form_data.disable_same_day) {
                        tour_date_options.disable.push("today");
                    }
                    
                    if (tf_params.tour_form_data.disable_specific) {
                        var disable_specific_string = tf_params.tour_form_data.disable_specific.split(", ");
                        disable_specific_string.forEach(function(date) {
                            tour_date_options.disable.push(date);
                        });
                    }
                }
            }
        }
        
        // remove empty attributes from tour_date_options object
        // tour_date_options = Object.fromEntries(Object.entries(tour_date_options).filter(([_, v]) => v != '' ));

        if(tf_params.tour_form_data.tf_tour_selected_template === 'design-1') {
            $(".tours-check-in-out").flatpickr(tour_date_options);

            $("select[name='check-in-time']").on("change", function () {
                var selectedTime = $(this).val();
                $("select[name='check-in-time']").not(this).val(selectedTime);
            });

            $(".acr-select input[type='number']").on("change", function () {
                var inputName = $(this).attr("name");
                var selectedValue = $(this).val();

                // Update all inputs with the same name
                $(".acr-select input[type='number'][name='" + inputName + "']").val(selectedValue)
            });
        }

        if(tf_params.tour_form_data.tf_tour_selected_template === 'design-2') {
            $(".tours-check-in-out").flatpickr(tour_date_options);
            if(tour_date_options.defaultDate){
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                const startDate = new Date(tour_date_options.defaultDate);
                $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-date").html(startDate.getDate());
                $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
            }
            function dateSetToFields(selectedDates, instance) {
                if (selectedDates.length === 1) {
                    const monthNames = [
                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ];
                    if(selectedDates[0]){
                        const startDate = selectedDates[0];
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-date").html(startDate.getDate());
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                    }
                }
                if (selectedDates.length === 2) {
                    const monthNames = [
                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ];
                    if(selectedDates[0]){
                        const startDate = selectedDates[0];
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout  span.tf-booking-date").html(startDate.getDate());
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                    }
                    if(selectedDates[1]){
                        const endDate = selectedDates[1];
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout  span.tf-booking-date").html(endDate.getDate());
                        $(".tf-single-template__two .tf-bottom-booking-bar .tf-booking-form-checkinout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                    }
                }
            }

            $("select[name='check-in-time']").on("change", function () {
                var selectedTime = $(this).val();
                $("select[name='check-in-time']").not(this).val(selectedTime);
            });

            $(".acr-select input[type='tel']").on("change", function () {
                var inputName = $(this).attr("name");
                var selectedValue = $(this).val();

                // Update all inputs with the same name
                $(".acr-select input[type='tel'][name='" + inputName + "']").val(selectedValue)
            });
        }

        if(tf_params.tour_form_data.tf_tour_selected_template === 'default') {
            $("#check-in-out-date").flatpickr(tour_date_options);
        }

        $(document).on('click', "#tour-deposit > div > div.tf_button_group > button", function (e) {
            e.preventDefault();
            var form = $(document).find('form.tf_tours_booking');
            var has_deposit = $(this).data('deposit');
            if (has_deposit === true) {
                form.find('input[name="deposit"]').val(1);
            } else {
                form.find('input[name="deposit"]').val(0);
            }
            form.submit();
        });

    });

})(jQuery, window);
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
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
        
        const regexMap = {
            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
        };
        const dateRegex = regexMap[tf_params.date_format_for_users];

        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
            enableTime: false,
            minDate: "today",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            mode: "range",
            dateFormat: "Y/m/d",
            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                })
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                })
            },
        });

        $(".tf-reviews-slider").each(function () {
            var $this = $(this);
            $this.slick({
                dots: true,
                arrows: false,
                slidesToShow: 3,
                infinite: false,
                speed: 2000,
                autoplay: false,
                autoplaySpeed: 2000,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
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
        });

        $(".tf-archive-template__three #tf-check-out").on('click', function () {
            $(".tf-search-input.form-control").click();
        });

        $(".tf-archive-template__three #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                dateSetToFieldsTempThree(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                dateSetToFieldsTempThree(selectedDates, instance);
            },
        });

        function dateSetToFieldsTempThree(selectedDates, instance) {
            const format = tf_params.date_format_for_users;
            if (selectedDates.length === 2) {
                if (selectedDates[0]) {
                    let checkInDate = instance.formatDate(selectedDates[0], format);
                    $(".tf-archive-template__three #tf-check-in").val(checkInDate);
                }

                if (selectedDates[1]) {
                    let checkOutDate = instance.formatDate(selectedDates[1], format);
                    $(".tf-archive-template__three #tf-check-out").val(checkOutDate);
                }
            }
        }

        $(".tf-archive-booking-form__style-3 #tf-check-out").on('click', function () {
            $(".tf-search-input.form-control").click();
        });

        $(".tf-archive-booking-form__style-3 #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                dateSetToFieldsTemp3(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                dateSetToFieldsTemp3(selectedDates, instance);
            },
        });

        function dateSetToFieldsTemp3(selectedDates, instance) {
            const format = tf_params.date_format_for_users;
            if (selectedDates.length === 2) {
                if (selectedDates[0]) {
                    let checkInDate = instance.formatDate(selectedDates[0], format);
                    $(".tf-archive-booking-form__style-3 #tf-check-in").val(checkInDate);
                }

                if (selectedDates[1]) {
                    let checkOutDate = instance.formatDate(selectedDates[1], format);
                    $(".tf-archive-booking-form__style-3 #tf-check-out").val(checkOutDate);
                }
            }
        }

        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
            enableTime: false,
            minDate: "today",
            altInput: true,
            altFormat: tf_params.date_format_for_users,
            mode: "range",
            dateFormat: "Y/m/d",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                    return `${d1} - ${d2}`;
                });
            },
        });

        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
            let calander = flatpickr(this, {
                enableTime: false,
                minDate: "today",
                mode: "range",
                dateFormat: "Y/m/d",
                altInput: true,
                altFormat: tf_params.date_format_for_users,

                onChange: function (selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                        return `${date1} - ${date2}`;
                    });
                    instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                        return `${d1} - ${d2}`;
                    });
                },
            });

            // open flatpickr on focus
            calander.open();
        })

        $(".tf-archive-template__one .tf_dropoff_date").on("click", function () {
            $("#tf_pickup_date").trigger("click");
        });
        $(".tf-archive-template__one #tf_pickup_date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",
            showMonths: $(window).width() >= 1240 ? 2 : 1,

            onReady: function (selectedDates, dateStr, instance) {
                dateSetToFieldsCarTempOne(selectedDates, instance);
            },

            onChange: function (selectedDates, dateStr, instance) {
                dateSetToFieldsCarTempOne(selectedDates, instance);
            },
        });

        function dateSetToFieldsCarTempOne(selectedDates, instance) {
            if (selectedDates.length === 2) {
                if (selectedDates[0]) {
                    const startDate = flatpickr.formatDate(selectedDates[0], "Y/m/d");
                    $(".tf-archive-template__one #tf_pickup_date").val(startDate);
                }
                if (selectedDates[1]) {
                    const endDate = flatpickr.formatDate(selectedDates[1], "Y/m/d");
                    $(".tf-archive-template__one .tf-select-date #tf_dropoff_date").val(endDate);
                }
            }
        }

        $(".tf-archive-template__two .tf-tour-archive-block .tf-booking-date-wrap").on("click", function () {
            $("#check-in-out-date").trigger("click");
        });
        $(".tf-archive-template__two .tf-tour-archive-block #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsTempTwoTour(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsTempTwoTour(selectedDates, instance);
            },
        });

        function dateSetToFieldsTempTwoTour(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-archive-template__two .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
                    $(".tf-archive-template__two .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-archive-template__two .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
                    $(".tf-archive-template__two .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $(".tf-archive-template__two .tf-booking-date-wrap").on("click", function () {
            $("#check-in-out-date").trigger("click");
        });
        $(".tf-archive-template__two #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsTempTwo(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsTempTwo(selectedDates, instance);
            },
        });

        function dateSetToFieldsTempTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-archive-template__two .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                    $(".tf-archive-template__two .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-archive-template__two .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                    $(".tf-archive-template__two .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $(".tf-archive-booking-form__style-2 .tf-tour-archive-block .tf-booking-date-wrap").on("click", function () {
            $("#check-in-out-date").trigger("click");
        });
        $(".tf-archive-booking-form__style-2 .tf-tour-archive-block #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsStyleTwoTour(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsStyleTwoTour(selectedDates, instance);
            },
        });

        function dateSetToFieldsStyleTwoTour(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                }
            }
        }

        $(".tf-archive-booking-form__style-2 .tf-booking-date-wrap").on("click", function () {
            $("#check-in-out-date").trigger("click");
        });
        $(".tf-archive-booking-form__style-2 #check-in-out-date").flatpickr({
            enableTime: false,
            mode: "range",
            dateFormat: "Y/m/d",
            minDate: "today",

            onReady: function (selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsStyleTwo(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                    return `${date1} - ${date2}`;
                });
                dateSetToFieldsStyleTwo(selectedDates, instance);
            },
        });

        function dateSetToFieldsStyleTwo(selectedDates, instance) {
            if (selectedDates.length === 2) {
                const monthNames = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (selectedDates[0]) {
                    const startDate = selectedDates[0];
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                }
                if (selectedDates[1]) {
                    const endDate = selectedDates[1];
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                    $(".tf-archive-booking-form__style-2 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                }
            }
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
            var step = input.attr('step') ? input.attr('step') : 1;
            if (!input.val()) {
                input.val(0);
            }
            if (input.val() < max) {
                input.val(parseInt(input.val()) + parseInt(step)).change();
            }
            // input focus disable
            input.blur();
        });

        // Number Decrement
        $('.acr-dec, .quanity-acr-dec').on('click', function (e) {

            var input = $(this).parent().find('input');
            var min = input.attr('min') ? input.attr('min') : 0;
            var step = input.attr('step') ? input.attr('step') : 1;
            if (!input.val()) {
                input.val(0);
            }
            if (input.val() > min) {
                input.val(input.val() - parseInt(step)).change();
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

        // Shortcode Design 2 Adults change trigger
        $(document).on('change', '.adults-style2', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();
            let $this = $(this).closest('.tf_hotel-shortcode-design-2');
            // Declare child outside the if block
            let child = 0;

            if (thisEml.parent().parent().siblings().find('.childs-style2').length > 0) {
                child = parseInt(thisEml.parent().parent().siblings().find('.childs-style2').val());
            } else {
                child = parseInt(0);
            }
            // Declare intant outside the if block
            let intant = 0;

            if (thisEml.parent().parent().siblings().find('.infant-style2').length > 0) {
                intant = parseInt(thisEml.parent().parent().siblings().find('.childs-style2').val());
            } else {
                intant = parseInt(0);
            }
            let total_people = child + intant + parseInt(thisVal);
            if (total_people > 1) {
                $this.find(".tf_guest_number .guest").text(total_people);
            }
        });
        // Shortcode Design 2 Childs change trigger
        $(document).on('change', '.childs-style2', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();
            let $this = $(this).closest('.tf_hotel-shortcode-design-2');

            // Declare adults outside the if block
            let adults = 0;

            if (thisEml.parent().parent().siblings().find('.adults-style2').length > 0) {
                adults = parseInt(thisEml.parent().parent().siblings().find('.adults-style2').val());
            } else {
                adults = parseInt(0);
            }
            // Declare intant outside the if block
            let intant = 0;

            if (thisEml.parent().parent().siblings().find('.infant-style2').length > 0) {
                intant = parseInt(thisEml.parent().parent().siblings().find('.infant-style2').val());
            } else {
                intant = parseInt(0);
            }

            let total_people = adults + intant + parseInt(thisVal);
            if (total_people > 1) {
                $this.find(".tf_guest_number .guest").text(total_people);
            }
        });
        // Shortcode Design 2 Infants change trigger
        $(document).on('change', '.infant-style2', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();
            let $this = $(this).closest('.tf_hotel-shortcode-design-2');

            // Declare adults outside the if block
            let adults = 0;

            if (thisEml.parent().parent().siblings().find('.adults-style2').length > 0) {
                adults = parseInt(thisEml.parent().parent().siblings().find('.adults-style2').val());
            } else {
                adults = parseInt(0);
            }

            // Declare child outside the if block
            let child = 0;

            if (thisEml.parent().parent().siblings().find('.childs-style2').length > 0) {
                child = parseInt(thisEml.parent().parent().siblings().find('.childs-style2').val());
            } else {
                child = parseInt(0);
            }

            let total_people = adults + child + parseInt(thisVal);
            if (total_people > 1) {
                $this.find(".tf_guest_number .guest").text(total_people);
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
            let step = $(this).attr("data-step");
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
            let step = $(this).attr("data-step");
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
                            if ($(".tf-traveller-info-box").html().trim() == "") {
                                $('.tf-traveller-info-box').html(response.traveller_info);
                            } else {
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
        $('body').on('click', '.tf-booking-popup-btn', function (e) {
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
        $(document).on('change', '[name=deposit]', function () {
            tourPopupBooking();
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
})();

