(function($) {

    $(document).ready(function () {

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            $('.tf-setup-step-1').show();

            //add step parameter to url if not exists and if exists replace it
            var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_one");
            } else {
                url.searchParams.set("step", "step_one");
            }
            window.history.pushState({}, '', url);

        });

        $(document).on('click', '.tf-setup-step-1 .tf-setup-next-btn', function (e) {
            e.preventDefault();
            $('.tf-setup-step-1').hide();
            $('.tf-setup-step-2').show();

            //add step parameter to url
            var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_two");
            } else {
                url.searchParams.set("step", "step_two");
            }
            window.history.pushState({}, '', url);
        });

        //on window history change
        window.onpopstate = function (event) {
            var url = new URL(window.location.href);
            var step = url.searchParams.get("step");
            if (step === "step_one") {
                $('.tf-welcome-step').hide();
                $('.tf-setup-step-1').show();
            } else if (step === "step_two") {
                $('.tf-setup-step-1').hide();
                $('.tf-setup-step-2').show();
            } else {
                $('.tf-setup-step-2').hide();
                $('.tf-welcome-step').show();
            }
        }




        var totalSteps = $(".steps li").length;

        $(".submit").on("click", function(){
            return false;
        });

        $(".steps li:nth-of-type(1)").addClass("active");
        $(".myContainer .form-container:nth-of-type(1)").addClass("active");

        $(".form-container").on("click", ".next", function() {
            $(".steps li").eq($(this).parents(".form-container").index() + 1).addClass("active");
            $(this).parents(".form-container").removeClass("active").next().addClass("active flipInX");
        });

        $(".form-container").on("click", ".back", function() {
            $(".steps li").eq($(this).parents(".form-container").index() - totalSteps).removeClass("active");
            $(this).parents(".form-container").removeClass("active flipInX").prev().addClass("active flipInY");
        });


        /*=========================================================
        *     If you won't to make steps clickable, Please comment below code
        =================================================================*/
        $(".steps li").on("click", function() {
            var stepVal = $(this).find("span").text();
            $(this).prevAll().addClass("active");
            $(this).addClass("active");
            $(this).nextAll().removeClass("active");
            $(".myContainer .form-container").removeClass("active flipInX");
            $(".myContainer .form-container:nth-of-type("+ stepVal +")").addClass("active flipInX");
        });


    });

})(jQuery);