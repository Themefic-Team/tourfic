; (function ($, win) {
    $.fn.inViewport = function (cb) {
        return this.each(function (i, el) {
            function visPx() {
                var H = $(this).height(),
                    r = el.getBoundingClientRect(), t = r.top, b = r.bottom;
                return cb.call(el, Math.max(0, t > 0 ? H - t : (b < H ? b : H)));
            } visPx();
            $(win).on("resize scroll", visPx);
        });
    };
}(jQuery, window));

(function ($) {
    'use strict';

    $(document).ready(function () {
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
            asNavFor: '.tf_slider-nav',
            variableWidth: true
        });

        $('.single-slider-wrapper .tf_slider-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.tf_slider-for',
            dots: false,
            arrows: false,
            centerMode: true,
            focusOnSelect: true
        });

        $('.tf-hero-slider-wrapper').slick({
            arrows: true,
            fade: false,
            prevArrow: '<button class="tf-hero-slider-arrow slide-arrow prev-arrow"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button class="tf-hero-slider-arrow slide-arrow next-arrow"><i class="fas fa-chevron-right"></i></button>'
        });

        $('.tf-tourbox').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
            fade: false,
            prevArrow: '<button class="tf-tourbox-arrow prev-arrow"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button class="tf-tourbox-arrow next-arrow"><i class="fas fa-chevron-right"></i></button>'
        });

        $('.tf-custom-review-slider-area').slick({
            arrows: true,
            fade: false,
            slidesToShow: 3,
            prevArrow: '<button class="tf-cr-slider-arrow slide-arrow prev-arrow"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button class="tf-cr-slider-arrow slide-arrow next-arrow"><i class="fas fa-chevron-right"></i></button>'
        });

        
        $(".tf-hero-btm-icon").on('click',function () {
            $(".tf-hero-slider-fixed").addClass("show");
        });

        $(".tf-hero-slider-cross-icon").on('click',function () {
            $(".tf-hero-slider-fixed").removeClass("show");
        });

        sbp.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickPrev');
        });

        sbn.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickNext');
        });

        // Tab controlling
        $('.tf_tab-nav a').on('click', function (e) {
            e.preventDefault();
            var targetDiv = $(this).attr('href');

            if (!$(this).hasClass('active')) {
                $(this).addClass('active').siblings().removeClass('active');
            }
            $('.tf-tab-container').find(targetDiv).addClass('active').siblings().removeClass('active');

        });

        // FullWidth Container JS
        function fullwidthInit(selector) {
            function fullWidth(selector) {
                $(selector).each(function () {
                    $(this).width("100%").css({ marginLeft: "-0px" });

                    var window_width = $(window).width();

                    var left_margin = "-" + $(this).offset().left + "px";

                    $(this).width(window_width).css({ marginLeft: left_margin });
                    console.log("Width:", window_width, "Margin Left:", left_margin);
                });
            }
            $(window).on("resize load", function () {
                fullWidth(selector);
            });
        }

        // Usage DOM: <div data-fullwidth="true">...</div> in JS: fullwidthInit("[data-fullwidth=true]");
        fullwidthInit("[data-fullwidth=true]");

        // Share copy
        $('button#share_link_button').click(function () {

            $(this).addClass('copied');
            setTimeout(function () { $('button#share_link_button').removeClass('copied'); }, 3000);
            $(this).parent().find("#share_link_input").select();
            document.execCommand("copy");
        });

        // Toggle
        $('[data-toggle="true"]').click(function (e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).slideToggle('fast');
        });

        $('.faq-head').click(function (e) {
            $(this).parent().toggleClass('active').find('.faq-content').slideToggle('fast');
        });

        //Itinerary accordion
        $('.itinerary-head').on('click', function (e) {
            $(this).parent().toggleClass('active').find('.itinerary-content').slideToggle('fast');
        });


        // Date picker
        var dateToday = new Date();
        var checkin_input = jQuery("#check-in-date"),
            checkout_input = jQuery("#check-out-date");
        var hotel_checkin_input = jQuery(".tf-hotel-check-in");
        var hotel_checkout_input = jQuery(".tf-hotel-check-out");

        var dateFormat = 'DD-MM-YYYY';

        // Trigger Check-in Date
        $('.tf_selectdate-wrap, #check-in-out-date').daterangepicker({
            "locale": {
                "format": dateFormat,
                "separator": " - ",
                "firstDay": 1
            },
            minDate: dateToday,
            autoApply: true,
        }, function (start, end, label) {
            checkin_input.val(start.format(dateFormat));
            hotel_checkin_input.val(start.format(dateFormat));
            $('.checkin-date-text').text(start.format(dateFormat));

            checkout_input.val(end.format(dateFormat));
            hotel_checkout_input.val(end.format(dateFormat));
            $('.checkout-date-text').text(end.format(dateFormat));
        });

        var fixedCheckIn = $('.tf-tour-booking-wrap').data('fixed-check-in');
        var fixedCheckOut = $('.tf-tour-booking-wrap').data('fixed-check-out');
        if (fixedCheckIn) {
            fixedCheckIn = new Date(fixedCheckIn);
        } else {
            fixedCheckIn = false;
        }
        if (fixedCheckOut) {
            fixedCheckOut = new Date(fixedCheckOut);
        } else {
            fixedCheckOut = false;
        }

        
        var checkin_input = jQuery(".tf_tours_date-wrap #check-in-date"),
            checkout_input = jQuery(".tf_tours_date-wrap #check-out-date");
        $('.tours-check-in-out').daterangepicker({
            "locale": {
                "format": dateFormat,
                "separator": " - ",
                "firstDay": 1
            },
            minDate: fixedCheckIn,
            maxDate: fixedCheckOut,
            autoApply: true,
        }, function (start, end, label) {
            checkin_input.val(start.format(dateFormat));
            $('.checkin-date-text').text(start.format(dateFormat));
            $('#check-in-date').val(start.format(dateFormat));
            $('#check-out-date').val(start.format(dateFormat));
            checkout_input.val(end.format(dateFormat));
            $('.checkout-date-text').text(end.format(dateFormat));
        });

        //position fixed of sticky tour booking form
        $(window).scroll(function(){
            var sticky = $('.tf-tour-booking-wrap'),
                scroll = $(window).scrollTop();
          
            if (scroll >= 800) sticky.addClass('tf-tours-fixed');
            else sticky.removeClass('tf-tours-fixed');
          });

        // Number Decrement
        $('.acr-dec').on('click', function (e) {

            var input = $(this).parent().find('input');
            var min = input.attr('min');

            if (input.val() > min) {
                input.val(input.val() - 1).change();
            }

        });

        // Number Increment
        $('.acr-inc').on('click', function (e) {
            var input = $(this).parent().find('input');
            input.val(parseInt(input.val()) + 1).change();
        });

        // Adults change trigger
        $(document).on('change', '#adults', function () {
            var thisVal = $(this).val();

            if (thisVal > 1) {
                $('.adults-text').text(thisVal + " Adults");
            } else {
                $('.adults-text').text(thisVal + " Adult");
            }

        });

        // Children change trigger
        $(document).on('change', '#children', function () {
            var thisVal = $(this).val();

            if (thisVal > 1) {
                $('.child-text').text(thisVal + " Children");
            } else {
                $('.child-text').text(thisVal + " Child");
            }

        });

         // Infant change trigger
         $(document).on('change', '#infant', function () {
            var thisVal = $(this).val();

            if (thisVal > 1) {
                $('.infant-text').text(thisVal + " Infants");
            } else {
                $('.infant-text').text(thisVal + " Infant");
            }

        });

        // Room change trigger
        $(document).on('change', '#room', function () {
            var thisVal = $(this).val();

            if (thisVal > 1) {
                $('.room-text').text(thisVal + " Rooms");
            } else {
                $('.room-text').text(thisVal + " Room");
            }
        });

        // Adult, Child, Room Selection toggle
        $(document).on('click', '.tf_selectperson-wrap .tf_input-inner,.tf_person-selection-wrap .tf_person-selection-inner', function () {
            $('.tf_acrselection-wrap').slideToggle('fast');
        });

        jQuery(document).on("click", function (event) {

            if (!jQuery(event.target).closest(".tf_selectperson-wrap").length) {
                jQuery(".tf_acrselection-wrap").slideUp("fast");

            }
        });

        // Comment Reply Toggle
        $(document).on('click', '#reply-title', function () {
            var $this = $(this);
            $('#commentform').slideToggle('fast', 'swing', function () {
                $this.parent().toggleClass('active');
            });
        });

        // Smooth scroll to id
        $(".reserve-button a").click(function () {
            $('html, body').animate({
                scrollTop: $("#rooms").offset().top - 32
            }, 1000);
        });

        //Popup

        $('.tf_slider-for').magnificPopup({
            delegate: 'div a',
            type: 'image',
            fixedContentPos: true,
            closeOnBgClick: true,
            alignTop: false,
            tLoading: 'Loading image #%curr%...',
            mainClass: 'tourfic-popup-wrapper',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
            },
            callbacks: {
                open: function () {
                    // Will fire when this exact popup is opened
                    // this - is Magnific Popup object

                    jQuery('#elementor-lightbox-slideshow-single-img').addClass('dialog-type-lightboxjhfsjdfhreuru');
                },
                close: function () {
                    // Will fire when popup is closed
                }
                // e.t.c.
            }
        });

        // Ask question
        $(document).on('click', '#tf-ask-question-trigger', function (e) {
            e.preventDefault();
            $('#tf-ask-question').fadeIn().find('.response').html("");
        });

        // Close Ask question
        $(document).on('click', 'span.close-aq', function () {
            $('#tf-ask-question').fadeOut();
        });

        // Ask question Submit
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

                    $this.find('.response').html("Sending your question...");
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



        // Change view
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

        // Change view
        
        var filter_xhr;
        $(document).on('change', '[name*=tf_filters],[name*=tf_features], #destination, #adults, #room, #children, #check-in-date, #check-out-date, #check-in-out-date', function () {
            var dest = $('#destination').val();
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var checkin = $('#check-in-date').val();
            var checkout = $('#check-out-date').val();
            var posttype = $('.tf-post-type').val();

            var filters = [];

            $('[name*=tf_filters]').each(function () {
                if ($(this).is(':checked')) {
                    filters.push($(this).val());
                }
            });
            var filters = filters.join();

            var features = [];

            $('[name*=tf_features]').each(function () {
                if ($(this).is(':checked')) {
                    features.push($(this).val());
                }
            });
            var features = features.join();

            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('type', posttype);
            formData.append('dest', dest);
            formData.append('adults', adults);
            formData.append('room', room);
            formData.append('children', children);
            formData.append('checkin', checkin);
            formData.append('checkout', checkout);
            formData.append('filters', filters);
            formData.append('features', features);

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

                },
                complete: function (data) {
                    $('.archive_ajax_result').unblock();
                    console.log(posttype,dest);
                },
                success: function (data) {
                    $('.archive_ajax_result').unblock();

                    $('.archive_ajax_result').html(data);
                },
                error: function (data) {
                    console.log(data);
                },

            });

        });

    });

    $(window).load(function () {

        // Trigger Animation
        jQuery('[data-width]').each(function () {

            var $this = jQuery(this);

            var width = $this.attr('data-width');
            /*
                        $this.inViewport(function(px) {
                            if( px > 0 ) {
                                $this.css('width', +width+'%');
                            } else {
                                $this.css('width', '0%');
                            }
                        });
            */
        });

    });

})(jQuery);

// Ajax Scripts
(function ($) {
    'use strict';

    $(document).ready(function () {

        // Email Capture
        $(document).on('submit', 'form.tf-room', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_room_booking');

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

                    $('.tf_notice_wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status == 'error') {
                        var errorHtml = "";

                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                errorHtml += '<div class="woocommerce-error">' + text + '</div>';
                            });
                        }

                        $('.tf_notice_wrapper').html(errorHtml).show();

                        $("html, body").animate({ scrollTop: 0 }, 300);
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
    });

})(jQuery);

// Ajax Scripts for tour booking
(function ($) {
    'use strict';

    $(document).ready(function () {

        // Email Capture
        $(document).on('submit', 'form.tf_tours_booking', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_tours_booking');

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

                    $('.tf_notice_wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status == 'error') {
                        var errorHtml = "";

                        if (response.errors) {
                            response.errors.forEach(function (text) {
                                errorHtml += '<div class="woocommerce-error">' + text + '</div>';
                            });
                        }

                        $('.tf_notice_wrapper').html(errorHtml).show();

                        $("html, body").animate({ scrollTop: 0 }, 300);
                        return false;
                    } else {

                        if (response.redirect_to) {
                            window.location.replace(response.redirect_to);
                        } else {
                            jQuery(document.body).trigger('added_to_cart');
                        }

                    }
                    console.log(response);
                },
                error: function (data) {
                    console.log(data);

                },

            });

        });
    });

})(jQuery);
// Infinite Scroll
(function ($) {
    'use strict';

    $(document).ready(function () {

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
                    flag = true;
                },
                success: function (data) {
                    //console.log(data);
                    $('.archive_ajax_result').append($('.archive_ajax_result', data).html());

                    $('.tf_posts_navigation').html($('.tf_posts_navigation', data).html());

                    //document.title = $(data).filter('title').text();

                    flag = false;

                    $(document).find('.tf_posts_navigation').removeClass('loading');

                }
            });

            //console.log(main_xhr);
        };

        // Feed Ajax Trigger
        $(document).on('click', '.tf_posts_navigation a.next.page-numbers', function (e) {
            e.preventDefault();

            var targetUrl = (e.target.href) ? e.target.href : $(this).context.href;
            amPushAjax(targetUrl);
            window.history.pushState({ url: "" + targetUrl + "" }, "", targetUrl);
        });
        // End Feed Ajax Trigger

        // Feed Click Trigger
        $(window).on('scroll', function (e) {
            $('.tf_posts_navigation a.next.page-numbers').each(function (i, el) {

                var $this = $(this);

                var H = $(window).height(),
                    r = el.getBoundingClientRect(),
                    t = r.top,
                    b = r.bottom;

                var tAdj = parseInt(t - (H / 2));

                if (flag === false && (H >= tAdj)) {
                    //console.log( 'inview' );
                    $this.trigger('click');
                } else {
                    //console.log( 'outview' );
                }
            });
        });
        // End Feed Click Trigger

        //Ratings copy/move under gallery
        var avg_rating = $('.tf-overall-ratings .overall-rate').text();
        $('.tf_tours-title-area .reviews span').html(avg_rating);

    });

})(jQuery);


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
        if (!val) { return false; }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        var $notfound = [];
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                $notfound.push('found');

                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function (e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
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

            b.innerHTML += 'Not Found';
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

var destinations = tf_params.destinations;
var tour_destinations = tf_params.tour_destinations;

//Autocomplete for Hotel
tourfic_autocomplete(document.getElementById("destination"), destinations);
//Autocomplete for Tours
tourfic_autocomplete(document.getElementById("tour_destination"), tour_destinations);

/**
 * Searchbox widgets tab scripts
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
    evt.currentTarget.className += " active";
}
jQuery('#tf-hotel-booking-form').css('display','block');

