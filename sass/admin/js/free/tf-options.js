(function ($) {
    'use strict';
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

        /*
        * window url on change tab click
        * @author: Foysal
        */
        $(window).on('hashchange load', function () {
            let hash = window.location.hash;
            let query = window.location.search;
            let slug = hash.replace('#tab=', '');

            if (hash) {
                let selectedTab = $('.tf-tablinks[data-tab="' + slug + '"]'),
                    parentDiv = selectedTab.closest('.tf-admin-tab-item');

                selectedTab.trigger('click');
                parentDiv.trigger('click');
            }

            if (query.indexOf('dashboard') > -1) {
                let submenu = $("#toplevel_page_tf_settings").find(".wp-submenu");
                submenu.find("a").filter(function (a, e) {
                    return e.href.indexOf(query) > -1;
                }).parent().addClass("current");
            }
        });

        /*
        * Tab click
        * @author: Foysal
        */
        $(document).on('click', '.tf-tablinks', function (e) {
            e.preventDefault();
            let firstTabId,
                $this = $(this),
                parentDiv = $this.closest('.tf-admin-tab-item'),
                parentTabId = parentDiv.children('.tf-tablinks').attr('data-tab'),
                tabcontent = $('.tf-tab-content'),
                tablinks = $('.tf-tablinks');

            tabcontent.hide();
            tablinks.removeClass('active');

            let tabId = $this.attr('data-tab');
            $('#' + tabId).css('display', 'flex');

            if ($this.next().hasClass('tf-submenu')) {
                firstTabId = parentDiv.find('.tf-submenu li:first-child .tf-tablinks').data('tab');
            }

            if (firstTabId === tabId) {
                parentDiv.find('.tf-submenu li:first-child .tf-tablinks').addClass('active');
            } else {
                $this.addClass('active');
            }
            // url hash update
            window.location.hash = '#tab=' + tabId;

            $(".tf-admin-tab").removeClass('active');

            let submenu = $("#toplevel_page_tf_settings").find(".wp-submenu");
            submenu.find("a").filter(function (a, e) {
                let slug = e.hash.replace('#tab=', '');
                return tabId === slug || parentTabId === slug;
            }).parent().addClass("current").siblings().removeClass("current")
        });

        /*
        * Submenu toggle
        * @author: Foysal
        */
        $(document).on('click', '.tf-admin-tab-item', function (e) {
            e.preventDefault();
            let $this = $(this);

            $this.addClass('open');
            $this.children('ul').slideDown();
            $this.siblings('.tf-admin-tab-item').children('ul').slideUp();
            $this.siblings('.tf-admin-tab-item').removeClass('open');
            $this.siblings('.tf-admin-tab-item').find('li').removeClass('open');
            $this.siblings('.tf-admin-tab-item').find('ul').slideUp();
        });

        /*
        * Each date field initialize flatpickr
        * @author: Foysal
        */
        const tfDateInt = dateSelector => {
            $(dateSelector).each(function () {
                let $this = $(this),
                    dateField = $this.find('input.flatpickr'),
                    format = dateField.data('format'),
                    multiple = dateField.data('multiple'),
                    minDate = dateField.data('min-date');

                if (dateField.length === 2) {
                    let startDate = $this.find('.tf-date-from input.flatpickr').flatpickr({
                        dateFormat: format,
                        minDate: minDate,
                        altInput: true,
                        altFormat: tf_options.tf_admin_date_format,
                        onChange: function (selectedDates, dateStr, instance) {
                            endDate.set('minDate', dateStr);
                        }
                    });
                    let endDate = $this.find('.tf-date-to input.flatpickr').flatpickr({
                        dateFormat: format,
                        minDate: minDate,
                        altInput: true,
                        altFormat: tf_options.tf_admin_date_format,
                        onChange: function (selectedDates, dateStr, instance) {
                            startDate.set('maxDate', dateStr);
                        }
                    });
                } else {
                    dateField.flatpickr({
                        dateFormat: format,
                        minDate: minDate,
                        altInput: true,
                        altFormat: tf_options.tf_admin_date_format,
                        mode: multiple ? 'multiple' : 'single',
                    });
                }
            });
        }
        tfDateInt('.tf-field-date');

        /*
        * Each time field initialize flatpickr
        * @author: Foysal
        */
        const tfTimeInt = timeSelector => {
            $(timeSelector).each(function () {
                let $this = $(this),
                    timeField = $this.find('input.flatpickr'),
                    format = timeField.data('format');

                timeField.flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: format,
                });
            });
        }
        tfTimeInt('.tf-field-time');


        /*
        * Each color field initialize wpColorPicker
        * @author: Foysal
        */
        const tfColorInt = colorSelector => {
            $(colorSelector).each(function () {
                let $this = $(this),
                    colorField = $this.find('input.tf-color');

                colorField.wpColorPicker();
            });
        }
        tfColorInt('.tf-field-color');

        /*
        * Custom modal
        * @author: Foysal
        */
        TF_dependency();

        function TF_dependency() {
            $('.tf-tab-content, .tf-taxonomy-metabox').each(function () {
                var $this = $(this);
                $this.find('[data-controller]').each(function () {
                    var $tffields = $(this);
                    if ($tffields.length) {
                        // alert($tffields.length);
                        var normal_ruleset = $.tf_deps.createRuleset(),
                            global_ruleset = $.tf_deps.createRuleset(),
                            normal_depends = [],
                            global_depends = [];

                        $tffields.each(function () {

                            var $field = $(this),
                                controllers = $field.data('controller').split('|'),
                                conditions = $field.data('condition').split('|'),
                                values = $field.data('value').toString().split('|'),
                                is_global = $field.data('depend-global') ? true : false,
                                ruleset = normal_ruleset;

                            $.each(controllers, function (index, depend_id) {

                                var value = values[index] || '',
                                    condition = conditions[index] || conditions[0];

                                ruleset = ruleset.createRule($this.find('[data-depend-id="' + depend_id + '"]'), condition, value);

                                ruleset.include($field);

                                if (is_global) {
                                    global_depends.push(depend_id);
                                } else {
                                    normal_depends.push(depend_id);
                                }

                            });

                        });

                        if (normal_depends.length) {
                            $.tf_deps.enable($this, normal_ruleset, normal_depends);
                        }

                        if (global_depends.length) {
                            $.tf_deps.enable(TF.vars.$body, global_ruleset, global_depends);
                        }
                    }
                });


            });
        }


        /*
        * Custom modal
        * @author: Foysal
        */
        $(document).on('click', '.tf-modal-btn', function (e) {
            e.preventDefault();
            let $this = $(this),
                modal = $('#tf-icon-modal');

            if (modal.length > 0 && modal.hasClass('tf-modal-show')) {
                modal.removeClass('tf-modal-show');
                $('body').removeClass('tf-modal-open');
            } else {
                modal.addClass('tf-modal-show');
                $('body').addClass('tf-modal-open');
            }
        });
        $(document).on("click", '.tf-modal-close', function () {
            $('.tf-modal').removeClass('tf-modal-show');
            $('body').removeClass('tf-modal-open');
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                $("body").removeClass("tf-modal-open");
                $(".tf-modal").removeClass("tf-modal-show");
            }
        });

        /*
        * Icon tab
        * @author: Foysal
        */
        $(document).on('click', '.tf-icon-tab', function (e) {
            e.preventDefault();
            let $this = $(this),
                tab = $this.data('tab');

            $('.tf-icon-tab').removeClass('active');
            $this.addClass('active');

            $('#' + tab).addClass('active').siblings().removeClass('active');
        });

        /*
        * Icon select
        * @author: Foysal
        */
        $(document).on('click', '.tf-icon-select .tf-admin-btn, .tf-icon-select .tf-icon-preview', function (e) {
            e.preventDefault();
            let btn = $(this);

            let fieldId = btn.closest('.tf-icon-select').attr('id');
            $('#tf-icon-modal').data('icon-field', fieldId);
        });

        /*
        * Icon select
        * @author: Foysal
        */
        $(document).on('click', '.tf-icon-list li', function (e) {
            e.preventDefault();
            let $this = $(this);

            $('.tf-icon-list li').removeClass('active');
            $this.addClass('active');

            //remove disabled class
            $('.tf-icon-insert').removeClass('disabled');
        });

        /*
        * Icon insert
        * @author: Foysal
        */
        $(document).on('click', '.tf-icon-insert', function (e) {
            e.preventDefault();
            let $this = $(this),
                fieldId = $('#tf-icon-modal').data('icon-field'),
                field = $('#' + fieldId),
                preview = field.find('.tf-icon-preview'),
                icon = $('.tf-icon-list li.active').data('icon');

            if (icon) {
                preview.removeClass('tf-hide');
                field.find('.tf-icon-preview-wrap i').attr('class', icon);
                field.find('.tf-icon-value').val(icon).trigger('change');

                //Close modal
                $('.tf-modal').removeClass('tf-modal-show');
                $('body').removeClass('tf-modal-open');
            }
        })

        /*
        * Icon search
        * @author: Foysal
        */
        $(document).on('change keyup', '.tf-icon-search-input', function () {

            let searchVal = $(this).val(),
                $icons = $('#tf-icon-modal').find('.tf-icon-list li');

            $icons.each(function () {

                var $this = $(this);

                if ($this.data('icon').search(new RegExp(searchVal, 'i')) < 0) {
                    $this.hide();
                } else {
                    $this.show();
                }

            });

        });

        /*
        * Icon remove
        * @author: Foysal
        */
        $(document).on('click', '.tf-icon-preview .remove-icon', function (e) {
            e.preventDefault();
            let $this = $(this),
                preview = $this.closest('.tf-icon-preview'),
                iconSelect = $this.closest('.tf-icon-select'),
                iconLi = $('#tf-icon-modal').find('.tf-icon-list li');

            preview.addClass('tf-hide');
            iconSelect.find('.tf-icon-preview-wrap i').attr('class', '');
            iconSelect.find('.tf-icon-value').val('').trigger('change');

            //remove active class
            iconLi.removeClass('active');
        })


        /*
        * Options ajax save
        * @author: Foysal
        */
        $(document).on('submit', '.tf-option-form.tf-ajax-save', function (e) {
            e.preventDefault();
            let $this = $(this),
                submitBtn = $this.find('.tf-submit-btn'),
                data = new FormData(this);
            var fontsfile = $('.itinerary-fonts-file').prop("files");
            if (typeof fontsfile !== "undefined") {
                for (var i = 0; i < fontsfile.length; i++) {
                    data.append('file[]', fontsfile[i]);
                }
            }
            data.append('action', 'tf_options_save');

            $.ajax({
                url: tf_options.ajax_url,
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    submitBtn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    if (obj.status === 'success') {
                        notyf.success(obj.message);
                    } else {
                        notyf.error(obj.message);
                    }
                    submitBtn.removeClass('tf-btn-loading');

                },
                error: function (error) {
                    console.log(error);
                }
            });
        });

        /*
        * Each select2 field initialize select2
        * @author: Foysal, Sydur
        */
        const tfSelect2Int = select2Selector => {
            let $this = select2Selector,
                id = $this.attr('id'),
                placeholder = $this.data('placeholder');

            $('#' + id + '').select2({
                placeholder: placeholder,
                allowClear: true,
            });
        }

        $('select.tf-select2').each(function () {
            var $this = $(this);
            tfSelect2Int($this);
        });

        /*
        * Room Full Calendar
        * @since 2.10.2
        * @auther: Foysal
        */
        var roomCal = function (container) {
            var self = this;
            this.container = container;
            this.calendar = null
            this.roomCalData = null;
            this.fullCalendar;
            this.timeOut;
            this.fullCalendarOptions = {
                initialView: 'dayGridMonth',
                firstDay: 1,
                headerToolbar: {
                    start: 'today',
                    center: 'title',
                    end: 'prev,next'
                },
                displayEventTime: true,
                selectable: true,
                select: function ({start, end, startStr, endStr, allDay, jsEvent, view, resource}) {
                    if (moment(start).isBefore(moment(), 'day') || moment(end).isBefore(moment(), 'day')) {
                        self.fullCalendar.unselect();
                        setCheckInOut("", "", self.roomCalData);
                    } else {
                        var zone = moment(start).format("Z");
                        zone = zone.split(":");
                        zone = "" + parseInt(zone[0]) + ":00";
                        var check_in = moment(start).utcOffset(zone).format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        var check_out = moment(end).utcOffset(zone).subtract(1, 'day').format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        setCheckInOut(check_in, check_out, self.roomCalData);
                    }
                },
                events: function ({start, end, startStr, endStr, timeZone}, successCallback, failureCallback) {
                    $.ajax({
                        url: tf_options.ajax_url,
                        dataType: "json",
                        type: "POST",
                        data: {
                            action: "tf_get_hotel_availability",
                            new_post: $(self.container).find('[name="new_post"]').val(),
                            hotel_id: $(self.container).find('[name="hotel_id"]').val(),
                            room_index: $(self.container).find('[name="room_index"]').val(),
                            avail_date: $(self.container).find('.avail_date').val(),
                        },
                        beforeSend: function () {
                            $(self.container).css({'pointer-events': 'none', 'opacity': '0.5'});
                            $(self.calendar).addClass('tf-content-loading');
                        },
                        success: function (doc) {
                            if (typeof doc == "object") {
                                successCallback(doc);
                            }

                            $(self.container).css({'pointer-events': 'auto', 'opacity': '1'});
                            $(self.calendar).removeClass('tf-content-loading');
                        },
                        error: function (e) {
                            console.log(e);
                        }
                    });
                },
                eventContent: function (arg) {
                    const title = arg.event.title;
                    const eventTitleElement = document.createElement('div');
                    eventTitleElement.classList.add('fc-event-title');
                    eventTitleElement.innerHTML = title;
                    return {domNodes: [eventTitleElement]};
                },
                eventClick: function ({event, el, jsEvent, view}) {
                    let startTime = moment(event.start, String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase())
                        .format(String(tf_options.tf_admin_date_format || 'MM/DD/YYYY').toUpperCase());
                    let endTime;
                    if (event.end) {
                        endTime = moment(event.end, String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase())
                            .format(String(tf_options.tf_admin_date_format || 'MM/DD/YYYY').toUpperCase());
                    } else {
                        endTime = startTime;
                    }
                    setCheckInOut(startTime, endTime, self.roomCalData);
                    let priceBy = $(self.container).closest('.tf-single-repeater-room').find('.tf_room_pricing_by').val();
                    //if (priceBy === '1') {
                    if (typeof event.extendedProps.price != 'undefined') {
                        $("[name='tf_room_price']", self.roomCalData).val(event.extendedProps.price);
                    }
                    //} else {
                    if (typeof event.extendedProps.adult_price != 'undefined') {
                        $("[name='tf_room_adult_price']", self.roomCalData).val(event.extendedProps.adult_price);
                    }
                    if (typeof event.extendedProps.child_price != 'undefined') {
                        $("[name='tf_room_child_price']", self.roomCalData).val(event.extendedProps.child_price);
                    }
                    //}
                    if (event.extendedProps.status) {
                        $("[name='tf_room_status'] option[value=" + event.extendedProps.status + "]", self.roomCalData).prop("selected", true);
                    }
                },
            };
            this.init = function () {
                self.container = jQuery(container);
                self.calendar = container.querySelector('.tf-room-cal');
                self.roomCalData = $('.tf-room-cal-field', self.container);
                setCheckInOut('', '', self.roomCalData);
                self.initCalendar();
            }
            this.initCalendar = function () {
                if (typeof FullCalendar != 'undefined') {
                    self.fullCalendar = new FullCalendar.Calendar(self.calendar, self.fullCalendarOptions);
                    self.fullCalendar.render();
                }
            }
        };

        function setCheckInOut(check_in, check_out, roomCalData) {
            $('.tf_room_check_in', roomCalData).val(check_in);
            $('.tf_room_check_out', roomCalData).val(check_out);
        }

        function resetForm(roomCalData) {
            $('.tf_room_check_in', roomCalData).val('');
            $('.tf_room_check_out', roomCalData).val('');
            $('[name="tf_room_price"]', roomCalData).val('');
            $('[name="tf_room_adult_price"]', roomCalData).val('');
            $('[name="tf_room_child_price"]', roomCalData).val('');
        }

        const tfHotelCalendar = () => {
            $('.tf-room-cal-wrap').each(function (index, el) {
                var $this = $(this);
                var room = new roomCal(el);
                room.init();

                let checkIn = $(el).find('[name="tf_room_check_in"]').flatpickr({
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    altInput: true,
                    altFormat: tf_options.tf_admin_date_format,
                    onChange: function (selectedDates, dateStr, instance) {
                        checkOut.set('minDate', dateStr);
                    }
                });

                let checkOut = $(el).find('[name="tf_room_check_out"]').flatpickr({
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    altInput: true,
                    altFormat: tf_options.tf_admin_date_format,
                    onChange: function (selectedDates, dateStr, instance) {
                        checkIn.set('maxDate', dateStr);
                    }
                });
            });
        }
        tfHotelCalendar();

        $(document).on('click', '.tf_room_cal_update', function (e) {
            e.preventDefault();

            let btn = $(this);
            let container = btn.closest('.tf-room-cal-wrap');
            let containerEl = btn.closest('.tf-room-cal-wrap')[0];
            let cal = container.find('.tf-room-cal');
            let data = $('input, select', container.find('.tf-room-cal-field')).serializeArray();
            let priceBy = container.closest('.tf-single-repeater-room').find('.tf_room_pricing_by').val();
            let avail_date = container.find('.avail_date');
            data.push({name: 'action', value: 'tf_add_hotel_availability'});
            data.push({name: 'price_by', value: priceBy});
            data.push({name: 'avail_date', value: avail_date.val()});

            $.ajax({
                url: tf_options.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    container.css({'pointer-events': 'none', 'opacity': '0.5'})
                    cal.addClass('tf-content-loading');
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    if (typeof response == 'object') {
                        if (response.data.status === true) {
                            avail_date.val(response.data.avail_date)
                            notyf.success(response.data.message);
                            resetForm(container);

                            var room = new roomCal(containerEl);
                            room.init();
                            if (room.fullCalendar) {
                                room.fullCalendar.refetchEvents();
                            }
                        } else {
                            notyf.error(response.data.message);
                        }

                        container.css({'pointer-events': 'auto', 'opacity': '1'})
                        cal.removeClass('tf-content-loading');
                        btn.removeClass('tf-btn-loading');
                    }
                },
                error: function (e) {
                    console.log(e);
                    container.css({'pointer-events': 'auto', 'opacity': '1'})
                    cal.removeClass('tf-content-loading');
                    btn.removeClass('tf-btn-loading');
                },
                complete: function () {
                    container.css({'pointer-events': 'auto', 'opacity': '1'});
                    cal.removeClass('tf-content-loading');
                    btn.removeClass('tf-btn-loading');
                },
            });
        });

        /*$(document).on('change', '.tf_room_pricing_by', function (e) {
            let room = $(this).closest('.tf-single-repeater-room');
            let pricing_by = $(this).val();

            if (pricing_by === '1') {
                room.find('.tf-price-by-room').show();
                room.find('.tf-price-by-person').hide();
            } else if (pricing_by === '2') {
                room.find('.tf-price-by-person').show();
                room.find('.tf-price-by-room').hide();
            }
        });*/

        // Switcher Value Changed
        $(document).on("change", ".tf-switch", function (e) {
            var $this = $(this);
            if (this.checked) {
                var value = $this.val(1);
            } else {
                var value = $this.val('');
            }

            if ($this.hasClass('tf_room_availability_by_date')) {
                tfHotelCalendar();
            }
        });

        /*
        * Options WP editor
        * @author: Sydur
        */
        function TF_wp_editor($id) {
            wp.editor.initialize($id, {
                tinymce: {
                    wpautop: true,
                    plugins: 'charmap colorpicker hr lists paste tabfocus textcolor fullscreen wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
                    toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv,listbuttons',
                    toolbar2: 'styleselect,strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
                    //   textarea_rows : 20
                },
                quicktags: {buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'},
                mediaButtons: false,
            });
        }

        $('textarea.wp_editor, textarea.tf_wp_editor').each(function () {
            let $id = $(this).attr('id');
            TF_wp_editor($id);
        });

        /*
        * Booking Confirmation Field Fixed
        * @since 2.9.28
        * @author: Jahid
        */
        TF_Booking_Confirmation();

        function TF_Booking_Confirmation() {
            if ($('.tf-repeater-wrap .tf-single-repeater-book-confirm-field').length > 0) {
                $('.tf-repeater-wrap .tf-single-repeater-book-confirm-field').each(function () {
                    let $this = $(this);
                    let repeaterCount = $this.find('input[name="tf_repeater_count"]').val();
                    if (0 == repeaterCount || 1 == repeaterCount || 2 == repeaterCount) {
                        $this.find('.tf_hidden_fields').hide();
                        $this.find('.tf-repeater-icon-clone').hide();
                        $this.find('.tf-repeater-icon-delete').hide();
                    }
                });
            }
        }

        /*
        * Add New Repeater Item
        * @author: Sydur
        */
        $(document).on('click', '.tf-repeater-icon-add', function () {
            var $this = $(this);
            var $this_parent = $this.parent().parent();
            var id = $(this).attr("data-repeater-id");
            var max = $(this).attr("data-repeater-max");
            var add_value = $this_parent.find('.tf-single-repeater-clone-' + id + ' .tf-single-repeater-' + id + '').clone();
            var count = $this_parent.find('.tf-repeater-wrap-' + id + ' .tf-single-repeater-' + id + '').length;
            var parent_field = add_value.find(':input[name="tf_parent_field"]').val();
            var current_field = add_value.find(':input[name="tf_current_field"]').val();

            $this_parent.find('.tf-repeater-wrap .tf-field-notice-inner').remove();
            // Chacked maximum repeater
            if (max != '' && count >= max) {
                $this_parent.find('.tf-repeater-wrap').append('<div class="tf-field-notice-inner tf-notice-danger" style="display: block;">You have reached limit in free version. Please subscribe to Pro for unlimited access</div>');
                return false;
            }

            // Repeater Count Add Value
            add_value.find(':input[name="tf_repeater_count"]').val(count);

            // Repeater Room Unique ID
            var room_uniqueid = add_value.find('.unique-id input');
            if (typeof room_uniqueid !== "undefined") {
                add_value.find('.unique-id input').val(new Date().valueOf() + count);
            }
            let repeatDateField = add_value.find('.tf-field-date');
            if (repeatDateField.length > 0) {
                repeatDateField.find('input').each(function () {

                    if ($(this).attr('name') == '' || typeof $(this).attr('name') === "undefined") {
                        $(this).remove()
                    }
                });
                tfDateInt(repeatDateField);
            }

            let repeatTimeField = add_value.find('.tf-field-time');
            if (repeatTimeField.length > 0) {
                tfTimeInt(repeatTimeField);
            }

            let repeatColorField = add_value.find('.tf-field-color');
            if (repeatColorField.length > 0) {
                tfColorInt(repeatColorField);
            }

            if (parent_field == '') {
                // Update  repeater name And id
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                });
                var update_paren = add_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                }
                add_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {
                // Update  repeater name And id
                var update_paren = add_value.find(':input[name="tf_parent_field"]').val();
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                });
            }
            // Update Repeaterr label
            add_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                    $(this).attr("for", for_value);
                }
            });
            // Update Icon select id
            add_value.find('.tf-icon-select').each(function (index) {
                var icon_id = $(this).attr("id");
                if (typeof icon_id !== "undefined") {
                    icon_id = icon_id + index + count;
                    $(this).attr("id", icon_id)

                }
            });
            // Update Data depend id
            add_value.find('[data-depend-id]').each(function () {
                var data_depend_id = $(this).attr("data-depend-id");
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            // Update Data Controller
            add_value.find('[data-controller]').each(function () {
                var data_controller = $(this).attr("data-controller");
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][00]', '[' + current_field + '][' + count + ']');
                    $(this).attr("data-controller", data_controller);
                }
            });

            // Replace Old editor
            add_value.find('.wp-editor-wrap').each(function () {
                var textarea = $(this).find('.tf_wp_editor').show();
                // Get content of a specific editor:
                var tf_editor_ex_data = $('#' + textarea.attr('id') + '').val();
                if (tf_editor_ex_data && typeof tf_editor_ex_data !== "undefined") {
                    var textarea_content = tinymce.get(textarea.attr('id')).getContent();
                } else {
                    var textarea_content = '';
                }
                textarea.val(textarea_content);
                $(this).closest('.tf-field-textarea').append(textarea);
                $(this).remove();
            });

            // Update Data Append value
            var append = $this_parent.find('.tf-repeater-wrap-' + id + '');

            add_value.appendTo(append).show();

            // replace new editor
            add_value.find('textarea.parent_wp_editor').each(function () {
                var count = Math.random().toString(36).substring(3, 9) + 1;
                // this.id = this.id.replace('' + current_field + '__00', '' + current_field + '__' + count + '');
                $(this).attr('id', current_field + count);
                $(this).attr('data-count-id', count);
                var parent_repeater_id = $(this).attr('id');
                TF_wp_editor(parent_repeater_id);
            });

            // replace new Select 2
            add_value.find('select.tf-select2-parent').each(function () {
                this.id = this.id.replace('' + current_field + '__00', '' + current_field + '__' + count + '');
                var parent_repeater_id = $(this).attr('id');
                var $this = $(this);
                tfSelect2Int($this);
            });

            // repeater dependency repeater
            TF_dependency();

            // Booking Confirmation repeater Hidden field
            TF_Booking_Confirmation();

            tfHotelCalendar();
        });

        // Repeater Delete Value
        $(document).on('click', '.tf-repeater-icon-delete', function () {
            var max = $(this).attr("data-repeater-max");
            var $this_parent = $(this).closest('.tf-repeater-wrap');
            var count = $this_parent.find('.tf-single-repeater').length;
            // Chacked maximum repeater

            if (confirm("Are you sure to delete this item?")) {
                $this_parent.find('.tf-field-notice-inner').remove();
                $(this).closest('.tf-single-repeater').remove();
            }
            return false;
        });

        /*
        * Clone Repeater Item
        * @author: Sydur
        */
        $(document).on('click', '.tf-repeater-icon-clone', function () {
            var $this_parent = $(this).closest('.tf-repeater-wrap');
            let clone_value = $(this).closest('.tf-single-repeater').clone();
            var max = $(this).attr("data-repeater-max");
            var parent_field = clone_value.find('input[name="tf_parent_field"]').val();
            var current_field = clone_value.find('input[name="tf_current_field"]').val();
            var repeater_count = clone_value.find('input[name="tf_repeater_count"]').val();
            var count = $this_parent.find('.tf-single-repeater-' + current_field + '').length;

            $this_parent.find('.tf-field-notice-inner').remove();
            // Chacked maximum repeater
            if (max != '' && count >= max) {
                $this_parent.append('<div class="tf-field-notice-inner tf-notice-danger" style="display: block;">You have reached limit in free version. Please subscribe to Pro for unlimited access</div>');
                return false;
            }

            // Repeater Room Unique ID
            var room_uniqueid = clone_value.find('.unique-id input');
            if (typeof room_uniqueid !== "undefined") {
                clone_value.find('.unique-id input').val(new Date().valueOf() + count);
            }

            let repeatDateField = clone_value.find('.tf-field-date');

            if (repeatDateField.length > 0) {
                repeatDateField.find('input').each(function () {
                    if ($(this).attr('name') == '' || typeof $(this).attr('name') === "undefined") {
                        $(this).remove();
                    }
                });
                tfDateInt(repeatDateField);
            }

            let repeatTimeField = clone_value.find('.tf-field-time');
            if (repeatTimeField.length > 0) {
                tfTimeInt(repeatTimeField);
            }

            let repeatColorField = clone_value.find('.tf-field-color');
            if (repeatColorField.length > 0) {
                tfColorInt(repeatColorField);
            }

            if (parent_field == '') {
                // Replace input id and name
                clone_value.find(':input').each(function () {
                    if ($(this).closest('.tf-single-repeater-clone').length == 0) {
                        this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                        this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    }
                });
                var update_paren = clone_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                }
                clone_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {
                // Replace input id and name
                clone_value.find(':input').each(function () {
                    if ($(this).closest('.tf-single-repeater-clone').length == 0) {
                        this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                        this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    }
                });
            }
            clone_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    var for_value = $(this).attr("for", for_value);
                }
            });
            // Update Icon select id
            clone_value.find('.tf-icon-select').each(function (index) {
                var icon_id = $(this).attr("id");
                if (typeof icon_id !== "undefined") {
                    icon_id = icon_id + index + count;
                    $(this).attr("id", icon_id)

                }
            });
            // Replace Data depend id ID
            clone_value.find('[data-depend-id]').each(function () {
                var data_depend_id = $(this).attr("data-depend-id");
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            // Replace Data depend id ID
            clone_value.find('[data-controller]').each(function () {
                var data_controller = $(this).attr("data-controller");
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    $(this).attr("data-controller", data_controller);
                }
            });
            // Replace Data repeter Count id ID
            clone_value.find('input[name="tf_repeater_count"]').val(count)

            // Replace Old editor
            clone_value.find('.wp-editor-wrap').each(function () {
                var textarea = $(this).find('.tf_wp_editor').show();
                // Get content of a specific editor:
                var tf_editor_ex_data = $('#' + textarea.attr('id') + '').val();
                var textarea_id = textarea.attr('id');
                if (textarea_id != '' && typeof textarea_id !== "undefined") {
                    // var textarea_content = tinymce.get(textarea.attr('id')).getContent();
                    var textarea_content = tinymce.editors[textarea_id].getContent();
                } else {
                    var textarea_content = '';
                }
                textarea.val(textarea_content);
                $(this).closest('.tf-field-textarea').append(textarea);
                $(this).remove();
            });

            // Replace Old Select 2
            clone_value.find('.tf-field-select2').each(function () {

                var get_selected_value = $(this).find('select.tf-select-two').select2('val')
                $(this).find('select.tf-select-two').removeAttr("data-select2-id aria-hidden tabindex");
                $(this).find('select.tf-select-two option').removeAttr("data-select2-id");
                $(this).find('select.tf-select-two').removeClass("select2-hidden-accessible");
                var select2 = $(this).find('select.tf-select-two').show();

                select2.val(get_selected_value);
                $(this).find('.tf-fieldset').append(select2);
                $(this).find('span.select2-container').remove();
            });

            //Append Value
            $(this).closest('.tf-repeater-wrap').append(clone_value).show();

            // Clone Wp Editor
            clone_value.find('textarea.parent_wp_editor, textarea.wp_editor').each(function () {
                var count = Math.random().toString(36).substring(3, 9) + 1;
                $(this).attr('id', current_field + count);
                $(this).attr('data-count-id', count);
                var parent_repeater_id = $(this).attr('id');
                TF_wp_editor(parent_repeater_id);
            });

            // Clone Select 2
            clone_value.find('select.tf-select2-parent, select.tf-select2').each(function () {
                this.id = this.id.replace('' + current_field + '__' + repeater_count, '' + current_field + '__' + count + '');
                var $this = $(this);
                tfSelect2Int($this);
            });

            // Dependency value
            TF_dependency();

            tfHotelCalendar();
        });

        // Repeater show hide
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function () {
            var tf_repater_fieldname = $(this).closest('.tf-single-repeater').find('input[name=tf_current_field]').val();
            $(this).closest('.tf-single-repeater-' + tf_repater_fieldname + '').find('.tf-repeater-content-wrap').slideToggle();
            $(this).closest('.tf-single-repeater-' + tf_repater_fieldname + '').children('.tf-repeater-content-wrap').toggleClass('hide');
            if ($(this).closest('.tf-single-repeater-' + tf_repater_fieldname + '').children('.tf-repeater-content-wrap').hasClass('hide') == true) {
                $(this).closest('.tf-single-repeater-' + tf_repater_fieldname + ' .tf-repeater-header').children('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-down"></i>');
            } else {
                $(this).closest('.tf-single-repeater-' + tf_repater_fieldname + ' .tf-repeater-header').children('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-up"></i>');
            }

            tfHotelCalendar();
        });

        // Repeater Drag and  show
        $(".tf-repeater-wrap").sortable({
            handle: '.tf-repeater-icon-move',
            start: function (event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
                var textareaID = $(ui.item).find('.tf_wp_editor').attr('id');

            },
            stop: function (event, ui) { // re-initialize TinyMCE when sort is completed
                $(ui.item).find('.tf_wp_editor').each(function () {
                    var textareaID = $(this).attr('id');
                    tinyMCE.execCommand('mceRemoveEditor', false, textareaID);
                    tinyMCE.execCommand('mceAddEditor', false, textareaID);
                });

                // $(this).find('.update-warning').show();
            }
        });


        // TAB jquery
        $(document).on('click', '.tf-tab-item', function () {
            var $this = $(this);
            var tab_id = $this.data('tab-id');
            if ($this.parent().parent().find('.tf-tab-item-content').hasClass("show") == true) {
                $this.parent().parent().find('.tf-tab-item-content').removeClass('show');
            }

            $this.parent().find('.tf-tab-item').removeClass('show');

            $this.addClass('show');
            $this.parent().parent().find('.tf-tab-item-content[data-tab-id = ' + tab_id + ']').addClass('show');

        });

    });
})(jQuery);


function openTab(evt, tabName) {
    evt.preventDefault();
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tf-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tf-tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.target.className += " active";
    jQuery(".tf-admin-tab").removeClass('active');
}

var frame, gframe;
(function ($) {
    // Single Image remove
    $(document).on("click", ".tf-image-close", function (e) {
        e.preventDefault();
        $this = $(this);
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $this.parent().parent().find('input').val('');
        $this.parent().html('');

    });

    // Gallery Image remove
    $(document).on("click", ".tf-gallery-remove", function (e) {
        e.preventDefault();
        $this = $(this);
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $this.parent().parent().find('input').val('');
        $this.parent().parent().find('.tf-fieldset-gallery-preview').html('');
        $('a.tf-gallery-edit, a.tf-gallery-remove').css("display", "none");

    });

    $(document).ready(function () {

        // Single Image Upload

        $('body').on('click', '.tf-media-upload', function (e) {
            var $this = $(this);
            var fieldname = $(this).attr("tf-field-name");
            var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

            frame = wp.media({
                title: "Select Image",
                button: {
                    text: "Insert Image"
                },
                multiple: false
            });
            frame.on('select', function () {

                var attachment = frame.state().get('selection').first().toJSON();
                $this.parent().parent().find('input').val(attachment.url);
                $this.parent().parent().find('.tf-fieldset-media-preview').html(`<div class="tf-image-close" tf-field-name='${fieldname}'>✖</div><img src='${attachment.url}' />`);
            });
            frame.open();
            return false;
        });

        // Gallery Image Upload

        $('body').on('click', '.tf-gallery-upload, .tf-gallery-edit', function (e) {
            var $this = $(this);
            var fieldname = $(this).attr("tf-field-name");
            var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');
            gframe = wp.media({
                title: "Select Gallery",
                button: {
                    text: "Insert Gallery"
                },
                multiple: 'add'
            });

            gframe.on('open', function () {
                var selection = gframe.state().get('selection');
                var ids_value = $this.parent().parent().find('input').val();

                if (ids_value.length > 0) {
                    var ids = ids_value.split(',');

                    ids.forEach(function (id) {
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }
            });

            gframe.on('select', function () {
                var image_ids = [];
                var image_urls = [];
                var attachments = gframe.state().get('selection').toJSON();
                $this.parent().parent().find('.tf-fieldset-gallery-preview').html('');
                for (i in attachments) {
                    var attachment = attachments[i];
                    image_ids.push(attachment.id);
                    image_urls.push(attachment.url);
                    $this.parent().parent().find('.tf-fieldset-gallery-preview').append(`<img src='${attachment.url}' />`);
                }
                $this.parent().parent().find('input').val(image_ids.join(","));
                $this.parent().find('a.tf-gallery-edit, a.tf-gallery-remove').css("display", "inline-block");
            });

            gframe.open();
            return false;
        });


        // Texonomy submit event
        $('#addtag > .submit #submit').click(function () {
            $(".tf-fieldset-media-preview").html("");
        });

        if (tf_options.gmaps != "googlemap") {
            $(".tf-field-map").each(function () {
                var $this = $(this),
                    $map = $this.find('.tf--map-osm'),
                    $search_input = $this.find('.tf--map-search input'),
                    $latitude = $this.find('.tf--latitude'),
                    $longitude = $this.find('.tf--longitude'),
                    $zoom = $this.find('.tf--zoom'),
                    map_data = $map.data('map');

                var mapInit = L.map($map.get(0), map_data);


                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(mapInit);

                var mapMarker = L.marker(map_data.center, {draggable: true}).addTo(mapInit);

                var update_latlng = function (data) {
                    $latitude.val(data.lat);
                    $longitude.val(data.lng);
                    $zoom.val(mapInit.getZoom());
                };

                mapInit.on('click', function (data) {
                    mapMarker.setLatLng(data.latlng);
                    update_latlng(data.latlng);
                });

                mapInit.on('zoom', function () {
                    update_latlng(mapMarker.getLatLng());
                });

                mapMarker.on('drag', function () {
                    update_latlng(mapMarker.getLatLng());
                });

                if (!$search_input.length) {
                    $search_input = $('[data-depend-id="' + $this.find('.tf--address-field').data('address-field') + '"]');
                }

                var cache = {};

                $search_input.autocomplete({
                    source: function (request, response) {

                        var term = request.term;

                        if (term in cache) {
                            response(cache[term]);
                            return;
                        }

                        $.get('https://nominatim.openstreetmap.org/search', {
                            format: 'json',
                            q: term,
                        }, function (results) {

                            var data;

                            if (results.length) {
                                data = results.map(function (item) {
                                    return {
                                        value: item.display_name,
                                        label: item.display_name,
                                        lat: item.lat,
                                        lon: item.lon
                                    };
                                }, 'json');
                            } else {
                                data = [{
                                    value: 'no-data',
                                    label: 'No Results.'
                                }];
                            }

                            cache[term] = data;
                            response(data);

                        });

                    },
                    select: function (event, ui) {

                        if (ui.item.value === 'no-data') {
                            return false;
                        }

                        var latLng = L.latLng(ui.item.lat, ui.item.lon);

                        mapInit.panTo(latLng);
                        mapMarker.setLatLng(latLng);
                        update_latlng(latLng);

                    },
                    create: function (event, ui) {
                        $(this).autocomplete('widget').addClass('tf-map-ui-autocomplate');
                    }
                });

                var input_update_latlng = function () {

                    var latLng = L.latLng($latitude.val(), $longitude.val());

                    mapInit.panTo(latLng);
                    mapMarker.setLatLng(latLng);

                };

                $latitude.on('change', input_update_latlng);
                $longitude.on('change', input_update_latlng);

                setInterval(function () {
                    mapInit.invalidateSize();
                }, 100);
            });
        }

        $('.tf-mobile-tabs').click(function (e) {
            e.preventDefault();
            $(".tf-admin-tab").toggleClass('active');
        });


        $('.tf-faq-title').click(function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-faq-desc").slideUp(400);
                $(".tf-faq-title").removeClass("active");
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
        });
    });


})(jQuery);


// Field Dependency

(function ($) {

    'use strict';

    function Rule(controller, condition, value) {
        this.init(controller, condition, value);
    }

    $.extend(Rule.prototype, {

        init: function (controller, condition, value) {

            this.controller = controller;
            this.condition = condition;
            this.value = value;
            this.rules = [];
            this.controls = [];

        },

        evalCondition: function (context, control, condition, val1, val2) {

            if (condition == '==') {

                return this.checkBoolean(val1) == this.checkBoolean(val2);

            } else if (condition == '!=') {

                return this.checkBoolean(val1) != this.checkBoolean(val2);

            } else if (condition == '>=') {

                return Number(val2) >= Number(val1);

            } else if (condition == '<=') {

                return Number(val2) <= Number(val1);

            } else if (condition == '>') {

                return Number(val2) > Number(val1);

            } else if (condition == '<') {

                return Number(val2) < Number(val1);

            } else if (condition == '()') {

                return window[val1](context, control, val2);

            } else if (condition == 'any') {

                if ($.isArray(val2)) {
                    for (var i = val2.length - 1; i >= 0; i--) {
                        if ($.inArray(val2[i], val1.split(',')) !== -1) {
                            return true;
                        }
                    }
                } else {
                    if ($.inArray(val2, val1.split(',')) !== -1) {
                        return true;
                    }
                }

            } else if (condition == 'not-any') {

                if ($.isArray(val2)) {
                    for (var i = val2.length - 1; i >= 0; i--) {
                        if ($.inArray(val2[i], val1.split(',')) == -1) {
                            return true;
                        }
                    }
                } else {
                    if ($.inArray(val2, val1.split(',')) == -1) {
                        return true;
                    }
                }

            }

            return false;

        },

        checkBoolean: function (value) {

            switch (value) {

                case true:
                case 'true':
                case 1:
                case '1':
                    value = true;
                    break;

                case null:
                case false:
                case 'false':
                case 0:
                case '0':
                    value = false;
                    break;

            }

            return value;
        },

        checkCondition: function (context) {

            if (!this.condition) {
                return true;
            }

            var control = context.find(this.controller);

            var control_value = this.getControlValue(context, control);

            if (control_value === undefined) {
                return false;
            }

            control_value = this.normalizeValue(control, this.value, control_value);

            return this.evalCondition(context, control, this.condition, this.value, control_value);
        },

        normalizeValue: function (control, baseValue, control_value) {

            if (typeof baseValue == 'number') {
                return parseFloat(control_value);
            }

            return control_value;
        },

        getControlValue: function (context, control) {

            if (control.length > 1 && (control.attr('type') == 'radio' || control.attr('type') == 'checkbox')) {

                return control.filter(':checked').map(function () {
                    return this.value;
                }).get();

            } else if (control.attr('type') == 'checkbox' || control.attr('type') == 'radio') {

                return control.is(':checked');

            }

            return control.val();

        },

        createRule: function (controller, condition, value) {
            var rule = new Rule(controller, condition, value);
            this.rules.push(rule);
            return rule;
        },

        include: function (input) {
            this.controls.push(input);
        },

        applyRule: function (context, enforced) {

            var result;

            if (typeof (enforced) == 'undefined') {
                result = this.checkCondition(context);
            } else {
                result = enforced;
            }

            var controls = $.map(this.controls, function (elem, idx) {
                return context.find(elem);
            });

            if (result) {

                $(controls).each(function () {
                    $(this).removeClass('tf-depend-on');
                });

                $(this.rules).each(function () {
                    this.applyRule(context);
                });

            } else {

                $(controls).each(function () {
                    $(this).addClass('tf-depend-on');
                });

                $(this.rules).each(function () {
                    this.applyRule(context, false);
                });

            }
        }
    });

    function Ruleset() {
        this.rules = [];
    };

    $.extend(Ruleset.prototype, {

        createRule: function (controller, condition, value) {
            var rule = new Rule(controller, condition, value);
            this.rules.push(rule);
            return rule;
        },

        applyRules: function (context) {
            $(this.rules).each(function () {
                this.applyRule(context);
            });
        }
    });

    $.tf_deps = {

        createRuleset: function () {
            return new Ruleset();
        },

        enable: function (selection, ruleset, depends) {

            selection.on('change keyup', function (elem) {

                var depend_id = elem.target.getAttribute('data-depend-id') || elem.target.getAttribute('data-sub-depend-id');

                if (depends.indexOf(depend_id) !== -1) {
                    ruleset.applyRules(selection);
                }

            });

            ruleset.applyRules(selection);

            return true;
        }
    };

})(jQuery);

/*
* Author @Jahid
* Report Chart
*/

(function ($) {
    $(document).ready(function () {
        if (tf_options.tf_chart_enable == 1) {
            var ctx = document.getElementById('tf_months'); // node
            var ctx = document.getElementById('tf_months').getContext('2d'); // 2d context
            var ctx = $('#tf_months'); // jQuery instance
            var ctx = 'tf_months'; // element id

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    // Information about the dataset
                    datasets: [{
                        label: "Completed Booking",
                        borderColor: '#003C79',
                        tension: 0.1,
                        data: tf_options.tf_complete_order,
                        fill: false
                    },
                        {
                            label: "Cancelled Booking",
                            borderColor: 'red',
                            tension: 0.1,
                            data: tf_options.tf_cancel_orders,
                            fill: false
                        }
                    ]
                },

                // Configuration options
                options: {
                    layout: {
                        padding: 10,
                    },
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: ""
                    }
                }

            });
        }

        $(document).on('change', '#tf-month-report', function () {
            var monthTarget = $(this).val();
            if (monthTarget != 0) {
                $("#tf-report-loader").addClass('show');
                $('.tf-order-report').find('iframe').remove();
                var yearTarget = $("#tf-year-report").val();
                jQuery.ajax({
                    type: 'post',
                    url: tf_options.ajax_url,
                    data: {
                        action: 'tf_month_reports',
                        month: monthTarget,
                        year: yearTarget,
                    },
                    success: function (data) {
                        var response = JSON.parse(data);
                        var ctx = document.getElementById('tf_months'); // node
                        var ctx = document.getElementById('tf_months').getContext('2d'); // 2d context
                        var ctx = $('#tf_months'); // jQuery instance
                        var ctx = 'tf_months'; // element id

                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: response.months_day_number,
                                // Information about the dataset
                                datasets: [{
                                    label: "Completed Booking",
                                    borderColor: '#003C79',
                                    tension: 0.1,
                                    data: response.tf_complete_orders,
                                    fill: false
                                },
                                    {
                                        label: "Cancelled Booking",
                                        borderColor: 'red',
                                        tension: 0.1,
                                        data: response.tf_cancel_orders,
                                        fill: false
                                    }
                                ]
                            },

                            // Configuration options
                            options: {
                                layout: {
                                    padding: 10,
                                },
                                legend: {
                                    display: true
                                },
                                title: {
                                    display: true,
                                    text: response.tf_search_month
                                }
                            }

                        });
                        $("#tf-report-loader").removeClass('show');
                    }
                })
            }
        });


        $(document).on('change', '#tf-year-report', function () {
            var yearTarget = $(this).val();
            var monthTarget = $("#tf-month-report").val();
            if (yearTarget != 0 && monthTarget != 0) {
                $("#tf-report-loader").addClass('show');
                $('.tf-order-report').find('iframe').remove();
                jQuery.ajax({
                    type: 'post',
                    url: tf_options.ajax_url,
                    data: {
                        action: 'tf_month_reports',
                        month: monthTarget,
                        year: yearTarget,
                    },
                    success: function (data) {
                        var response = JSON.parse(data);
                        var ctx = document.getElementById('tf_months'); // node
                        var ctx = document.getElementById('tf_months').getContext('2d'); // 2d context
                        var ctx = $('#tf_months'); // jQuery instance
                        var ctx = 'tf_months'; // element id

                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: response.months_day_number,
                                // Information about the dataset
                                datasets: [{
                                    label: "Completed Booking",
                                    borderColor: '#003C79',
                                    tension: 0.1,
                                    data: response.tf_complete_orders,
                                    fill: false
                                },
                                    {
                                        label: "Cancelled Booking",
                                        borderColor: 'red',
                                        tension: 0.1,
                                        data: response.tf_cancel_orders,
                                        fill: false
                                    }
                                ]
                            },

                            // Configuration options
                            options: {
                                layout: {
                                    padding: 10,
                                },
                                legend: {
                                    display: true
                                },
                                title: {
                                    display: true,
                                    text: response.tf_search_month
                                }
                            }

                        });
                        $("#tf-report-loader").removeClass('show');
                    }
                })
            }
        });

    });
})(jQuery);

/**
 * Shortcode generator js
 * @author Abu Hena
 * @since 2.9.3
 */
(function ($) {
    //get each of the field value
    $(document).on('click', '.tf-generate-tour .tf-btn', function (event) {
        event.preventDefault();
        var arr = [];

        $(this).parents('.tf-shortcode-generator-single').find(".tf-sg-field-wrap").each(function () {
            var $this = $(this);
            var data = $this.find('.tf-setting-field').val();
            var option_name = $this.find('.tf-setting-field').attr('data-term');
            var post_count = $this.find('.post-count').attr('data-count');

            if (option_name != undefined && option_name != '') {
                data = option_name + '=' + (data.length ? data : '""');
            }
            if (post_count != undefined && post_count != '') {
                data = post_count + '=' + (data.length ? data : '""');
            }
            arr.push(data);
        });

        var allData = arr.filter(Boolean);
        var shortcode = "[" + allData.join(' ') + "]";

        $(this).parents('.tf-shortcode-generator-single').find('.tf-shortcode-value').val(shortcode);
        $(this).parents('.tf-shortcode-generator-single').find('.tf-copy-item').slideDown();
    });

    $(document).on('click', '.tf-sg-close', function (event) {
        $(this).parents('.tf-shortcode-generators').find('.tf-sg-form-wrapper').fadeOut();
    });

    $(document).on('click', '.tf-shortcode-btn', function (event) {
        var $this = $(this);
        $this.parents('.tf-shortcode-generator-single').find('.tf-sg-form-wrapper').fadeIn();

        $this.parents('.tf-shortcode-generator-single').mouseup(function (e) {
            var container = $(this).find(".tf-shortcode-generator-form");
            var container_parent = container.parent(".tf-sg-form-wrapper");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container_parent.fadeOut();
            }
        });

    });

    //Copy the shortcode value
    $(document).on('click', '.tf-copy-btn', function () {
        var fieldIdValue = $(this).parent('.tf-shortcode-field').find('#tf-shortcode');
        if (fieldIdValue) {
            fieldIdValue.select();
            document.execCommand("copy");
        }
        //show the copied message
        $(this).parents('.tf-copy-item').append('<div><span class="tf-copied-msg">Copied<span></div>');
        $("span.tf-copied-msg").animate({opacity: 0}, 1000, function () {
            $(this).slideUp('slow', function () {
                $(this).remove();
            });
        });
    });


})(jQuery);

