(function ($) {
    $(document).ready(function () {

        /**
         * Single Tour Gallery
         * 
         * Fancybox
         */
        $('[data-fancybox="tour-gallery"]').fancybox({
            loop: true,
            buttons: [
                "zoom",
                "slideShow",
                "fullScreen",
                "close"
            ],
            hash: false,
        });

    });
})(jQuery);