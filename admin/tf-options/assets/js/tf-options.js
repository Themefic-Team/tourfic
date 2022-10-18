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