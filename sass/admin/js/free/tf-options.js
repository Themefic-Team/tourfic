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

            tfApartmentCalendar()
            tfHotelCalendar()
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

            roomOptionsArr();
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
            if(!$('.tf-map-modal').length) {
                if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                    $("body").removeClass("tf-modal-open");
                    $(".tf-modal").removeClass("tf-modal-show");
                }
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
            let searchVal = $('.tf-icon-search-input').val();

            tfIconInfiniteScroll();
            tfIconFilter(searchVal);
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
        * Icon search
        * @author: Foysal
        */
        //debounce
        const debounce = (func, delay) => {
            let debounceTimer;
            return function () {
                const context = this;
                const args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            }
        }

        $(document).on('keyup', '.tf-icon-search-input', debounce(function (e) {
            let searchVal = $(this).val();
            tfIconFilter(searchVal);
        }, 500));

        const tfIconFilter = (searchVal) => {
            let type = $('.tf-icon-tab-pane.active').data('type');
            let iconList = $('.tf-icon-tab-pane.active .tf-icon-list');

            $.ajax({
                url: tf_options.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_icon_search',
                    _nonce: tf_admin_params.tf_nonce,
                    search: searchVal,
                    type: type,
                },
                beforeSend: function () {
                    iconList.html('<div class="tf-icon-loading">Loading...</div>');
                },
                success: function (response) {
                    if(!response.success){
                        notyf.error(response.data)
                    } else {
                        iconList.html(response.data.html);
                        $('.tf-icon-tab-pane.active').attr('data-max', response.data.count);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        /*
        * Icon Infinite Scroll
        * @author: Foysal
        */
        const tfIconInfiniteScroll = () => {
            var loading = false;
            var startIndex = 100;
            let iconList = $('.tf-icon-tab-pane.active .tf-icon-list');
            let iconListBottom = 0;
            let searchVal = $('.tf-icon-search-input').val();

            iconList.on("scroll", function () {
                let type = $('.tf-icon-tab-pane.active').data('type');
                let max = $('.tf-icon-tab-pane.active').data('max');
                iconListBottom = iconList[0].scrollHeight - iconList.height();

                if (iconList.scrollTop() >= iconListBottom && !loading && startIndex < max) {
                    loading = true;
                    $.ajax({
                        url: tf_options.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'tf_load_more_icons',
                            _nonce: tf_admin_params.tf_nonce,
                            start_index: startIndex,
                            type: type,
                            search: searchVal,
                        },
                        beforeSend: function () {
                            $('.tf-icon-list').append('<div class="tf-icon-loading">Loading...</div>');
                        },
                        success: function (response) {
                            if(!response.success){
                                $('.tf-icon-loading').remove();
                                notyf.error(response.data)
                            } else {
                                loading = false;
                                $('#tf-icon-tab-'+type+' .tf-icon-list').append(response.data);
                                $('.tf-icon-loading').remove();
                                startIndex += 100;
                            }

                        },
                        error: function (xhr, status, error) {
                            loading = false;
                            console.error(error);
                            $('.tf-icon-loading').remove();
                        }
                    });
                }
            });
        }
        tfIconInfiniteScroll();

        /*
        * Options ajax save
        * @author: Foysal
        */

        $(document).on("click", '.tf-setting-save-btn .tf-submit-btn', function (e) {
            e.preventDefault();
            $('.tf-option-form.tf-ajax-save').submit();

        })

        $(document).on('click', '.tf-setting-save-btn .tf-reset-btn', function (e) {

            $.confirm({
                icon: 'fa fa-warning',
                title: tf_options.swal_reset_title_text,
                content: tf_options.swal_reset_other_text,
                type: 'red',
                typeAnimated: false,
                boxWidth: '500px',
                animationSpeed: 500,
                animation: 'scale',
                closeAnimation: 'scale',
                animateFromElement: false,
                useBootstrap: false,
                theme: 'modern',
                buttons: {
                    confirm: {
                        text: tf_options.swal_reset_btn_text,
                        btnClass: 'btn-blue',
                        action: function () {
                            $.ajax({
                                url: tf_options.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'tf_options_reset',
                                    tf_option_nonce: tf_admin_params.tf_nonce,
                                },
                                beforeSend: function () {
                                    $('.tf-setting-save-btn .tf-reset-btn').addClass('tf-btn-loading');
                                },
                                success: function (response) {

                                    let data = JSON.parse(response)
                                    
                                    if (data.status === 'success') {
                                        notyf.success(data.message);
                                        window.location.reload();
                                    } else {
                                        notyf.error(data.message);
                                    }
                                    
                                    $('.tf-setting-save-btn .tf-reset-btn').removeClass('tf-btn-loading');
                                },
                                error: function (xhr, status, error) {
                                    console.log(error);
                                }
                            }).done(function () {
                                // window.location.reload();
                            });
                        }
                    },
                    cancel: {
                        text: tf_options.swal_reset_cancel_btn_text,
                        btnClass: 'btn-red',
                    }
                }
            })

            });

        $(document).find("#tf-settings-header-search-filed").on("keyup", debounce(
            function () {
                var value = $(this).val().toLowerCase();
                let div = document.createElement('div');
                div.classList.add('tf-search-results');
                if( value.length >= 3 ) {
                    $.ajax({
                        url: tf_options.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'tf_search_settings_autocomplete',
                            tf_option_nonce: tf_admin_params.tf_nonce,
                            search: value,
                        },
                        success: function (response) {
                            let data = JSON.parse(response)
                            let notfound = 0;
                            let resultDiv = document.createElement('ul');
                            if (data.status === 'success') {
                                $.each( data.message, function( key, obj ) {
                                    if( obj.field_title.toLowerCase().indexOf(value) != -1 ) {
                                        let textDiv = document.createElement('li');
                                        let titleDiv = document.createElement('div');
                                        titleDiv.classList.add('tf-search-result-title');
                                        let link = document.createElement('a');
                                        link.href = `#tab=${obj.parent_id}`;
                                        let icon = document.createElement('i');
                                        let title = document.createElement('p');
                                        let path = document.createElement('span');
                                        title.innerHTML = obj.field_title;
                                        path.innerHTML = obj.path;
                                        icon.classList.add(...obj.icon.split(' '));
                                        resultDiv.classList.add('tf-search-result');
                                        textDiv.setAttribute('data-id', obj.id);
                                        textDiv.setAttribute('data-tab-id', obj.tab_id);
                                        link.append(icon);
                                        titleDiv.append(title);
                                        titleDiv.append(path);
                                        link.append(titleDiv);
                                        textDiv.append(link);
                                        resultDiv.append(textDiv);
                                    } else {
                                        notfound = 1;
                                    }
                                    if( $('.tf-search-results').length || value < 3 ) {
                                        $('.tf-search-results').remove();
                                    } else {
                                        div.append(resultDiv);
                                    }
                                });

                                if( notfound == 1 ) {
                                    let not_found = document.createElement("p");
                                    not_found.classList.add('tf-search-not-found');
                                    not_found.innerHTML = tf_admin_params.setting_search_no_result;
                                    resultDiv.append(not_found);
                                }
                                $(".tf-setting-search").append(div);
                            } else {
                                console.log("Something went wrong!");
                            }
                        }
                    })
                    
                } else {
                    $(".tf-search-results").hide();
                }
            }, 700 
        ));

        $(document).on('click', function (e) {
            if( e.target.id !== 'tf-settings-header-search-filed' && $('.tf-search-results').length ) {
                $('.tf-search-results').hide();
            }
        });


        $("#tf-settings-header-search-filed").on('focus', function (e) {
            if( $('.tf-search-results').length ) {
                $('.tf-search-results').show();
            }
        });

        $(document).on('click', '.tf-search-result li', function (e) {
            let id = $(this).data('id');
            let selector = `label[for='tf_settings\\[${id}\\]']`;
            let tabId = $(this).closest('li').data('tab-id');
            if( tabId ) {
                $('.tf-tab-item[data-tab-id="'+tabId+'"]').trigger('click');
            }
            $('html, body').animate({
                scrollTop: $(document).find(selector).closest('.tf-field').offset().top
            }, 100);
        
        });

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
            // get tf_import_option from data
            let tf_import_option =  false
            if (typeof data.get('tf_import_option') !== "undefined" && data.get('tf_import_option').trim() != '') {

                //  confirm data before send
                if (!confirm(tf_options.tf_export_import_msg.import_confirm)) {
                    return;
                }

                tf_import_option = true;
            }
            data.append('action', 'tf_options_save');

            $.ajax({
                url: tf_options.ajax_url,
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    if(tf_import_option == true ){
                        $this.find('.tf-import-btn').addClass('tf-btn-loading');
                    }
                    submitBtn.addClass('tf-btn-loading');
                    $('.tf-setting-save-btn .tf-submit-btn').addClass('tf-btn-loading');
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    if (obj.status === 'success') {
                        
                        notyf.success(obj.message);

                        if(tf_import_option == true ){
                            window.location.reload();;
                        }
                    } else {
                        notyf.error(obj.message);
                    }
                    submitBtn.removeClass('tf-btn-loading');
                    $(".tf-setting-save-btn .tf-submit-btn").removeClass('tf-btn-loading');
                    if(tf_import_option == true ){
                        $this.find('.tf-import-btn').removeClass('tf-btn-loading');
                    }
                },
                error: function (error) {
                    submitBtn.removeClass('tf-btn-loading');
                    console.log(error['responseText']);
                    //if error msg contain max_input_vars then show a proper msg
                    if(error['responseText'].includes('max_input_vars')) {
                        notyf.error({
                            message: tf_admin_params.max_input_vars_notice,
                            duration: 15000,
                            dismissible: true
                        });
                    } else {
                        notyf.error({
                            message: error['responseText'],
                            duration: 6000
                        });
                    }
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
                placeholder = $this.data('placeholder'),
                deleteData = $this.data('delete');

            if(deleteData === 'yes'){
                $('#' + id + '').select2({
                    placeholder: placeholder,
                    allowClear: true,
                    templateResult: TfFormatOption,
                    templateSelection: function (state) {
                        if (!state.id) {
                            return state.text;
                        }
                
                        // Get the edit URL from the option's data attribute
                        var editUrl = $(state.element).data('edit-url');
                        if(editUrl){
                            var $state = $(
                                '<span>' + state.text + ' <a target="_blank" href="'+editUrl+'" class="tf-edit-room"><i class="fa-regular fa-pen-to-square"></i></a></span>'
                            );
                            return $state;
                        }
                
                        return state.text;
                    }
                });
            }else{
                $('#' + id + '').select2({
                    placeholder: placeholder,
                    allowClear: true,
                    templateSelection: function (state) {
                        if (!state.id) {
                            return state.text;
                        }
                
                        // Get the edit URL from the option's data attribute
                        var editUrl = $(state.element).data('edit-url');
                        if(editUrl){
                            var $state = $(
                                '<span>' + state.text + ' <a target="_blank" href="'+editUrl+'" class="tf-edit-room"><i class="fa-regular fa-pen-to-square"></i></a></span>'
                            );
                            return $state;
                        }
                
                        return state.text;
                    }
                });
            }
        
        }

        $('select.tf-select2').each(function () {
            var $this = $(this);
            tfSelect2Int($this);
        });

        function TfFormatOption(option) {
            if (!option.id) {
              return option.text;
            }

           var $option = $(
              '<span style="display: flex; justify-content: space-between;">' + option.text + '<span class="tf-remove-button" data-id="' + option.id + '">Remove</span></span>'
            );
    
            return $option;
        }
        $(document).on('select2:selecting', '.tf-select2', function (e) {

            if (e.params.args.originalEvent.target.className === 'tf-remove-button') {
                e.stopPropagation();
                e.preventDefault();

                let $this = $(this);
                let parentDiv = $this.closest('.tf-fieldset');
                let categoryName = parentDiv.find('#category_name').val();
                let categorySelect = parentDiv.find('#category_select_field_name').val();
                var termId=$(e.params.args.originalEvent.target).data("id");

                $.ajax({
                    url: tf_options.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'tf_delete_category_data',
                        _nonce: tf_admin_params.tf_nonce,
                        term_id: termId,
                        categoryName: categoryName
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            // Remove the option and trigger the change event
                            let $selectField = $('#' + categorySelect);

                            // Remove the option from Select2
                            $selectField.find('option[value="' + termId + '"]').remove();

                            // Close the Select2 dropdown
                            $selectField.select2('close');

                        } else {
                            
                        }
                    }
                });
            }
        });


        $('select.tf-shortcode-select2').each(function(e) {
            let $this = $(this);
            let id = $this.attr("id");
            tfSelect2Int($this);

            $(this).on("select2:select", function (e) { 
                var select_val = $(e.currentTarget).val();
                if(select_val && select_val.includes("'all'")) {
                    $(this).val(["'all'"]).trigger('change.select2');
                }
            });
        })

        /*
        * Room options count
        */
        function roomOptionsArr(){
            var optionsArr = [];
            $('.tf-repeater-wrap-room-options .tf-single-repeater-room-options').each(function(i){
                // Get the dynamic index from the tf_repeater_count field
                let index = $(this).find('[name="tf_repeater_count"]').val();
                // Extract the option title and type using the dynamic index
                let optionType = $(this).find(`[name="tf_room_opt[room-options][${index}][option_pricing_type]"]`).val();
                let optionTitle = $(this).find(`[name="tf_room_opt[room-options][${index}][option_title]"]`).val();
                if (index !== undefined) {
                    optionsArr[index] = {
                        index: index,
                        title: optionTitle,
                        type: optionType
                    };
                }
            })
            return optionsArr;
        }

        $(window).on('load', function () {
            roomOptionsArr();
        });

        /*
        * Room Availability Calendar
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
                        setRoomCheckInOut("", "", self.roomCalData);
                    } else {
                        var zone = moment(start).format("Z");
                        zone = zone.split(":");
                        zone = "" + parseInt(zone[0]) + ":00";
                        var check_in = moment(start).utcOffset(zone).format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        var check_out = moment(end).utcOffset(zone).subtract(1, 'day').format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        setRoomCheckInOut(check_in, check_out, self.roomCalData);
                    }
                },
                events: function ({start, end, startStr, endStr, timeZone}, successCallback, failureCallback) {
                    $.ajax({
                        url: tf_options.ajax_url,
                        dataType: "json",
                        type: "POST",
                        data: {
                            action: "tf_get_hotel_room_availability",
                            _nonce: tf_admin_params.tf_nonce,
                            new_post: $(self.container).find('[name="new_post"]').val(),
                            room_id: $(self.container).find('[name="room_id"]').val(),
                            avail_date: $(self.container).find('.avail_date').val(),
                            option_arr: roomOptionsArr(),
                        },
                        beforeSend: function () {
                            $(self.container).css({'pointer-events': 'none', 'opacity': '0.5'});
                            $(self.calendar).addClass('tf-content-loading');
                        },
                        success: function (doc) {
                            if (typeof doc == "object") {
                                successCallback(doc?.avail_data);
                            }

                            $('.tf-single-options').html(doc?.options_html);

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
                    setRoomCheckInOut(startTime, endTime, self.roomCalData);
                    let priceBy = $('.tf_room_pricing_by').val();
                    if (priceBy === '1') {
                        if (typeof event.extendedProps.price != 'undefined') {
                            $("[name='tf_room_price']", self.roomCalData).val(event.extendedProps.price);
                        }
                    } else if(priceBy === '2'){
                        if (typeof event.extendedProps.adult_price != 'undefined') {
                            $("[name='tf_room_adult_price']", self.roomCalData).val(event.extendedProps.adult_price);
                        }
                        if (typeof event.extendedProps.child_price != 'undefined') {
                            $("[name='tf_room_child_price']", self.roomCalData).val(event.extendedProps.child_price);
                        }
                    } else {
                        if(event.extendedProps.options_count != 0) {
                            for (var i = 0; i <= event.extendedProps.options_count - 1; i++) {
                                $("[name='tf_room_option_" + i + "']", self.roomCalData).prop('checked', event.extendedProps["tf_room_option_" + i] == 1);

                                $("[name='tf_option_room_price_" + i + "']", self.roomCalData).val(event.extendedProps["tf_option_room_price_" + i]);
                                $("[name='tf_option_adult_price_" + i + "']", self.roomCalData).val(event.extendedProps["tf_option_adult_price_" + i]);
                                $("[name='tf_option_child_price_" + i + "']", self.roomCalData).val(event.extendedProps["tf_option_child_price_" + i]);
                            }
                        }
                    }
                    if (event.extendedProps.status) {
                        $("[name='tf_room_status'] option[value=" + event.extendedProps.status + "]", self.roomCalData).prop("selected", true);
                    }
                },
            };
            this.init = function () {
                self.container = jQuery(container);
                self.calendar = container.querySelector('.tf-room-cal');
                self.roomCalData = $('.tf-room-cal-field', self.container);
                setRoomCheckInOut('', '', self.roomCalData);
                self.initCalendar();
            }
            this.initCalendar = function () {
                if (typeof FullCalendar != 'undefined') {
                    self.fullCalendar = new FullCalendar.Calendar(self.calendar, self.fullCalendarOptions);
                    self.fullCalendar.render();
                }
            }
        };

        function setRoomCheckInOut(check_in, check_out, roomCalData) {
            $('.tf_room_check_in', roomCalData).val(check_in);
            $('.tf_room_check_out', roomCalData).val(check_out);
        }

        function roomResetForm(roomCalData) {
            $('.tf_room_check_in', roomCalData).val('');
            $('.tf_room_check_out', roomCalData).val('');
            $('[name="tf_room_price"]', roomCalData).val('');
            $('[name="tf_room_adult_price"]', roomCalData).val('');
            $('[name="tf_room_child_price"]', roomCalData).val('');
        }

        const tfHotelCalendar = () => {
            $('.tf-room-cal-wrap').each(function (index, el) {
                var room = new roomCal(el);
                room.init();
            });
        }
        tfHotelCalendar();

        $('.tf-room-cal-wrap').each(function (index, el) {
            let checkIn = $(el).find('[name="tf_room_check_in"]').flatpickr({
                dateFormat: tf_options.tf_admin_date_format || 'MM/DD/YYYY',
                minDate: 'today',
                altInput: true,
                altFormat: tf_options.tf_admin_date_format,
                onChange: function (selectedDates, dateStr, instance) {
                    checkOut.set('minDate', dateStr);
                }
            });

            let checkOut = $(el).find('[name="tf_room_check_out"]').flatpickr({
                dateFormat: tf_options.tf_admin_date_format || 'MM/DD/YYYY',
                minDate: 'today',
                altInput: true,
                altFormat: tf_options.tf_admin_date_format,
                onChange: function (selectedDates, dateStr, instance) {
                    checkIn.set('maxDate', dateStr);
                }
            });
        });

        $(document).on('click', '.tf_room_cal_update', function (e) {
            e.preventDefault();

            let btn = $(this);
            let container = btn.closest('.tf-room-cal-wrap');
            let containerEl = btn.closest('.tf-room-cal-wrap')[0];
            let cal = container.find('.tf-room-cal');
            let data = $('input, select', container.find('.tf-room-cal-field')).serializeArray();
            let priceBy = $('.tf_room_pricing_by').val();
            let avail_date = container.find('.avail_date');
            data.push({name: 'action', value: 'tf_add_hotel_room_availability'});
            data.push({name: '_nonce', value: tf_admin_params.tf_nonce});
            data.push({name: 'price_by', value: priceBy});
            data.push({name: 'avail_date', value: avail_date.val()});
            data.push({name: 'options_count', value: roomOptionsArr().length});

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
                            roomResetForm(container);

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

        $(document).on('change', '.tf_room_pricing_by', function (e) {
            let pricing_by = $(this).val();

            if (pricing_by === '1') {
                $('.tf-price-by-room').show();
                $('.tf-price-by-person').hide();
                $('.tf-room-cal-field .tf-single-option').hide();
            } else if (pricing_by === '2') {
                $('.tf-price-by-person').show();
                $('.tf-price-by-room').hide();
                $('.tf-room-cal-field .tf-single-option').hide();
            } else if(pricing_by === '3') {
                $('.tf-price-by-room').hide();
                $('.tf-price-by-person').hide();
                $('.tf-room-cal-field .tf-single-option').show();
            }
        });

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
            if ($this.hasClass('tf_apartment_availability_by_date')){
                tfApartmentCalendar();
            }
        });

        /*
        * Apartment Availability Calendar
        * @since 2.10.2
        * @auther: Foysal
        */
        var apartmentCal = function (container) {
            var self = this;
            this.container = container;
            this.calendar = null
            this.apartmentCalData = null;
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
                        setAptCheckInOut("", "", self.apartmentCalData);
                    } else {
                        var zone = moment(start).format("Z");
                        zone = zone.split(":");
                        zone = "" + parseInt(zone[0]) + ":00";
                        var check_in = moment(start).utcOffset(zone).format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        var check_out = moment(end).utcOffset(zone).subtract(1, 'day').format(String(tf_options.tf_admin_date_format || "MM/DD/YYYY").toUpperCase());
                        setAptCheckInOut(check_in, check_out, self.apartmentCalData);
                    }
                },
                events: function ({start, end, startStr, endStr, timeZone}, successCallback, failureCallback) {
                    $.ajax({
                        url: tf_options.ajax_url,
                        dataType: "json",
                        type: "POST",
                        data: {
                            action: "tf_get_apartment_availability",
                            _nonce: tf_admin_params.tf_nonce,
                            new_post: $('[name="new_post"]').val(),
                            apartment_id: $('[name="apartment_id"]').val(),
                            apt_availability: $('.apt_availability').val(),
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
                    setAptCheckInOut(startTime, endTime, self.apartmentCalData);
                    let pricingType = $('.tf_apt_pricing_type').val();
                    if (pricingType === 'per_night') {
                        if (typeof event.extendedProps.price != 'undefined') {
                            $("[name='tf_apt_price']", self.apartmentCalData).val(event.extendedProps.price);
                        }
                    } else {
                        if (typeof event.extendedProps.adult_price != 'undefined') {
                            $("[name='tf_apt_adult_price']", self.apartmentCalData).val(event.extendedProps.adult_price);
                        }
                        if (typeof event.extendedProps.child_price != 'undefined') {
                            $("[name='tf_apt_child_price']", self.apartmentCalData).val(event.extendedProps.child_price);
                        }
                        if (typeof event.extendedProps.infant_price != 'undefined') {
                            $("[name='tf_apt_infant_price']", self.apartmentCalData).val(event.extendedProps.infant_price);
                        }
                    }
                    if (event.extendedProps.status) {
                        $("[name='tf_apt_status'] option[value=" + event.extendedProps.status + "]", self.apartmentCalData).prop("selected", true);
                    }
                },
            };
            this.init = function () {
                self.container = jQuery(container);
                self.calendar = container.querySelector('.tf-apt-cal');
                self.apartmentCalData = $('.tf-apt-cal-field', self.container);
                setAptCheckInOut('', '', self.apartmentCalData);
                self.initCalendar();
            }
            this.initCalendar = function () {
                if (typeof FullCalendar != 'undefined') {
                    self.fullCalendar = new FullCalendar.Calendar(self.calendar, self.fullCalendarOptions);
                    self.fullCalendar.render();
                }
            }
        };

        function setAptCheckInOut(check_in, check_out, apartmentCalData) {
            $('.tf_apt_check_in', apartmentCalData).val(check_in);
            $('.tf_apt_check_out', apartmentCalData).val(check_out);
        }

        function aptResetForm(apartmentCalData) {
            $('.tf_apt_check_in', apartmentCalData).val('');
            $('.tf_apt_check_out', apartmentCalData).val('');
            $('[name="tf_apt_price"]', apartmentCalData).val('');
            $('[name="tf_apt_adult_price"]', apartmentCalData).val('');
            $('[name="tf_apt_child_price"]', apartmentCalData).val('');
            $('[name="tf_apt_infant_price"]', apartmentCalData).val('');
        }

        const tfApartmentCalendar = () => {
            $('.tf-apt-cal-wrap').each(function (index, el) {
                var apt = new apartmentCal(el);
                apt.init();

                let checkIn = $(el).find('[name="tf_apt_check_in"]').flatpickr({
                    dateFormat: tf_options.tf_admin_date_format || 'MM/DD/YYYY',
                    minDate: 'today',
                    altInput: true,
                    altFormat: tf_options.tf_admin_date_format,
                    onChange: function (selectedDates, dateStr, instance) {
                        checkOut.set('minDate', dateStr);
                    }
                });

                let checkOut = $(el).find('[name="tf_apt_check_out"]').flatpickr({
                    dateFormat: tf_options.tf_admin_date_format || 'MM/DD/YYYY',
                    minDate: 'today',
                    altInput: true,
                    altFormat: tf_options.tf_admin_date_format,
                    onChange: function (selectedDates, dateStr, instance) {
                        checkIn.set('maxDate', dateStr);
                    }
                });
            });
        }
        tfApartmentCalendar();

        $(document).on('click', '.tf_apt_cal_update', function (e) {
            e.preventDefault();

            let btn = $(this);
            let container = btn.closest('.tf-apt-cal-wrap');
            let containerEl = btn.closest('.tf-apt-cal-wrap')[0];
            let cal = container.find('.tf-apt-cal');
            let data = $('input, select', container.find('.tf-apt-cal-field')).serializeArray();
            let pricingType = $('.tf_apt_pricing_type').val();
            let aptAvailability = container.find('.apt_availability');
            data.push({name: 'action', value: 'tf_add_apartment_availability'});
            data.push({name: '_nonce', value: tf_admin_params.tf_nonce});
            data.push({name: 'pricing_type', value: pricingType});
            data.push({name: 'apt_availability', value: aptAvailability.val()});

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
                            aptAvailability.val(response.data.apt_availability)
                            notyf.success(response.data.message);
                            aptResetForm(container);

                            var apt = new apartmentCal(containerEl);
                            apt.init();
                            if (apt.fullCalendar) {
                                apt.fullCalendar.refetchEvents();
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

        $(document).on('change', '.tf_apt_pricing_type', function (e) {
            let pricingType = $(this).val();

            if (pricingType === 'per_night') {
                $('.tf-price-by-night').show();
                $('.tf-price-by-person').hide();
            } else if (pricingType === '2') {
                $('.tf-price-by-person').show();
                $('.tf-price-by-night').hide();
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

            setTimeout(function() { 
                TF_wp_editor($id);
            }, 1000);
            
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
            if ($('.tf-repeater-wrap .tf-single-repeater-hotel-book-confirm-field').length > 0) {
                $('.tf-repeater-wrap .tf-single-repeater-hotel-book-confirm-field').each(function () {
                    let $this = $(this);
                    let repeaterCount = $this.find('input[name="tf_repeater_count"]').val();
                    if (0 == repeaterCount || 1 == repeaterCount || 2 == repeaterCount) {
                        $this.find('.tf_hidden_fields').hide();
                        $this.find('.tf-repeater-icon-clone').hide();
                        $this.find('.tf-repeater-icon-delete').hide();
                    }
                });
            }
            if ($('.tf-repeater-wrap .tf-single-repeater-car-book-confirm-field').length > 0) {
                $('.tf-repeater-wrap .tf-single-repeater-car-book-confirm-field').each(function () {
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
            var maxIndex = parseInt($(this).closest('.tf-repeater').attr("data-max-index")) + 1;
            $(this).closest('.tf-repeater').attr("data-max-index", maxIndex);

            $this_parent.find('.tf-repeater-wrap .tf-field-notice-inner').remove();
            // Chacked maximum repeater
            if (!tf_admin_params.is_pro && max != '' && count >= max) {
                $this_parent.find('.tf-repeater-wrap').append('<div class="tf-field-notice-inner tf-notice-danger" style="display: block;">You have reached limit in free version. Please subscribe to Pro for unlimited access</div>');
                return false;
            }

            // Repeater Count Add Value
            add_value.find(':input[name="tf_repeater_count"]').val(maxIndex);

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
                repeatColorField.find('input.tf-color').each(function () {
                    var color_field =  $(this).clone(); 
                    if($(this).closest('li').length > 0){
                        $(this).closest('li').append(color_field);
                    }else{
                        $(this).closest('.tf-fieldset').append(color_field);
                    }
                    $(this).closest('.wp-picker-container').remove();
                 });
                tfColorInt(repeatColorField);
            }

            if (parent_field == '') {
                // Update  repeater name And id
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                });
                var update_paren = add_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                }
                add_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {
                // Update  repeater name And id
                var update_paren = add_value.find(':input[name="tf_parent_field"]').val();
                add_value.find(':input').each(function () {
                    this.name = this.name.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                    this.id = this.id.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                });
            }
            // Update Repeaterr label
            add_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                    $(this).attr("for", for_value);
                }
            });
            // Update Icon select id
            add_value.find('.tf-icon-select').each(function (index) {
                var icon_id = $(this).attr("id");
                if (typeof icon_id !== "undefined") {
                    icon_id = icon_id + index + maxIndex;
                    $(this).attr("id", icon_id)

                }
            });
            // Update Data depend id
            add_value.find('[data-depend-id]').each(function () {
                var data_depend_id = $(this).attr("data-depend-id");
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            // Update Data Controller
            add_value.find('[data-controller]').each(function () {
                var data_controller = $(this).attr("data-controller");
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][00]', '[' + current_field + '][' + maxIndex + ']');
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
                this.id = this.id.replace('' + current_field + '__00', '' + current_field + '__' + maxIndex + '');
                var parent_repeater_id = $(this).attr('id');
                var $this = $(this);
                tfSelect2Int($this);
            });

            // repeater dependency repeater
            TF_dependency();

            // Booking Confirmation repeater Hidden field
            TF_Booking_Confirmation();
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
            var maxIndex = parseInt($(this).closest('.tf-repeater').attr("data-max-index")) + 1;
            $(this).closest('.tf-repeater').attr("data-max-index", maxIndex);

            $this_parent.find('.tf-field-notice-inner').remove();
            // Chacked maximum repeater
            if (!tf_admin_params.is_pro && max != '' && count >= max) {
                $this_parent.append('<div class="tf-field-notice-inner tf-notice-danger" style="display: block;">You have reached limit in free version. Please subscribe to Pro for unlimited access</div>');
                return false;
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
                repeatColorField.find('input.tf-color').each(function () {
                    var color_field =  $(this).clone(); 
                    if($(this).closest('li').length > 0){
                        $(this).closest('li').append(color_field);
                    }else{
                        $(this).closest('.tf-fieldset').append(color_field);
                    }
                    $(this).closest('.wp-picker-container').remove();
                 });
                tfColorInt(repeatColorField);
            }

            if (parent_field == '') {
                // Replace input id and name
                clone_value.find(':input').each(function () {
                    if ($(this).closest('.tf-single-repeater-clone').length == 0) {
                        this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                        this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                    }
                });
                var update_paren = clone_value.find('.tf-repeater input[name="tf_parent_field"]').val();
                if (typeof update_paren !== "undefined") {
                    var update_paren = update_paren.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                }
                clone_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);

            } else {
                // Replace input id and name
                clone_value.find(':input').each(function () {
                    if ($(this).closest('.tf-single-repeater-clone').length == 0) {
                        this.name = this.name.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                        this.id = this.id.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                    }
                });
            }
            clone_value.find('label').each(function () {
                var for_value = $(this).attr("for");
                if (typeof for_value !== "undefined") {
                    for_value = for_value.replace('_____', '').replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                    var for_value = $(this).attr("for", for_value);
                }
            });
            // Update Icon select id
            clone_value.find('.tf-icon-select').each(function (index) {
                var icon_id = $(this).attr("id");
                if (typeof icon_id !== "undefined") {
                    icon_id = icon_id + index + maxIndex;
                    $(this).attr("id", icon_id)

                }
            });
            // Replace Data depend id ID
            clone_value.find('[data-depend-id]').each(function () {
                var data_depend_id = $(this).attr("data-depend-id");
                if (typeof data_depend_id !== "undefined") {
                    data_depend_id = data_depend_id.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                    $(this).attr("data-depend-id", data_depend_id);
                }
            });
            // Replace Data depend id ID
            clone_value.find('[data-controller]').each(function () {
                var data_controller = $(this).attr("data-controller");
                if (typeof data_controller !== "undefined") {
                    data_controller = data_controller.replace('[' + current_field + '][' + repeater_count + ']', '[' + current_field + '][' + maxIndex + ']');
                    $(this).attr("data-controller", data_controller);
                }
            });
            // Replace Data repeter Count id ID
            clone_value.find('input[name="tf_repeater_count"]').val(maxIndex)

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
                this.id = this.id.replace('' + current_field + '__' + repeater_count, '' + current_field + '__' + maxIndex + '');
                var $this = $(this);
                tfSelect2Int($this);
            });

            // Dependency value
            TF_dependency();
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

        // switch-group Drag and  show
        $(".tf-switch-group-wrap").sortable({
            placeholder: "tf-switch-drag-highlight"
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

            tfHotelCalendar();
            tfApartmentCalendar();
        });

        // Select 2 add new category
        $(document).on('click', '.tf-add-category span', function (event) { 
            event.preventDefault();
            var $this = $(this);
            var parentDiv = $this.closest('.tf-fieldset');
            parentDiv.children('.tf-popup-box').css('display', 'flex');
        });

        // Close Popup
        $(document).on('click', '.tf-add-category-box-close', function (event) { 
            event.preventDefault();
            $('.tf-popup-box').hide();
        });

        // Create Category
        $(document).on('click', '.tf-category-button', function (event) { 
            event.preventDefault();
            var $this = $(this);
            var parentDiv = $this.closest('.tf-add-category-box');
            let categoryName = parentDiv.find('#category_name').val();
            let categoryTitle = parentDiv.find('#category_title').val();
            let parentCategory = parentDiv.find('#parent_category').val();
            let categorySelect = parentDiv.find('#category_select_field_name').val();

            $.ajax({
                url: tf_options.ajax_url,
                method: 'POST',
                data: {
                    action: 'tf_insert_category_data',
                    _nonce: tf_admin_params.tf_nonce,
                    categoryName: categoryName,
                    categoryTitle: categoryTitle,
                    parentCategory: parentCategory
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.insert_category) {
                        // Store to List and Selected
                        var newOption = new Option(data.insert_category.title, data.insert_category.id, true, true);
                        $('#'+categorySelect).append(newOption).trigger('change');

                        // Store to Popup List
                        var newPopuOption = new Option(data.insert_category.title, data.insert_category.id, false, false);
                        parentDiv.find('#parent_category').append(newPopuOption).trigger('change');
                    }
                    $('.tf-popup-box').hide();
                    parentDiv.find('#category_title').val('');
                    parentDiv.find('#parent_category').val('');
                }
            });

        });

        // Create Post
        $(document).on('click', '.tf-add-new-post-button', function (event) { 
            event.preventDefault();
            var $this = $(this);
            var parentDiv = $this.closest('.tf-add-category-box');
            let postType = parentDiv.find('.post_type').val();
            let postTitle = parentDiv.find('.post_title').val();
            let postSelect = parentDiv.find('.post_select_field_name').val();
            let fieldId = parentDiv.find('.field_id').val();
            let postId = parentDiv.find('.post_id').val();

            if(postTitle){
                $.ajax({
                    url: tf_options.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'tf_insert_post_data',
                        _nonce: tf_admin_params.tf_nonce,
                        postType: postType,
                        postTitle: postTitle,
                        fieldId: fieldId,
                        postId: postId
                    },
                    beforeSend: function(){
                        $this.addClass('tf-btn-loading');
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.insert_post) {
                            // Store to List and Selected
                            var newOption = new Option(data.insert_post.title, data.insert_post.id, true, true);
                            
                            if(fieldId == 'tf_rooms'){
                                $(newOption).attr('data-edit-url', data.insert_post.edit_url);
                            }
                            
                            $('#'+postSelect).append(newOption).trigger('change');
                        }
                        $this.removeClass('tf-btn-loading');
                        $('.tf-popup-box').hide();
                        parentDiv.find('.post_title').val('');
                    }
                });
            } else {
                notyf.error('Please enter title');
            }

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
        $('#addtag > .submit #submit').on("click", function () {
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

                $latitude.on('change', function () {

                })

                function updateLocationField(latitude, longitude) {
                    var apiUrl = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latitude + '&lon=' + longitude;

                    $.ajax({
                        url: apiUrl,
                        dataType: 'json',
                        success: function (data) {
                            $search_input.val(data.display_name)
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error:', textStatus, errorThrown);
                        }
                    });
                }

                mapInit.on('click', function (data) {
                    mapMarker.setLatLng(data.latlng);
                    update_latlng(data.latlng);
                    updateLocationField(data.latlng.lat, data.latlng.lng)
                });

                mapInit.on('zoom', function () {
                    update_latlng(mapMarker.getLatLng());
                });

                mapMarker.on('drag', function () {
                    update_latlng(mapMarker.getLatLng());
                });

                mapMarker.on('dragend', function (e) {
                    let currentLng = e.target._latlng.lng
                    let currentLat = e.target._latlng.lat

                    updateLocationField(currentLat, currentLng)
                })

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

        $('.tf-mobile-tabs').on("click", function (e) {
            e.preventDefault();
            $(".tf-admin-tab").toggleClass('active');
        });


        $('.tf-faq-title').on("click", function () {
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
                        _nonce: tf_admin_params.tf_nonce,
                        month: monthTarget,
                        year: yearTarget,
                    },
                    success: function (data) {
                        if(!data.success){
                            $("#tf-report-loader").removeClass('show');
                            notyf.error(data.data)
                        } else {
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
                        _nonce: tf_admin_params.tf_nonce,
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
            var section_title = $this.find('.tf-shortcode-title-field ').attr('data-title');
            var section_subtitle = $this.find('.tf-shortcode-subtitle-field ').attr('data-subtitle');

            var tour_tab_title = $this.find('.tf-shortcode-tour-tab-title-field ').attr('data-tour-tab-title');
            var hotel_tab_title = $this.find('.tf-shortcode-hotel-tab-title-field ').attr('data-hotel-tab-title');
            var apartment_tab_title = $this.find('.tf-shortcode-apartment-tab-title-field ').attr('data-apartment-tab-title');
            var car_tab_title = $this.find('.tf-shortcode-car-tab-title-field ').attr('data-car-tab-title');

            if (option_name != undefined && option_name != '') {
                data = option_name + '=' + (data.length ? data : '""');
            }
            if (post_count != undefined && post_count != '') {
                data = post_count + '=' + (data.length ? data : '""');
            }
            if (section_title != undefined && section_title != '' && data.length) {
                data = section_title + '=' + (data.length ? `"${data}"` : '""');
            }
            if (section_subtitle != undefined && section_subtitle != '' && data.length ) {
                data = section_subtitle + '=' + (data.length ? `"${data}"` : '""');
            }
            if (tour_tab_title != undefined && tour_tab_title != '' && data.length) {
                data = tour_tab_title + '=' + (data.length ? `"${data}"` : '""');
            }
            if (hotel_tab_title != undefined && hotel_tab_title != '' && data.length ) {
                data = hotel_tab_title + '=' + (data.length ? `"${data}"` : '""');
            }
            if (apartment_tab_title != undefined && apartment_tab_title != '' && data.length ) {
                data = apartment_tab_title + '=' + (data.length ? `"${data}"` : '""');
            }
            if (car_tab_title != undefined && car_tab_title != '' && data.length ) {
                data = car_tab_title + '=' + (data.length ? `"${data}"` : '""');
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

        $this.parents('.tf-shortcode-generator-single').on("mouseup", function (e) {
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
    
    $(document).ready(function () {
        // $('.tf-import-btn').on('click', function (event) {
        //     event.preventDefault();
        //     // Get the import URL from the button's href attribute
        //     var importUrl = $(this).attr('href');
        //     // Get the import data from the textarea
        //     var importData = $('textarea[name="tf_import_option"]').val().trim();
        //     if (importData == '') {
        //         alert(tf_options.tf_export_import_msg.import_empty);
        //         let importField = $('textarea[name="tf_import_option"]');
        //         importField.focus();
        //         importField.css('border', '1px solid red');
        //         return;
        //     } else {
        //         //confirm data before send
        //         if (!confirm(tf_options.tf_export_import_msg.import_confirm)) {
        //             return;
        //         }
        //         $.ajax({
        //             url: importUrl,
        //             method: 'POST',
        //             data: {
        //                 action: 'tf_import',
        //                 nonce: tf_admin_params.tf_nonce,
        //                 tf_import_option: importData,
        //             },
        //             beforeSend: function () {
        //                 $('.tf-import-btn').html('Importing...');
        //                 $('.tf-import-btn').attr('disabled', 'disabled');
        //             },
        //             success: function (response) {
        //                 if (response.success) {
        //                     alert(tf_options.tf_export_import_msg.imported);
        //                     $('.tf-import-btn').html('Imported');
        //                     window.location.reload();
        //                 } else {
        //                     alert('Something went wrong!');
        //                 }
        //             }
        //         });
        //     }
        // })
        $(document).on('click', '.tf-import-btn', function (event) { 
            event.preventDefault();
            var textarea = $('textarea[name="tf_import_option"]'); 
            var importData = textarea.val().trim();
            if (importData == '') {
                alert(tf_options.tf_export_import_msg.import_empty);
                let importField = $('textarea[name="tf_import_option"]');
                importField.focus();
                importField.css('border', '1px solid red');
                return;
            } 
            // Triger the form submit
            $(".tf-option-form").submit(); 
        });

        $(document).on('click', '.tf-export-btn', function (event) {
            event.preventDefault();

            $.ajax({
                url: tf_options.ajax_url,
                method: 'POST',
                data: {
                    action: 'tf_export_data',
                    _nonce: tf_admin_params.tf_nonce,
                },
                beforeSend: function () {
                    $('.tf-export-btn').html('Exporting...');
                    $('.tf-export-btn').attr('disabled', 'disabled');
                },
                success: function (response) {
                    let obj = JSON.parse(response);

                    if (obj.status === 'success') {
                        // Create a blob with the response value
                        var blob = new Blob([obj.data], {type: 'text/plain'});

                        // Create a temporary URL for the blob
                        var url = window.URL.createObjectURL(blob);

                        // Create a temporary link element
                        var link = document.createElement('a');
                        link.href = url;
                        link.download = 'tf-settings-export.json';

                        // Programmatically click the link to initiate the file download
                        link.click();

                        // Clean up the temporary URL
                        window.URL.revokeObjectURL(url);
                    } else {
                        notyf.error(obj.message);
                    }
                    $('.tf-export-btn').html('Export');
                    $('.tf-export-btn').removeAttr('disabled');
                },
                error: function (response) {
                    console.log(response);
                    $('.tf-export-btn').html('Export');
                    $('.tf-export-btn').removeAttr('disabled');
                }
            });


        });

        // Select 2 add new category
        $(document).on('click', '.tf-add-category i', function (event) { 
            event.preventDefault();
            $this = $(this);
            parentDiv = $this.closest('.tf-fieldset');
            parentDiv.children('#tf-popup-box').css('display', 'flex');
        });

        // Close Popup
        $(document).on('click', '.tf-add-category-box-close', function (event) { 
            event.preventDefault();
            $('#tf-popup-box').hide();
        });

    });


    /*
    * Author @Jahid
    * Color Palatte Change
    */
    jQuery(document).ready(function ($) {

        // Function to get the selected design
        function getSelectedDesign() {
            return $('input[name="tf_settings\\[color-palette-template\\]"]:checked').val();
        }
        
        const designDefault = {
            'd1': {
                brand: {
                    default: '#0E3DD8',
                    dark: '#0A2B99',
                    lite: '#C9D4F7',
                },
                text: {
                    heading: '#1C2130',
                    paragraph: '#494D59',
                    lite: '#F3F5FD',
                },
                border: {
                    default: '#16275F',
                    lite: '#D1D7EE',
                },
                filling: {
                    background: '#ffffff',
                    foreground: '#F5F7FF',
                },
            },
            'd2': {
                brand: {
                    default: '#B58E53',
                    dark: '#917242',
                    lite: '#FAEEDC',
                },
                text: {
                    heading: '#30281C',
                    paragraph: '#595349',
                    lite: '#FDF9F3',
                },
                border: {
                    default: '#5F4216',
                    lite: '#EEE2D1',
                },
                filling: {
                    background: '#ffffff',
                    foreground: '#FDF9F3',
                },
            },
            'd3': {
                brand: {
                    default: '#F97415',
                    dark: '#C75605',
                    lite: '#FDDCC3',
                },
                text: {
                    heading: '#30241C',
                    paragraph: '#595049',
                    lite: '#FDF7F3',
                },
                border: {
                    default: '#5F3416',
                    lite: '#EEDDD1',
                },
                filling: {
                    background: '#ffffff',
                    foreground: '#FFF9F5',
                },
            },
            'd4': {
                brand: {
                    default: '#003061',
                    dark: '#002952',
                    lite: '#C2E0FF',
                },
                text: {
                    heading: '#1C2630',
                    paragraph: '#495159',
                    lite: '#F3F8FD',
                },
                border: {
                    default: '#163A5F',
                    lite: '#D1DFEE',
                },
                filling: {
                    background: '#ffffff',
                    foreground: '#F5FAFF',
                },
            },
        };
    
        // Function to update custom colors based on the selected design
        function updateCustomColors(selectedDesign) {
            if (!selectedDesign) return;
    
            const colorPalettes = {
                'design-1': 'tf-d1',
                'design-2': 'tf-d2',
                'design-3': 'tf-d3',
                'design-4': 'tf-d4'
            };
    
            const selectedPalette = colorPalettes[selectedDesign];
            if (!selectedPalette) return;
    
            // Define the fields to be updated
            const fields = ['brand', 'text', 'border', 'filling'];
    
            fields.forEach(field => {
                $(`input[name^="tf_settings[${selectedPalette}-${field}]"]`).each(function () {
                    let fieldName = $(this).attr('name').split('[')[2].replace(']', ''); // Extract the sub-field (e.g., 'default', 'dark', 'lite')
                    let fieldValue = $(this).val();
                    let $customField = $(`input[name="tf_settings[tf-custom-${field}][${fieldName}]"]`);

                    if ($customField.length) {
                        $customField.val(fieldValue).trigger('change');
                    }
                });
            });
        }     
    
        // Initialize wpColorPicker for all relevant inputs
        $('input[name^="tf_settings[tf-d"]').wpColorPicker({
            change: function (event, ui) {
                let $colorField = $(event.target);
                let originalValue = $colorField.val();
                let newValue = ui.color.toString();

                updateCustomColors(getSelectedDesign());
    
                if (newValue !== originalValue) {
                    // Switch to custom palette
                    $('#tf_settings\\[color-palette-template\\]\\[custom\\]').prop("checked", true);
                    $('.tf-field.tf-field-color.tf-depend-hidden').addClass('tf-depend-on');
                    $('.tf-field.tf-field-color.tf-depend-hidden[data-value="custom"]').removeClass('tf-depend-on');
    
                    // Extract the field type and sub-field name
                    let nameAttr = $colorField.attr('name');
                    let match = nameAttr.match(/\[tf-(d\d+)-(brand|text|border|filling)]\[(.*?)\]/);
                    if (!match) return;
    
                    let design = match[1]; // e.g., 'd1', 'd2', etc.
                    let fieldType = match[2]; // e.g., 'brand', 'text', etc.
                    let fieldName = match[3]; // e.g., 'default', 'dark', 'lite', etc.
    
                    // Update the corresponding custom field
                    let $customColorField = $(`input[name="tf_settings[tf-custom-${fieldType}][${fieldName}]"]`);
                    if ($customColorField.length) {
                        
                        let value = $(`input[name="tf_settings[tf-${design}-${fieldType}][${fieldName}]"]`).val();
                        $(`input[name="tf_settings[tf-custom-${fieldType}][${fieldName}]"]`).val(value).trigger('change');
                        $(`input[name="tf_settings[tf-${design}-${fieldType}][${fieldName}]"]`).val(designDefault[design][fieldType][fieldName]).trigger('change');
                        $customColorField.val(newValue).trigger('change');
                    }
                }
            }
        });
    });
    
    /* Plugin insatall from dashboard sidebar */
    jQuery(document).ready(function($) {
        $('.tf-plugin-button').not('.pro').on('click', function(e) {
            e.preventDefault();

            let button = $(this);
            let action = button.data('action');
            let pluginSlug = button.data('plugin');
            let pluginFileName = button.data('plugin_filename');

            if (!action || !pluginSlug) return;

            let loader = button.find('.loader');
            let originalText = button.clone().children().remove().end().text().trim();

            if (action === 'install') {
                button.contents().first().replaceWith('Installing..');
            } else if (action === 'activate') {
                button.contents().first().replaceWith('Activating..');
            }

            button.addClass('loading').prop('disabled', true);
            loader.show();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'themefic_manage_plugin',
                    security: tf_admin_params.tf_nonce,
                    plugin_slug: pluginSlug,
                    plugin_filename: pluginFileName,
                    plugin_action: action
                },
                success: function(response) {
                    button.removeClass('loading').prop('disabled', false);
                    loader.hide();

                    if (response.success) {
                        if (action === 'install') {
                            button.contents().first().replaceWith('Activate');
                            button.data('action', 'activate').removeClass('install').addClass('activate');
                        } else if (action === 'activate') {
                            button.replaceWith('<span class="tf-plugin-button tf-plugin-status active">Activated</span>');
                        }
                    } else {
                        button.contents().first().replaceWith(originalText);
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    button.contents().first().replaceWith(originalText).removeClass('loading').prop('disabled', false);
                    loader.hide();
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
})(jQuery);