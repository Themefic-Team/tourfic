(function ($, win) {
    $(document).ready(function () {

        //###############################
        //         Ajax Functions       #
        //###############################
        
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
            // for (var value of formData.values()) {
            //     console.log(value);
            // }

            // Tour Extra
            var tour_extra_total = 0;
            jQuery('.tour-extra-single input:checkbox:checked').each(function(){
                tour_extra_total += isNaN(parseInt(jQuery(this).val())) ? 0 : parseInt(jQuery(this).val());
            });     
            formData.append('tour_extra_total', tour_extra_total);

            var tour_extra_title = $(".tour-extra-single input:checkbox:checked").map(function () {
                return $(this).data('title')
            }).get();
            formData.append('tour_extra_title', tour_extra_title);

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

        /**
         * Hotel room availability
         */
        $(document).on('click', '#tf-single-hotel-avail .tf-submit', function(e) {
            e.preventDefault();

            if($.trim($('input[name=check-in-out-date]').val()) == ''){
                $('.tf_booking-dates .tf_label-row').append('<span clss="required"><b>This field is required!</b></span>');
                return;
            }

            var tf_room_avail_nonce = $("input[name=tf_room_avail_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            var adult = $('select[name=adults] option').filter(':selected').val();
            var child = $('select[name=children] option').filter(':selected').val();
            var check_in_out = $('input[name=check-in-out-date]').val();
            //console.log(post_id);

            var data = {
                action: 'tf_room_availability',
                tf_room_avail_nonce: tf_room_avail_nonce,
                post_id: post_id,
                adult: adult,
                child: child,
                check_in_out: check_in_out,
            };

            jQuery.ajax({
                url: tf_params.ajax_url,
                type: 'post',
                data: data,
                success: function (data) {
                    $('html, body').animate({
                        scrollTop: $("#rooms").offset().top
                    }, 2000);
                    //console.log(data);
                    $("#rooms").html(data);
                },
                error: function (jqXHR, exception) {
                    var error_msg = '';
                    if (jqXHR.status === 0) {
                        var error_msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        var error_msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        var error_msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        var error_msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        var error_msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        var error_msg = 'Ajax request aborted.';
                    } else {
                        var error_msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(error_msg);
                }
            });

        });

        /**
         * Click to go back to availability form
         */
         $(document).on('click', '.hotel-room-availability', function(e) {
            e.preventDefault();

            $('html, body').animate({
                scrollTop: $("#tf-single-hotel-avail").offset().top
            }, 2000);
        });   

        /**
         * Ajax hotel booking
         * 
         * tf_hotel_booking
         */
        $(document).on('click', '.tf-room-book', function (e) {
            e.preventDefault();

            var $this = $(this);

            var tf_room_booking_nonce = $("input[name=tf_room_booking_nonce]").val();
            var post_id = $('input[name=post_id]').val();
            var room_id = $(this).closest('.room-submit-wrap').find('input[name=room_id]').val();
            var location = $('input[name=location]').val();
            var adult = $('input[name=adult]').val();
            var child = $('input[name=child]').val();
            var check_in_date = $('input[name=check_in_date]').val();
            var check_out_date = $('input[name=check_out_date]').val();
            var room = $(this).closest('.pricing').find('select[name=room-selected] option').filter(':selected').val();
            //console.log(post_id);

            var data = {
                action: 'tf_hotel_booking',
                tf_room_booking_nonce: tf_room_booking_nonce,
                post_id: post_id,
                room_id: room_id,
                location: location,
                adult: adult,
                child: child,
                check_in_date: check_in_date,
                check_out_date: check_out_date,
                room: room,
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

        sbp.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickPrev');
        });

        sbn.on("click", function () {
            $(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickNext');
        });

        /**
         * Rating bar
         */
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

        $(window).load(function () {
            // Trigger Animation
            jQuery('[data-width]').each(function () {  
                var $this = jQuery(this);   
                var width = $this.attr('data-width');
               
                $this.inViewport(function(px) {
                    if( px > 0 ) {
                        $this.css('width', +width+'%');
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
        //fullwidthInit("[data-fullwidth=true]");

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
            setTimeout(function () { $('button#share_link_button').removeClass('copied'); }, 3000);
            $(this).parent().find("#share_link_input").select();
            document.execCommand("copy");
        });

        /**
         * Toggle FAQ
         */
        $('.faq-head').click(function (e) {
            $(this).parent().toggleClass('active').find('.faq-content').slideToggle('fast');
        });

        /**
         * Toggle Itinerary
         */
         $('.itinerary-head').on('click', function (e) {
            $(this).parent().toggleClass('active').find('.itinerary-content').slideToggle('fast');
        });

        /**
         * Related Tour
         * 
         * Slick
         */
        $('.tf-suggestion-items-wrapper').slick({
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


    });
})(jQuery, window);