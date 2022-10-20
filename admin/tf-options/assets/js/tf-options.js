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

        // Repeater jquery
        $(".tf-repeater").each(function(){
            let $this = $(this);
            let tf_repeater_add = $this.find('.tf-repeater-icon-add');
            tf_repeater_add.on('click', function(){
                let add_value = $this.find('.tf-single-repeater-clone .tf-single-repeater').clone();
                let count = $this.find('.tf-repeater-wrap .tf-single-repeater').length;
                // count =  count+1;
                // console.log(add_value)
                add_value.find(':input').each(function (){

                    this.name = this.name.replace( '_____', '' ).replace('[0]', '['+ count +']');
                    this.id = this.id.replace( '_____', '' ).replace('[0]', '['+ count +']');
                });
                let append = $this.find('.tf-repeater-wrap');
                add_value.appendTo(append).show();
                // $this.find('.tf-repeater-wrap').append(add_value).show();
            });

        });
        $(document).on('click', '.tf-repeater-icon-delete', function(){
            $(this).closest('.tf-single-repeater').remove();
        });
        $(document).on('click', '.tf-repeater-icon-clone', function(){
            alert(1);
            let clone_value = $(this).closest('.tf-single-repeater').html();
            $(this).closest('.tf-repeater-wrap').append('<div class="tf-single-repeater">'+clone_value+'</div>').show();
        });
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function(){
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

        $('input[name="'+fieldname+'"]').val('');
        $('.'+tf_preview_class+'').html('');

    });

    // Gallery Image remove
    $(document).on("click", ".tf-gallery-remove", function (e) {
        e.preventDefault();
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $('input[name="'+fieldname+'"]').val('');
        $('.tf-fieldset > .'+tf_preview_class+'').html('');
        $('a.'+tf_preview_class+'').css("display","none");

    });

    $(document).ready(function () {

        // Single Image Upload

        $('body').on('click', '.tf-media-upload', function(e) {
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
                $('input[name="'+fieldname+'"]').val(attachment.url);
                $('.'+tf_preview_class+'').html(`<div class="tf-image-close" tf-field-name='${fieldname}'>✖</div><img src='${attachment.sizes.thumbnail.url}' />`);
            });
            frame.open();
            return false;
        });

        // Gallery Image Upload

        $('body').on('click', '.tf-gallery-upload', function(e) {
            var fieldname = $(this).attr("tf-field-name");
            var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');
            gframe = wp.media({
                title: "Select Gallery",
                button: {
                    text: "Insert Gallery"
                },
                multiple: true,
                editable:   true
            });

            gframe.on('select', function () {
                var image_ids = [];
                var image_urls = [];
                var attachments = gframe.state().get('selection').toJSON();
                $('.tf-fieldset > .'+tf_preview_class+'').html('');
                for (i in attachments) {
                    var attachment = attachments[i];
                    image_ids.push(attachment.id);
                    image_urls.push(attachment.sizes.thumbnail.url);
                    $('.tf-fieldset > .'+tf_preview_class+'').append(`<img src='${attachment.sizes.thumbnail.url}' />`);
                }
                $('input[name="'+fieldname+'"]').val(image_ids.join(","));
                $('a.'+tf_preview_class+'').css("display","inline-block");
            });

            gframe.open();
            return false;
        });
    });
})(jQuery);