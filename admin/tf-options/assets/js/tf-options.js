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