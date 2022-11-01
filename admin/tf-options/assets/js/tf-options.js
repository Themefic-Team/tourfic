(function ($) {
    'use strict';
    $(document).ready(function () {

        /*
        * window url on change tab click
        * @author: Foysal
        */
        $(window).on('hashchange load', function () {
            let firstTabId;
            let hash = window.location.hash;
            let slug = hash.replace('#tab=', '');

            if (hash) {
                let selectedTab = $('.tf-tablinks[data-tab="' + slug + '"]'),
                    parentDiv = selectedTab.closest('.tf-admin-tab-item');

                selectedTab.trigger('click');
                parentDiv.trigger('click');
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
                tabcontent = $('.tf-tab-content'),
                tablinks = $('.tf-tablinks');

            tabcontent.hide();
            tablinks.removeClass('active');

            let tabId = $this.attr('data-tab');
            $('#' + tabId).show();

            if($this.next().hasClass('tf-submenu')) {
                firstTabId = parentDiv.find('.tf-submenu li:first-child .tf-tablinks').data('tab');
            }

            if(firstTabId === tabId) {
                parentDiv.find('.tf-submenu li:first-child .tf-tablinks').addClass('active');
            } else {
                $this.addClass('active');
            }
            // url hash update
            window.location.hash = '#tab=' + tabId;
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
                    multiple = dateField.data('multiple');

                if (dateField.length === 2) {
                    let startDate = $this.find('.tf-date-from input.flatpickr').flatpickr({
                        dateFormat: format,
                        onChange: function (selectedDates, dateStr, instance) {
                            endDate.set('minDate', dateStr);
                        }
                    });
                    let endDate = $this.find('.tf-date-to input.flatpickr').flatpickr({
                        dateFormat: format,
                        onChange: function (selectedDates, dateStr, instance) {
                            startDate.set('maxDate', dateStr);
                        }
                    });
                } else {
                    dateField.flatpickr({
                        dateFormat: format,
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
        * Each select2 field initialize select2
        * @author: Foysal
        */
        const tfSelect2Int = select2Selector => {
            $(select2Selector).each(function () {
                let $this = $(this),
                    selectField = $this.find('select.tf-select2'),
                    placeholder = selectField.data('placeholder');

                selectField.select2({
                    placeholder: placeholder,
                    allowClear: true,
                });
            });
        }
        tfSelect2Int('.tf-field-select2');

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
        function TF_dependency (){
            $('.tf-tab-content, .tf-taxonomy-metabox').each(function () { 
                var $this = $(this);
                $this.find('[data-controller]').each(function (){
                   var $tffields = $(this);
                    if ($tffields.length) {
                        // alert($tffields.length);
                            var normal_ruleset = $.csf_deps.createRuleset(),
                            global_ruleset = $.csf_deps.createRuleset(),
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
                                $.csf_deps.enable($this, normal_ruleset, normal_depends);
                            }
                            
                            if (global_depends.length) {
                                $.csf_deps.enable(CSF.vars.$body, global_ruleset, global_depends);
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
        $(document).on('click', '.tf-ajax-save', function (e) {
            e.preventDefault();
            let $this = $(this),
                form = $this.closest('form.tf-option-form'),
                data = form.serializeArray();

            $.ajax({
                url: tf_options.ajax_url,
                type: 'POST',
                data: {
                    optionData: data,
                    action: 'tf_options_save',
                },
                beforeSend: function () {
                    $this.addClass('tf-loading');
                },
                success: function (response) {
                    $this.removeClass('tf-loading');
                    $this.addClass('tf-success');
                    setTimeout(function () {
                        $this.removeClass('tf-success');
                    }, 2000);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });

        // Repeater jquery
        $(document).on('click', '.tf-repeater-icon-add', function () {
            // $(this).closest('.tf-single-repeater').remove();
            var $this = $(this);
            var $this_parent = $this.parent().parent();
            var id = $(this).attr("data-repeater-id");
            //    alert(id);
            var add_value = $this_parent.find('.tf-single-repeater-clone-' + id + ' .tf-single-repeater-' + id + '').clone();
            var count = $this_parent.find('.tf-repeater-wrap-' + id + ' .tf-single-repeater-' + id + '').length;
            var parent_field = add_value.find(':input[name="tf_parent_field"]').val();
            var current_field = add_value.find(':input[name="tf_current_field"]').val();

            add_value.find(':input[name="tf_repeater_count"]').val(count);
            let repeatDateField = add_value.find('.tf-field-date');
            if (repeatDateField.length > 0) {
                tfDateInt(repeatDateField);
            }

            let repeatTimeField = add_value.find('.tf-field-time');
            if (repeatTimeField.length > 0) {
                tfTimeInt(repeatTimeField);
            }

            let repeatSelect2Field = add_value.find('.tf-field-select2');
            if (repeatSelect2Field.length > 0) {
                tfSelect2Int(repeatSelect2Field);
            }

            let repeatColorField = add_value.find('.tf-field-color');
            if (repeatColorField.length > 0) {
                tfColorInt(repeatColorField);
            }

            if (parent_field == '') {
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                });
                var update_paren = add_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                }
                add_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {
                var update_paren = add_value.find(':input[name="tf_parent_field"]').val();
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                });
            }
            add_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']');
                    $(this).attr("for", for_value);
                }
            });
            add_value.find('[data-depend-id]').each(function () { 
                var data_depend_id = $(this).attr("data-depend-id"); 
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']'); 
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            add_value.find('[data-controller]').each(function () { 
                var data_controller = $(this).attr("data-controller"); 
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][0]', '[' + current_field + '][' + count + ']'); 
                    $(this).attr("data-controller", data_controller);
                }
            });
            

            var append = $this_parent.find('.tf-repeater-wrap-' + id + '');
            add_value.appendTo(append).show();
            TF_dependency();
        });

        $(document).on('click', '.tf-repeater-icon-delete', function () {
            if (confirm("Are you sure to delete this item?")) {
                $(this).closest('.tf-single-repeater').remove();
            }
            return false;
        });
        $(document).on('click', '.tf-repeater-icon-clone', function () {
            var $this_parent = $(this).closest('.tf-repeater-wrap');
            let clone_value = $(this).closest('.tf-single-repeater').clone();

            var parent_field = clone_value.find('input[name="tf_parent_field"]').val();
            var current_field = clone_value.find('input[name="tf_current_field"]').val();
            var repeater_count = clone_value.find('input[name="tf_repeater_count"]').val();
            var count = $this_parent.find('.tf-single-repeater-' + current_field + '').length;


            let repeatDateField = clone_value.find('.tf-field-date');

            if (repeatDateField.length > 0) {
                tfDateInt(repeatDateField);
            }

            let repeatTimeField = clone_value.find('.tf-field-time');
            if (repeatTimeField.length > 0) {
                tfTimeInt(repeatTimeField);
            }

            let repeatSelect2Field = clone_value.find('.tf-field-select2');
            if (repeatSelect2Field.length > 0) {
                tfSelect2Int(repeatSelect2Field);
            }

            let repeatColorField = clone_value.find('.tf-field-color');
            if (repeatColorField.length > 0) {
                tfColorInt(repeatColorField);
            }
            if (parent_field == '') {
                clone_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                });
                var update_paren = clone_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                }
                clone_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {

                clone_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                });
            }
            clone_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']');
                    var for_value = $(this).attr("for", for_value);
                }
            });
            clone_value.find('[data-depend-id]').each(function () { 
                var data_depend_id = $(this).attr("data-depend-id"); 
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']'); 
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            clone_value.find('[data-controller]').each(function () { 
                var data_controller = $(this).attr("data-controller"); 
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + count + ']'); 
                    $(this).attr("data-controller", data_controller);
                }
            });

            clone_value.find('input[name="tf_repeater_count"]').val(count)
            $(this).closest('.tf-repeater-wrap').append(clone_value).show();
            TF_dependency();
        });
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function () { 
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').slideToggle();
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').toggleClass('hide');
            if ($(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').hasClass('hide') == true) {
                $(this).closest('.tf-single-repeater').find('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-up"></i>');
            } else {
                $(this).closest('.tf-single-repeater').find('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-down"></i>');
            }
        });
        $(".tf-repeater-wrap").sortable();


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
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $('input[name="' + fieldname + '"]').val('');
        $('.' + tf_preview_class + '').html('');

    });

    // Gallery Image remove
    $(document).on("click", ".tf-gallery-remove", function (e) {
        e.preventDefault();
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $('input[name="' + fieldname + '"]').val('');
        $('.tf-fieldset > .' + tf_preview_class + '').html('');
        $('a.' + tf_preview_class + '').css("display", "none");

    });

    $(document).ready(function () {

        // Single Image Upload

        $('body').on('click', '.tf-media-upload', function (e) {
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
                $('input[name="' + fieldname + '"]').val(attachment.url);
                $('.' + tf_preview_class + '').html(`<div class="tf-image-close" tf-field-name='${fieldname}'>✖</div><img src='${attachment.sizes.thumbnail.url}' />`);
            });
            frame.open();
            return false;
        });

        // Gallery Image Upload

        $('body').on('click', '.tf-gallery-upload', function (e) {
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
                var ids_value = jQuery('input[name="' + fieldname + '"]').val();

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
                $('.tf-fieldset > .' + tf_preview_class + '').html('');
                for (i in attachments) {
                    var attachment = attachments[i];
                    image_ids.push(attachment.id);
                    image_urls.push(attachment.sizes.thumbnail.url);
                    $('.tf-fieldset > .' + tf_preview_class + '').append(`<img src='${attachment.sizes.thumbnail.url}' />`);
                }
                $('input[name="' + fieldname + '"]').val(image_ids.join(","));
                $('a.' + tf_preview_class + '').css("display", "inline-block");
            });

            gframe.open();
            return false;
        });
        // Texonomy submit event
        $('#addtag > .submit #submit').click(function () {
            $(".tf-fieldset-media-preview").html("");
        });

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

        });

        $('.tf-mobile-tabs').click(function (e) {
            e.preventDefault();
            $(".tf-admin-tab").toggleClass('active');
        });
    });


})(jQuery);


// Field Dependency

(function($) {

    'use strict';
  
    function Rule(controller, condition, value) {
      this.init(controller, condition, value);
    }
  
    $.extend(Rule.prototype, {
  
      init: function(controller, condition, value) {
  
        this.controller = controller;
        this.condition  = condition;
        this.value      = value;
        this.rules      = [];
        this.controls   = [];
  
      },
  
      evalCondition: function(context, control, condition, val1, val2) {
  
        if( condition == '==' ) {
  
          return this.checkBoolean(val1) == this.checkBoolean(val2);
  
        } else if( condition == '!=' ) {
  
          return this.checkBoolean(val1) != this.checkBoolean(val2);
  
        } else if( condition == '>=' ) {
  
          return Number(val2) >= Number(val1);
  
        } else if( condition == '<=' ) {
  
          return Number(val2) <= Number(val1);
  
        } else if( condition == '>' ) {
  
          return Number(val2) > Number(val1);
  
        } else if( condition == '<' ) {
  
          return Number(val2) < Number(val1);
  
        } else if( condition == '()' ) {
  
          return window[val1](context, control, val2);
  
        } else if( condition == 'any' ) {
  
          if( $.isArray( val2 ) ) {
            for (var i = val2.length - 1; i >= 0; i--) {
              if( $.inArray( val2[i], val1.split(',') ) !== -1 ) {
                return true;
              }
            }
          } else {
            if( $.inArray( val2, val1.split(',') ) !== -1 ) {
              return true;
            }
          }
  
        } else if( condition == 'not-any' ) {
  
          if( $.isArray( val2 ) ) {
            for (var i = val2.length - 1; i >= 0; i--) {
              if( $.inArray( val2[i], val1.split(',') ) == -1 ) {
                return true;
              }
            }
          } else {
            if( $.inArray( val2, val1.split(',') ) == -1 ) {
              return true;
            }
          }
  
        }
  
        return false;
  
      },
  
      checkBoolean: function(value) {
  
        switch( value ) {
  
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
  
      checkCondition: function( context ) {
  
        if( !this.condition ) {
          return true;
        }
  
        var control = context.find(this.controller);
  
        var control_value = this.getControlValue(context, control);
  
        if( control_value === undefined ) {
          return false;
        }
  
        control_value = this.normalizeValue(control, this.value, control_value);
  
        return this.evalCondition(context, control, this.condition, this.value, control_value);
      },
  
      normalizeValue: function( control, baseValue, control_value ) {
  
        if( typeof baseValue == 'number' ) {
          return parseFloat( control_value );
        }
  
        return control_value;
      },
  
      getControlValue: function(context, control) {
  
        if( control.length > 1 && ( control.attr('type') == 'radio' || control.attr('type') == 'checkbox' ) ) {
  
          return control.filter(':checked').map(function() { return this.value; }).get();
  
        } else if ( control.attr('type') == 'checkbox' || control.attr('type') == 'radio' ) {
  
          return control.is(':checked');
  
        }
  
        return control.val();
  
      },
  
      createRule: function(controller, condition, value) {
        var rule = new Rule(controller, condition, value);
        this.rules.push(rule);
        return rule;
      },
  
      include: function(input) {
        this.controls.push(input);
      },
  
      applyRule: function(context, enforced) {
  
        var result;
  
        if( typeof( enforced ) == 'undefined' ) {
          result = this.checkCondition(context);
        } else {
          result = enforced;
        }
  
        var controls = $.map(this.controls, function(elem, idx) {
          return context.find(elem);
        });
  
        if( result ) {
  
          $(controls).each(function() {
            $(this).removeClass('csf-depend-on');
          });
  
          $(this.rules).each(function() {
            this.applyRule(context);
          });
  
        } else {
  
          $(controls).each(function() {
            $(this).addClass('csf-depend-on');
          });
  
          $(this.rules).each(function() {
            this.applyRule(context, false);
          });
  
        }
      }
    });
  
    function Ruleset() {
      this.rules = [];
    };
  
    $.extend(Ruleset.prototype, {
  
      createRule: function(controller, condition, value) {
        var rule = new Rule(controller, condition, value);
        this.rules.push(rule);
        return rule;
      },
  
      applyRules: function(context) {
        $(this.rules).each(function() {
          this.applyRule(context);
        });
      }
    });
  
    $.csf_deps = {
  
      createRuleset: function() {
        return new Ruleset();
      },
  
      enable: function(selection, ruleset, depends) {
  
        selection.on('change keyup', function(elem) {
  
          var depend_id = elem.target.getAttribute('data-depend-id') || elem.target.getAttribute('data-sub-depend-id');
  
          if( depends.indexOf( depend_id ) !== -1 ) {
            ruleset.applyRules(selection);
          }
  
        });
  
        ruleset.applyRules(selection);
  
        return true;
      }
    };
  
  })(jQuery);
  
  (function ($) {
      'use strict';
      $(document).ready(function () {
        
        
    });
  })(jQuery);