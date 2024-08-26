(function ($, win) {
    $(document).ready(function () {
        // FAQ Accordion
        $('.tf-faq-head').on("click", function () {
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
            $('.tf-details-menu ul li').removeClass('active');
            $this.addClass("active");
        });

        // Car Location Autocomplete

        function tourfic_car_autocomplete(inp, arr) {
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
            document.addEventListener("click", function (e) {
                // closeAllLists(e.target);
                if (e.target.id == "content" || e.target.id == "") {
                    closeAllLists(e.target);
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

        /*
        * Add Extra
        * @author Jahid
        */
        $(document).on('submit', '.tf-car-extra-infos', function (e) {
            e.preventDefault();
            let form = $(this);
            const formData = new FormData(e.target);
            submitBtn = form.find('.tf-extra-submit'),
            formData.append('action', 'tf_extra_add_to_booking');
            formData.append('_nonce', tf_params.nonce);

        
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
                    form.css({'opacity': '1', 'pointer-events': 'all'});
                    submitBtn.removeClass('tf-btn-loading');
                    $('.tf-added-extra').html(response);
                    if(response){
                        $('.tf-extra-added-info').show();
                    }
                }
            });

        });

        $(document).on('click', '.tf-single-added-extra .delete', function (e) {
            e.preventDefault();
            let $this = $(this);
            $this.closest('.tf-single-added-extra').remove();
            var count = $('.tf-added-extra .tf-single-added-extra').length;
            if(count==0){
                $('.tf-extra-added-info').hide();
            }
        });

        $(document).on('click', '.tf-booking-popup-header .tf-close-popup', function (e) {
            e.preventDefault();
            $('.tf-car-booking-popup').hide();
        });

        /*
        * Car Booking Popup
        * @author Jahid
        */
        $(document).on('click', '.tf-car-booking', function (e) {
            e.preventDefault();
            $('.tf-car-booking-popup').css('display', 'flex');
            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            if( !pickup || !dropoff || !pickup_date || !dropoff_date || !pickup_time || !dropoff_time ){
                $('.error-notice').text('Fill up the all fields');
            }else{
                $('.error-notice').hide();
                $('.tf-car-booking-popup').css('display', 'flex');
            }
        });

        $(document).on('click', '.booking-next', function (e) {
            let $this = $(this);
            $('.tf-booking-tabs ul li').removeClass('active');
            $('.tf-booking-tabs ul li.booking').addClass('active');

            $('.tf-protection-content').hide();
            $('.tf-booking-bar').hide();

            $('.tf-booking-form-fields').show();

            $('#protection_value').val($this.attr('data-charge'));
        });

        /*
        * Car Booking
        * @author Jahid
        */

        $(document).on('click', '.booking-process', function (e) {
            let $this = $(this);
            if($this.hasClass('tf-final-step')){
                var pickup = $('#tf_pickup_location').val();
                let dropoff = $('#tf_dropoff_location').val();
                let pickup_date = $('.tf_pickup_date').val();
                let dropoff_date = $('.tf_dropoff_date').val();
                let pickup_time = $('.tf_pickup_time').val();
                let dropoff_time = $('.tf_dropoff_time').val();

                if( !pickup || !dropoff || !pickup_date || !dropoff_date || !pickup_time || !dropoff_time ){
                    $('.error-notice').text('Fill up the all fields');
                    return;
                }
            }

            var pickup = $('#tf_pickup_location').val();
            let dropoff = $('#tf_dropoff_location').val();
            let pickup_date = $('.tf_pickup_date').val();
            let dropoff_date = $('.tf_dropoff_date').val();
            let pickup_time = $('.tf_pickup_time').val();
            let dropoff_time = $('.tf_dropoff_time').val();

            var data = {
                action: 'tf_car_booking',
                car_booking_nonce: tf_params.nonce,
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
                    form.css({'opacity': '0.5', 'pointer-events': 'none'});
                    $this.addClass('tf-btn-loading');
                },
                success: function (response) {
                    
                }
            });

        });


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
                console.log(view);
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').removeClass('list-view');
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').addClass('grid-view');
            }else{
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').addClass('list-view');
                $('.tf-car-details-column .tf-car-archive-result .tf-car-result').removeClass('grid-view');
            }
        });

    });

})(jQuery, window);