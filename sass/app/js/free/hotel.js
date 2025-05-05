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
        $(document).on('click', '.hotel-room-book', function (e) {
            e.preventDefault();

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

                console.log('click input');
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
        // $(document).on('click', 'form .tf-hotel-booking-popup-btn', function (e) {
        //     e.preventDefault();
        //     var $this = $(this);

        //     hotelPopupBooking($this);
        // });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.matches('.tf-hotel-booking-popup-btn')) {
                e.preventDefault();
                
                alert("Test");
            }
        });
        

        // $(document).on('submit', 'form.tf-room', function (e) {
        //     e.preventDefault();

        //     var $this = $(this);
        //     var formData = new FormData(this);

        //     // if ($(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val()) {
        //     //     var room = $(this).closest('.reserve').find('select[name=hotel_room_selected] option').filter(':selected').val();
        //     //     var deposit = $(this).closest('.room-submit-wrap').find('input[name=make_deposit]').is(':checked');
        //     // } else {
        //     //     var room = $("#hotel_room_number").val();
        //     // }
        //     var deposit = $this.find("input[name=hotel_room_depo]").val();
        //     var airport_service = $this.find('[name="airport_service"]:checked').val();

        //     formData.append('action', 'tf_hotel_booking');
        //     formData.append('_ajax_nonce', tf_params.nonce);
        //     formData.append('deposit', deposit);
        //     formData.append('airport_service', airport_service);


        //     $.ajax({
        //         type: 'post',
        //         url: tf_params.ajax_url,
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         beforeSend: function (data) {
        //             $this.block({
        //                 message: null,
        //                 overlayCSS: {
        //                     background: "#fff",
        //                     opacity: .5
        //                 }
        //             });
        //             $('#tour_room_details_loader').show();
        //             $('.tf-notice-wrapper').html("").hide();
        //         },
        //         error: function (data) {
        //             console.log(data);
        //         },
        //         complete: function (data) {
        //             $this.unblock()
        //             $('#tour_room_details_loader').hide();
        //             $('.tf-withoutpayment-booking').removeClass('show');
        //             $this.find('.tf-withoutpayment-booking-confirm').addClass('show');
        //         },
        //     })
        // });


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