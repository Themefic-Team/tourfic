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
         * Scroll to Review Section
         */
        $(".tf-top-review a").click(function () {
            $('html, body').animate({
                scrollTop: $("#tf-review").offset().top - 32
            }, 1000);
        });

        /**
         * Scroll to Map Section
         */
        $(".tf-map-link a").click(function () {
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
        const makeFilter = () => {
            var dest = $('#tf-place').val();
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var infant = $('#infant').val();
            var checked = $('#check-in-out-date').val();
            var startprice = $('.widget_tf_price_filters input[name="from"]').val();
            var endprice = $('.widget_tf_price_filters input[name="to"]').val();
            var tf_author = $('#tf_author').val();
            // split date range into dates
            var checkedArr = checked.split(' - ');
            var checkin = checkedArr[0];
            var checkout = checkedArr[1];
            var posttype = $('.tf-post-type').val();

            if ($.trim(checkin) === '' && tf_params.date_hotel_search && posttype === 'tf_hotel') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_tour_search && posttype === 'tf_tours') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            if ($.trim(checkin) === '' && tf_params.date_apartment_search && posttype === 'tf_apartment') {

                if ($('#tf-required').length === 0) {
                    $('.tf_booking-dates .tf_label-row').append('<span id="tf-required" class="required" style="color:white;"><b>' + tf_params.field_required + '</b></span>');
                }
                return;
            }

            let filters = termIdsByFeildName('tf_filters');
            let tfHotelTypes = termIdsByFeildName('tf_hotel_types');
            let features = termIdsByFeildName('tf_features');
            let tour_features = termIdsByFeildName('tour_features');
            let attractions = termIdsByFeildName('tf_attractions');
            let activities = termIdsByFeildName('tf_activities');
            let tfTourTypes = termIdsByFeildName('tf_tour_types');
            let tfApartmentFeatures = termIdsByFeildName('tf_apartment_features');
            let tfApartmentTypes = termIdsByFeildName('tf_apartment_types');

            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('type', posttype);
            formData.append('dest', dest);
            formData.append('adults', adults);
            formData.append('room', room);
            formData.append('children', children);
            formData.append('infant', infant);
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
            if (startprice) {
                formData.append('startprice', startprice);
            }
            if (endprice) {
                formData.append('endprice', endprice);
            }
            if (tf_author) {
                formData.append('tf_author', tf_author);
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
                        $('.tf-total-results').find('span').html(postsCount);
                    }

                },
                success: function (data, e) {
                    $('.archive_ajax_result').unblock();
                    $('#tf_ajax_searchresult_loader').hide();
                    $('.archive_ajax_result').html(data);
                    // @KK show notice in every success request
                    notyf.success(tf_params.ajax_result_success);
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
        $(document).on('click', '.tf_posts_ajax_navigation a.page-numbers', function (e) {
            e.preventDefault();
            page = tf_page_pagination_number($(this).clone());
            paginationMakeFilter(page);
        });

        // Creating a function for reuse this filter in any where we needs.
        const paginationMakeFilter = (page) => {
            var dest = $('#tf-place').val();
            var page = page;
            var adults = $('#adults').val();
            var room = $('#room').val();
            var children = $('#children').val();
            var checked = $('#check-in-out-date').val();
            var startprice = $('.widget_tf_price_filters input[name="from"]').val();
            var endprice = $('.widget_tf_price_filters input[name="to"]').val();
            var tf_author = $('#tf_author').val();
            // split date range into dates
            var checkedArr = checked.split(' - ');
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

            var formData = new FormData();
            formData.append('action', 'tf_trigger_filter');
            formData.append('type', posttype);
            formData.append('page', page);
            formData.append('dest', dest);
            formData.append('adults', adults);
            formData.append('room', room);
            formData.append('children', children);
            formData.append('checkin', checkin);
            formData.append('checkout', checkout);
            formData.append('filters', filters);
            formData.append('tf_hotel_types', tfHotelTypes);
            formData.append('features', features);
            formData.append('tour_features', tour_features);
            formData.append('attractions', attractions);
            formData.append('activities', activities);
            formData.append('tf_tour_types', tfTourTypes);
            formData.append('tf_apartment_features', tfApartmentFeatures);
            formData.append('tf_apartment_types', tfApartmentTypes);
            formData.append('checked', checked);
            if (startprice) {
                formData.append('startprice', startprice);
            }
            if (endprice) {
                formData.append('endprice', endprice);
            }
            if (tf_author) {
                formData.append('tf_author', tf_author);
            }
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

                    if ($.trim(checkin) !== '') {
                        $('.tf_booking-dates .tf_label-row').find('#tf-required').remove();
                    }
                },
                complete: function (data) {
                    $('.archive_ajax_result').unblock();

                    // total posts 0 if not found by @hena
                    if ($('.tf-nothing-found')[0]) {
                        $('.tf_posts_navigation').hide();
                        var foundPosts = $('.tf-nothing-found').data('post-count');
                        $('.tf-total-results').find('span').html(foundPosts);
                    } else {
                        $('.tf_posts_navigation').show();
                        var postsCount = $('.tf-posts-count').html();
                        $('.tf-total-results').find('span').html(postsCount);
                    }

                },
                success: function (data, e) {
                    $('.archive_ajax_result').unblock();
                    $('.archive_ajax_result').html(data);
                    // @KK show notice in every success request
                    notyf.success(tf_params.ajax_result_success);
                },
                error: function (data) {
                    console.log(data);
                },

            });
        };

        // Look for submission and change on filter widgets
        $(document).on('submit', '#tf-widget-booking-search', function (e) {
            e.preventDefault();
            makeFilter()
        });
        $(document).on('change', '[name*=tf_filters],[name*=tf_hotel_types],[name*=tf_features],[name*=tour_features],[name*=tf_attractions],[name*=tf_activities],[name*=tf_tour_types],[name*=tf_apartment_features],[name*=tf_apartment_types]', function () {
            makeFilter();
        })

        // Archive Page Filter
        $(document).on('submit', '.tf_archive_search_result', function (e) {
            e.preventDefault();
            makeFilter()
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

        $(window).load(function () {
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
        $('.share-toggle[data-toggle="true"]').click(function (e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).slideToggle('fast');
        });

        // Copy button
        $('button#share_link_button').click(function () {

            $(this).addClass('copied');
            setTimeout(function () {
                $('button#share_link_button').removeClass('copied');
            }, 3000);
            $(this).parent().find("#share_link_input").select();
            document.execCommand("copy");
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

        // Design 3 related tour
        $('.tf-design-3-slider-items-wrapper').slick({
            dots: false,
            arrows: true,
            infinite: true,
            speed: 300,
            autoplaySpeed: 2000,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow:'<button type="button" class="slick-prev pull-left"><svg xmlns="http://www.w3.org/2000/svg" width="49" height="24" viewBox="0 0 49 24" fill="none"><path d="M8.32843 10.997H44.5V12.997H8.32843L13.6924 18.3609L12.2782 19.7751L4.5 11.997L12.2782 4.21875L13.6924 5.63296L8.32843 10.997Z" fill="#B58E53"/></svg></button>',
            nextArrow:'<button type="button" class="slick-next pull-right"><svg xmlns="http://www.w3.org/2000/svg" width="49" height="24" viewBox="0 0 49 24" fill="none"><path d="M40.6716 10.997H4.5V12.997H40.6716L35.3076 18.3609L36.7218 19.7751L44.5 11.997L36.7218 4.21875L35.3076 5.63296L40.6716 10.997Z" fill="#B58E53"/></svg></button>',
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
            targetNode.addClass('fa-heart');
            targetNode.addClass('tf-text-red');
            targetNode.removeClass('fa-heart-o');
            targetNode.removeClass('add-wishlist');


        }
        /* blank icon */
        const wishIcon = targetNode => {
            targetNode.addClass('add-wishlist');
            targetNode.addClass('fa-heart-o');
            targetNode.removeClass('fa-heart');
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
                            console.log(source.dataset.slug);
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
                            console.log(source.dataset.slug);
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
        $(document).on('click', '.tf_selectperson-wrap .tf_input-inner,.tf_person-selection-wrap .tf_person-selection-inner', function () {
            $('.tf_acrselection-wrap').slideToggle('fast');
        });
        // Close
        jQuery(document).on("click", function (event) {
            if (!jQuery(event.target).closest(".tf_selectperson-wrap").length) {
                jQuery(".tf_acrselection-wrap").slideUp("fast");
            }
        });

        /**
         * Number/text change horizontal search form
         */
        // Number Increment
        $('.acr-inc').on('click', function (e) {
            var input = $(this).parent().find('input');
            var max = input.attr('max') ? input.attr('max') : 999;
            var step = input.attr('step') ? input.attr('step') : 1;

            if (input.val() < max) {
                input.val(parseInt(input.val()) + parseInt(step)).change();
            }
            // input focus disable
            input.blur();
        });

        // Number Decrement
        $('.acr-dec').on('click', function (e) {

            var input = $(this).parent().find('input');
            var min = input.attr('min');
            var step = input.attr('step') ? input.attr('step') : 1;

            if (input.val() > min) {
                input.val(input.val() - parseInt(step)).change();
            }
        });

        // Adults change trigger
        $(document).on('change', '#adults', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.adults-text').text(thisVal + " " + tf_params.adult);
            }

        });

        // Children change trigger
        $(document).on('change', '#children', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.child-text').text(thisVal + " " + tf_params.children);
            }

        });

        // Infant change trigger
        $(document).on('change', '#infant', function () {
            let thisEml = $(this);
            let thisVal = thisEml.val();

            if (thisVal > 1) {
                thisEml.closest('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
            } else {
                thisEml.closest('.tf_selectperson-wrap').find('.infant-text').text(thisVal + " " + tf_params.infant);
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

        /**
         * Ask question
         */
        $(document).on('click', '#tf-ask-question-trigger', function (e) {
            e.preventDefault();
            $('#tf-ask-question').fadeIn().find('.response').html("");
        });

        // Close Ask question
        $(document).on('click', 'span.close-aq', function () {
            $('#tf-ask-question').fadeOut();
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

        $(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner").click(function () {
            $('.tf-more-info').toggleClass('show');
        });
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner, .tf-more-info").length) {
                $('.tf-more-info').removeClass('show');
            }
        });

        // FAQ Accordion
        $('.tf-faq-title').click(function () {
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
        $('.tf-faq-collaps').click(function () {
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
        $(document).on('click', '.tf-tablinks', function (e) {
            let formId = $(this).data('form-id');
            tfOpenForm(event, formId);
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
            $this.parent('.tf-filter').find('.filter-item').filter(function (index) {
                return index > 3;
            }).removeClass("hidden");
            $this.hide();

            $this.parent('.tf-filter').find('.see-less').show();
        });

        /* see less checkbox filter started */

        $('a.see-less').on('click', function (e) {
            var $this = $(this);
            e.preventDefault();
            $this.parent('.tf-filter').find('.filter-item').filter(function (index) {
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
            $(this).find('.filter-item').filter(function (index) {
                return index > 3;
            }).addClass("hidden");

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
        $(document).on('click', '.tf-traveller-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-single-travel').each(function () {
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
                tf_hasErrorsFlag = true;
                return false;
            }
            tf_hasErrorsFlag = false;
        });

        // Booking Confirmation Form Validation
        $(document).on('click', '.tf-book-confirm-error', function (e) {
            let hasErrors = [];
            let $this = $(this).closest('.tf-withoutpayment-booking');
            $('.error-text').text("");
            $this.find('.tf-confirm-fields').each(function () {
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
                    $('.tf-booking-step-' + i).addClass("done");
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
            $('[name*=tf-tour-extra]').each(function () {
                if ($(this).is(':checked')) {
                    extras.push($(this).val());
                }
            });
            var extras = extras.join();
            var data = {
                action: 'tf_tour_booking_popup',
                post_id: post_id,
                adults: adults,
                children: children,
                infant: infant,
                check_in_date: check_in_date,
                check_in_time: check_in_time,
                tour_extra: extras,
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
        $(document).on('click', '.tf-booking-popup-btn', function (e) {
            e.preventDefault();
            $(".tf-withoutpayment-booking input[type='text'], .tf-withoutpayment-booking input[type='email'], .tf-withoutpayment-booking input[type='date'], .tf-withoutpayment-booking select, .tf-withoutpayment-booking textarea").val("");

            $('.tf-booking-content-extra input[type="checkbox"]').each(function () {
                if ($(this).prop('checked') == true) {
                    $(this).prop('checked', false);
                }
            });
            tourPopupBooking();
        });

        $(document).on('change', '[name*=tf-tour-extra]', function () {
            tourPopupBooking();
        });
        $(document).on('change', '[name=deposit]', function () {
            tourPopupBooking();
        });

        // Popup Close
        $(document).on('click', '.tf-booking-times span', function (e) {
            $('.tf-withoutpayment-booking').removeClass('show');
            $('.tf-withoutpayment-booking-confirm').removeClass('show');
            // Reset Tabs
            $(".tf-booking-tab-menu ul li").removeClass("active");
            $(".tf-booking-tab-menu ul li").removeClass("done");
            $(".tf-booking-tab-menu ul li:first-child").addClass("active");
            // Reset Content
            $(".tf-booking-content").hide();
            $(".tf-booking-content:first").show();
            // Reset Pagination
            $(".tf-control-pagination").hide();
            $(".tf-control-pagination:first").show();
        });

        /*
        * Custom modal
        * @author: Foysal
        */
        $(document).on('click', '.tf-modal-btn', function () {
            var dataTarget = $(this).attr('data-target');
            $(dataTarget).addClass('tf-modal-show');
            $('body').addClass('tf-modal-open');
        });
        $(document).on("click", '.tf-modal-close', function () {
            $('.tf-modal').removeClass('tf-modal-show');
            $('body').removeClass('tf-modal-open');
        });
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                $("body").removeClass("tf-modal-open");
                $(".tf-modal").removeClass("tf-modal-show");
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

        // Design 2 Toggle share buttons
        $('.tf-share-toggle').click(function (e) {
            e.preventDefault();
            $('.tf-off-canvas-share').toggleClass('show');
        });

        // Copy button
        $('a#share_link_button').click(function (e) {
            e.preventDefault();
            $(this).addClass('copied');
            setTimeout(function () {
                $('a#share_link_button').removeClass('copied');
            }, 3000);
            $(this).parent().find("#share_link_input").select();
            document.execCommand("copy");
        });

    });

    /*
    * Template 2 Script Start
    * @author: Jahid
    */

    $(document).ready(function () {

        // $.autofilter({
        //     animation:true,
        //     duration:300
        // });

        $('.tf-template-3 .tf-reviews-slider').slick({
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

        $(".tf-template-3 .tf-question").click(function(){
            $(this).siblings().removeClass("tf-active");
            $(this).siblings().find('.tf-question-desc').slideUp();
            $(this).parents(".tf-questions-col").siblings().find('.tf-question').removeClass("tf-active");
            $(this).parents(".tf-questions-col").siblings().find('.tf-question-desc').slideUp();

            $(this).addClass("tf-active");
            $(this).find('.tf-question-desc').slideDown();
        });

        $(".tf-template-3 .tf-hero-hotel.tf-popup-buttons").click(function(){
            $(".tf-hotel-popup").addClass("tf-show")
        });

        $(document).on('click', '.tf-template-3 .tf-popup-close', function () {
            $(".tf-popup-wrapper").removeClass("tf-show")
        });

        $('.tf-template-3 .tf-details-menu a').on('click', function() {
            $(this).addClass('tf-hashlink');
            $(this).closest('li').siblings().find('a').removeClass('tf-hashlink');
        });

        //Room Filter Showing 
        $('.tf-template-3 .tf-available-rooms-head .tf-filter').on('click', function() {
            $('.tf-room-filter').toggleClass('tf-filter-show');
        });

        // Full Description Showing
        $('.tf-template-3 span.tf-see-description').on('click', function() {
            $('.tf-short-description').hide();
            $('.tf-full-description').show(500);
        });

        $('.tf-template-3 .acr-inc , .tf-template-3 .acr-dec').on('click', function() {

            if ($('input#infant').length){
            var guest = Number($('input#adults').val()) + Number($('input#children').val()) + Number($('input#infant').val()) ;
            }else{
                var guest = Number($('input#adults').val()) + Number($('input#children').val());
            }
            if (guest.toString().length < 2) {
                guest = '0' + guest;
            }
            $('span.tf-guest').html(guest);
            var room = Number($('input#room').val()) ;
            if (room.toString().length < 2) {
                room = '0' + room;
            }
            $('span.tf-room').html(room);
        })

        $(document).mouseup(function(e)
        {
            var container = $(".tf-template-3 .tf_acrselection-wrap");
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                $(".tf-template-3 .tf-booking-form-guest-and-room .tf_acrselection-wrap").removeClass("tf-show");
            }
        });
        $(".tf-template-3 .tf-booking-form-guest-and-room").click(function(){
            $(".tf-template-3 .tf-booking-form-guest-and-room .tf_acrselection-wrap").addClass("tf-show");
        });

        $(".tf-template-3 .tf-review-open.button").click(function(){
            $(".tf-template-3 .tf-sitebar-widgets .tf-review-form-wrapper").toggleClass("tf-review-show");
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
    });

    /*
    * Template 2 Script End
    * @author: Jahid
    */

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