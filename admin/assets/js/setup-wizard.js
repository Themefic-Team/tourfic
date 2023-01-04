(function($) {

    $(document).ready(function () {

        $(document).on('click', '.tf-setup-start-btn', function (e) {
            e.preventDefault();
            $('.tf-welcome-step').hide();
            $('.tf-setup-step-1').show();

            //add step parameter to url if not exists and if exists replace it
            var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_1");
            } else {
                url.searchParams.set("step", "step_1");
            }
            window.history.pushState({}, '', url);

        });

        $(document).on('click', '.tf-setup-next-btn, .tf-setup-skip-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let nextStep = step + 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + nextStep).fadeIn(300);
            });

            //add step parameter to url if not exists and if exists replace it
            var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_" + nextStep);
            } else {
                url.searchParams.set("step", "step_" + nextStep);
            }
            window.history.pushState({}, '', url);
        });

        $(document).on('click', '.tf-setup-prev-btn', function (e) {
            e.preventDefault();
            let step = $(this).closest('.tf-setup-step-container').data('step');
            let prevStep = step - 1;
            $('.tf-setup-step-' + step).fadeOut(300, function () {
                $('.tf-setup-step-' + prevStep).fadeIn(300);
            });

            //add step parameter to url if not exists and if exists replace it
            var url = new URL(window.location.href);
            if (url.searchParams.get("step") === null) {
                url.searchParams.append("step", "step_" + prevStep);
            } else {
                url.searchParams.set("step", "step_" + prevStep);
            }
            window.history.pushState({}, '', url);
        });

        //on window history change
        window.onpopstate = function (event) {
            var url = new URL(window.location.href);
            var step = url.searchParams.get("step");
            if (step !== null) {
                $('.tf-setup-step-container').hide();
                $('.tf-' + step).show();
            }
        }


    });

})(jQuery);