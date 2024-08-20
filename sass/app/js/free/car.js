(function ($, win) {
    $(document).ready(function () {
        // FAQ Accordion
        $('.tf-faq-head').on("click", function () {
            var $this = $(this);
            if (!$this.hasClass("active")) {
                $(".tf-question-desc").slideUp(400);
                $(".tf-faq-head").removeClass("active");
                $('.tf-faq-col').removeClass('active');
            }
            $this.toggleClass("active");
            $this.next().slideToggle();
            $(this).closest('.tf-faq-col').toggleClass('active');
        });

        // Tabs Section
        $('.tf-details-menu ul li').on("click", function () {
            var $this = $(this);
            $('.tf-details-menu ul li').removeClass('active');
            $this.addClass("active");
        });
    });

})(jQuery, window);