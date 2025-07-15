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