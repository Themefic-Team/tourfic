(function ($) {
    $(document).ready(function () {

        /**
         * Ajax login
         */
        $(document).on('click', '#tf-login .tf-submit', function (e) {
            e.preventDefault();

            var tf_login_nonce = $("input[name=tf_login_nonce]").val();
            var user = $("input[name=tf_log_user]").val();
            var pass = $("input[name=tf_log_pass]").val();

            var data = {
                action: 'tf_login',
                tf_login_nonce: tf_login_nonce,
                user: user,
                pass: pass,
            };

            $.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: data,
                beforeSend: function (response) {
                    
                },
                complete: function (response) {
                    
                },
                success: function (response) {
                    $(".tf-login-response").html(response);
                },
                error: function (data) {
                   
                }
            });

        });

        /**
         * Open login popup
         *
         * add class "tf-login-popup" in button/link
         */
        $(document).on('click', '.tf-login-popup', function (e) {
            e.preventDefault();

            $.fancybox.open({
                src: '#tf-login-popup',
                type: 'inline',
            });

        });

        /**
         * Ajax registration
         */
        $(document).on('click', '#tf-register .tf-submit', function (e) {
            e.preventDefault();

            var tf_reg_data = $("#tf-register").serializeArray();
            var tf_reg_nonce = $("input[name=tf_reg_nonce]").val();
            var user = $("input[name=tf_user]").val();
            var email = $("input[name=tf_email]").val();
            var pass = $("input[name=tf_pass]").val();
            var pass_confirm = $("input[name=tf_pass_confirm]").val();
            var role = $('input[name="tf_role"]:checked').val();

            var data = {
                action: 'tf_registration',
                tf_reg_nonce: tf_reg_nonce,
                tf_reg_data: tf_reg_data,
                user: user,
                email: email,
                pass: pass,
                pass_confirm: pass_confirm,
                role: role,
            };

            $.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: data,
                beforeSend: function (response) {
                    
                },
                complete: function (response) {
                    
                },
                success: function (response) {
                    $(".tf-reg-response").html(response);
                },
                error: function (data) {
                    console.log(data);
                }
            });

        });

        /**
         * Resend email verification url
         */
        $(document).on('click', '.resend-email-verification', function (e) {
            e.preventDefault();

            var user_id = $(this).attr("data-id");
            console.log(user_id);

            var data = {
                action: 'tf_resend_verification',
                user_id: user_id,
            };

            $.ajax({
                type: 'post',
                url: tf_vendor_params.ajax_url,
                data: data,
                success: function (response) {
                    $(".tf-verification-msg").html(tf_pro_params.email_sent_success);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        /**
         * Open registration popup
         *
         * add class "tf-reg-popup" in button/link
         */
        $(document).on('click', '.tf-reg-popup', function (e) {
            e.preventDefault();

            $.fancybox.open({
                src: '#tf-reg-popup',
                type: 'inline',
            });

        });
    });
})(jQuery);