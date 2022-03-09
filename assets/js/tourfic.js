(function ($, win) {
    $(document).ready(function () {

        //###############################
        //         Hotel Functions      #
        //###############################

        /**
         * Hotel room availability
         * 
         * Ajax
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
         * Click to go back to hotel availability form
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
         $(document).on('click', '.hotel-room-book', function (e) {
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
            var room = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
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

        //###############################
        //         Tour Functions       #
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

        /**
         * Wishlist Functionality 
         * 
         */
        // Create an instance of Notyf
        const notyf = new Notyf({
            ripple: true,
            dismissable: true,
            position: {
                x: 'right',
                y: 'bottom',
            },
        });


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
            } return false;
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
            targetNode.addClass('fas');
            targetNode.addClass('tf-text-red');
            targetNode.removeClass('far');
            targetNode.removeClass('add-wishlist');


        }
        /* blank icon */
        const wishIcon = targetNode => {
            targetNode.addClass('add-wishlist');
            targetNode.addClass('far');
            targetNode.removeClass('fas');
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

            /* For logged in user */
            if ($('body').hasClass('logged-in')) {
                data.action = 'tf_add_to_wishlists';
                data.nonce = targetNode.data('nonce');
                $.post(tf_params.ajax_url, data,
                    function (data) {
                        if (data.success) {
                            wishIconFill(targetNode);
                            notyf.success({
                                message: data.data + '. <a class="wish-button" href="'+wishPageUrl+'">'+wishPageTitle+'</a>',
                                duration: 4e3
                            });
                        }
                    },
                );
            } else {
                /* For guest */
                if (addWish(data) === true) {
                    wishIconFill(targetNode);
                    notyf.success({
                        message: 'Item added to wishlist. <a class="wish-button" href="'+wishPageUrl+'">'+wishPageTitle+'</a>',
                        duration: 4e3
                    });
                } else notyf.error('Item not added to wishlist');

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
                let data = { id, action: 'tf_remove_wishlist', type, nonce: targetNode.data('nonce') }
                $.get(tf_params.ajax_url, data,
                    function (data) {
                        if (data.success) {
                            if (tf_params.single != '1') {
                                tableNode.closest('.tf-wishlists').html(data.data);
                            }
                            wishIcon(targetNode);
                            notyf.success('Item removed from wishlist');
                        }
                    }
                );

            } else {
                /* For guest */
                if (removeWish(id) == true) {
                    wishIcon(targetNode);
                    notyf.success('Item removed from wishlist');
                } else {
                    notyf.error('Item not removed from wishlist');
                };
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
                            inp.closest('input').nextElementSibling.value = source.dataset.slug
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

        /**
         * Initiate autocomplete on inputs
         */

        // Hotel location autocomplete
        var hotel_location_input = document.getElementById("tf-location");
        var hotel_locations = tf_params.locations;
        if(hotel_location_input){
            tourfic_autocomplete(hotel_location_input, hotel_locations);
        }
        // Tour destination autocomplete
        var tour_destination_input = document.getElementById("tf-destination");
        var tour_destinations = tf_params.tour_destinations;
        if(tour_destination_input){
            tourfic_autocomplete(tour_destination_input, tour_destinations);
        }


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
    evt.currentTarget.className += " active";
}
jQuery('#tf-hotel-booking-form').css('display','block');