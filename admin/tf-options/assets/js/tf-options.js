(function ($) {
    'use strict';
    $(document).ready(function () {
        /*
        * Section tab first one click on load
        * @author: Foysal
        */
        $(window).on('load', function () {
            if ($('.tf-admin-tab .tf-tablinks').length > 0) {
                $('.tf-admin-tab .tf-tablinks').first().trigger('click').addClass('active');
            }
        });

        /*
        * Each date field initialize flatpickr
         */
        $('.tf-field-date').each(function () {
            let $this = $(this),
                dateField = $this.find('input.flatpickr'),
                format = dateField.data('format'),
                multiple = dateField.data('multiple');

            if (dateField.length === 2) {
                let startDate = $this.find('.tf-date-from input.flatpickr').flatpickr({
                    dateFormat: format,
                    onChange: function (selectedDates, dateStr, instance) {
                        endDate.set('minDate', dateStr);
                        console.log('start', selectedDates, dateStr);
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

        /*
        * Each time field initialize flatpickr
         */
        $('.tf-field-time').each(function () {
            let $this = $(this),
                timeField = $this.find('input.flatpickr'),
                format = timeField.data('format');

            timeField.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: format,
            });
        });

        /*
        * Each select2 field initialize select2
         */
        $('.tf-field-select2').each(function () {
            let $this = $(this),
                selectField = $this.find('select.tf-select2'),
                placeholder = selectField.data('placeholder');

            selectField.select2({
                placeholder: placeholder,
                allowClear: true,
            });
        });

        /*
        * Each color field initialize wpColorPicker
         */
        $('.tf-field-color').each(function () {
            let $this = $(this),
                colorField = $this.find('input.tf-color');

            colorField.wpColorPicker();
        });

        //Custom modal
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
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                $("body").removeClass("tf-modal-open");
                $(".tf-modal").removeClass("tf-modal-show");
            }
        });

        /*
        * Icon tab
        * */
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
         */
        $(document).on('click', '.tf-icon-select .tf-admin-btn, .tf-icon-select .tf-icon-preview', function (e) {
            e.preventDefault();
            let btn = $(this);

            //icon select
            $(document).on('click', '.tf-icon-list li', function (e) {
                e.preventDefault();
                let $this = $(this);

                $('.tf-icon-list li').removeClass('active');
                $this.addClass('active');

                //remove disabled class
                $('.tf-icon-insert').removeClass('disabled');
            });

            //insert btn click
            $(document).on('click', '.tf-icon-insert', function (e) {
                e.preventDefault();
                let $this = $(this),
                    preview = btn.closest('.tf-icon-select').find('.tf-icon-preview'),
                    icon = $('.tf-icon-list li.active').data('icon');

                if (icon) {
                    preview.removeClass('tf-hide');
                    btn.closest('.tf-icon-select').find('.tf-icon-preview-wrap i').attr('class', icon);
                    btn.closest('.tf-icon-select').find('.tf-icon-value').val(icon).trigger('change');

                    //Close modal
                    $('.tf-modal').removeClass('tf-modal-show');
                    $('body').removeClass('tf-modal-open');
                }
            })
        });

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

        //Icon remove
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

        // Repeater jquery
        $(".tf-repeater").each(function () {
            let $this = $(this);
            let tf_repeater_add = $this.find('.tf-repeater-icon-add');
            tf_repeater_add.on('click', function () {
                let add_value = $this.find('.tf-single-repeater-clone .tf-single-repeater').clone();
                let count = $this.find('.tf-repeater-wrap .tf-single-repeater').length;
                // count =  count+1;
                // console.log(add_value)
                add_value.find(':input').each(function () {

                    this.name = this.name.replace('_____', '').replace('[0]', '[' + count + ']');
                    this.id = this.id.replace('_____', '').replace('[0]', '[' + count + ']');
                });
                let append = $this.find('.tf-repeater-wrap');
                add_value.appendTo(append).show();
                // $this.find('.tf-repeater-wrap').append(add_value).show();
            });

        });
        $(document).on('click', '.tf-repeater-icon-delete', function () {
            $(this).closest('.tf-single-repeater').remove();
        });
        $(document).on('click', '.tf-repeater-icon-clone', function () {
            alert(1);
            let clone_value = $(this).closest('.tf-single-repeater').html();
            $(this).closest('.tf-repeater-wrap').append('<div class="tf-single-repeater">' + clone_value + '</div>').show();
        });
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function () {
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').toggleClass("hide")
        });

    });
})(jQuery);


function openTab(evt, tabName) {
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
    });
})(jQuery);