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
        $(document).on('click', '.tf-details-menu ul li', function (e) {
            var $clicked = $(this);
            var key = String( $clicked.data('menu') || $clicked.attr('data-menu') ).trim();

            if ( key === '' ) {
                return;
            }

            // remove .active from all menu items in all menus
            $('.tf-details-menu ul li').removeClass('active');

            // add .active to every li whose data-menu matches the clicked one
            $('.tf-details-menu ul li').filter(function () {
                return String( $(this).data('menu') || $(this).attr('data-menu') ).trim() === key;
            }).addClass('active');
        });
        
        // Car Location Autocomplete

        function tourfic_car_autocomplete(inp, arr) {

            inp.addEventListener("focus", function () {
                closeAllLists();
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
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
            var pickup_id = $('#tf_pickup_id').val();
            let dropoff_id = $('#tf_dropoff_id').val();
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
                dropoff_time: dropoff_time,
                pickup_id: pickup_id,
                dropoff_id: dropoff_id
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
            var pickup_id = $('#tf_pickup_id').val();
            let dropoff_id = $('#tf_dropoff_id').val();
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
                pickup_id: pickup_id,
                dropoff_id: dropoff_id,
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
                submitBtn = form.find('button[type="submit"]'),
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
            var pickup_id = $('#tf_pickup_id').val();
            let dropoff_id = $('#tf_dropoff_id').val();
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
                pickup_id: pickup_id,
                dropoff_id: dropoff_id,
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
        * Car menu scroll
        * @author Mofazzal Hossain
        */
        let $scrollContainer = $('.tf-details-menu ul');

        $scrollContainer.on('click', 'li', function (e) {
            let $item = $(this);

            // Remove previous active and set new one
            $scrollContainer.find('li').removeClass('active');
            $item.addClass('active');

            scrollToItem($item);
        });

        function scrollToItem($item) {
            let container = $scrollContainer.get(0);
            let containerLeft = container.scrollLeft;
            let containerWidth = $scrollContainer.outerWidth();
            let itemLeft = $item.position().left + containerLeft;
            let itemWidth = $item.outerWidth();

            let scrollTo = itemLeft - (containerWidth / 2) + (itemWidth / 2);

            // Animate scroll
            $scrollContainer.animate({
                scrollLeft: scrollTo
            }, 400);
        }


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

        /*
        * Booking Bar Show
        * @author Mofazzal Hossain
        */
        if($('.tf-single-car-details-warper .tf-details-menu').length){
            $(window).on('scroll resize', function () {
                // Check the position of the target div
                var $target = $('.tf-single-car-details-warper .tf-details-menu');
                var $bookingBar = $('.tf-single-booking-bar');
                var $wpAdminBar = $('#wpadminbar');
                var $header = $('header');
                var $desktopHeader = $('.tft-header-desktop');
                
                var targetOffset = $target.offset().top;
                var targetHeight = $target.outerHeight();
                var targetBottom = targetOffset + targetHeight;

                var scrollPosition = $(window).scrollTop();

                // Calculate heights
                var wpAdminBarHeight = $wpAdminBar.length ? $wpAdminBar.outerHeight() : 0;
                var headerHeight = $header.length ? $header.outerHeight() : 0;
                
                if($header.hasClass('tf-navbar-shrink')) {
                    headerHeight = $header.outerHeight();
                }else {
                    headerHeight = 0;
                }
                
                // Total offset is admin bar + header heights
                var totalOffset = wpAdminBarHeight + headerHeight;
                $bookingBar.css('top', totalOffset + 'px');
                
                // Adjust scroll position check to account for the total offset
                if (scrollPosition + totalOffset > targetBottom) {
                    $bookingBar.fadeIn(function () {
                        if ($bookingBar.is(':visible')) {
                            $header.css("box-shadow", "none");
                            $desktopHeader.css("box-shadow", "none");
                        }
                    });
                } else {
                    $bookingBar.fadeOut(function () {
                        $header.css("box-shadow", ""); 
                        $desktopHeader.css("box-shadow", "");
                    });
                }
            });
        }

        // Back to Booking Form
        $(document).on('click', '.tf-back-to-booking', function (e) {
            e.preventDefault(); 
            $('.tf-single-booking-bar').fadeOut();
            var bookingBarHeight = $('.tf-single-booking-bar').outerHeight() || 0;
            var $wpAdminBar = $('#wpadminbar');
            var $header = $('header');
            var wpAdminBarHeight = $wpAdminBar.length ? $wpAdminBar.outerHeight() : 0;
            var headerHeight = $header.length ? $header.outerHeight() : 0;
            var totalOffset = wpAdminBarHeight + headerHeight;
            
            $('html, body').animate({
                scrollTop: $('.tf-date-select-box').offset().top - totalOffset
            }, 1000); 
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
                    $button.text(tf_params.car_mobile_button_hide);
                } else {
                    $button.text(tf_params.car_mobile_button_book_now);
                }
            });
        });

        if ($('#car-location').length) {
            const map = L.map('car-location').setView([tf_params.single_car_data.address_latitude, tf_params.single_car_data.address_longitude], tf_params.single_car_data.address_zoom);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.single_car_data.address_latitude, tf_params.single_car_data.address_longitude], {alt: tf_params.single_car_data.address}).addTo(map)
                .bindPopup(tf_params.single_car_data.address);
        }
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
