(function ($) {
    'use strict';

    $(document).ready(function () {
        var notyf = new Notyf({
            duration: 3000,
            dismissable: true
        });

        // Date picker
        var tour_checkin_input  = $(".tf-tour-check-in");
        var tour_checkin_text   = $(".checkin-date-text");
        var tour_checkout_input = $(".tf-tour-check-out");
        var tour_checkout_text  = $(".checkout-date-text");
        var optional_config     = {
                mode      : "range",
                minDate   : "today",
                dateFormat: "Y/m/d",
                allowInput: true,
                onChange  : function (selectedDates, dateStr, instance) {                    
                   if (selectedDates[0] != null) {
                       tour_checkin_input.attr('value', selectedDates[0].toLocaleDateString())
                       tour_checkin_text.text(selectedDates[0].toLocaleDateString())
                   }
                   if (selectedDates[1] != null) {
                        tour_checkout_input.attr('value', selectedDates[1].toLocaleDateString())
                        tour_checkout_text.text(selectedDates[1].toLocaleDateString())
                   }
                   
                },
        }
        $('#tf-tour-date-field').flatpickr(optional_config)

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
                $('.child-text').text(thisVal + " Children");
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


        /**
         * Search Result Sidebar Ajax
         */
        var filter_xhr;
        // Creating a function for reuse this filter in any where we needs.
        const makeFilter = () => { 
            var dest = $('#tf-place').val();
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var checked = $('#check-in-out-date').val();
            // @KK split date range into dates
            var checkedArr = checked.split(' to ');
            var checkin = checkedArr[0];
            var checkout = checkedArr[1];
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
            formData.append('checked', checked);

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
                },
                success: function (data) {
                    $('.archive_ajax_result').unblock();

                    $('.archive_ajax_result').html(data);
                    // @KK show notice in every success request 
                    notyf.success('Results refreshed successfully');
                },
                error: function (data) {
                    console.log(data);
                },

            });
        };
        // @KK Look for submission and change on filter widgets
        $(document).on('submit', '#tf-widget-booking-search', function (e) {
            e.preventDefault();
            makeFilter()
        });
        $(document).on('change', '[name*=tf_filters],[name*=tf_features]', function () {
            makeFilter();
        })

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
        if(avg_rating){
            $('.reviews span').html(avg_rating);
        }else{
            $('.reviews span').html("0/5");
        }

        $(".tf-travel-text h4").click(function(){
            $(this).siblings('.tf-travel-contetn').slideToggle();
            $(this).parents('.tf-travel-itinerary-item').siblings().find('.tf-travel-contetn').slideUp();
        });
        $(".tf-faq-title").click(function(){
            $(this).siblings('.tf-faq-desc').slideToggle();
            $(this).parents('.tf-faq-item').siblings().find('.tf-faq-desc').slideUp();
        });

        
        $(".tf-header-menu-triger").click(function(){
            $('.tf-header-menu-wrap').slideToggle();
        });

    });

})(jQuery);
