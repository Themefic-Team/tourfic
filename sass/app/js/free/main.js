;(function ($) {
    'use-strict';
    var HotelV2Action = {
        isCalendarShown: false,
        wrapper: $(".search-result-page.layout5, .search-result-page.layout6 , .st-style-4.st-style-elementor, .search-result-page.tour-layout6,.search-result-page.tour-layout7,.search-result-page.activity-layout4,.search-result-page.activity-layout5,.search-result-page.car-layout3,.search-result-page.car-layout4 , .search-result-page.layout-rental-4 , .search-result-page.layout-rental-5, .page-template-template-transfer-search .st-style-elementor.search-result-page"),
        init() {
            let base = this;
            this.initGuestDropdown();
            this.initDateDropdown();
            this.initAccountPopup();
            this.initPasswordField();
            this.initSearchFieldFocus();
            this.initElementor();
            this.initSearchByMoveMap();
            this.initPriceSlider();
            this.initCheckboxFilter();
            this.initTriggers();
            this.initMapToggle();
            this.initMenu();
            this.initTopbar();
            this.initCalendarAvailability();
            this.initCalendarAvailabilityRental();
            this.initItemHover();
            this.initGoToTop();
            jQuery(function () {
                base.initGallery();
            });
            $(window).on('resize', function () {
                base.calcPositionArrowSlider();
            });
            jQuery(function () {
                setTimeout(function () {
                    base.calcPositionArrowSlider();
                }, 700);
            });
        },
        initItemHover() {
            $(document).on('mouseover', '.item-elementor', function () {
                let postID = $(this).data('id');
                $('.stt-price-label .inner[data-marker-id="' + postID + '"]').parent().addClass('hover');
            });
            $(document).on('mouseleave', '.item-elementor', function () {
                let postID = $(this).data('id');
                $('.stt-price-label .inner[data-marker-id="' + postID + '"]').parent().removeClass('hover');
            });
        },
        counterSlider(event) {
            var element = event.target;
            var items = event.item.count;
            var item = event.item.index + 1;
            if (item > items) {
                item = item - items
            }
            $('.count', sliderEl).text(item + ' / ' + items);
        },
        initGallery() {
            let sliderEl = $('.st-gallery.style-slider');
            if (sliderEl.length) {
                let sliderItems = 2;
                $('.owl-carousel', sliderEl).on('initialized.owl.carousel changed.owl.carousel', function (e) {
                    if (!e.namespace) {
                        return;
                    }
                    var carousel = e.relatedTarget;
                    $('.count', sliderEl).text(carousel.relative(carousel.current()) + 1 + ' / ' + carousel.items().length);
                }).owlCarousel({center: true, items: sliderItems, loop: true, margin: 10, dots: false, nav: true, responsive: {768: {center: true, items: sliderItems}, 0: {center: false, items: 1}},});
            }
            let owlSliderEl = $('.st-owl-slider');
            if (owlSliderEl.length) {
                let items = owlSliderEl.data('items') || 3;
                let loop = owlSliderEl.data('loop') === undefined ? true : owlSliderEl.data('loop');
                let margin = owlSliderEl.data('margin') || 20;
                let dots = owlSliderEl.data('dots') === undefined ? true : owlSliderEl.data('dots');
                let nav = owlSliderEl.data('nav') === undefined ? true : owlSliderEl.data('nav');
                let responsive = owlSliderEl.data('responsive') === undefined ? false : owlSliderEl.data('responsive');
                let args = {items: items, margin: margin, dots: dots, nav: nav}
                if (responsive) {
                    args.responsive = responsive;
                }
                owlSliderEl.owlCarousel(args);
            }
        },
        initCalendarAvailability() {
            $('.rate-calendar').each(function () {
                let t = $(this);
                let inputCalendar = $('.st-room-availability-input', t);
                if (inputCalendar.length > 0) {
                    if (window.matchMedia('(max-width: 767px)').matches) {
                        var singleDatePicker = true;
                    } else {
                        var singleDatePicker = false;
                    }
                    let options = {
                        parentEl: t,
                        singleDatePicker: singleDatePicker,
                        showCalendar: true,
                        alwaysShow: true,
                        autoApply: true,
                        disabledPast: true,
                        classNotAvailable: ['disabled', 'off'],
                        enableLoading: true,
                        showEventTooltip: true,
                        sameDate: false,
                        minDate: new Date(),
                        dateFormat: 'DD/MM/YYYY',
                        customClass: 'st-availability-calendar-wrapper',
                        widthSingle: 500,
                    };
                    if (t.hasClass('style-2')) {
                        options.singleDatePicker = true;
                    }
                    options.fetchEvents = function (start, end, el, callback) {
                        var events = [];
                        if (el.flag_get_events) {
                            return false;
                        }
                        el.flag_get_events = true;
                        el.container.find('.loader-wrapper').show();
                        var data = {action: 'st_get_availability_hotel_room', start: start.format('YYYY-MM-DD'), end: end.format('YYYY-MM-DD'), post_id: inputCalendar.data('room-id'), security: st_params._s};
                        $.post(st_params.ajax_url, data, function (respon) {
                            if (typeof respon === 'object') {
                                if (typeof respon.events === 'object') {
                                    events = respon.events;
                                } else {
                                    events = respon;
                                }
                            } else {
                                console.log('Can not get data');
                            }
                            callback(events, el);
                            el.flag_get_events = false;
                            el.container.find('.loader-wrapper').hide();
                        }, 'json');
                    }
                    if (typeof locale_daterangepicker == 'object') {
                        options.locale = locale_daterangepicker;
                    }
                    inputCalendar.daterangepicker(options, function (start, end, label) {
                    });
                    var dp = inputCalendar.data('daterangepicker');
                    dp.show();
                }
            });
        },
        initCalendarAvailabilityRental() {
            $('.rate-calendar').each(function () {
                let t = $(this);
                let inputCalendar = $('.calendar_input', t);
                let options = {
                    parentEl: t,
                    showCalendar: true,
                    alwaysShow: true,
                    autoApply: true,
                    disabledPast: true,
                    classNotAvailable: ['disabled', 'off'],
                    enableLoading: true,
                    showEventTooltip: true,
                    sameDate: false,
                    minDate: new Date(),
                    dateFormat: 'DD/MM/YYYY',
                    customClass: 'st-availability-calendar-wrapper',
                    widthSingle: 500,
                };
                if (t.hasClass('style-2')) {
                    options.singleDatePicker = true;
                }
                options.fetchEvents = function (start, end, el, callback) {
                    var events = [];
                    if (el.flag_get_events) {
                        return false;
                    }
                    el.flag_get_events = true;
                    el.container.find('.loader-wrapper').show();
                    var data = {action: 'st_get_availability_rental_single', start: start.format('YYYY-MM-DD'), end: end.format('YYYY-MM-DD'), post_id: inputCalendar.data('room-id'), security: st_params._s};
                    $.post(st_params.ajax_url, data, function (respon) {
                        if (typeof respon === 'object') {
                            if (typeof respon.events === 'object') {
                                events = respon.events;
                            } else {
                                events = respon;
                            }
                        } else {
                            console.log('Can not get data');
                        }
                        callback(events, el);
                        el.flag_get_events = false;
                        el.container.find('.loader-wrapper').hide();
                    }, 'json');
                }
                if (typeof locale_daterangepicker == 'object') {
                    options.locale = locale_daterangepicker;
                }
                inputCalendar.daterangepicker(options, function (start, end, label) {
                });
                var dp = inputCalendar.data('daterangepicker');
                if (dp) {
                    dp.show();
                }
            });
        },
        initTopbar() {
            var topbar = $('#topbar');
            $('.cursor, .current_langs', topbar).on('click', function (e) {
                e.preventDefault();
                let dropdown = $(this).parent().find('.nav-drop-menu');
                $('.nav-drop-menu', topbar).not(dropdown).hide();
                dropdown.toggle();
            })
            $(document).mouseup(function (e) {
                var container = $("#topbar .nav-drop");
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.find('.nav-drop-menu').hide();
                }
            });
        },
        initMenu() {
            $('.header__left .menu-toggle').on('click', function () {
                $('#st-main-menu').toggleClass('open');
                $('.header__center .overlay').show();
                $('body').css({'overflow': 'hidden'})
            });
            $('#st-main-menu .back-menu, .header__center .overlay').on('click', function (e) {
                e.preventDefault();
                $('#st-main-menu').removeClass('open');
                $('.header__center .overlay').hide();
                $('body').css({'overflow': ''})
            });
            $('#st-main-menu a .fa').on('click', function (e) {
                e.preventDefault();
            });
        },
        initMapToggle() {
            let base = this;
            $('.toggle-map', base.wrapper).on('click', function () {
                let t = $(this);
                let pageWrapper = $('.st-results', base.wrapper);
                if (!t.hasClass('open')) {
                    $('.maparea').addClass('to-full');
                    pageWrapper.addClass('nomap');
                    t.addClass('open');
                } else {
                    setTimeout(function () {
                        $('.maparea').removeClass('to-full');
                    }, 400)
                    pageWrapper.removeClass('nomap');
                    t.removeClass('open');
                }
            });
            $('.map-view-popup.style-2 .close').on('click', function () {
                $(this).parent().fadeOut();
            });
        },
        initTriggers() {
            let base = this;
            $(document).on('st_clear_filter_action', function (event) {
                $('.top-filter .dropdown-toggle.active .count', base.wrapper).empty();
                $('.top-filter .dropdown-toggle.active', base.wrapper).removeClass('active');
                let filterPrice = $('.filter-price .dropdown.active', base.wrapper);
                $('span[data-text]', filterPrice).text($('span[data-text]', filterPrice).data('text'));
                filterPrice.removeClass('active');
            });
        },
        initCheckboxFilter() {
            let base = this;
            $('.top-filter input[type="checkbox"]', base.wrapper).on('change', function () {
                let parent = $(this).closest('.form-extra-field'), countChecked = $('input[type="checkbox"]:checked', parent).length, dropdown = $('.dropdown-toggle', parent);
                countChecked > 0 ? $('.count', dropdown).text(' (' + countChecked + ') ') : $('.count', dropdown).empty();
                countChecked > 0 ? dropdown.addClass('active') : dropdown.removeClass('active');
            });
        },
        initPriceSlider() {
            let base = this;
            $('.price_range.irs-hidden-input', base.wrapper).on('change', function () {
                let parent = $(this).closest('.range-slider'), from = $('.irs-from', parent).text(), to = $('.irs-to', parent).text();
                $('.min-max-value .item-value', parent).first().find('span').text(from);
                $('.min-max-value .item-value', parent).last().find('span').text(to);
            });
            $('.price-action a.clear-price', base.wrapper).on('click', function () {
                let priceRange = $(".range-slider .price_range").data("ionRangeSlider");
                priceRange.reset();
            });
            $('.filter-price .btn-apply-price-range', base.wrapper).on('click', function () {
                let parent = $(this).closest('.dropdown-menu'), from = $('.irs-from', parent).text(), to = $('.irs-to', parent).text();
                let textRender = from + ' - ' + to, elRender = $('.filter-price .dropdown', base.wrapper);
                elRender.find('span').first().text(textRender);
                if (!elRender.hasClass('active'))
                    elRender.addClass('active');
            })
        },
        initSearchByMoveMap() {
            if ($('.page-half-map').length) {
                if ($('#st-move-map').is(':checked')) {
                }
            }
        },
        calcPositionArrowSlider() {
            $('.st-list-destination').each(function () {
                let slide = $('.swiper-slide', $(this)).first(), arrow = $('.st-button-prev, .st-button-next', $(this));
                let slideHeight = parseInt(slide.find('.thumbnail img').height()) / 2 - 20;
                arrow.css({top: slideHeight + 'px'});
            });
        },
        initSearchFieldFocus() {
            let fieldDestination = $('.field-detination');
            fieldDestination.on('show.bs.dropdown', function () {
                $(this).parent().addClass('st-focus');
            }).on('hidden.bs.dropdown', function () {
                $(this).parent().removeClass('st-focus');
            });
            let fieldGuest = $('.field-guest .dropdown');
            fieldGuest.on('show.bs.dropdown', function () {
                $(this).parent().addClass('st-focus');
            }).on('hidden.bs.dropdown', function () {
                $(this).parent().removeClass('st-focus');
            });
        },
        initPasswordField() {
            $('.field-password .stt-icon').on('click', function () {
                let t = $(this), parent = t.parent();
                if (t.hasClass('ic-view')) {
                    parent.addClass('viewing').find('input').attr('type', 'text');
                    t.removeClass('ic-view');
                } else {
                    parent.removeClass('viewing').find('input').attr('type', 'password');
                    t.addClass('ic-view');
                }
            })
        },
        initAccountPopup() {
            let wrapper = $('.login-regiter-popup.style-9');
            if (wrapper.length) {
                let accountTab = $('.account-tabs li', wrapper);
                accountTab.on('click', function () {
                    let t = $(this), target = t.data('target');
                    if (!t.hasClass('active')) {
                        accountTab.removeClass('active');
                        t.addClass('active');
                        $('.login-form-wrapper', wrapper).removeClass('active');
                        $('.login-form-wrapper.' + target, wrapper).addClass('active');
                    }
                });
                $('.create-account-action a').on('click', function (e) {
                    if ($(this).closest('.modal ').length) {
                        e.preventDefault();
                        $('.account-tabs li:last-child', wrapper).trigger('click');
                    }
                });
            }
        },
        formatDateDdMmYy(input) {
            if (st_params.date_format == 'dd/mm/yyyy' || (st_params.date_format == 'dd-mm-yyyy')) {
                var datePart = input.match(/\d+/g), day = datePart[0], month = datePart[1], year = datePart[2];
                return month + '/' + day + '/' + year;
            } else {
                return input;
            }
        },
        initDateDropdown() {
            let base = this;
            $('.form-date-search').each(function () {
                var parent = $(this), dateWrapper = $('.date-item-wrapper', parent), checkInInput = $('.check-in-input', parent), checkOutInput = $('.check-out-input', parent), checkInOut = $('.check-in-out', parent),
                    checkInRender = $('.check-in-render', parent), checkOutRender = $('.check-out-render', parent);
                var timepicker = parent.data('timepicker');
                if (typeof timepicker == 'undefined' || timepicker == '') {
                    timepicker = false;
                } else {
                    timepicker = true;
                    var start_at_text = parent.data('label-start-time');
                    var end_at_text = parent.data('label-end-time');
                }
                var options = {
                    singleDatePicker: false,
                    sameDate: true,
                    sameDateMulti: true,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'DD/MM/YYYY',
                    customClass: 'st-search-form-calendar',
                    widthSingle: 500,
                    onlyShowCurrentMonth: true,
                    timePicker: timepicker,
                    timePicker24Hour: (st_params.time_format == '12h') ? false : true,
                };
                if (checkInInput.val() != null && checkInInput.val() != '' && checkOutInput.val() != null && checkOutInput.val() != '') {
                    options.startDate = base.formatDateDdMmYy(checkInInput.val());
                    options.endDate = base.formatDateDdMmYy(checkOutInput.val());
                }
                var locale_daterangepicker_icon = {
                    labelStartTime: '<svg height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n\t viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">\n\n<g fill="#000000">\n\t<path d="M12,23.25C5.797,23.25,0.75,18.203,0.75,12C0.75,5.797,5.797,0.75,12,0.75c6.203,0,11.25,5.047,11.25,11.25\n\t\tC23.25,18.203,18.203,23.25,12,23.25z M12,2.25c-5.376,0-9.75,4.374-9.75,9.75s4.374,9.75,9.75,9.75s9.75-4.374,9.75-9.75\n\t\tS17.376,2.25,12,2.25z"/>\n\t<path d="M15.75,16.5c-0.2,0-0.389-0.078-0.53-0.22l-2.25-2.25c-0.302,0.145-0.632,0.22-0.969,0.22c-1.241,0-2.25-1.009-2.25-2.25\n\t\tc0-0.96,0.615-1.808,1.5-2.121V5.25c0-0.414,0.336-0.75,0.75-0.75s0.75,0.336,0.75,0.75v4.629c0.885,0.314,1.5,1.162,1.5,2.121\n\t\tc0,0.338-0.075,0.668-0.22,0.969l2.25,2.25c0.292,0.292,0.292,0.768,0,1.061C16.139,16.422,15.95,16.5,15.75,16.5z M12,11.25\n\t\tc-0.414,0-0.75,0.336-0.75,0.75s0.336,0.75,0.75,0.75s0.75-0.336,0.75-0.75S12.414,11.25,12,11.25z"/>\n</g>\n</svg>' + start_at_text,
                    labelEndTime: '<svg height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n\t viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">\n\n<g fill="#000000">\n\t<path d="M12,23.25C5.797,23.25,0.75,18.203,0.75,12C0.75,5.797,5.797,0.75,12,0.75c6.203,0,11.25,5.047,11.25,11.25\n\t\tC23.25,18.203,18.203,23.25,12,23.25z M12,2.25c-5.376,0-9.75,4.374-9.75,9.75s4.374,9.75,9.75,9.75s9.75-4.374,9.75-9.75\n\t\tS17.376,2.25,12,2.25z"/>\n\t<path d="M15.75,16.5c-0.2,0-0.389-0.078-0.53-0.22l-2.25-2.25c-0.302,0.145-0.632,0.22-0.969,0.22c-1.241,0-2.25-1.009-2.25-2.25\n\t\tc0-0.96,0.615-1.808,1.5-2.121V5.25c0-0.414,0.336-0.75,0.75-0.75s0.75,0.336,0.75,0.75v4.629c0.885,0.314,1.5,1.162,1.5,2.121\n\t\tc0,0.338-0.075,0.668-0.22,0.969l2.25,2.25c0.292,0.292,0.292,0.768,0,1.061C16.139,16.422,15.95,16.5,15.75,16.5z M12,11.25\n\t\tc-0.414,0-0.75,0.336-0.75,0.75s0.336,0.75,0.75,0.75s0.75-0.336,0.75-0.75S12.414,11.25,12,11.25z"/>\n</g>\n</svg>' + end_at_text,
                }
                if (typeof locale_daterangepicker == 'object') {
                    var setting_locale = {...locale_daterangepicker_icon, ...locale_daterangepicker};
                    options.locale = setting_locale;
                }
                checkInOut.on('blur');
                checkInOut.daterangepicker(options, function (start, end, label) {
                    if (parent.hasClass('form-date-travelpayout')) {
                        checkInInput.val(start.format('YYYY-MM-DD')).trigger('change');
                    } else {
                        checkInInput.val(start.format(parent.data('format'))).trigger('change');
                    }
                    checkInRender.html(start.format(parent.data('format'))).trigger('change');
                    if (parent.hasClass('form-date-travelpayout')) {
                        checkOutInput.val(end.format('YYYY-MM-DD')).trigger('change');
                    } else {
                        checkOutInput.val(end.format(parent.data('format'))).trigger('change');
                    }
                    checkOutRender.html(end.format(parent.data('format'))).trigger('change');
                    if (timepicker) {
                        checkInInput.val(start.format(parent.data('date-format'))).trigger('change');
                        $('.check-in-input-time', parent).val(start.format(parent.data('time-format'))).trigger('change');
                        checkOutInput.val(end.format(parent.data('date-format'))).trigger('change');
                        $('.check-out-input-time', parent).val(end.format(parent.data('time-format'))).trigger('change');
                        $('.check-out-input-time', parent).val(end.format(parent.data('time-format'))).trigger('change');
                    }
                    checkInOut.trigger('daterangepicker_change', [start, end]);
                });
                checkInOut.on('click.start.daterangepicker', function () {
                    if (base.isCalendarShown) {
                        $('.date-item-wrapper.checkin').removeClass('st-focus');
                        $('.date-item-wrapper.checkout').addClass('st-focus');
                    }
                    base.isCalendarShown = true;
                });
                checkInOut.on('show.daterangepicker', function () {
                    $('.date-item-wrapper.checkin').addClass('st-focus');
                    $('.date-item-wrapper.checkout').removeClass('st-focus');
                    base.isCalendarShown = false;
                });
                checkInOut.on('hide.daterangepicker', function () {
                    $('.date-item-wrapper').removeClass('st-focus');
                    base.isCalendarShown = false;
                });
            });
        },
        initGuestDropdown() {
            let search_v2 = $('.search-form-v2');
            let search_v3 = $('.search-form-v3');
            if (typeof search_v2 != 'undefined' || typeof search_v3 != 'undefined') {
                $('input[name="room_num_search"], input[name="adult_number"], input[name="child_number"]', search_v2, search_v3).on('change', function () {
                    let wrapper = $(this).parents('.field-guest');
                    let renderEl = $('.render span', wrapper), inputRoom = $('input[name="room_num_search"]', wrapper), inputAdult = $('input[name="adult_number"]', wrapper),
                        inputChild = $('input[name="child_number"]', wrapper), i108n = renderEl.data('text');
                    let numberGuest = parseInt(inputAdult.val()) + parseInt(inputChild.val());
                    let textRoom = '';
                    let textComma = '';
                    let numberRoom = '';
                    if (typeof i108n.room !== 'undefined') {
                        textRoom = (numberRoom === 1 ? i108n.room : i108n.rooms);
                        numberRoom = inputRoom.val();
                        textComma = (numberRoom !== '' ? ', ' : '');
                        if (typeof numberRoom != 'undefined') {
                            numberRoom = parseInt(inputRoom.val()) + ' ';
                        } else {
                            numberRoom = '';
                            textRoom = '';
                        }
                    }
                    if ($('.render', wrapper).hasClass('hide')) {
                        $('.render', wrapper).removeClass('hide');
                        $('.st-form-dropdown-icon label', wrapper).addClass('hide');
                    }
                    renderEl.text(numberGuest + ' ' + (numberGuest === 1 ? i108n.guest : i108n.guests) + textComma + numberRoom + '' + textRoom);
                });
            }
        },
        initElementor() {
            let base = this;
            $(window).on('elementor/frontend/init', function () {
                elementorFrontend.hooks.addAction('frontend/element_ready/st_destination.default', function ($wrapper) {
                    setTimeout(function () {
                        base.calcPositionArrowSlider();
                    }, 700);
                });
            });
        },
        initGoToTop() {
            $('#gotop').on('click', function () {
                $("body,html").animate({scrollTop: 0}, 700, function () {
                    $('#gotop').fadeOut()
                })
            });
            $(window).on('scroll', function () {
                var scrolltop = $(window).scrollTop();
                if (scrolltop > 200) {
                    $('#gotop').fadeIn()
                } else {
                    $('#gotop').fadeOut()
                }
            });
        }
    }
    HotelV2Action.init();
})(jQuery);