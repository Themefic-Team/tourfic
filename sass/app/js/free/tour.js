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

            var selectedPackage = $('.tf-booking-content-package input[name="tf_package"]:checked').val();
            if (selectedPackage !== undefined) {
                formData.append('selectedPackage', selectedPackage);
                var $selectedDiv = $('#package-' + selectedPackage).closest('.tf-single-package');
                var check_in_time = $selectedDiv.find('select[name=package_start_time] option').filter(':selected').val();
                formData.append('check-in-time', check_in_time);
            }
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
                            $('#tour_room_details_loader').hide();
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

        $(".tf-itinerary-single-meta li a, .ininerary-other-info li a").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).attr("id");
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
                submitBtn = form.find('button[type="submit"]'),
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
                    form.css({'pointer-events': 'none'});
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    form.css({'pointer-events': 'all'});
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
            inp.addEventListener("focus", function () {
                closeAllLists();
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
        
                for (const [slug, data] of Object.entries(arr)) {
                    let name = data.name;
                    let id = data.id;
        
                    let b = document.createElement("DIV");
                    b.innerHTML = name;
                    b.innerHTML += `<input type='hidden' value="${name}" data-slug="${slug}" data-id="${id}">`;
        
                    b.addEventListener("click", function () {
                        let source = this.getElementsByTagName("input")[0];
        
                        inp.value = source.value;
        
                        // store slug (1st hidden field)
                        inp.closest('input').nextElementSibling.value = source.dataset.slug;
        
                        // store ID (2nd hidden field)
                        inp.closest('input').nextElementSibling.nextElementSibling.value = source.dataset.id;
        
                        setTimeout(() => closeAllLists(), 100);
                    });
        
                    a.appendChild(b);
                }
            });
        
            var currentFocus;
        
            inp.addEventListener("keyup", function (e) {
                var a, b, i, val = this.value;
        
                closeAllLists();
                currentFocus = -1;
        
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
        
                var $notfound = [];
        
                for (const [slug, data] of Object.entries(arr)) {
                    let name = data.name;
                    let id = data.id;
        
                    if (name.substr(0, val.length).toUpperCase() === val.toUpperCase()) {
                        $notfound.push('found');
        
                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + name.substr(0, val.length) + "</strong>";
                        b.innerHTML += name.substr(val.length);
                        b.innerHTML += `<input type="hidden" value="${name}" data-slug="${slug}" data-id="${id}">`;
        
                        b.addEventListener("click", function () {
                            let source = this.getElementsByTagName("input")[0];
        
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug;
                            inp.closest('input').nextElementSibling.nextElementSibling.value = source.dataset.id;
        
                            closeAllLists();
                        });
        
                        a.appendChild(b);
        
                    } else {
                        $notfound.push('notfound');
                    }
                }
        
                if ($notfound.indexOf('found') === -1) {
                    b = document.createElement("DIV");
                    b.innerHTML += tf_params.no_found;
                    b.innerHTML += `<input type='hidden' value="">`;
                    b.addEventListener("click", function () {
                        inp.value = "";
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            });
        
            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
        
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });
        
            function addActive(x) {
                if (!x) return false;
                removeActive(x);
        
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
        
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

        // Tour price by onchange
        $('.tf_tours_booking .tours-check-in-out').on("change", function () {
            var date = $(this).val();
            let post_id = $('input[name="post_id"]').val();

            if( !date ){
                return;
            }
            var data = {
                action: 'tf_tour_price_calculation',
                _nonce: tf_params.nonce,
                post_id: post_id,
                date: date,
            };

            $.ajax({
                url: tf_params.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    if($('.tf-tour-booking-box')){
                        $('.tf-tour-booking-box').addClass('tf-box-loading');
                    }
                    if($('.tf-search-date-wrapper')){
                        $('.tf-search-date-wrapper').addClass('tf-box-loading');
                    }
                },
                success: function (response) {
                    if(response){
                        if(response.data.min_price){
                            $('.tf-tour-booking-box .tf-booking-price p').html(response.data.min_price);
                        }
                        if($('.acr-adult-price') && response.data.adult){
                            $('.acr-adult-price').html(response.data.adult);
                        }
                        if($('.acr-child-price') && response.data.child){
                            $('.acr-child-price').html(response.data.child);
                        }
                        if($('.acr-infant-price') && response.data.infant){
                            $('.acr-infant-price').html(response.data.infant);
                        }
                        if($('.tf-tour-booking-box')){
                            $('.tf-tour-booking-box').removeClass('tf-box-loading');
                        }
                        if($('.tf-search-date-wrapper')){
                            $('.tf-search-date-wrapper').removeClass('tf-box-loading');
                        }
                    }
                }
            });
        });
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
            
                if (footer.length === 0 || bookingBox.length === 0 || sticky.length === 0) {
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
                
                // Initialize empty object for times
                let times = {};
                const selectedDate = selectedDates[0];
                const timestamp = selectedDate.getTime();

                const tourAvailability = tf_params.tour_form_data.tour_availability;

                for (const key in tourAvailability) {
                    const availability = tourAvailability[key];

                    if (availability.status !== 'available') continue;

                    const from = new Date(availability.check_in.trim()).getTime();
                    const to   = new Date(availability.check_out.trim()).getTime();

                    if (timestamp >= from && timestamp <= to) {
                        const allowedTime = availability.allowed_time?.time || [];
                        if (Array.isArray(allowedTime)) {
                            allowedTime.forEach((t) => {
                                if (t && t.trim() !== '') {
                                    times[t] = t;
                                }
                            });
                        } else if (typeof allowedTime === 'object' && allowedTime !== null) {
                            Object.values(allowedTime).forEach((t) => {
                                if (t && t.trim() !== '') {
                                    times[t] = t;
                                }
                            });
                        }

                        break; // stop after first match
                    }
                }

                populateTimeSelect(times);

                
                if(tf_params.tour_form_data.tf_tour_selected_template === 'design-2') {
                    dateSetToFields(selectedDates, instance);
                }
            },

        };
        
        
        if (!tf_params.tour_form_data.is_all_unavailable && typeof tf_params.tour_form_data.tour_availability === 'object' && tf_params.tour_form_data.tour_availability && Object.keys(tf_params.tour_form_data.tour_availability).length > 0) {
            tour_date_options.minDate = "today";
            tour_date_options.disableMobile = "true";
            tour_date_options.enable = Object.entries(tf_params.tour_form_data.tour_availability)
            .filter(([dateRange, data]) => data.status === "available")
            .map(([dateRange, data]) => {
                const [fromRaw, toRaw] = dateRange.split(' - ').map(str => str.trim());

                const today = new Date();
                const formattedToday = today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate();
                let fromDate = fromRaw;

                return {
                    from: fromDate,
                    to: toRaw
                };
            });
        }else{
            tour_date_options.minDate = "today";
        }

        tour_date_options.disable = [];
        if (tf_params.tour_form_data.is_all_unavailable && typeof tf_params.tour_form_data.tour_availability === 'object' && tf_params.tour_form_data.tour_availability && Object.keys(tf_params.tour_form_data.tour_availability).length > 0) {
            tour_date_options.disable = Object.entries(tf_params.tour_form_data.tour_availability)
            .filter(([dateRange, data]) => data.status === "unavailable")
            .map(([dateRange, data]) => {
                const [fromRaw, toRaw] = dateRange.split(' - ').map(str => str.trim());

                const today = new Date();
                const formattedToday = today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate();
                let fromDate = fromRaw;

                return {
                    from: fromDate,
                    to: toRaw
                };
            });
        }

        if (tf_params.tour_form_data.disable_same_day) {
            tour_date_options.disable.push("today");
        }

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

        if ($('#tour-location').length) {
            const map = L.map('tour-location').setView([tf_params.tour_form_data.location_latitude, tf_params.tour_form_data.location_longitude], tf_params.tour_form_data.location_zoom);
            
            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.tour_form_data.location_latitude, tf_params.tour_form_data.location_longitude], {alt: tf_params.tour_form_data.location}).addTo(map)
                .bindPopup(tf_params.tour_form_data.location);
        }

    });

})(jQuery, window);