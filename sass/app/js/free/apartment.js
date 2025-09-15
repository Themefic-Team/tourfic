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

        // Apartment Location Autocomplete
        function tourfic_autocomplete(inp, arr) {
            var currentFocus;
        
            // Show autocomplete suggestions on focus
            inp.addEventListener("focus", function () {
                closeAllLists();
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
                a.classList.add("autocomplete-items");
                this.parentNode.appendChild(a);
        
                for (const [key, value] of Object.entries(arr)) {
                    let b = document.createElement("DIV");
                    b.innerHTML = value;
                    b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                    b.addEventListener("click", function () {
                        let source = this.getElementsByTagName("input")[0];
                        inp.value = source.value;
                        inp.closest('input').nextElementSibling.value = source.dataset.slug;
                        setTimeout(() => {
                            closeAllLists();
                        },100);
                    });
                    a.appendChild(b);
                }
            });
        
            // Filter suggestions on keyup
            inp.addEventListener("keyup", function (e) {
                var val = this.value.toLowerCase();
                closeAllLists();
                currentFocus = -1;
                
                if (!val) return false;
        
                let a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
        
                var found = false;
                for (const [key, value] of Object.entries(arr)) {
                    if (value.toLowerCase().startsWith(val)) {
                        found = true;
                        let b = document.createElement("DIV");
                        b.innerHTML = `<strong>${value.substr(0, val.length)}</strong>${value.substr(val.length)}`;
                        b.innerHTML += `<input type='hidden' value="${value}" data-slug='${key}'>`;
                        b.addEventListener("click", function (e) {
                            let source = this.getElementsByTagName("input")[0];
                            inp.value = source.value;
                            inp.closest('input').nextElementSibling.value = source.dataset.slug;
                
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
        
                // If no match found, show "No results found"
                if (!found) {
                    let b = document.createElement("DIV");
                    b.innerHTML = `<span>${tf_params.no_found}</span>`;
                    b.innerHTML += `<input type='hidden' value="">`;
                    b.addEventListener("click", function () {
                        inp.value = "";
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            });
        
            // Handle keyboard navigation
            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "-autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
        
                if (e.keyCode == 40) {
                    // Arrow DOWN
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    // Arrow UP
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    // ENTER key
                    e.preventDefault();
                    if (currentFocus > -1 && x) {
                        x[currentFocus].click();
                    }
                }
            });
        
            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = x.length - 1;
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
                    if (elmnt !== x[i] && elmnt !== inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
        
            // Close when clicking outside
            $(document).on('click', function (event) {
                if (!$(event.target).closest("#tf-apartment-location").length) {
                    $("#tf-apartment-location-autocomplete-list").hide();
                }
            });
        }
        

        /**
         * Ajax apartment booking
         * @author Foysal
         */
        $(document).on('submit', 'form#tf-apartment-booking', function (e) {
            e.preventDefault();

            var $this = $(this);

            var formData = new FormData(this);
            formData.append('action', 'tf_apartment_booking');

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

                    $('.tf-notice-wrapper').html("").hide();
                },
                complete: function (data) {
                    $this.unblock();
                },
                success: function (data) {
                    $this.unblock();

                    var response = JSON.parse(data);

                    if (response.status === 'error') {
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
                },
                error: function (data) {
                    console.log(data);
                },

            });
        });


        /*
        * Apartment Search submit
        * @since 2.9.7
        * @author Jahid
        */
        $(document).on('submit', '#tf_apartment_booking', function (e) {
            e.preventDefault();
            let form = $(this),
                submitBtn = form.find('button[type="submit"]'),
                formData = new FormData(form[0]);

            formData.append('action', 'tf_apartments_search');
            formData.append('_nonce', tf_params.nonce);

            if (formData.get('from') == null || formData.get('to') == null) {
                formData.append('from', tf_params.tf_apartment_min_price);
                formData.append('to', tf_params.tf_apartment_max_price);
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

        /*
        * Apartment room quick view
        * */
        $(document).on('click', '.tf-apt-room-qv', function (e) {
            e.preventDefault();
            $("#tour_room_details_loader").show();
            let post_id = $(this).data("post-id");
            let id = $(this).data("id");
            let data = {
                action: 'tf_apt_room_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                id: id,
                design: 'default'
            };

            $.ajax({
                type: 'post',
                url: tf_params.ajax_url,
                data: data,
                success: function (response) {
                    $("#tf_apt_room_details_qv").html(response);

                    $("#tour_room_details_loader").hide();
                    $.fancybox.open({
                        src: '#tf_apt_room_details_qv',
                        type: 'inline',
                    });
                }

            });
        });

        /**
         * Design 1 Apartment Room Popup
         *
         */
        $(document).on('click', '.tf-apt-room-qv-desgin-1', function (e) {

            e.preventDefault();
            $("#tour_room_details_loader").show();
            let post_id = $(this).data("post-id");
            let id = $(this).data("id");
            let data = {
                action: 'tf_apt_room_details_qv',
                _nonce: tf_params.nonce,
                post_id: post_id,
                id: id,
                design: 'design-1'
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


        /**
         * Apartment location autocomplete
         * @author Foysal
         */
        var apartment_location_input = document.getElementById("tf-apartment-location");
        var apartment_locations = tf_params.apartment_locations;
        if (apartment_location_input) {
            tourfic_autocomplete(apartment_location_input, apartment_locations);
        }
       
        /**
         * Apartment Min and Max Range
         * @author Foysal
         */
        if (tf_params.tf_apartment_min_price >= 0 && tf_params.tf_apartment_max_price > 0) {
            $('.tf-apartment-filter-range').alRangeSlider({
                range: {
                    min: parseInt(tf_params.tf_apartment_min_price),
                    max: parseInt(tf_params.tf_apartment_max_price),
                    step: 1
                },
                initialSelectedValues: {
                    from: parseInt(tf_params.tf_apartment_min_price),
                    to: parseInt(tf_params.tf_apartment_max_price)
                },
                grid: false,
                theme: "dark",
            });
        }

        /**
         * Apartment highlights slider
         * @author Foysal
         */
        $('.tf-apt-highlights-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
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
         * Apartment room slider
         * @author Foysal
         */
        $('.tf-apartment-room-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
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

        // Design Default Room Slider

        $('.tf-apartment-default-design-room-slider').slick({
            arrows: true,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow:"<button type='button' class='slick-prev'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\">\n" +
                "  <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M16.2071 5.29289C16.5976 5.68342 16.5976 6.31658 16.2071 6.70711L10.9142 12L16.2071 17.2929C16.5976 17.6834 16.5976 18.3166 16.2071 18.7071C15.8166 19.0976 15.1834 19.0976 14.7929 18.7071L8.79289 12.7071C8.40237 12.3166 8.40237 11.6834 8.79289 11.2929L14.7929 5.29289C15.1834 4.90237 15.8166 4.90237 16.2071 5.29289Z\" fill=\"#2A3343\"/>\n" +
                "</svg></button>",
            nextArrow:"<button type='button' class='slick-next'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\">\n" +
                "  <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M8.79289 5.29289C9.18342 4.90237 9.81658 4.90237 10.2071 5.29289L16.2071 11.2929C16.5976 11.6834 16.5976 12.3166 16.2071 12.7071L10.2071 18.7071C9.81658 19.0976 9.18342 19.0976 8.79289 18.7071C8.40237 18.3166 8.40237 17.6834 8.79289 17.2929L14.0858 12L8.79289 6.70711C8.40237 6.31658 8.40237 5.68342 8.79289 5.29289Z\" fill=\"#2A3343\"/>\n" +
                "</svg></button>",

            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
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
         * Related Apartment slider
         * @author Foysal
         */
        $('.tf-related-apartment-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 3000,
            slidesToShow: 4,
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
         * Apartment 2 design Highlights sliers
         *
         * Slick
         */
        $('.tf-features-block-slides').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 300,
            autoplay: false,
            autoplaySpeed: 2000,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
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

        $(document).on('click', '.tf-apartment-show-more', function (e) {
            if ($(this).siblings('.tf-full-description')) {
                $(this).siblings('.tf-full-description').show();
                $(this).siblings('.tf-description').hide();
                $(this).text("Show Less");
                $(this).addClass('tf-apartment-show-less');
            }
        });
        
        $(document).on('click', '.tf-apartment-show-less', function (e) {
            if ($(this).siblings('.tf-full-description')) {
                $(this).siblings('.tf-full-description').hide();
                $(this).siblings('.tf-description').show();
                $(this).text("Show More");
                $(this).removeClass('tf-apartment-show-less');
            }
        });
        
        $('.tf-single-review.tf_apartment .tf-single-details').each(function (index, val) {
            if (index > 1) {
                $(this).hide();
            }
        });

        $(".tf-apaartment-show-all").on('click', function (e) { 
            $('.tf-single-review.tf_apartment .tf-single-details').each(function (index, val) {
                $(val).show();
            });

            $('.show-all-review-wrap').hide();
        });

        if ($('#apartment-location').length) {
            const map = L.map('apartment-location').setView([tf_params.single_apartment_data.address_latitude, tf_params.single_apartment_data.address_longitude], tf_params.single_apartment_data.address_zoom);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const marker = L.marker([tf_params.single_apartment_data.address_latitude, tf_params.single_apartment_data.address_longitude], {alt: tf_params.single_apartment_data.address}).addTo(map)
                .bindPopup(tf_params.single_apartment_data.address);
        }

    });

})(jQuery, window);